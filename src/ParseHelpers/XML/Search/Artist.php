<?php

namespace aportela\LastFMWrapper\ParseHelpers\XML\Search;

class Artist extends \aportela\LastFMWrapper\ParseHelpers\ParseXMLHelper
{
    /**
     * @return array<\aportela\LastFMWrapper\ParseHelpers\XML\ArtistHelper>
     */
    public function parse(): array
    {
        $artistsXPath = $this->getXPath("//lfm/results/artistmatches/artist");
        if ($artistsXPath === false) {
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("artistmatches artist xpath not found");
        }
        $results = [];
        if (is_array($artistsXPath) && count($artistsXPath) > 0) {
            foreach ($artistsXPath as $artistElement) {
                $results[] = new \aportela\LastFMWrapper\ParseHelpers\XML\ArtistHelper($artistElement);
            }
        }
        return ($results);
    }
}
