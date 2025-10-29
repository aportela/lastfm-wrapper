<?php

namespace aportela\LastFMWrapper\ParseHelpers\JSON;

class TrackHelper extends \aportela\LastFMWrapper\ParseHelpers\TrackHelper
{
    public function __construct(object $object)
    {
        $this->mbId = !empty($object->mbid) ? (string)$object->mbid : null;
        $this->name = !empty($object->name) ? (string)$object->name : null;
        $this->url = !empty($object->url) ? (string)$object->url : null;
        if (isset($object->artist)) {
            switch (gettype($object->artist)) {
                case "object":
                    // Get Artist API (this returns artist as complete object)
                    $this->artist = isset($object->artist) ? new \aportela\LastFMWrapper\ParseHelpers\JSON\ArtistHelper($object->artist) : null;
                    break;
                case "string":
                    // Search Artist API (this returns artist name as string)
                    if (! empty((string)$object->artist)) {
                        $this->artist = new \aportela\LastFMWrapper\ParseHelpers\ArtistHelper();
                        $this->artist->name = (string)$object->artist;
                    }
                    break;
            }
        }
        $this->album = isset($object->album) ? new \aportela\LastFMWrapper\ParseHelpers\JSON\AlbumHelper($object->album) : null;
        if (isset($object->toptags) && isset($object->toptags->tag) && is_array($object->toptags->tag)) {
            foreach ($object->toptags->tag as $tag) {
                $this->tags[] = mb_strtolower(trim($tag->name));
            }
            $this->tags = array_unique($this->tags);
        }
    }
}
