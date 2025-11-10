<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\XML;

class AlbumWikiHelper extends \aportela\LastFMWrapper\ParseHelpers\AlbumWikiHelper
{
    public function __construct(\SimpleXMLElement $element)
    {
        $children = $element->children();
        if ($children != null) {
            $this->summary = empty($children->summary) ? null : (string) $children->summary;
            $this->content = empty($children->content) ? null : (string) $children->content;
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("album wiki element without children elements");
        }
    }
}
