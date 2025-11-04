<?php

namespace aportela\LastFMWrapper\ParseHelpers\XML;

class ArtistHelper extends \aportela\LastFMWrapper\ParseHelpers\ArtistHelper
{
    public function __construct(\SimpleXMLElement $element)
    {
        $children = $element->children();
        if ($children != null) {
            $this->mbId = !empty($children->mbid) ? (string)$children->mbid : null;
            $this->name = !empty($children->name) ? (string)$children->name : null;
            $this->url = !empty($children->url) ? (string)$children->url : null;

            if (isset($children->tags)) {
                $tags = $children->tags->children()->tag;
                if (isset($tags)) {
                    foreach ($tags as $tag) {
                        $this->tags[] = mb_strtolower(mb_trim($tag->children()->name));
                    }
                    $this->tags = array_unique($this->tags);
                }
            }

            if (isset($children->similar)) {
                $artists = $children->similar->children()->artist;
                if (isset($artists)) {
                    foreach ($artists as $artist) {
                        $this->similar[] = new \aportela\LastFMWrapper\ParseHelpers\XML\ArtistHelper($artist);
                    }
                }
            }

            $this->bio = isset($children->bio) ? new \aportela\LastFMWrapper\ParseHelpers\XML\ArtistBioHelper($children->bio) : null;
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("artist element without children elements");
        }
    }
}
