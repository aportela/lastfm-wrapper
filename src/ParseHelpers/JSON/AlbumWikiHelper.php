<?php

namespace aportela\LastFMWrapper\ParseHelpers\JSON;

class AlbumWikiHelper extends \aportela\LastFMWrapper\ParseHelpers\AlbumWikiHelper
{
    public function __construct(object $object)
    {
        $this->summary = ! empty($object->summary) ? (string) $object->summary : null;
        $this->content = ! empty($object->content) ? (string) $object->content : null;
    }
}
