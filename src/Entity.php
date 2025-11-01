<?php

namespace aportela\LastFMWrapper;

class Entity extends \aportela\LastFMWrapper\LastFM
{
    protected mixed $parser = null;

    public ?string $raw;

    private ?\aportela\SimpleFSCache\Cache $cache = null;

    /**
     * https://www.last.fm/api/intro
     * Your account may be suspended if your application is continuously making several calls per second or if you’re making excessive calls. See our API Terms of Service for more information on limits.
     */
    private const MIN_THROTTLE_DELAY_MS = 500; // min allowed: 2 request per second
    public const DEFAULT_THROTTLE_DELAY_MS = 1000; // default: 1 request per second

    private int $originalThrottleDelayMS = 0;
    private int $currentThrottleDelayMS = 0;
    private int $lastThrottleTimestamp = 0;

    protected bool $refreshExistingCache = false;

    public function __construct(\Psr\Log\LoggerInterface $logger, \aportela\LastFMWrapper\APIFormat $apiFormat, string $apiKey, ?\aportela\SimpleFSCache\Cache $cache = null, int $throttleDelayMS = self::DEFAULT_THROTTLE_DELAY_MS)
    {
        parent::__construct($logger, $apiFormat, $apiKey);
        $this->logger->debug("LastFMWrapper\Entity::__construct");
        if ($throttleDelayMS < self::MIN_THROTTLE_DELAY_MS) {
            throw new \aportela\LastFMWrapper\Exception\InvalidThrottleMsDelayException("min throttle delay ms required: " . self::MIN_THROTTLE_DELAY_MS);
        }
        $this->originalThrottleDelayMS = $throttleDelayMS;
        $this->currentThrottleDelayMS = $throttleDelayMS;
        $this->lastThrottleTimestamp = intval(microtime(true) * 1000);
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
        // allow incrementing current throttle delay to a max of 5 seconds
        if ($this->currentThrottleDelayMS < 5000) {
            // set next throttle delay with current value * 2 (wait more time on next api calls)
            $this->currentThrottleDelayMS *= 2;
        }
    }

    /**
     * reset throttle to original value
     */
    protected function resetThrottle(): void
    {
        $this->currentThrottleDelayMS = $this->originalThrottleDelayMS;
    }

    /**
     * throttle api calls
     */
    protected function checkThrottle(): void
    {
        if ($this->currentThrottleDelayMS > 0) {
            $currentTimestamp = intval(microtime(true) * 1000);
            while (($currentTimestamp - $this->lastThrottleTimestamp) < $this->currentThrottleDelayMS) {
                usleep(10);
                $currentTimestamp = intval(microtime(true) * 1000);
            }
            $this->lastThrottleTimestamp = $currentTimestamp;
        }
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
