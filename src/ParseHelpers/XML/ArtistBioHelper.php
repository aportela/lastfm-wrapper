<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\XML;

class ArtistBioHelper extends \aportela\LastFMWrapper\ParseHelpers\ArtistBioHelper
{
    public function __construct(\SimpleXMLElement $element)
    {
        $children = $element->children();
        if ($children != null) {
            $this->summary = ! empty($children->summary) ? (string) $children->summary : null;
            $this->content = ! empty($children->content) ? (string) $children->content : null;
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("artist bio element without children elements");
        }
    }
}
