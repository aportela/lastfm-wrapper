<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\JSON;

class TrackHelper extends \aportela\LastFMWrapper\ParseHelpers\TrackHelper
{
    public function __construct(object $object)
    {
        $this->rank = property_exists($object, "@attr") && is_object($object->{"@attr"}) && property_exists($object->{"@attr"}, "rank") && is_numeric($object->{"@attr"}->rank) ? intval($object->{"@attr"}->rank) : null;
        $this->mbId = $this->getObjectStringProperty($object, "mbid");
        $this->name = $this->getObjectStringProperty($object, "name");
        $this->url = $this->getObjectStringProperty($object, "url");
        if (isset($object->artist)) {
            switch (gettype($object->artist)) {
                case "object":
                    // Get Artist API (this returns artist as complete object)
                    $this->artist = isset($object->artist) ? new \aportela\LastFMWrapper\ParseHelpers\JSON\ArtistHelper($object->artist) : null;
                    break;
                case "string":
                    // Search Artist API (this returns artist name as string)
                    if ((string)$object->artist !== '' && (string)$object->artist !== '0') {
                        $this->artist = new \aportela\LastFMWrapper\ParseHelpers\ArtistHelper();
                        $this->artist->name = (string)$object->artist;
                    }

                    break;
            }
        }

        $this->album = property_exists($object, "album") && is_object($object->album) ? new \aportela\LastFMWrapper\ParseHelpers\JSON\AlbumHelper($object->album) : null;
        if (property_exists($object, "toptags") && is_object($object->toptags) && property_exists($object->toptags, "tag") && is_array($object->toptags->tag)) {
            foreach ($object->toptags->tag as $tag) {
                if (is_object($tag) && property_exists($tag, "name") && is_string($tag->name) && ! empty($tag->name)) {
                    $this->tags[] = mb_strtolower(mb_trim($tag->name));
                }
            }

            $this->tags = array_unique($this->tags);
        }
    }
}
