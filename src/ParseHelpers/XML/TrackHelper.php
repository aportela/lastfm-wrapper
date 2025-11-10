<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\XML;

class TrackHelper extends \aportela\LastFMWrapper\ParseHelpers\TrackHelper
{
    public function __construct(\SimpleXMLElement $element)
    {
        $children = $element->children();
        if ($children != null) {
            $this->rank = property_exists($element->attributes(), 'rank') && $element->attributes()->rank !== null ? (int)$element->attributes()->rank : null;
            $this->mbId = empty($children->mbid) ? null : (string)$children->mbid;
            $this->name = empty($children->name) ? null : (string)$children->name;
            $this->url = empty($children->url) ? null : (string)$children->url;
            if (property_exists($children, 'artist') && $children->artist !== null) {
                if ($children->artist->children()) {
                    // Get Artist API (this returns artist as complete object)
                    $this->artist = property_exists($children, 'artist') ? new \aportela\LastFMWrapper\ParseHelpers\XML\ArtistHelper($children->artist) : null;
                } elseif ((string)$children->artist !== '' && (string)$children->artist !== '0') {
                    // Search Artist API (this returns artist name as string)
                    $this->artist = new \aportela\LastFMWrapper\ParseHelpers\ArtistHelper();
                    $this->artist->name = (string)$children->artist;
                }
            }

            $this->album = property_exists($children, 'album') && $children->album !== null ? new \aportela\LastFMWrapper\ParseHelpers\XML\AlbumHelper($children->album) : null;

            if (property_exists($children, 'toptags') && $children->toptags !== null) {
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
