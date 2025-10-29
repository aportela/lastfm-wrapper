<?php

namespace aportela\LastFMWrapper\ParseHelpers\JSON;

class ArtistBioHelper extends \aportela\LastFMWrapper\ParseHelpers\ArtistBioHelper
{
    public function __construct(object $object)
    {
        $this->summary = ! empty($object->summary) ? (string) $object->summary : null;
        $this->content = ! empty($object->content) ? (string) $object->content : null;
    }
}
