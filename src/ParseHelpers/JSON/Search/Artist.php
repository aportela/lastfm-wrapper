<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\JSON\Search;

class Artist extends \aportela\LastFMWrapper\ParseHelpers\ParseJSONHelper
{
    /**
     * @return array<\aportela\LastFMWrapper\ParseHelpers\JSON\ArtistHelper>
     */
    public function parse(): array
    {
        if (! (property_exists($this->json, "results") && is_object($this->json->results) && property_exists($this->json->results, "artistmatches"))) {
            throw new \aportela\LastFMWrapper\Exception\InvalidJSONException("artistmatches artist array not found");
        }

        $results = [];
        if (is_object($this->json->results->artistmatches) && property_exists($this->json->results->artistmatches, "artist") && is_array($this->json->results->artistmatches->artist)) {
            foreach ($this->json->results->artistmatches->artist as $artistObject) {
                if (is_object($artistObject)) {
                    $results[] = new \aportela\LastFMWrapper\ParseHelpers\JSON\ArtistHelper($artistObject);
                }
            }
        }

        return ($results);
    }
}
