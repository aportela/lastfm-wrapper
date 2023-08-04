<?php

namespace aportela\LastFMWrapper;

class Album extends \aportela\LastFMWrapper\Entity
{
    private const SEARCH_API_URL = "http://ws.audioscrobbler.com/2.0/?method=album.search&artist=%s&album=%s&autocorrect=1&api_key=%s&limit=%d&format=%s";
    private const GET_API_URL = "http://ws.audioscrobbler.com/2.0/?method=album.getinfo&artist=%s&album=%s&api_key=%s&autocorrect=1&format=%s";

    public $mbId;
    public $name;
    public $artist;
    public $url;
    public $tags = array();
    public $tracks = array();

    public function search(string $artist, string $album, int $limit = 1): array
    {
        $url = sprintf(self::SEARCH_API_URL, urlencode($artist), urlencode(($album)), $this->apiKey, $limit, $this->apiFormat->value);
        $this->logger->debug("LastFMWrapper\Album::search", array("artist" => $artist, "album" => $album, "limit" => $limit, "apiURL" => $url));
        $response = $this->http->GET($url);
        if ($response->code == 200) {
            if ($this->apiFormat == \aportela\LastFMWrapper\APIFormat::JSON) {
                $json = json_decode($response->body);
                if (json_last_error()  == JSON_ERROR_NONE) {
                    if (isset($json->{"results"}) && isset($json->{"results"}->{"opensearch:totalResults"}) && $json->{"results"}->{"opensearch:totalResults"} > 0) {
                        $results = [];
                        foreach ($json->{"results"}->{"albummatches"}->{"album"} as $album) {
                            $results[] = (object) [
                                "name" => isset($album->{"name"}) ? (string) $album->{"name"} : null,
                                "artist" => isset($album->{"artist"}) ? (string) $album->{"artist"} : null,
                                "url" => isset($album->{"url"}) ? (string) $album->{"url"} : null
                            ];
                        }
                        return ($results);
                    } else {
                        if (isset($json->{"error"})) {
                            switch ($json->{"error"}) {
                                case 29:
                                    throw new \aportela\LastFMWrapper\Exception\RateLimitExceedException("artist: " . $artist . " - album: " . $album, $json->{"error"});
                                    break;
                                default:
                                    throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: " . $artist . " - album: " . $album, $json->{"error"});
                                    break;
                            }
                        } else {
                            throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: " . $artist . " - album: " . $album, "");
                        }
                    }
                } else {
                    throw new \aportela\LastFMWrapper\Exception\InvalidAPIResponseFormatException("invalid json");
                }
            } else {
                throw new \aportela\LastFMWrapper\Exception\InvalidAPIFormatException($this->apiFormat->value);
            }
        } else {
            throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: " . $artist . " - album: " . $album, $response->code);
        }
    }

    public function get(string $artist, string $album): void
    {
        $url = sprintf(self::GET_API_URL, urlencode($artist), urlencode($album), $this->apiKey, $this->apiFormat == \aportela\LastFMWrapper\APIFormat::JSON ? \aportela\LastFMWrapper\APIFormat::JSON->value : null);
        $this->logger->debug("LastFMWrapper\Album::get", array("artist" => $artist, "album" => $album, "apiURL" => $url));
        $response = $this->http->GET($url);
        if ($response->code == 200) {
            $this->parse($response->body);
        } else {
            throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: " . $artist . " - album: " . $album, $response->code);
        }
    }

    public function parse(string $rawText): void
    {
        $this->raw = $rawText;
        $this->mbId = null;
        $this->name = null;
        $this->artist = null;
        $this->url = null;
        $this->tags = [];
        $this->tracks = [];
        if ($this->apiFormat == \aportela\LastFMWrapper\APIFormat::JSON) {
            $json = json_decode($this->raw);
            if (json_last_error()  == JSON_ERROR_NONE) {
                if (isset($json->{"album"})) {
                    $this->mbId = isset($json->{"album"}->{"mbid"}) ? (string) $json->{"album"}->{"mbid"} : null;
                    $this->name = isset($json->{"album"}->{"name"}) ? (string) $json->{"album"}->{"name"} : null;
                    $this->name = isset($json->{"album"}->{"artist"}) ? (string) $json->{"album"}->{"artist"} : null;
                    $this->url = isset($json->{"album"}->{"url"}) ? (string) $json->{"album"}->{"url"} : null;
                    if (isset($json->{"album"}->{"tags"})) {
                        foreach ($json->{"album"}->{"tags"}->{"tag"} as $tag) {
                            $this->tags[] = trim(mb_strtolower((string) $tag->{"name"}));
                        }
                        $this->tags = array_unique($this->tags);
                    } else {
                        $this->tags = [];
                    }
                    if (isset($json->{"album"}->{"tracks"})) {
                        foreach ($json->{"album"}->{"tracks"}->{"track"} as $track) {
                            $this->tracks[] = (object) [
                                "name" => (string) $track->{"name"},
                                "url" => (string) $track->{"url"},
                                "artist" => (object) [
                                    "url" => (string) $track->{"artist"}->{"url"},
                                    "name" => (string) $track->{"artist"}->{"name"},
                                    "mbid" => (string) $track->{"artist"}->{"mbid"}
                                ],
                                "index" => $track->{"@attr"}->{"rank"}
                            ];
                        }
                    } else {
                        $this->similar = [];
                    }
                } else {
                    throw new \aportela\LastFMWrapper\Exception\InvalidAPIResponseFormatException("album field not found");
                }
            } else {
                throw new \aportela\LastFMWrapper\Exception\InvalidAPIResponseFormatException("invalid json");
            }
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidAPIFormatException($this->apiFormat->value);
        }
    }
}
