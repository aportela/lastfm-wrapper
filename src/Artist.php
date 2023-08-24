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
        $url = sprintf(self::SEARCH_API_URL, urlencode($name), $this->apiKey, $limit, $this->apiFormat->value);
        $this->logger->debug("LastFMWrapper\Artist::search", array("name" => $name, "limit" => $limit, "apiURL" => $url));
        $response = $this->http->GET($url);
        if ($response->code == 200) {
            $data = $this->parseHTTPResponseToObject($response->body);
            if (isset($data->{"results"}) && isset($data->{"results"}->{"opensearch:totalResults"}) && $data->{"results"}->{"opensearch:totalResults"} > 0) {
                $results = [];
                foreach ($data->{"results"}->{"artistmatches"}->{"artist"} as $artist) {
                    $results[] = (object) [
                        "mbId" => isset($artist->{"mbid"}) ? (string) $artist->{"mbid"} : null,
                        "name" => isset($artist->{"name"}) ? (string) $artist->{"name"} : null,
                        "url" => isset($artist->{"url"}) ? (string) $artist->{"url"} : null
                    ];
                }
                return ($results);
            } else {
                if (isset($data->{"error"})) {
                    switch ($data->{"error"}) {
                        case 29:
                            throw new \aportela\LastFMWrapper\Exception\RateLimitExceedException("artist:" . $name, $data->{"error"});
                            break;
                        default:
                            throw new \aportela\LastFMWrapper\Exception\HTTPException("artist:" . $name, $data->{"error"});
                            break;
                    }
                } else {
                    throw new \aportela\LastFMWrapper\Exception\HTTPException("artist:" . $name, $response->code);
                }
            }
        } else {
            throw new \aportela\LastFMWrapper\Exception\HTTPException("artist:" . $name, $response->code);
        }
    }

    public function get(string $name): void
    {
        $url = sprintf(self::GET_API_URL, urlencode($name), $this->apiKey, $this->apiFormat->value);
        $this->logger->debug("LastFMWrapper\Artist::get", array("name" => $name, "apiURL" => $url));
        $response = $this->http->GET($url);
        if ($response->code == 200) {
            $this->parse($response->body);
        } else {
            throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: " . $name, $response->code);
        }
    }

    public function parse(string $rawText): void
    {
        $this->raw = $rawText;
        $this->mbId = null;
        $this->name = null;
        $this->url = null;
        $this->tags = [];
        $this->similar = [];
        $this->bio = null;
        $data = $this->parseHTTPResponseToObject($this->raw);
        if (isset($data->{"artist"})) {
            $this->mbId = isset($data->{"artist"}->{"mbid"}) ? (string) $data->{"artist"}->{"mbid"} : null;
            $this->name = isset($data->{"artist"}->{"name"}) ? (string) $data->{"artist"}->{"name"} : null;
            $this->url = isset($data->{"artist"}->{"url"}) ? (string) $data->{"artist"}->{"url"} : null;
            if (isset($data->{"artist"}->{"tags"})) {
                foreach ($data->{"artist"}->{"tags"}->{"tag"} as $tag) {
                    $this->tags[] = trim(mb_strtolower((string) $tag->{"name"}));
                }
                $this->tags = array_unique($this->tags);
            } else {
                $this->tags = [];
            }
            if (isset($data->{"artist"}->{"similar"})) {
                foreach ($data->{"artist"}->{"similar"}->{"artist"} as $artist) {
                    $this->similar[] = (object) [
                        "name" => (string) $artist->{"name"},
                        "url" => (string) $artist->{"url"}
                    ];
                }
            } else {
                $this->similar = [];
            }
            $this->bio = (object) array(
                "summary" => isset($data->{"artist"}->{"bio"}) && isset($data->{"artist"}->{"bio"}->{"summary"}) ? (string) $data->{"artist"}->{"bio"}->{"summary"} : null,
                "content" => isset($data->{"artist"}->{"bio"}) && isset($data->{"artist"}->{"bio"}->{"content"}) ? (string) $data->{"artist"}->{"bio"}->{"content"} : null
            );
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidAPIResponseFormatException("artist field not found");
        }
    }

    public function getImageFromArtistPageURL(string $artistPageURL): ?string
    {
        $imageURL = null;
        $this->logger->debug("LastFMWrapper\Artist::getImageFromArtistPageURL", array("url" => $artistPageURL));
        if (str_starts_with($artistPageURL, "https://www.last.fm/music/")) {
            $response = $this->http->GET($artistPageURL);
            if ($response->code == 200) {
                $doc = new \DomDocument();
                $doc->loadHTML($response->body);
                $xpath = new \DOMXPath($doc);
                $query = '//*/meta[starts-with(@property, \'og:\')]';
                $metas = $xpath->query($query);
                $rmetas = array();
                foreach ($metas as $meta) {
                    if ($meta->getAttribute('property') == 'og:image') {
                        $imageURL = $meta->getAttribute('content');
                    }
                }
                return ($imageURL);
            } else {
                throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: " . $name, $response->code);
            }
        }
    }
}
