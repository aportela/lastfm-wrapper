<?php

namespace aportela\LastFMWrapper\ParseHelpers;

class ParseJSONHelper
{
    protected mixed $json;

    public function __construct(string $raw)
    {
        $this->json = json_decode($raw);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \aportela\LastFMWrapper\Exception\InvalidJSONException(json_last_error_msg(), json_last_error());
        }
    }
}
