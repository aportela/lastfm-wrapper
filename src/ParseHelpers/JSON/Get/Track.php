<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\JSON\Get;

class Track extends \aportela\LastFMWrapper\ParseHelpers\ParseJSONHelper
{
    public function parse(): \aportela\LastFMWrapper\ParseHelpers\JSON\TrackHelper
    {
        if (property_exists($this->json, "track") && is_object($this->json->track)) {
            return (new \aportela\LastFMWrapper\ParseHelpers\JSON\TrackHelper($this->json->track));
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidJSONException("track property not found");
        }
    }
}
