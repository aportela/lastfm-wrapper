<?php

namespace aportela\LastFMWrapper\ParseHelpers\JSON\Search;

class Album extends \aportela\LastFMWrapper\ParseHelpers\ParseJSONHelper
{
    public function parse(): mixed
    {
        if (! (isset($this->json->results) && isset($this->json->results->albummatches))) {
            throw new \aportela\LastFMWrapper\Exception\InvalidJSONException("albummatches album array not found");
        }
        $results = [];
        if (is_array($this->json->results->albummatches->artist)) {
            foreach ($this->json->results->albummatches->artist as $artistObject) {
                $results[] = new \aportela\LastFMWrapper\ParseHelpers\JSON\ArtistHelper($artistObject);
            }
        }
        return ($results);
    }
}
