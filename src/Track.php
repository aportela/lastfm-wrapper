<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper;

class Track extends \aportela\LastFMWrapper\Entity
{
    private const string SEARCH_API_URL = "http://ws.audioscrobbler.com/2.0/?method=track.search&artist=%s&track=%s&api_key=%s&limit=%d&format=%s";

    private const string GET_API_URL = "http://ws.audioscrobbler.com/2.0/?method=track.getinfo&artist=%s&track=%s&api_key=%s&autocorrect=1&format=%s";

    public function getHash(string $artist, string $track): string
    {
        return (md5("TRACK:" . mb_strtolower(mb_trim($artist)) . mb_strtolower(mb_trim($track))));
    }

    /**
     * @return array<\aportela\LastFMWrapper\ParseHelpers\TrackHelper>
     */
    public function search(string $artist, string $track, int $limit = 1): array
    {
        $url = sprintf(self::SEARCH_API_URL, urlencode($artist), urlencode($track), $this->apiKey, $limit, $this->apiFormat->value);
        $responseBody = $this->httpGET($url);
        if (!in_array($responseBody, [null, '', '0'], true)) {
            switch ($this->apiFormat) {
                case \aportela\LastFMWrapper\APIFormat::XML:
                    $this->parser = new \aportela\LastFMWrapper\ParseHelpers\XML\Search\Track($responseBody);
                    break;
                case \aportela\LastFMWrapper\APIFormat::JSON:
                    $this->parser = new \aportela\LastFMWrapper\ParseHelpers\JSON\Search\Track($responseBody);
                    break;
                default:
                    $this->logger->error(\aportela\LastFMWrapper\Track::class . '::search - Error: invalid API format', [$this->apiFormat]);
                    /** @var string $format */
                    $format = $this->apiFormat->value;
                    throw new \aportela\LastFMWrapper\Exception\InvalidAPIFormat('Invalid API format: ' . $format);
            }

            return ($this->parser->parse());
        } else {
            $this->logger->error(\aportela\LastFMWrapper\Track::class . '::search - Error: empty body on API response', [$url]);
            throw new \aportela\LastFMWrapper\Exception\InvalidAPIResponse('Empty body on API response for URL: ' . $url);
        }
    }

    public function get(string $artist, string $track): \aportela\LastFMWrapper\ParseHelpers\TrackHelper
    {
        $cacheHash = $this->getHash($artist, $track);
        if (!$this->getCache($cacheHash)) {
            $url = sprintf(self::GET_API_URL, urlencode($artist), urlencode($track), $this->apiKey, $this->apiFormat->value);
            $responseBody = $this->httpGET($url);
            if (!in_array($responseBody, [null, '', '0'], true)) {
                $this->saveCache($cacheHash, $responseBody);
                return ($this->parse($responseBody));
            } else {
                $this->logger->error(\aportela\LastFMWrapper\Track::class . '::get - Error: empty body on API response', [$url]);
                throw new \aportela\LastFMWrapper\Exception\InvalidAPIResponse('Empty body on API response for URL: ' . $url);
            }
        } elseif (!in_array($this->raw, [null, '', '0'], true)) {
            return ($this->parse($this->raw));
        } else {
            $this->logger->error(\aportela\LastFMWrapper\Track::class . '::get - Error: cached data for identifier is empty', [$cacheHash]);
            throw new \aportela\LastFMWrapper\Exception\InvalidCacheException(sprintf('Cached data for identifier (%s) is empty', $cacheHash));
        }
    }

    public function parse(string $rawText): \aportela\LastFMWrapper\ParseHelpers\TrackHelper
    {
        $this->reset();
        switch ($this->apiFormat) {
            case \aportela\LastFMWrapper\APIFormat::XML:
                $this->parser = new \aportela\LastFMWrapper\ParseHelpers\XML\Get\Track($rawText);
                break;
            case \aportela\LastFMWrapper\APIFormat::JSON:
                $this->parser = new \aportela\LastFMWrapper\ParseHelpers\JSON\Get\Track($rawText);
                break;
            default:
                $this->logger->error("\aportela\MusicBrainzWrapper\Track::parse - Error: invalid API format", [$this->apiFormat]);
                /** @var string $format */
                $format = $this->apiFormat->value;
                throw new \aportela\LastFMWrapper\Exception\InvalidAPIFormat('Invalid API format: ' . $format);
        }

        $this->raw = $rawText;
        return ($this->parser->parse());
    }
}
