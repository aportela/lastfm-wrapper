<?php

namespace aportela\LastFMWrapper\ParseHelpers\JSON\Get;

class Track extends \aportela\LastFMWrapper\ParseHelpers\ParseJSONHelper
{
    public function parse(): mixed
    {
        if (isset($this->json->track)) {
            return (new \aportela\LastFMWrapper\ParseHelpers\JSON\TrackHelper($this->json->track));
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidJSONException("track property not found");
        }
    }
}
