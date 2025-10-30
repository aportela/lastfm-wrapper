<?php

namespace aportela\LastFMWrapper;

class Artist extends \aportela\LastFMWrapper\Entity
{
    private const SEARCH_API_URL = "http://ws.audioscrobbler.com/2.0/?method=artist.search&artist=%s&api_key=%s&limit=%d&format=%s";
    private const GET_API_URL = "http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist=%s&api_key=%s&autocorrect=1&format=%s";

    /**
     * @return array<\aportela\LastFMWrapper\ParseHelpers\ArtistHelper>
     */
    public function search(string $name, int $limit = 1): array
    {
        $url = sprintf(self::SEARCH_API_URL, urlencode($name), $this->apiKey, $limit, $this->apiFormat->value);
        $this->logger->debug("LastFMWrapper\Artist::search", array("name" => $name, "limit" => $limit, "apiURL" => $url));
        $response = $this->httpGET($url);
        if ($response->code == 200) {
            $this->resetThrottle();
            if ($this->apiFormat == \aportela\LastFMWrapper\APIFormat::XML) {
                $this->parser = new \aportela\LastFMWrapper\ParseHelpers\XML\Search\Artist($response->body);
            } elseif ($this->apiFormat == \aportela\LastFMWrapper\APIFormat::JSON) {
                $this->parser = new \aportela\LastFMWrapper\ParseHelpers\JSON\Search\Artist($response->body);
            } else {
                throw new \aportela\LastFMWrapper\Exception\InvalidAPIFormat("");
            }
            $results = $this->parser->parse();
            if (count($results) > 0) {
                return ($results);
            } else {
                throw new \aportela\LastFMWrapper\Exception\NotFoundException("artist name: {$name}", 0);
            }
        } elseif ($response->code == 503) {
            $this->incrementThrottle();
            throw new \aportela\LastFMWrapper\Exception\RateLimitExceedException("artist name: {$name}", $response->code);
        } else {
            throw new \aportela\LastFMWrapper\Exception\HTTPException("artist name: {$name}", $response->code);
        }
    }

    public function get(string $name): \aportela\LastFMWrapper\ParseHelpers\ArtistHelper
    {
        $cacheHash = md5("ARTISTNAME:" . mb_strtolower(mb_trim($name)));
        if (!$this->getCache($cacheHash)) {
            $url = sprintf(self::GET_API_URL, urlencode($name), $this->apiKey, $this->apiFormat->value);
            $this->logger->debug("LastFMWrapper\Artist::get", array("name" => $name, "apiURL" => $url));
            $response = $this->httpGET($url);
            if ($response->code == 200) {
                $this->resetThrottle();
                $this->saveCache($cacheHash, $response->body);
                return ($this->parse($response->body));
            } elseif ($response->code == 503) {
                $this->incrementThrottle();
                throw new \aportela\LastFMWrapper\Exception\RateLimitExceedException("artist: {$name}", $response->code);
            } else {
                throw new \aportela\LastFMWrapper\Exception\HTTPException("artist: {$name}", $response->code);
            }
        } else {
            return ($this->parse($this->raw));
        }
    }

    public function parse(string $rawText): \aportela\LastFMWrapper\ParseHelpers\ArtistHelper
    {
        $this->reset();
        if ($this->apiFormat == \aportela\LastFMWrapper\APIFormat::XML) {
            $this->parser = new \aportela\LastFMWrapper\ParseHelpers\XML\Get\Artist($rawText);
        } elseif ($this->apiFormat == \aportela\LastFMWrapper\APIFormat::JSON) {
            $this->parser = new \aportela\LastFMWrapper\ParseHelpers\JSON\Get\Artist($rawText);
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidAPIFormat("");
        }
        $this->raw = $rawText;
        return ($this->parser->parse());
    }

    public function getImageFromArtistPageURL(string $artistPageURL): ?string
    {
        $cacheHash = md5("ARTISTIMAGEURL:" . mb_trim($artistPageURL));
        $imageURL = null;
        $this->logger->debug("LastFMWrapper\Artist::getImageFromArtistPageURL", array("url" => $artistPageURL));
        if (str_starts_with($artistPageURL, "https://www.last.fm/music/")) {
            if (!$this->getCache($cacheHash)) {
                $response = $this->httpGET($artistPageURL);
                if ($response->code == 200) {
                    $this->resetThrottle();
                    if (!empty($response->body)) {
                        $doc = new \DomDocument();
                        $doc->loadHTML($response->body);
                        $xpath = new \DOMXPath($doc);
                        $query = '//*/meta[starts-with(@property, \'og:\')]';
                        $metas = $xpath->query($query);
                        foreach ($metas as $meta) {
                            if ($meta instanceof \DOMElement && $meta->getAttribute('property') == 'og:image') {
                                $imageURL = $meta->getAttribute('content');
                                break;
                            }
                        }
                        if (! empty($imageURL)) {
                            $this->saveCache($cacheHash, $imageURL);
                        }
                        return ($imageURL);
                    } else {
                        throw new \aportela\LastFMWrapper\Exception\InvalidAPIResponseFormatException("empy body");
                    }
                } else {
                    throw new \aportela\LastFMWrapper\Exception\HTTPException("url: " . $artistPageURL, $response->code);
                }
            } else {
                return ($this->raw);
            }
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidURLException("invalid url: " . $artistPageURL);
        }
    }
}
