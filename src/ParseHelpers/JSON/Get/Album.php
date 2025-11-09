<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\JSON\Get;

class Album extends \aportela\LastFMWrapper\ParseHelpers\ParseJSONHelper
{
    public function parse(): \aportela\LastFMWrapper\ParseHelpers\JSON\AlbumHelper
    {
        if (isset($this->json->album)) {
            return (new \aportela\LastFMWrapper\ParseHelpers\JSON\AlbumHelper($this->json->album));
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidJSONException("album property not found");
        }
    }
}
