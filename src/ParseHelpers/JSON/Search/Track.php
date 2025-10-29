<?php

namespace aportela\LastFMWrapper\ParseHelpers\JSON\Search;

class Track extends \aportela\LastFMWrapper\ParseHelpers\ParseJSONHelper
{
    public function parse(): mixed
    {
        if (! (isset($this->json->results) && isset($this->json->results->trackmatches))) {
            throw new \aportela\LastFMWrapper\Exception\InvalidJSONException("trackmatches track array not found");
        }
        $results = [];
        if (is_array($this->json->results->trackmatches->track)) {
            foreach ($this->json->results->trackmatches->track as $trackObject) {
                $results[] = new \aportela\LastFMWrapper\ParseHelpers\JSON\TrackHelper($trackObject);
            }
        }
        return ($results);
    }
}
