<?php

namespace aportela\LastFMWrapper\ParseHelpers\XML\Get;

class Artist extends \aportela\LastFMWrapper\ParseHelpers\ParseXMLHelper
{
    public function parse(): mixed
    {
        $artistXPath = $this->getXPath("//lfm/artist");
        if ($artistXPath === false || count($artistXPath) != 1) {
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("lfm artist xpath not found");
        }
        return (new \aportela\LastFMWrapper\ParseHelpers\XML\ArtistHelper($artistXPath[0]));
    }
}
