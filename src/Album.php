<?php

namespace aportela\LastFMWrapper;

class Album extends \aportela\LastFMWrapper\Entity
{
    private const SEARCH_API_URL = "http://ws.audioscrobbler.com/2.0/?method=album.search&artist=%s&album=%s&autocorrect=1&api_key=%s&limit=%d&format=%s";
    private const GET_API_URL = "http://ws.audioscrobbler.com/2.0/?method=album.getinfo&artist=%s&album=%s&api_key=%s&autocorrect=1&format=%s";

    public ?string $mbId;
    public ?string $name;
    public mixed $artist;
    public ?string $url;
    public array $tags = [];
    public array $tracks = [];

    public function search(string $artist, string $album, int $limit = 1): array
    {
        $url = sprintf(self::SEARCH_API_URL, urlencode($artist), urlencode(($album)), $this->apiKey, $limit, $this->apiFormat->value);
        $this->logger->debug("LastFMWrapper\Album::search", array("artist" => $artist, "album" => $album, "limit" => $limit, "apiURL" => $url));
        $response = $this->http->GET($url);
        if ($response->code == 200) {
            $data = $this->parseHTTPResponseToObject($response->body);
            if (isset($data->{"results"}) && isset($data->{"results"}->{"opensearch:totalResults"}) && $data->{"results"}->{"opensearch:totalResults"} > 0) {
                $results = [];
                foreach ($data->{"results"}->{"albummatches"}->{"album"} as $album) {
                    $results[] = (object) [
                        "name" => isset($album->{"name"}) ? (string) $album->{"name"} : null,
                        "artist" => isset($album->{"artist"}) ? (string) $album->{"artist"} : null,
                        "url" => isset($album->{"url"}) ? (string) $album->{"url"} : null
                    ];
                }
                return ($results);
            } else {
                if (isset($data->{"error"})) {
                    if ($data->{"error"} == 29) {
                        throw new \aportela\LastFMWrapper\Exception\RateLimitExceedException("artist: " . $artist . " - album: " . $album, $data->{"error"});
                    } else {
                        throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: " . $artist . " - album: " . $album, $data->{"error"});
                    }
                } else {
                    throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: " . $artist . " - album: " . $album, "");
                }
            }
        } else {
            throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: " . $artist . " - album: " . $album, $response->code);
        }
    }

    public function get(string $artist, string $album): void
    {
        $url = sprintf(self::GET_API_URL, urlencode($artist), urlencode($album), $this->apiKey, $this->apiFormat->value);
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
        $data = $this->parseHTTPResponseToObject($this->raw);
        if (isset($data->{"album"})) {
            $this->mbId = isset($data->{"album"}->{"mbid"}) ? (string) $data->{"album"}->{"mbid"} : null;
            $this->name = isset($data->{"album"}->{"name"}) ? (string) $data->{"album"}->{"name"} : null;
            $this->name = isset($data->{"album"}->{"artist"}) ? (string) $data->{"album"}->{"artist"} : null;
            $this->url = isset($data->{"album"}->{"url"}) ? (string) $data->{"album"}->{"url"} : null;
            if (isset($data->{"album"}->{"tags"})) {
                foreach ($data->{"album"}->{"tags"}->{"tag"} as $tag) {
                    $this->tags[] = trim(mb_strtolower((string) $tag->{"name"}));
                }
                $this->tags = array_unique($this->tags);
            } else {
                $this->tags = [];
            }
            if (isset($data->{"album"}->{"tracks"})) {
                foreach ($data->{"album"}->{"tracks"}->{"track"} as $track) {
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
                $this->tracks = [];
            }
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidAPIResponseFormatException("album field not found");
        }
    }
}
