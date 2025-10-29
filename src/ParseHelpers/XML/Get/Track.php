<?php

namespace aportela\LastFMWrapper\ParseHelpers\XML\Get;

class Track extends \aportela\LastFMWrapper\ParseHelpers\ParseXMLHelper
{
    public function parse(): mixed
    {
        $trackXPath = $this->getXPath("//lfm/track");
        if ($trackXPath === false || count($trackXPath) != 1) {
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("lfm track xpath not found");
        }
        return (new \aportela\LastFMWrapper\ParseHelpers\XML\TrackHelper($trackXPath[0]));
    }
}
