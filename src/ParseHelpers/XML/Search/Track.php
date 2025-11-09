<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\XML\Search;

class Track extends \aportela\LastFMWrapper\ParseHelpers\ParseXMLHelper
{
    /**
     * @return array<\aportela\LastFMWrapper\ParseHelpers\XML\TrackHelper>
     */
    public function parse(): array
    {
        $tracksXPath = $this->getXPath("//lfm/results/trackmatches/track");
        if ($tracksXPath === false) {
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("trackmatches track xpath not found");
        }
        
        $results = [];
        if (is_array($tracksXPath) && $tracksXPath !== []) {
            foreach ($tracksXPath as $trackXPath) {
                $results[] = new \aportela\LastFMWrapper\ParseHelpers\XML\TrackHelper($trackXPath);
            }
        }
        
        return ($results);
    }
}
