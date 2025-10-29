<?php

namespace aportela\LastFMWrapper\ParseHelpers\XML;

class ImageHelper extends \aportela\LastFMWrapper\ParseHelpers\ImageHelper
{
    public function __construct(\SimpleXMLElement $element)
    {
        $this->url = ! empty((string) $element) ? (string) $element : null;
        $this->size = ! empty($element->attributes()->size) ? \aportela\LastFMWrapper\ImageSize::fromString((string) $element->attributes()->size) : \aportela\LastFMWrapper\ImageSize::NONE;
    }
}
