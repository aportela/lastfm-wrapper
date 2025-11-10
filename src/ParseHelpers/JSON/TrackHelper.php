<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\JSON;

class TrackHelper extends \aportela\LastFMWrapper\ParseHelpers\TrackHelper
{
    public function __construct(object $object)
    {
        $this->rank = isset($object->{"@attr"}) && isset($object->{"@attr"}->rank) ? intval($object->{"@attr"}->rank) : null;
        $this->mbId = empty($object->mbid) ? null : (string)$object->mbid;
        $this->name = empty($object->name) ? null : (string)$object->name;
        $this->url = empty($object->url) ? null : (string)$object->url;
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
        
        $this->album = isset($object->album) ? new \aportela\LastFMWrapper\ParseHelpers\JSON\AlbumHelper($object->album) : null;
        if (isset($object->toptags) && isset($object->toptags->tag) && is_array($object->toptags->tag)) {
            foreach ($object->toptags->tag as $tag) {
                if (is_object($tag) && isset($tag->name) && ! empty($tag->name)) {
                    $this->tags[] = mb_strtolower(mb_trim($tag->name));
                }
            }
            
            $this->tags = array_unique($this->tags);
        }
    }
}
