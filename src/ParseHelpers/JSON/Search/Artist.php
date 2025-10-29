<?php

namespace aportela\LastFMWrapper\ParseHelpers\JSON\Search;

class Artist extends \aportela\LastFMWrapper\ParseHelpers\ParseJSONHelper
{
    public function parse(): mixed
    {
        if (! (isset($this->json->results) && isset($this->json->results->artistmatches))) {
            throw new \aportela\LastFMWrapper\Exception\InvalidJSONException("artistmatches artist array not found");
        }
        $results = [];
        if (is_array($this->json->results->artistmatches->artist)) {
            foreach ($this->json->results->artistmatches->artist as $artistObject) {
                $results[] = new \aportela\LastFMWrapper\ParseHelpers\JSON\ArtistHelper($artistObject);
            }
        }
        return ($results);
    }
}
