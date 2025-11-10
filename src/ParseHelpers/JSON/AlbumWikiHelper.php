<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\JSON;

class AlbumWikiHelper extends \aportela\LastFMWrapper\ParseHelpers\AlbumWikiHelper
{
    public function __construct(object $object)
    {
        $this->summary = property_exists($object, "summary") && is_string($object->summary) ? $object->summary : null;
        $this->content = property_exists($object, "content") && is_string($object->content) ? $object->content : null;
    }
}
