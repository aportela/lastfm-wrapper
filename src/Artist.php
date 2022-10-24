<?php

namespace aportela\LastFMWrapper;

class Artist extends \aportela\LastFMWrapper\Entity
{
    private const SEARCH_API_URL = "http://ws.audioscrobbler.com/2.0/?method=artist.search&artist=%s&api_key=%s&limit=%d&format=%s";
    private const GET_API_URL = "http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist=%s&api_key=%s&autocorrect=1&format=%s";

    public $mbId;
    public $name;
    public $url;
    public $tags = array();
    public $similar = array();
    public $bio;

    public function search(string $name, int $limit = 1): array
    {
        $url = sprintf(self::SEARCH_API_URL, urlencode($name), $this->apiKey, $limit, $this->apiFormat);
        $this->logger->debug("LastFMWrapper\Artist::search", array("name" => $name, "limit" => $limit, "apiURL" => $url));
        $response = $this->http->GET($url);
        if ($response->code == 200) {
            if ($this->apiFormat == \aportela\LastFMWrapper\LastFM::API_FORMAT_JSON) {
                $json = json_decode($response->body);
                if (json_last_error()  == JSON_ERROR_NONE) {
                    if (isset($json->{"results"}) && isset($json->{"results"}->{"opensearch:totalResults"}) && $json->{"results"}->{"opensearch:totalResults"} > 0) {
                        $results = [];
                        foreach ($json->{"results"}->{"artistmatches"}->{"artist"} as $artist) {
                            $results[] = (object) [
                                "mbId" => isset($artist->{"mbid"}) ? (string) $artist->{"mbid"} : null,
                                "name" => isset($artist->{"name"}) ? (string) $artist->{"name"} : null,
                                "url" => isset($artist->{"url"}) ? (string) $artist->{"url"} : null
                            ];
                        }
                        return ($results);
                    } else {
                        if (isset($json->{"error"})) {
                            switch ($json->{"error"}) {
                                case 29:
                                    throw new \aportela\LastFMWrapper\Exception\RateLimitExceedException("artist:" . $name, $json->{"error"});
                                    break;
                                default:
                                    throw new \aportela\LastFMWrapper\Exception\HTTPException("artist:" . $name, $json->{"error"});
                                    break;
                            }
                        } else {
                            throw new \aportela\LastFMWrapper\Exception\HTTPException("artist:" . $name, $response->code);
                        }
                    }
                } else {
                    throw new \aportela\LastFMWrapper\Exception\InvalidAPIResponseFormatException("invalid json");
                }
            } else {
                throw new \aportela\LastFMWrapper\Exception\InvalidAPIFormatException($this->apiFormat);
            }
        } else {
            throw new \aportela\LastFMWrapper\Exception\HTTPException("artist:" . $name, $response->code);
        }
    }

    public function get(string $name): void
    {
        $url = sprintf(self::GET_API_URL, urlencode($name), $this->apiKey, $this->apiFormat == \aportela\LastFMWrapper\LastFM::API_FORMAT_JSON ? \aportela\LastFMWrapper\LastFM::API_FORMAT_JSON : null);
        $this->logger->debug("LastFMWrapper\Artist::get", array("name" => $name, "apiURL" => $url));
        $response = $this->http->GET($url);
        if ($response->code == 200) {
            $this->parse($response->body);
        } else {
            throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: " . $name, $response->code);
        }
    }

    public function parse(string $rawText)
    {
        $this->raw = $rawText;
        $this->mbId = null;
        $this->name = null;
        $this->url = null;
        $this->tags = [];
        $this->similar = [];
        $this->bio = null;
        if ($this->apiFormat == \aportela\LastFMWrapper\LastFM::API_FORMAT_JSON) {
            $json = json_decode($this->raw);
            if (json_last_error()  == JSON_ERROR_NONE) {
                if (isset($json->{"artist"})) {
                    $this->mbId = isset($json->{"artist"}->{"mbid"}) ? (string) $json->{"artist"}->{"mbid"} : null;
                    $this->name = isset($json->{"artist"}->{"name"}) ? (string) $json->{"artist"}->{"name"} : null;
                    $this->url = isset($json->{"artist"}->{"url"}) ? (string) $json->{"artist"}->{"url"} : null;
                    if (isset($json->{"artist"}->{"tags"})) {
                        foreach ($json->{"artist"}->{"tags"}->{"tag"} as $tag) {
                            $this->tags[] = trim(mb_strtolower((string) $tag->{"name"}));
                        }
                        $this->tags = array_unique($this->tags);
                    } else {
                        $this->tags = [];
                    }
                    if (isset($json->{"artist"}->{"similar"})) {
                        foreach ($json->{"artist"}->{"similar"}->{"artist"} as $artist) {
                            $this->similar[] = (object) [
                                "name" => (string) $artist->{"name"},
                                "url" => (string) $artist->{"url"}
                            ];
                        }
                    } else {
                        $this->similar = [];
                    }
                    $this->bio = (object) array(
                        "summary" => isset($json->{"artist"}->{"bio"}) && isset($json->{"artist"}->{"bio"}->{"summary"}) ? (string) $json->{"artist"}->{"bio"}->{"summary"} : null,
                        "content" => isset($json->{"artist"}->{"bio"}) && isset($json->{"artist"}->{"bio"}->{"content"}) ? (string) $json->{"artist"}->{"bio"}->{"content"} : null
                    );
                } else {
                    throw new \aportela\LastFMWrapper\Exception\InvalidAPIResponseFormatException("artist field not found");
                }
            } else {
                throw new \aportela\LastFMWrapper\Exception\InvalidAPIResponseFormatException("invalid json");
            }
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidAPIFormatException($this->apiFormat);
        }
    }
}
