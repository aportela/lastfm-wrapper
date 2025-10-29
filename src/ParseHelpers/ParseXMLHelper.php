<?php

namespace aportela\LastFMWrapper\ParseHelpers;

abstract class ParseXMLHelper
{
    protected mixed $xml;

    public function __construct(string $raw)
    {
        libxml_clear_errors();
        $this->xml = simplexml_load_string($raw);
        if ($this->xml === false) {

            $errorMessage = "invalid xml";
            $errorCode = 0;
            $lastError = libxml_get_last_error();
            if ($lastError) {
                $errorMessage = "Error: " . $lastError->message . " (Line: " . $lastError->line . ", Column: " . $lastError->column . ")";
                $errorCode = $lastError->code;
            }
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException($errorMessage, $errorCode);
        }
    }

    protected function getXPath(string $path): mixed
    {
        return ($this->xml->xpath($path));
    }

    abstract public function parse(): mixed;
}
