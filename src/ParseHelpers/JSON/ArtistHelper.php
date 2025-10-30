<?php

namespace aportela\LastFMWrapper\ParseHelpers\JSON;

class ArtistHelper extends \aportela\LastFMWrapper\ParseHelpers\ArtistHelper
{
    public function __construct(object $object)
    {
        $this->mbId = !empty($object->mbid) ? (string)$object->mbid : null;
        $this->name = !empty($object->name) ? (string)$object->name : null;
        $this->url = !empty($object->url) ? (string)$object->url : null;
        if (isset($object->image) && is_array(($object->image))) {
            foreach ($object->image as $image) {
                $this->image[] = new \aportela\LastFMWrapper\ParseHelpers\JSON\ImageHelper($image);
            }
        }
        if (isset($object->tags) && isset($object->tags->tag)) {
            foreach ($object->tags->tag as $tag) {
                $this->tags[] = mb_strtolower(mb_trim($tag->name));
            }
            $this->tags = array_unique($this->tags);
        }

        if (isset($object->similar) && isset($object->similar->artist) && is_array($object->similar->artist)) {
            foreach ($object->similar->artist as $artist) {
                $this->similar[] = new \aportela\LastFMWrapper\ParseHelpers\JSON\ArtistHelper($artist);
            }
        }

        $this->bio = isset($object->bio) ? new \aportela\LastFMWrapper\ParseHelpers\JSON\ArtistBioHelper($object->bio) : null;
    }
}
