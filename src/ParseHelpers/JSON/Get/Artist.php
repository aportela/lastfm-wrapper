<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\JSON\Get;

class Artist extends \aportela\LastFMWrapper\ParseHelpers\ParseJSONHelper
{
    public function parse(): \aportela\LastFMWrapper\ParseHelpers\JSON\ArtistHelper
    {
        if (property_exists($this->json, "artist") && is_object($this->json->artist)) {
            return (new \aportela\LastFMWrapper\ParseHelpers\JSON\ArtistHelper($this->json->artist));
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidJSONException("artist property not found");
        }
    }
}
