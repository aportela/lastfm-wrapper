<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\XML;

class AlbumHelper extends \aportela\LastFMWrapper\ParseHelpers\AlbumHelper
{
    public function __construct(\SimpleXMLElement $element)
    {
        $children = $element->children();
        if ($children != null) {
            $this->mbId = !empty($children->mbid) ? (string)$children->mbid : null;
            // WARNING: sometimes on album object/element of API responses name property is missing and replaced by title
            // ex: track album details on getTrack API response
            if (! empty($children->name)) {
                $this->name = (string)$children->name;
            } elseif (! empty($children->title)) {
                $this->name = (string)$children->title;
            } else {
                throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("album name||title property not found");
            }

            if (isset($children->artist)) {
                if ($children->artist->children()) {
                    // Get Artist API (this returns artist as complete object)
                    $this->artist = isset($children->artist) ? new \aportela\LastFMWrapper\ParseHelpers\XML\ArtistHelper($children->artist) : null;
                } else {
                    // Search Artist API (this returns artist name as string)
                    $artistName = (string)$children->artist;
                    if (! empty($artistName)) {
                        $this->artist = new \aportela\LastFMWrapper\ParseHelpers\ArtistHelper();
                        $this->artist->name = $artistName;
                    }
                }
            }
            $this->url = ! empty($children->url) ? (string)$children->url : null;

            if (isset($children->tags)) {
                $tags = $children->tags->children()->tag;
                if (isset($tags)) {
                    foreach ($tags as $tag) {
                        $this->tags[] = mb_strtolower(mb_trim(strval($tag->children()->name)));
                    }
                    $this->tags = array_unique($this->tags);
                }
            }

            if (isset($children->tracks)) {
                $tracks = $children->tracks->children()->track;
                if (isset($tracks)) {
                    foreach ($tracks as $track) {
                        $this->tracks[] = new \aportela\LastFMWrapper\ParseHelpers\XML\TrackHelper($track);
                    }
                }
            }
            $this->wiki = isset($children->wiki) ? new \aportela\LastFMWrapper\ParseHelpers\XML\AlbumWikiHelper($children->wiki) : null;
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("album element without children elements");
        }
    }
}
