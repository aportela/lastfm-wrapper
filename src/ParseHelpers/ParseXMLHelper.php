<?php

namespace aportela\LastFMWrapper\ParseHelpers;

class ParseXMLHelper
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

    /**
     * @return array<\SimpleXMLElement>|null|false
     */
    protected function getXPath(string $path): array|null|false
    {
        if (is_object($this->xml)) {
            return ($this->xml->xpath($path));
        } else {
            return (false);
        }
    }
}
