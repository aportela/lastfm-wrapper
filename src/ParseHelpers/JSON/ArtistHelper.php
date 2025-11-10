<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\JSON;

class ArtistHelper extends \aportela\LastFMWrapper\ParseHelpers\ArtistHelper
{
    public function __construct(object $object)
    {
        $this->mbId = $this->getObjectStringProperty($object, "mbid");
        $this->name = $this->getObjectStringProperty($object, "name");
        $this->url = $this->getObjectStringProperty($object, "url");

        if (property_exists($object, "tags") && is_object($object->tags) && property_exists($object->tags, "tag") && is_array($object->tags->tag)) {
            foreach ($object->tags->tag as $tag) {
                if (is_object($tag) && property_exists($tag, "name") && is_string($tag->name)) {
                    $this->tags[] = mb_strtolower(mb_trim($tag->name));
                }
            }

            $this->tags = array_unique($this->tags);
        }

        if (property_exists($object, "similar") && is_object($object->similar) && property_exists($object->similar, "artist") && is_array($object->similar->artist)) {
            foreach ($object->similar->artist as $artist) {
                if (is_object($artist)) {
                    $this->similar[] = new \aportela\LastFMWrapper\ParseHelpers\JSON\ArtistHelper($artist);
                }
            }
        }

        $this->bio = property_exists($object, "bio") && is_object($object->bio) ? new \aportela\LastFMWrapper\ParseHelpers\JSON\ArtistBioHelper($object->bio) : null;
    }
}
