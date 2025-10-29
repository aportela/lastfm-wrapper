<?php

namespace aportela\LastFMWrapper\ParseHelpers\XML\Search;

class Artist extends \aportela\LastFMWrapper\ParseHelpers\ParseXMLHelper
{
    public function parse(): mixed
    {
        $artistsXPath = $this->getXPath("//lfm/results/artistmatches/artist");
        if ($artistsXPath === false) {
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("artist-list xpath not found");
        }
        $results = [];
        if (count($artistsXPath) > 0) {
            foreach ($artistsXPath as $artistElement) {
                $results[] = new \aportela\LastFMWrapper\ParseHelpers\XML\ArtistHelper($artistElement);
            }
        }
        return ($results);
    }
}
