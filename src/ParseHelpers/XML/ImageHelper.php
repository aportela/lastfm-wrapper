<?php

namespace aportela\LastFMWrapper\ParseHelpers\XML;

class ImageHelper extends \aportela\LastFMWrapper\ParseHelpers\ImageHelper
{
    public function __construct(\SimpleXMLElement $element)
    {
        $this->url = (string) $element;
        $this->size = \aportela\LastFMWrapper\ImageSize::fromString((string) $element->attributes()->size);
    }
}
