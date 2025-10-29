<?php

namespace aportela\LastFMWrapper\ParseHelpers\XML;

class ArtistHelper extends \aportela\LastFMWrapper\ParseHelpers\ArtistHelper
{
    public function __construct(\SimpleXMLElement $element)
    {
        $this->mbId = !empty($element->children()->mbid) ? (string)$element->children()->mbid : null;
        $this->name = (string)$element->children()->name;
        $this->url = (string)$element->children()->url;
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

        if (isset($element->children()->similar) && isset($element->children()->similar->artist)) {
            foreach ($element->children()->similar->children()->artist as $artist) {
                $this->similar[] = new \aportela\LastFMWrapper\ParseHelpers\XML\ArtistHelper($artist);
            }
        }

        $this->bio = (object) array(
            "summary" => isset($element->children()->bio) && isset($element->children()->bio->children()->summary) ? (string) $element->children()->bio->summary : null,
            "content" => isset($element->children()->bio) && isset($element->children()->bio->children()->content) ? (string) $element->children()->bio->content : null,
        );
    }
}
