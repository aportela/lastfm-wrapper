<?php

namespace aportela\LastFMWrapper\ParseHelpers\JSON;

class TrackHelper extends \aportela\LastFMWrapper\ParseHelpers\TrackHelper
{
    public function __construct(object $object)
    {
        $this->mbId = !empty($object->mbid) ? (string)$object->mbid : null;
        $this->name = (string)$object->name;
        $this->url = !empty($object->url) ? (string)$object->url : null;
        $this->artist = new \aportela\LastFMWrapper\ParseHelpers\JSON\ArtistHelper($object->artist);
        $this->album = new \aportela\LastFMWrapper\ParseHelpers\JSON\AlbumHelper($object->album);
        if (isset($object->toptags) && isset($object->toptags->tag) && is_array($object->toptags->tag)) {
            foreach ($object->toptags->tag as $tag) {
                $this->tags[] = mb_strtolower(trim($tag->name));
            }
            $this->tags = array_unique($this->tags);
        }
    }
}
