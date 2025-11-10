<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\JSON\Search;

class Album extends \aportela\LastFMWrapper\ParseHelpers\ParseJSONHelper
{
    /**
     * @return array<\aportela\LastFMWrapper\ParseHelpers\JSON\AlbumHelper>
     */
    public function parse(): array
    {
        if (! (property_exists($this->json, "results") && is_object($this->json->results) && property_exists($this->json->results, "albummatches"))) {
            throw new \aportela\LastFMWrapper\Exception\InvalidJSONException("albummatches album array not found");
        }

        $results = [];
        if (is_object($this->json->results->albummatches) && property_exists($this->json->results->albummatches, "album") && is_array($this->json->results->albummatches->album)) {
            foreach ($this->json->results->albummatches->album as $albumObject) {
                if (is_object($albumObject)) {
                    $results[] = new \aportela\LastFMWrapper\ParseHelpers\JSON\AlbumHelper($albumObject);
                }
            }
        }

        return ($results);
    }
}
