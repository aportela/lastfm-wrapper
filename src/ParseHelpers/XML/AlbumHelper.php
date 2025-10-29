<?php

namespace aportela\LastFMWrapper\ParseHelpers\XML;

class AlbumHelper extends \aportela\LastFMWrapper\ParseHelpers\AlbumHelper
{
    public function __construct(\SimpleXMLElement $element)
    {
        $this->mbId = !empty($element->children()->mbid) ? (string)$element->children()->mbid : null;
        // WARNING: sometimes on album object/element of API responses name property is missing and replaced by title
        // ex: track album details on getTrack API response
        if (isset($element->children()->name)) {
            $this->name = (string)$element->children()->name;
        } else if (isset($element->children()->title)) {
            $this->name = (string)$element->children()->title;
        }
        $this->artist = (string)$element->children()->artist;
        $this->url = (string)$element->children()->url;
        /*
        if (isset($element->children()->image)) {
            foreach ($element->children()->image as $image) {
                $this->image[] = new \aportela\LastFMWrapper\ParseHelpers\XML\ImageHelper($image);
            }
        }

        if (isset($element->tags) && isset($element->tags->tag)) {
            foreach ($element->tags->tag as $tag) {
                $this->tags[] = mb_strtolower(trim($tag->children()->name));
            }
            $this->tags = array_unique($this->tags);
        }
            */
    }
}
