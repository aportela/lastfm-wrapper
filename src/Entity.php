<?php

namespace aportela\LastFMWrapper;

class Entity extends \aportela\LastFMWrapper\LastFM
{
    protected mixed $parser = null;

    public ?string $raw;

    private ?\aportela\SimpleFSCache\Cache $cache = null;
    private \aportela\SimpleThrottle\Throttle $throttle;

    /**
     * https://www.last.fm/api/intro
     * Your account may be suspended if your application is continuously making several calls per second or if you’re making excessive calls. See our API Terms of Service for more information on limits.
     */
    private const MIN_THROTTLE_DELAY_MS = 500; // min allowed: 2 request per second
    public const DEFAULT_THROTTLE_DELAY_MS = 1000; // default: 1 request per second

    protected bool $refreshExistingCache = false;

    public function __construct(\Psr\Log\LoggerInterface $logger, \aportela\LastFMWrapper\APIFormat $apiFormat, string $apiKey, int $throttleDelayMS = self::DEFAULT_THROTTLE_DELAY_MS, ?\aportela\SimpleFSCache\Cache $cache = null)
    {
        parent::__construct($logger, $apiFormat, $apiKey);
        $this->logger->debug("LastFMWrapper\Entity::__construct");
        if ($throttleDelayMS < self::MIN_THROTTLE_DELAY_MS) {
            throw new \aportela\LastFMWrapper\Exception\InvalidThrottleMsDelayException("min throttle delay ms required: " . self::MIN_THROTTLE_DELAY_MS);
        }
        $this->throttle = new \aportela\SimpleThrottle\Throttle($this->logger, $throttleDelayMS, 5000, 10);
        $this->cache = $cache;
        $this->reset();
    }

    public function __destruct()
    {
        parent::__destruct();
        $this->logger->debug("LastFMWrapper\Entity::__destruct");
    }

    protected function reset(): void
    {
        $this->raw = null;
    }

    /**
     * increment throttle delay (time between api calls)
     * call this function when api returns rate limit exception
     * (or connection reset errors caused by remote server busy ?)
     */
    protected function incrementThrottle(): void
    {
        $this->throttle->increment(\aportela\SimpleThrottle\ThrottleDelayIncrementType::MULTIPLY_BY_2);
    }

    /**
     * reset throttle to original value
     */
    protected function resetThrottle(): void
    {
        $this->throttle->reset();
    }

    /**
     * throttle api calls
     */
    protected function checkThrottle(): void
    {
        $this->throttle->throttle();
    }

    /**
     * save current raw data into disk cache
     */
    protected function saveCache(string $hash, string $raw): bool
    {
        if ($this->cache !== null) {
            return ($this->cache->save($hash, $raw));
        } else {
            return (false);
        }
    }

    /**
     * remove cache entry
     */
    protected function removeCache(string $hash): bool
    {
        if ($this->cache !== null) {
            return ($this->cache->remove($hash));
        } else {
            return (false);
        }
    }

    /**
     * read disk cache into current raw data
     */
    protected function getCache(string $hash): bool
    {
        $this->reset();
        if ($this->cache !== null) {
            if ($cache = $this->cache->get($hash)) {
                $this->raw = $cache;
                return (true);
            } else {
                return (false);
            }
        } else {
            return (false);
        }
    }

    /**
     * http handler GET method wrapper for catching CurlExecException (connection errors / server busy ?)
     */
    protected function httpGET(string $url): \aportela\HTTPRequestWrapper\HTTPResponse
    {
        $this->logger->debug("Opening url: {$url}");
        try {
            return ($this->http->GET($url));
        } catch (\aportela\HTTPRequestWrapper\Exception\CurlExecException $e) {
            $this->logger->error("Error opening URL " . $url, [$e->getCode(), $e->getMessage()]);
            $this->incrementThrottle(); // sometimes api calls return connection error, interpret this as rate limit response
            throw new \aportela\LastFMWrapper\Exception\RemoteAPIServerConnectionException("Error opening URL " . $url, 0, $e);
        }
    }
}
