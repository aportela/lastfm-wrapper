<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\XML;

class TrackHelper extends \aportela\LastFMWrapper\ParseHelpers\TrackHelper
{
    public function __construct(\SimpleXMLElement $element)
    {
        $children = $element->children();
        if ($children != null) {
            $this->rank = isset($element->attributes()->rank) ? (int)$element->attributes()->rank : null;
            $this->mbId = !empty($children->mbid) ? (string)$children->mbid : null;
            $this->name = !empty($children->name) ? (string)$children->name : null;
            $this->url = !empty($children->url) ? (string)$children->url : null;
            if (isset($children->artist)) {
                if ($children->artist->children()) {
                    // Get Artist API (this returns artist as complete object)
                    $this->artist = isset($children->artist) ? new \aportela\LastFMWrapper\ParseHelpers\XML\ArtistHelper($children->artist) : null;
                } else {
                    // Search Artist API (this returns artist name as string)
                    if (! empty((string)$children->artist)) {
                        $this->artist = new \aportela\LastFMWrapper\ParseHelpers\ArtistHelper();
                        $this->artist->name = (string)$children->artist;
                    }
                }
            }

            $this->album = isset($children->album) ? new \aportela\LastFMWrapper\ParseHelpers\XML\AlbumHelper($children->album) : null;

            if (isset($children->toptags)) {
                $tags = $children->toptags->children()->tag;
                if (isset($tags)) {
                    foreach ($tags as $tag) {
                        $this->tags[] = mb_strtolower(mb_trim(strval($tag->children()->name)));
                    }

                    $this->tags = array_unique($this->tags);
                }
            }
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("track element without children elements");
        }
    }
}
