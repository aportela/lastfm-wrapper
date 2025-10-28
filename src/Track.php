<?php

namespace aportela\LastFMWrapper;

class Track extends \aportela\LastFMWrapper\Entity
{
    private const SEARCH_API_URL = "http://ws.audioscrobbler.com/2.0/?method=track.search&artist=%s&track=%s&api_key=%s&limit=%d&format=%s";
    private const GET_API_URL = "http://ws.audioscrobbler.com/2.0/?method=track.getinfo&artist=%s&track=%s&api_key=%s&autocorrect=1&format=%s";

    public ?string $name;
    public ?string $url;
    public mixed $artist;
    public mixed $album;
    /**
     * @var array<string>
     */
    public array $tags = [];

    /**
     * @return array<mixed>
     */
    public function search(string $artist, string $track, int $limit = 1): array
    {
        $url = sprintf(self::SEARCH_API_URL, urlencode($artist), urlencode($track), $this->apiKey, $limit, $this->apiFormat->value);
        $this->logger->debug("LastFMWrapper\Album::search", array("artist" => $artist, "track" => $track, "limit" => $limit, "apiURL" => $url));
        $response = $this->http->GET($url);
        if ($response->code == 200) {
            $this->resetThrottle();
            $data = $this->parseHTTPResponseToObject($response->body);
            if (isset($data->{"results"}) && isset($data->{"results"}->{"opensearch:totalResults"}) && $data->{"results"}->{"opensearch:totalResults"} > 0) {
                $results = [];
                foreach ($data->{"results"}->{"trackmatches"}->{"track"} as $track) {
                    $results[] = (object) [
                        "name" => isset($track->{"name"}) ? (string) $track->{"name"} : null,
                        "artist" => isset($track->{"artist"}) ? (string) $track->{"artist"} : null,
                        "url" => isset($track->{"url"}) ? (string) $track->{"url"} : null
                    ];
                }
                return ($results);
            } else {
                if (isset($data->{"error"})) {
                    $error = intval($data->{"error"});
                    if ($error) {
                        throw new \aportela\LastFMWrapper\Exception\RateLimitExceedException("artist: " . $artist . " - track: " . $track, $error);
                    } else {
                        throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: " . $artist . " - track: " . $track, $error);
                    }
                } else {
                    throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: " . $artist . " - track: " . $track, $response->code);
                }
            }
        } else {
            throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: " . $artist . " - track: " . $track, $response->code);
        }
    }

    public function get(string $artist, string $track): void
    {
        $cacheHash = md5("TRACK:" . trim($artist) . trim($track));
        if (!$this->getCache($cacheHash)) {
            $url = sprintf(self::GET_API_URL, urlencode($artist), urlencode($track), $this->apiKey, $this->apiFormat->value);
            $this->logger->debug("LastFMWrapper\Track::get", array("artist" => $artist, "track" => $track, "apiURL" => $url));
            $response = $this->http->GET($url);
            if ($response->code == 200) {
                $this->resetThrottle();
                $this->resetThrottle();
                $this->saveCache($cacheHash, $response->body);
                $this->parse($response->body);
            } else {
                throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: " . $artist . " - track: " . $track, $response->code);
            }
        } else {
            $this->parse($this->raw);
        }
    }

    public function parse(string $rawText): void
    {
        $this->raw = $rawText;
        $this->name = null;
        $this->url = null;
        $this->tags = [];
        $this->artist = null;
        $this->album = null;
        $data = $this->parseHTTPResponseToObject($this->raw);
        if (isset($data->{"track"})) {
            $this->name = isset($data->{"track"}->{"name"}) ? (string) $data->{"track"}->{"name"} : null;
            $this->url = isset($data->{"track"}->{"url"}) ? (string) $data->{"track"}->{"url"} : null;
            if (isset($data->{"track"}->{"toptags"})) {
                foreach ($data->{"track"}->{"toptags"}->{"tag"} as $tag) {
                    $this->tags[] = trim(mb_strtolower((string) $tag->{"name"}));
                }
                $this->tags = array_unique($this->tags);
            } else {
                $this->tags = [];
            }
            $this->artist = (object) [
                "name" => (string) $data->{"track"}->{"artist"}->{"name"},
                "mbId" => (string) $data->{"track"}->{"artist"}->{"mbid"},
                "url" => (string) $data->{"track"}->{"artist"}->{"url"}
            ];
            $this->album = (object) [
                "artist" => (string) $data->{"track"}->{"album"}->{"artist"},
                "title" => (string) $data->{"track"}->{"album"}->{"title"},
                "url" => (string) $data->{"track"}->{"album"}->{"url"}
            ];
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidAPIResponseFormatException("track field not found");
        }
    }
}
