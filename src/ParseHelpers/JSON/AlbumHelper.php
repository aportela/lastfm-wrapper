<?php

namespace aportela\LastFMWrapper\ParseHelpers\JSON;

class AlbumHelper extends \aportela\LastFMWrapper\ParseHelpers\AlbumHelper
{
    public function __construct(object $object)
    {
        $this->mbId = !empty($object->mbid) ? (string)$object->mbid : null;
        // WARNING: sometimes on album object/element of API responses name property is missing and replaced by title
        // ex: track album details on getTrack API response
        if (! empty($object->name)) {
            $this->name = (string)$object->name;
        } elseif (! empty($object->title)) {
            $this->name = (string)$object->title;
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidJSONException("album name||title property not found");
        }
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
        $this->url = !empty($object->url) ? (string)$object->url : null;
        if (isset($object->tags) && isset($object->tags->tag) && is_array($object->tags->tag)) {
            foreach ($object->tags->tag as $tag) {
                if (is_object($tag) && isset($tag->name) && ! empty($tag->name)) {
                    $this->tags[] = mb_strtolower(mb_trim($tag->name));
                }
            }
            $this->tags = array_unique($this->tags);
        }
        if (isset($object->tracks) && isset($object->tracks->track) && is_array($object->tracks->track)) {
            foreach ($object->tracks->track as $track) {
                if (is_object($track)) {
                    $this->tracks[] = new \aportela\LastFMWrapper\ParseHelpers\JSON\TrackHelper($track);
                }
            }
        }

        $this->wiki = isset($object->wiki) ? new \aportela\LastFMWrapper\ParseHelpers\JSON\AlbumWikiHelper($object->wiki) : null;
    }
}
