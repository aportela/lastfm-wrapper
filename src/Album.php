<?php

namespace aportela\LastFMWrapper;

class Album extends \aportela\LastFMWrapper\Entity
{
    private const SEARCH_API_URL = "http://ws.audioscrobbler.com/2.0/?method=album.search&artist=%s&album=%s&autocorrect=1&api_key=%s&limit=%d&format=%s";
    private const GET_API_URL = "http://ws.audioscrobbler.com/2.0/?method=album.getinfo&artist=%s&album=%s&api_key=%s&autocorrect=1&format=%s";

    /**
     * @return array<mixed>
     */
    public function search(string $artist, string $album, int $limit = 1): array
    {
        $url = sprintf(self::SEARCH_API_URL, urlencode($artist), urlencode(($album)), $this->apiKey, $limit, $this->apiFormat->value);
        $this->logger->debug("LastFMWrapper\Album::search", array("artist" => $artist, "album" => $album, "limit" => $limit, "apiURL" => $url));
        $response = $this->httpGET($url);
        if ($response->code == 200) {
            $this->resetThrottle();
            if ($this->apiFormat == \aportela\LastFMWrapper\APIFormat::XML) {
                $this->parser = new \aportela\LastFMWrapper\ParseHelpers\XML\Search\Album($response->body);
            } elseif ($this->apiFormat == \aportela\LastFMWrapper\APIFormat::JSON) {
                $this->parser = new \aportela\LastFMWrapper\ParseHelpers\JSON\Search\Album($response->body);
            } else {
                throw new \aportela\LastFMWrapper\Exception\InvalidAPIFormat("");
            }
            $results = $this->parser->parse();
            if (count($results) > 0) {
                return ($results);
            } else {
                throw new \aportela\LastFMWrapper\Exception\NotFoundException("artist: {$artist} album: {$album}", 0);
            }
        } elseif ($response->code == 503) {
            $this->incrementThrottle();
            throw new \aportela\LastFMWrapper\Exception\RateLimitExceedException("artist: {$artist} album: {$album}", $response->code);
        } else {
            throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: {$artist} album: {$album}", $response->code);
        }
    }

    public function get(string $artist, string $album): \aportela\LastFMWrapper\ParseHelpers\AlbumHelper
    {
        $cacheHash = md5("ALBUM:" . mb_strtolower(mb_trim($artist)) . mb_strtolower(mb_trim($album)));
        if (!$this->getCache($cacheHash)) {
            $url = sprintf(self::GET_API_URL, urlencode($artist), urlencode($album), $this->apiKey, $this->apiFormat->value);
            $this->logger->debug("LastFMWrapper\Album::get", array("artist" => $artist, "album" => $album, "apiURL" => $url));
            $response = $this->http->GET($url);
            if ($response->code == 200) {
                $this->resetThrottle();
                $this->saveCache($cacheHash, $response->body);
                return ($this->parse($response->body));
            } elseif ($response->code == 503) {
                $this->incrementThrottle();
                throw new \aportela\LastFMWrapper\Exception\RateLimitExceedException("artist: {$artist} album: {$album}", $response->code);
            } else {
                throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: {$artist} album: {$album}", $response->code);
            }
        } else {
            return ($this->parse($this->raw));
        }
    }

    public function parse(string $rawText): \aportela\LastFMWrapper\ParseHelpers\AlbumHelper
    {
        $this->reset();
        if ($this->apiFormat == \aportela\LastFMWrapper\APIFormat::XML) {
            $this->parser = new \aportela\LastFMWrapper\ParseHelpers\XML\Get\Album($rawText);
        } elseif ($this->apiFormat == \aportela\LastFMWrapper\APIFormat::JSON) {
            $this->parser = new \aportela\LastFMWrapper\ParseHelpers\JSON\Get\Album($rawText);
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidAPIFormat("");
        }
        $this->raw = $rawText;
        return ($this->parser->parse());
    }
}
