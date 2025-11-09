<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper;

abstract class Entity extends \aportela\LastFMWrapper\LastFM
{
    protected mixed $parser = null;

    public ?string $raw;
    
    private readonly \aportela\SimpleThrottle\Throttle $throttle;

    /**
     * https://www.last.fm/api/intro
     * Your account may be suspended if your application is continuously making several calls per second or if you’re making excessive calls. See our API Terms of Service for more information on limits.
     */
    private const int MIN_THROTTLE_DELAY_MS = 500;

     // min allowed: 2 request per second
    public const DEFAULT_THROTTLE_DELAY_MS = 1000; // default: 1 request per second

    public function __construct(\Psr\Log\LoggerInterface $logger, \aportela\LastFMWrapper\APIFormat $apiFormat, string $apiKey, int $throttleDelayMS = self::DEFAULT_THROTTLE_DELAY_MS, private readonly ?\aportela\SimpleFSCache\Cache $cache = null)
    {
        parent::__construct($logger, $apiFormat, $apiKey);
        if ($throttleDelayMS < self::MIN_THROTTLE_DELAY_MS) {
            $this->logger->critical(\aportela\LastFMWrapper\Entity::class . '::__construct - ERROR: invalid throttleDelayMS', [$throttleDelayMS, self::MIN_THROTTLE_DELAY_MS]);
            throw new \aportela\LastFMWrapper\Exception\InvalidThrottleMsDelayException("min throttle delay ms required: " . self::MIN_THROTTLE_DELAY_MS);
        }
        
        $this->throttle = new \aportela\SimpleThrottle\Throttle($this->logger, $throttleDelayMS, 5000, 10);
        $this->reset();
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
     * get cache format
     */
    protected function getCacheFormat(): \aportela\SimpleFSCache\CacheFormat|bool
    {
        if ($this->cache instanceof \aportela\SimpleFSCache\Cache) {
            return ($this->cache->getFormat());
        } else {
            return (false);
        }
    }

    /**
     * set cache format
     */
    protected function setCacheFormat(\aportela\SimpleFSCache\CacheFormat $cacheFormat): bool
    {
        if ($this->cache instanceof \aportela\SimpleFSCache\Cache) {
            $this->cache->setFormat($cacheFormat);
            return (true);
        } else {
            return (false);
        }
    }

    /**
     * save current raw data into disk cache
     */
    protected function saveCache(string $identifier, string $raw): bool
    {
        if ($this->cache instanceof \aportela\SimpleFSCache\Cache) {
            return ($this->cache->set($identifier, $raw));
        } else {
            return (false);
        }
    }

    /**
     * remove cache entry
     */
    protected function removeCache(string $identifier): bool
    {
        if ($this->cache instanceof \aportela\SimpleFSCache\Cache) {
            return ($this->cache->delete($identifier));
        } else {
            return (false);
        }
    }

    /**
     * read disk cache into current raw data
     */
    protected function getCache(string $identifier): bool
    {
        $this->reset();
        if ($this->cache instanceof \aportela\SimpleFSCache\Cache) {
            $cacheData = $this->cache->get($identifier, false);
            if (is_string($cacheData)) {
                $this->raw = $cacheData;
                return (true);
            } else {
                return (false);
            }
        } else {
            return (false);
        }
    }

    /**
     * http handler GET method wrapper for manage throttle & response, also catches CurlExecException (connection errors / server busy ?)
     */
    protected function httpGET(string $url): ?string
    {
        $this->logger->debug(\aportela\LastFMWrapper\Entity::class . '::httpGET - Opening URL', [$url]);
        try {
            $this->checkThrottle();
            $response = $this->http->GET($url);
            if ($response->code === 200) {
                $this->resetThrottle();
                return ($response->body);
            } elseif ($response->code === 404) {
                $this->logger->error(\aportela\LastFMWrapper\Entity::class . '::httpGET - Error opening URL', [$url, $response->code, $response->body]);
                throw new \aportela\LastFMWrapper\Exception\NotFoundException('Error opening URL: ' . $url, $response->code);
            } elseif ($response->code === 503) {
                $this->incrementThrottle();
                $this->logger->error(\aportela\LastFMWrapper\Entity::class . '::httpGET - Error opening URL', [$url, $response->code, $response->body]);
                throw new \aportela\LastFMWrapper\Exception\RateLimitExceedException('Error opening URL: ' . $url, $response->code);
            } else {
                $this->logger->error(\aportela\LastFMWrapper\Entity::class . '::httpGET - Error opening URL', [$url, $response->code, $response->body]);
                throw new \aportela\LastFMWrapper\Exception\HTTPException('Error opening URL: ' . $url, $response->code);
            }
        } catch (\aportela\HTTPRequestWrapper\Exception\CurlExecException $curlExecException) {
            $this->logger->error(\aportela\LastFMWrapper\Entity::class . '::httpGET - Error opening URL', [$url, $curlExecException->getCode(), $curlExecException->getMessage()]);
            $this->incrementThrottle(); // sometimes api calls return connection error, interpret this as rate limit response
            throw new \aportela\LastFMWrapper\Exception\RemoteAPIServerConnectionException('Error opening URL: ' . $url, 0, $curlExecException);
        }
    }
}
