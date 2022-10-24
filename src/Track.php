<?php

namespace aportela\LastFMWrapper;

class Track extends \aportela\LastFMWrapper\Entity
{
    private const SEARCH_API_URL = "http://ws.audioscrobbler.com/2.0/?method=track.search&artist=%s&track=%s&api_key=%s&limit=%d&format=%s";
    private const GET_API_URL = "http://ws.audioscrobbler.com/2.0/?method=track.getinfo&artist=%s&track=%s&api_key=%s&autocorrect=1&format=%s";

    public $mbId;
    public $name;
    public $url;
    public $artist;
    public $album;
    public $tags = array();

    public function search(string $artist, string $track, int $limit = 1): array
    {
        $url = sprintf(self::SEARCH_API_URL, urlencode($artist), urlencode($track), $this->apiKey, $limit, $this->apiFormat);
        $this->logger->debug("LastFMWrapper\Album::search", array("artist" => $artist, "track" => $track, "limit" => $limit, "apiURL" => $url));
        $response = $this->http->GET($url);
        if ($response->code == 200) {
            if ($this->apiFormat == \aportela\LastFMWrapper\LastFM::API_FORMAT_JSON) {
                $json = json_decode($response->body);
                if (json_last_error()  == JSON_ERROR_NONE) {
                    if (isset($json->{"results"}) && isset($json->{"results"}->{"opensearch:totalResults"}) && $json->{"results"}->{"opensearch:totalResults"} > 0) {
                        $results = [];
                        foreach ($json->{"results"}->{"trackmatches"}->{"track"} as $track) {
                            $results[] = (object) [
                                "mbId" => isset($track->{"mbid"}) ? (string) $track->{"mbid"} : null,
                                "name" => isset($track->{"name"}) ? (string) $track->{"name"} : null,
                                "artist" => isset($track->{"artist"}) ? (string) $track->{"artist"} : null,
                                "url" => isset($track->{"url"}) ? (string) $track->{"url"} : null
                            ];
                        }
                        return ($results);
                    } else {
                        if (isset($json->{"error"})) {
                            switch ($json->{"error"}) {
                                case 29:
                                    throw new \aportela\LastFMWrapper\Exception\RateLimitExceedException("artist: " . $artist . " - track: " . $track, $json->{"error"});
                                    break;
                                default:
                                    throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: " . $artist . " - track: " . $track, $json->{"error"});
                                    break;
                            }
                        } else {
                            throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: " . $artist . " - track: " . $track, $response->code);
                        }
                    }
                } else {
                    throw new \aportela\LastFMWrapper\Exception\InvalidAPIResponseFormatException("invalid json");
                }
            } else {
                throw new \aportela\LastFMWrapper\Exception\InvalidAPIFormatException($this->apiFormat);
            }
        } else {
            throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: " . $artist . " - track: " . $track, $response->code);
        }
    }

    public function get(string $artist, string $track): void
    {
        $url = sprintf(self::GET_API_URL, urlencode($artist), urlencode($track), $this->apiKey, $this->apiFormat == \aportela\LastFMWrapper\LastFM::API_FORMAT_JSON ? \aportela\LastFMWrapper\LastFM::API_FORMAT_JSON : null);
        $this->logger->debug("LastFMWrapper\Track::get", array("artist" => $artist, "track" => $track, "apiURL" => $url));
        $response = $this->http->GET($url);
        if ($response->code == 200) {
            $this->parse($response->body);
        } else {
            throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: " . $artist . " - track: " . $track, $response->code);
        }
    }

    public function parse(string $rawText)
    {
        $this->raw = $rawText;
        $this->mbId = null;
        $this->name = null;
        $this->url = null;
        $this->tags = [];
        $this->artist = null;
        $this->album = null;
        if ($this->apiFormat == \aportela\LastFMWrapper\LastFM::API_FORMAT_JSON) {
            $json = json_decode($this->raw);
            if (json_last_error()  == JSON_ERROR_NONE) {
                if (isset($json->{"track"})) {
                    $this->mbId = isset($json->{"track"}->{"mbid"}) ? (string) $json->{"track"}->{"mbid"} : null;
                    $this->name = isset($json->{"track"}->{"name"}) ? (string) $json->{"track"}->{"name"} : null;
                    $this->url = isset($json->{"track"}->{"url"}) ? (string) $json->{"track"}->{"url"} : null;
                    if (isset($json->{"track"}->{"toptags"})) {
                        foreach ($json->{"track"}->{"toptags"}->{"tag"} as $tag) {
                            $this->tags[] = trim(mb_strtolower((string) $tag->{"name"}));
                        }
                        $this->tags = array_unique($this->tags);
                    } else {
                        $this->tags = [];
                    }
                    $this->artist = (object) [
                        "name" => (string) $json->{"track"}->{"artist"}->{"name"},
                        "mbId" => (string) $json->{"track"}->{"artist"}->{"mbid"},
                        "url" => (string) $json->{"track"}->{"artist"}->{"url"}
                    ];
                    $this->album = (object) [
                        "artist" => (string) $json->{"track"}->{"album"}->{"artist"},
                        "title" => (string) $json->{"track"}->{"album"}->{"title"},
                        "mbId" => (string) $json->{"track"}->{"album"}->{"mbid"},
                        "url" => (string) $json->{"track"}->{"album"}->{"url"}
                    ];
                } else {
                    throw new \aportela\LastFMWrapper\Exception\InvalidAPIResponseFormatException("track field not found");
                }
            } else {
                throw new \aportela\LastFMWrapper\Exception\InvalidAPIResponseFormatException("invalid json");
            }
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidAPIFormatException($this->apiFormat);
        }
    }
}
