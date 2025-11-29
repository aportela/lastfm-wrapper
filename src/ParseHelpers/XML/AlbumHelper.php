<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\XML;

class AlbumHelper extends \aportela\LastFMWrapper\ParseHelpers\AlbumHelper
{
    public function __construct(\SimpleXMLElement $element)
    {
        $children = $element->children();
        if ($children != null) {
            $this->mbId = empty($children->mbid) ? null : (string) $children->mbid;
            // WARNING: sometimes on album object/element of API responses name property is missing and replaced by title
            // ex: track album details on getTrack API response
            if (! empty($children->name)) {
                $this->name = (string) $children->name;
            } elseif (! empty($children->title)) {
                $this->name = (string) $children->title;
            } else {
                throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("album name||title property not found");
            }

            if (property_exists($children, 'artist') && $children->artist !== null) {
                $childrenCompleteArtist = $children->artist->children();
                if (property_exists($childrenCompleteArtist, "name") && property_exists($childrenCompleteArtist, "mbid") && property_exists($childrenCompleteArtist, "url")) {
                    // Get Artist API (this returns artist as complete object)
                    $this->artist = property_exists($children, 'artist') ? new \aportela\LastFMWrapper\ParseHelpers\XML\ArtistHelper($children->artist) : null;
                } else {
                    // Search Artist API (this returns artist name as string)
                    $artistName = (string) $children->artist;
                    if ($artistName !== '' && $artistName !== '0') {
                        $this->artist = new \aportela\LastFMWrapper\ParseHelpers\ArtistHelper();
                        $this->artist->name = $artistName;
                    }
                }
            }

            $this->url = empty($children->url) ? null : (string) $children->url;

            if (property_exists($children, 'tags') && $children->tags !== null) {
                $tags = $children->tags->children()->tag;
                if (isset($tags)) {
                    foreach ($tags as $tag) {
                        $this->tags[] = mb_strtolower(mb_trim(strval($tag->children()->name)));
                    }

                    $this->tags = array_unique($this->tags);
                }
            }

            if (property_exists($children, 'tracks') && $children->tracks !== null) {
                $tracks = $children->tracks->children()->track;
                if (isset($tracks)) {
                    foreach ($tracks as $track) {
                        $this->tracks[] = new \aportela\LastFMWrapper\ParseHelpers\XML\TrackHelper($track);
                    }
                }
            }

            $this->wiki = property_exists($children, 'wiki') && $children->wiki !== null ? new \aportela\LastFMWrapper\ParseHelpers\XML\AlbumWikiHelper($children->wiki) : null;
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("album element without children elements");
        }
    }
}
