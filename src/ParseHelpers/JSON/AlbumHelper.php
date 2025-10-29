<?php

namespace aportela\LastFMWrapper\ParseHelpers\JSON;

class AlbumHelper extends \aportela\LastFMWrapper\ParseHelpers\AlbumHelper
{
    public function __construct(object $object)
    {
        $this->mbId = !empty($object->mbid) ? (string)$object->mbid : null;
        // WARNING: sometimes on album object/element of API responses name property is missing and replaced by title
        // ex: track album details on getTrack API response
        if (isset($object->name)) {
            $this->name = (string)$object->name;
        } else if (isset($object->title)) {
            $this->name = (string)$object->title;
        }
        $this->artist = (string)$object->artist;
        $this->url = !empty($object->url) ? (string)$object->url : null;
        if (isset($object->tags) && isset($object->tags->tag) && is_array($object->tags->tag)) {
            foreach ($object->tags->tag as $tag) {
                $this->tags[] = mb_strtolower(trim($tag->name));
            }
            $this->tags = array_unique($this->tags);
        }
    }
}
