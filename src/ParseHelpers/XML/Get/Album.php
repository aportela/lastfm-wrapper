<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\XML\Get;

class Album extends \aportela\LastFMWrapper\ParseHelpers\ParseXMLHelper
{
    public function parse(): \aportela\LastFMWrapper\ParseHelpers\XML\AlbumHelper
    {
        $albumXPath = $this->getXPath("//lfm/album");
        if ($albumXPath !== false && is_array($albumXPath) && count($albumXPath) == 1) {
            return (new \aportela\LastFMWrapper\ParseHelpers\XML\AlbumHelper($albumXPath[0]));
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("lfm album xpath not found");
        }
    }
}
