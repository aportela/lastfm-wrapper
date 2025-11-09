<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\XML\Get;

class Track extends \aportela\LastFMWrapper\ParseHelpers\ParseXMLHelper
{
    public function parse(): \aportela\LastFMWrapper\ParseHelpers\XML\TrackHelper
    {
        $trackXPath = $this->getXPath("//lfm/track");
        if ($trackXPath !== false && is_array($trackXPath) && count($trackXPath) == 1) {
            return (new \aportela\LastFMWrapper\ParseHelpers\XML\TrackHelper($trackXPath[0]));
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("lfm track xpath not found");
        }
    }
}
