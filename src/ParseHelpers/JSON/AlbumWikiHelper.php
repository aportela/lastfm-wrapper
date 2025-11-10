<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\JSON;

class AlbumWikiHelper extends \aportela\LastFMWrapper\ParseHelpers\AlbumWikiHelper
{
    public function __construct(object $object)
    {
        $this->summary = empty($object->summary) ? null : (string) $object->summary;
        $this->content = empty($object->content) ? null : (string) $object->content;
    }
}
