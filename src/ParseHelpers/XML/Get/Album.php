<?php

namespace aportela\LastFMWrapper\ParseHelpers\XML\Get;

class Album extends \aportela\LastFMWrapper\ParseHelpers\ParseXMLHelper
{
    public function parse(): mixed
    {
        $albumXPath = $this->getXPath("//lfm/album");
        if ($albumXPath === false || count($albumXPath) != 1) {
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("lfm album xpath not found");
        }
        return (new \aportela\LastFMWrapper\ParseHelpers\XML\ArtistHelper($albumXPath[0]));
    }
}
