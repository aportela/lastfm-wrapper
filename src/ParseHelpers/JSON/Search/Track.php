<?php

namespace aportela\LastFMWrapper\ParseHelpers\JSON\Search;

class Track extends \aportela\LastFMWrapper\ParseHelpers\ParseJSONHelper
{
    /**
     * @return array<\aportela\LastFMWrapper\ParseHelpers\JSON\TrackHelper>
     */
    public function parse(): array
    {
        if (! (isset($this->json->results) && isset($this->json->results->trackmatches))) {
            throw new \aportela\LastFMWrapper\Exception\InvalidJSONException("trackmatches track array not found");
        }
        $results = [];
        if (isset($this->json->results->trackmatches->track) && is_array($this->json->results->trackmatches->track)) {
            foreach ($this->json->results->trackmatches->track as $trackObject) {
                if (is_object($trackObject)) {
                    $results[] = new \aportela\LastFMWrapper\ParseHelpers\JSON\TrackHelper($trackObject);
                }
            }
        }
        return ($results);
    }
}
