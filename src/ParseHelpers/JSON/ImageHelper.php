<?php

namespace aportela\LastFMWrapper\ParseHelpers\JSON;

class ImageHelper extends \aportela\LastFMWrapper\ParseHelpers\ImageHelper
{
    public function __construct(object $object)
    {
        $this->url = (string) $object->{"#text"};
        $this->size = \aportela\LastFMWrapper\ImageSize::fromString((string) $object->size);
    }
}
