<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\XML\Get;

class Artist extends \aportela\LastFMWrapper\ParseHelpers\ParseXMLHelper
{
    public function parse(): \aportela\LastFMWrapper\ParseHelpers\XML\ArtistHelper
    {
        $artistXPath = $this->getXPath("//lfm/artist");
        if ($artistXPath !== false && is_array($artistXPath) && count($artistXPath) == 1) {
            return (new \aportela\LastFMWrapper\ParseHelpers\XML\ArtistHelper($artistXPath[0]));
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("lfm artist xpath not found");
        }
    }
}
