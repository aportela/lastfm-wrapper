<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers;

class ParseJSONHelper
{
    protected object $json;

    public function __construct(string $raw)
    {
        $obj = json_decode($raw);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \aportela\LastFMWrapper\Exception\InvalidJSONException(json_last_error_msg(), json_last_error());
        } elseif (! is_object($obj)) {
            throw new \aportela\LastFMWrapper\Exception\InvalidJSONException("invalid object");
        }
        
        $this->json = $obj;
    }
}
