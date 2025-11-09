<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers;

class ParseXMLHelper
{
    protected \SimpleXMLElement $xml;

    public function __construct(string $raw)
    {
        libxml_clear_errors();
        if (($element = simplexml_load_string($raw)) === false) {
            $errorMessage = "invalid xml";
            $errorCode = 0;
            $lastError = libxml_get_last_error();
            if ($lastError) {
                $errorMessage = "Error: " . $lastError->message . " (Line: " . $lastError->line . ", Column: " . $lastError->column . ")";
                $errorCode = $lastError->code;
            }
            
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException($errorMessage, $errorCode);
        }
        
        $this->xml = $element;
    }

    /**
     * @return array<\SimpleXMLElement>|null|false
     */
    protected function getXPath(string $path): array|null|false
    {
        return ($this->xml->xpath($path));
    }
}
