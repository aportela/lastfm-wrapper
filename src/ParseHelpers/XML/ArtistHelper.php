<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\XML;

class ArtistHelper extends \aportela\LastFMWrapper\ParseHelpers\ArtistHelper
{
    public function __construct(\SimpleXMLElement $element)
    {
        $children = $element->children();
        if ($children != null) {
            $this->mbId = empty($children->mbid) ? null : (string)$children->mbid;
            $this->name = empty($children->name) ? null : (string)$children->name;
            $this->url = empty($children->url) ? null : (string)$children->url;

            if (property_exists($children, 'tags') && $children->tags !== null) {
                $tags = $children->tags->children()->tag;
                if (isset($tags)) {
                    foreach ($tags as $tag) {
                        $this->tags[] = mb_strtolower(mb_trim(strval($tag->children()->name)));
                    }

                    $this->tags = array_unique($this->tags);
                }
            }

            if (property_exists($children, 'similar') && $children->similar !== null) {
                $artists = $children->similar->children()->artist;
                if (isset($artists)) {
                    foreach ($artists as $artist) {
                        $this->similar[] = new \aportela\LastFMWrapper\ParseHelpers\XML\ArtistHelper($artist);
                    }
                }
            }

            $this->bio = property_exists($children, 'bio') && $children->bio !== null ? new \aportela\LastFMWrapper\ParseHelpers\XML\ArtistBioHelper($children->bio) : null;
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("artist element without children elements");
        }
    }
}
