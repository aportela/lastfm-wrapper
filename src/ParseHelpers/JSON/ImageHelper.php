<?php

namespace aportela\LastFMWrapper\ParseHelpers\JSON;

class ImageHelper extends \aportela\LastFMWrapper\ParseHelpers\ImageHelper
{
    public function __construct(object $object)
    {
        $this->url = !empty($object->{"#text"}) ? (string) $object->{"#text"} : null;
        $this->size =  !empty($object->size) ? \aportela\LastFMWrapper\ImageSize::fromString((string) $object->size) : \aportela\LastFMWrapper\ImageSize::NONE;
    }
}
