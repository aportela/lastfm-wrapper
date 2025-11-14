<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\JSON;

class ArtistBioHelper extends \aportela\LastFMWrapper\ParseHelpers\ArtistBioHelper
{
    public function __construct(object $object)
    {
        $this->summary = property_exists($object, "summary") && is_string($object->summary) ? $object->summary : null;
        $this->trimSummary();
        $this->content = property_exists($object, "content") && is_string($object->content) ? $object->content : null;
    }
}
