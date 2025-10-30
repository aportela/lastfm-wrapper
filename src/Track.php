<?php

namespace aportela\LastFMWrapper;

class Track extends \aportela\LastFMWrapper\Entity
{
    private const SEARCH_API_URL = "http://ws.audioscrobbler.com/2.0/?method=track.search&artist=%s&track=%s&api_key=%s&limit=%d&format=%s";
    private const GET_API_URL = "http://ws.audioscrobbler.com/2.0/?method=track.getinfo&artist=%s&track=%s&api_key=%s&autocorrect=1&format=%s";

    /**
     * @return array<\aportela\LastFMWrapper\ParseHelpers\TrackHelper>
     */
    public function search(string $artist, string $track, int $limit = 1): array
    {
        $url = sprintf(self::SEARCH_API_URL, urlencode($artist), urlencode($track), $this->apiKey, $limit, $this->apiFormat->value);
        $this->logger->debug("LastFMWrapper\Track::search", array("artist" => $artist, "track" => $track, "limit" => $limit, "apiURL" => $url));
        $response = $this->httpGET($url);
        if ($response->code == 200) {
            $this->resetThrottle();
            if ($this->apiFormat == \aportela\LastFMWrapper\APIFormat::XML) {
                $this->parser = new \aportela\LastFMWrapper\ParseHelpers\XML\Search\Track($response->body);
            } elseif ($this->apiFormat == \aportela\LastFMWrapper\APIFormat::JSON) {
                $this->parser = new \aportela\LastFMWrapper\ParseHelpers\JSON\Search\Track($response->body);
            } else {
                throw new \aportela\LastFMWrapper\Exception\InvalidAPIFormat("");
            }
            $results = $this->parser->parse();
            if (count($results) > 0) {
                return ($results);
            } else {
                throw new \aportela\LastFMWrapper\Exception\NotFoundException("artist: {$artist} track: {$track}", 0);
            }
        } elseif ($response->code == 503) {
            $this->incrementThrottle();
            throw new \aportela\LastFMWrapper\Exception\RateLimitExceedException("artist: {$artist} track: {$track}", $response->code);
        } else {
            throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: {$artist} track: {$track}", $response->code);
        }
    }

    public function get(string $artist, string $track): \aportela\LastFMWrapper\ParseHelpers\TrackHelper
    {
        $cacheHash = md5("TRACK:" . mb_strtolower(mb_trim($artist)) . mb_strtolower(mb_trim($track)));
        if (!$this->getCache($cacheHash)) {
            $url = sprintf(self::GET_API_URL, urlencode($artist), urlencode($track), $this->apiKey, $this->apiFormat->value);
            $this->logger->debug("LastFMWrapper\Track::get", array("artist" => $artist, "track" => $track, "apiURL" => $url));
            $response = $this->http->GET($url);
            if ($response->code == 200) {
                $this->resetThrottle();
                $this->resetThrottle();
                $this->saveCache($cacheHash, $response->body);
                return ($this->parse($response->body));
            } elseif ($response->code == 503) {
                $this->incrementThrottle();
                throw new \aportela\LastFMWrapper\Exception\RateLimitExceedException("artist: {$artist} track: {$track}", $response->code);
            } else {
                throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: {$artist} track: {$track}", $response->code);
            }
        } else {
            return ($this->parse($this->raw));
        }
    }

    public function parse(string $rawText): \aportela\LastFMWrapper\ParseHelpers\TrackHelper
    {
        $this->reset();
        if ($this->apiFormat == \aportela\LastFMWrapper\APIFormat::XML) {
            $this->parser = new \aportela\LastFMWrapper\ParseHelpers\XML\Get\Track($rawText);
        } elseif ($this->apiFormat == \aportela\LastFMWrapper\APIFormat::JSON) {
            $this->parser = new \aportela\LastFMWrapper\ParseHelpers\JSON\Get\Track($rawText);
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidAPIFormat("");
        }
        $this->raw = $rawText;
        return ($this->parser->parse());
    }
}
