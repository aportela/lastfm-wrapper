<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\JSON\Search;

class Track extends \aportela\LastFMWrapper\ParseHelpers\ParseJSONHelper
{
    /**
     * @return array<\aportela\LastFMWrapper\ParseHelpers\JSON\TrackHelper>
     */
    public function parse(): array
    {
        if (! (property_exists($this->json, "results") && is_object($this->json->results) && property_exists($this->json->results, "trackmatches"))) {
            throw new \aportela\LastFMWrapper\Exception\InvalidJSONException("trackmatches track array not found");
        }

        $results = [];
        if (is_object($this->json->results->trackmatches) && property_exists($this->json->results->trackmatches, "track") && is_array($this->json->results->trackmatches->track)) {
            foreach ($this->json->results->trackmatches->track as $trackObject) {
                if (is_object($trackObject)) {
                    $results[] = new \aportela\LastFMWrapper\ParseHelpers\JSON\TrackHelper($trackObject);
                }
            }
        }

        return ($results);
    }
}
