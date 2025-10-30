<?php

namespace aportela\LastFMWrapper\ParseHelpers\JSON\Get;

class Artist extends \aportela\LastFMWrapper\ParseHelpers\ParseJSONHelper
{
    public function parse(): \aportela\LastFMWrapper\ParseHelpers\JSON\ArtistHelper
    {
        if (isset($this->json->artist)) {
            return (new \aportela\LastFMWrapper\ParseHelpers\JSON\ArtistHelper($this->json->artist));
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidJSONException("artist property not found");
        }
    }
}
