<?php

namespace aportela\LastFMWrapper\ParseHelpers\JSON;

class ArtistHelper extends \aportela\LastFMWrapper\ParseHelpers\ArtistHelper
{
    public function __construct(object $object)
    {
        $this->mbId = !empty($object->mbid) ? (string)$object->mbid : null;
        $this->name = (string)$object->name;
        $this->url = (string)$object->url;
        if (isset($object->image) && is_array(($object->image))) {
            foreach ($object->image as $image) {
                $this->image[] = new \aportela\LastFMWrapper\ParseHelpers\JSON\ImageHelper($image);
            }
        }
        if (isset($object->tags) && isset($object->tags->tag) && is_array($object->tags->tag)) {
            foreach ($object->tags->tag as $tag) {
                $this->tags[] = mb_strtolower(trim($tag->name));
            }
            $this->tags = array_unique($this->tags);
        }

        if (isset($object->similar) && isset($object->similar->artist) && is_array($object->similar->artist)) {
            foreach ($object->similar->artist as $artist) {
                $this->similar[] = new \aportela\LastFMWrapper\ParseHelpers\JSON\ArtistHelper($artist);
            }
        }

        $this->bio = (object) array(
            "summary" => isset($object->bio) && isset($object->bio->summary) ? (string) $object->bio->summary : null,
            "content" => isset($object->bio) && isset($object->bio->content) ? (string) $object->bio->content : null,
        );
    }
}
