<?php

namespace aportela\LastFMWrapper\ParseHelpers\XML\Search;

class Track extends \aportela\LastFMWrapper\ParseHelpers\ParseXMLHelper
{
    public function parse(): mixed
    {
        $tracksXPath = $this->getXPath("//lfm/results/trackmatches/track");
        if ($tracksXPath === false) {
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("trackmatches track xpath not found");
        }
        $results = [];
        if (count($tracksXPath) > 0) {
            foreach ($tracksXPath as $trackElement) {
                $results[] = new \aportela\LastFMWrapper\ParseHelpers\XML\TrackHelper($trackElement);
            }
        }
        return ($results);
    }
}
