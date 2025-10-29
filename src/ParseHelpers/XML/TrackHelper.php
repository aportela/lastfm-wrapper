<?php

namespace aportela\LastFMWrapper\ParseHelpers\XML;

class TrackHelper extends \aportela\LastFMWrapper\ParseHelpers\TrackHelper
{
    public function __construct(\SimpleXMLElement $element)
    {
        $this->mbId = !empty($element->children()->mbid) ? (string)$element->children()->mbid : null;
        $this->name = (string)$element->children()->name;
        $this->url = (string)$element->children()->url;
        $this->artist = new \aportela\LastFMWrapper\ParseHelpers\XML\ArtistHelper($element->children()->artist);
        $this->album = new \aportela\LastFMWrapper\ParseHelpers\XML\AlbumHelper($element->children()->album);
        if (isset($element->children()->toptags) && isset($element->children()->toptags->children()->tag)) {
            foreach ($element->children()->toptags->children()->tag as $tag) {
                $this->tags[] = mb_strtolower(trim($tag->children()->name));
            }
            $this->tags = array_unique($this->tags);
        }
    }
}
