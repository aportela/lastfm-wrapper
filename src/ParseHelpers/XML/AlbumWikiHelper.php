<?php

namespace aportela\LastFMWrapper\ParseHelpers\XML;

class AlbumWikiHelper extends \aportela\LastFMWrapper\ParseHelpers\AlbumWikiHelper
{
    public function __construct(\SimpleXMLElement $element)
    {
        $children = $element->children();
        if ($children != null) {
            $this->summary = ! empty($children->summary) ? (string) $children->summary : null;
            $this->content = ! empty($children->content) ? (string) $children->content : null;
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("album wiki element without children elements");
        }
    }
}
