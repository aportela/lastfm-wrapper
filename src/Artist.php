<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper;

class Artist extends \aportela\LastFMWrapper\Entity
{
    private const string SEARCH_API_URL = "http://ws.audioscrobbler.com/2.0/?method=artist.search&artist=%s&api_key=%s&limit=%d&format=%s";
    
    private const string GET_API_URL = "http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist=%s&api_key=%s&autocorrect=1&format=%s";

    /**
     * @return array<\aportela\LastFMWrapper\ParseHelpers\ArtistHelper>
     */
    public function search(string $name, int $limit = 1): array
    {
        $url = sprintf(self::SEARCH_API_URL, urlencode($name), $this->apiKey, $limit, $this->apiFormat->value);
        $responseBody = $this->httpGET($url);
        if (!in_array($responseBody, [null, '', '0'], true)) {
            switch ($this->apiFormat) {
                case \aportela\LastFMWrapper\APIFormat::XML:
                    $this->parser = new \aportela\LastFMWrapper\ParseHelpers\XML\Search\Artist($responseBody);
                    break;
                case \aportela\LastFMWrapper\APIFormat::JSON:
                    $this->parser = new \aportela\LastFMWrapper\ParseHelpers\JSON\Search\Artist($responseBody);
                    break;
                default:
                    $this->logger->error(\aportela\LastFMWrapper\Artist::class . '::search - Error: invalid API format', [$this->apiFormat]);
                    throw new \aportela\LastFMWrapper\Exception\InvalidAPIFormat('Invalid API format: ' . $this->apiFormat->value);
            }
            
            return ($this->parser->parse());
        } else {
            $this->logger->error(\aportela\LastFMWrapper\Artist::class . '::search - Error: empty body on API response', [$url]);
            throw new \aportela\LastFMWrapper\Exception\InvalidAPIResponse('Empty body on API response for URL: ' . $url);
        }
    }

    public function get(string $name): \aportela\LastFMWrapper\ParseHelpers\ArtistHelper
    {
        $cacheHash = md5("ARTISTNAME:" . mb_strtolower(mb_trim($name)));
        if (!$this->getCache($cacheHash)) {
            $url = sprintf(self::GET_API_URL, urlencode($name), $this->apiKey, $this->apiFormat->value);
            $responseBody = $this->httpGET($url);
            if (!in_array($responseBody, [null, '', '0'], true)) {
                $this->saveCache($cacheHash, $responseBody);
                return ($this->parse($responseBody));
            } else {
                $this->logger->error(\aportela\LastFMWrapper\Artist::class . '::get - Error: empty body on API response', [$url]);
                throw new \aportela\LastFMWrapper\Exception\InvalidAPIResponse('Empty body on API response for URL: ' . $url);
            }
        } elseif (!in_array($this->raw, [null, '', '0'], true)) {
            return ($this->parse($this->raw));
        } else {
            $this->logger->error(\aportela\LastFMWrapper\Artist::class . '::get - Error: cached data for identifier is empty', [$cacheHash]);
            throw new \aportela\LastFMWrapper\Exception\InvalidCacheException(sprintf('Cached data for identifier (%s) is empty', $cacheHash));
        }
    }

    public function parse(string $rawText): \aportela\LastFMWrapper\ParseHelpers\ArtistHelper
    {
        $this->reset();
        switch ($this->apiFormat) {
            case \aportela\LastFMWrapper\APIFormat::XML:
                $this->parser = new \aportela\LastFMWrapper\ParseHelpers\XML\Get\Artist($rawText);
                break;
            case \aportela\LastFMWrapper\APIFormat::JSON:
                $this->parser = new \aportela\LastFMWrapper\ParseHelpers\JSON\Get\Artist($rawText);
                break;
            default:
                $this->logger->error("\aportela\MusicBrainzWrapper\Album::parse - Error: invalid API format", [$this->apiFormat]);
                throw new \aportela\LastFMWrapper\Exception\InvalidAPIFormat('Invalid API format: ' . $this->apiFormat->value);
        }
        
        $this->raw = $rawText;
        $artist = $this->parser->parse();
        if (!in_array($artist->url, [null, '', '0'], true)) {
            try {
                $artist->image = $this->getImageFromArtistPageURL($artist->url);
            } catch (\Exception $e) {
                $this->logger->info("\aportela\MusicBrainzWrapper\Album::parse - Error getting image from artist url page", [$artist->url, $e->getCode(), $e->getMessage()]);
            }
        }
        
        return ($artist);
    }

    public function getImageFromArtistPageURL(string $artistPageURL): ?string
    {
        $cacheHash = md5("ARTISTIMAGEURL:" . mb_trim($artistPageURL));
        $imageURL = null;
        if (str_starts_with($artistPageURL, "https://www.last.fm/music/")) {
            if (!$this->getCache($cacheHash)) {
                $responseBody = $this->httpGET($artistPageURL);
                if (!in_array($responseBody, [null, '', '0'], true)) {
                    $domDocument = new \DomDocument();
                    $domDocument->loadHTML($responseBody);
                    $domxPath = new \DOMXPath($domDocument);
                    $expression = "//*/meta[starts-with(@property, 'og:')]";
                    $metas = $domxPath->query($expression);
                    if ($metas !== false) {
                        for ($i = 0; $i < $metas->length; ++$i) {
                            $meta = $metas->item($i);
                            if ($meta instanceof \DOMElement && $meta->getAttribute('property') === 'og:image') {
                                $imageURL = $meta->getAttribute('content');
                                break;
                            }
                        }
                        
                        if (! empty($imageURL)) {
                            $previousCacheFormat = $this->getCacheFormat();
                            if (! is_bool($previousCacheFormat)) {
                                $this->setCacheFormat(\aportela\SimpleFSCache\CacheFormat::TXT);
                            }
                            
                            try {
                                $this->saveCache($cacheHash, $imageURL);
                            } finally {
                                if (! is_bool($previousCacheFormat)) {
                                    $this->setCacheFormat($previousCacheFormat);
                                }
                            }
                        }
                    }
                    
                    return ($imageURL);
                } else {
                    $this->logger->error(\aportela\LastFMWrapper\Artist::class . '::getImageFromArtistPageURL - Error: empty body on artist URL page request', [$artistPageURL]);
                    throw new \aportela\LastFMWrapper\Exception\InvalidAPIResponse('Empty body on artist page request, URL: ' . $artistPageURL);
                }
            } elseif (!in_array($this->raw, [null, '', '0'], true)) {
                return ($this->raw);
            } else {
                $this->logger->error(\aportela\LastFMWrapper\Artist::class . '::getImageFromArtistPageURL - Error: cached data for identifier is empty', [$cacheHash]);
                throw new \aportela\LastFMWrapper\Exception\InvalidCacheException(sprintf('Cached data for identifier (%s) is empty', $cacheHash));
            }
        } else {
            $this->logger->error(\aportela\LastFMWrapper\Artist::class . '::getImageFromArtistPageURL - Invalid Last.FM artist page URL', [$artistPageURL]);
            throw new \aportela\LastFMWrapper\Exception\InvalidCacheException('Invalid Last.FM artist page URL: ' . $artistPageURL);
        }
    }
}
