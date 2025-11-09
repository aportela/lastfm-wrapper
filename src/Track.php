<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper;

class Track extends \aportela\LastFMWrapper\Entity
{
    private const string SEARCH_API_URL = "http://ws.audioscrobbler.com/2.0/?method=track.search&artist=%s&track=%s&api_key=%s&limit=%d&format=%s";
    private const string GET_API_URL = "http://ws.audioscrobbler.com/2.0/?method=track.getinfo&artist=%s&track=%s&api_key=%s&autocorrect=1&format=%s";

    /**
     * @return array<\aportela\LastFMWrapper\ParseHelpers\TrackHelper>
     */
    public function search(string $artist, string $track, int $limit = 1): array
    {
        $url = sprintf(self::SEARCH_API_URL, urlencode($artist), urlencode($track), $this->apiKey, $limit, $this->apiFormat->value);
        $responseBody = $this->httpGET($url);
        if (! empty($responseBody)) {
            switch ($this->apiFormat) {
                case \aportela\LastFMWrapper\APIFormat::XML:
                    $this->parser = new \aportela\LastFMWrapper\ParseHelpers\XML\Search\Track($responseBody);
                    break;
                case \aportela\LastFMWrapper\APIFormat::JSON:
                    $this->parser = new \aportela\LastFMWrapper\ParseHelpers\JSON\Search\Track($responseBody);
                    break;
                default:
                    $this->logger->error("\aportela\LastFMWrapper\Track::search - Error: invalid API format", [$this->apiFormat]);
                    throw new \aportela\LastFMWrapper\Exception\InvalidAPIFormat("Invalid API format: {$this->apiFormat->value}");
            }
            return ($this->parser->parse());
        } else {
            $this->logger->error("\aportela\LastFMWrapper\Track::search - Error: empty body on API response", [$url]);
            throw new \aportela\LastFMWrapper\Exception\InvalidAPIResponse("Empty body on API response for URL: {$url}");
        }
    }

    public function get(string $artist, string $track): \aportela\LastFMWrapper\ParseHelpers\TrackHelper
    {
        $cacheHash = md5("TRACK:" . mb_strtolower(mb_trim($artist)) . mb_strtolower(mb_trim($track)));
        if (!$this->getCache($cacheHash)) {
            $url = sprintf(self::GET_API_URL, urlencode($artist), urlencode($track), $this->apiKey, $this->apiFormat->value);
            $responseBody = $this->httpGET($url);
            if (! empty($responseBody)) {
                $this->saveCache($cacheHash, $responseBody);
                return ($this->parse($responseBody));
            } else {
                $this->logger->error("\aportela\LastFMWrapper\Track::get - Error: empty body on API response", [$url]);
                throw new \aportela\LastFMWrapper\Exception\InvalidAPIResponse("Empty body on API response for URL: {$url}");
            }
        } else {
            if (! empty($this->raw)) {
                return ($this->parse($this->raw));
            } else {
                $this->logger->error("\aportela\LastFMWrapper\Track::get - Error: cached data for identifier is empty", [$cacheHash]);
                throw new \aportela\LastFMWrapper\Exception\InvalidCacheException("Cached data for identifier ({$cacheHash}) is empty");
            }
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
                throw new \aportela\LastFMWrapper\Exception\InvalidAPIFormat("Invalid API format: {$this->apiFormat->value}");
        }
        $this->raw = $rawText;
        return ($this->parser->parse());
    }
}
