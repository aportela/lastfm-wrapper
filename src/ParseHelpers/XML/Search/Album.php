<?php

namespace aportela\LastFMWrapper\ParseHelpers\XML\Search;

class Album extends \aportela\LastFMWrapper\ParseHelpers\ParseXMLHelper
{
    public function parse(): mixed
    {
        $albumsXPath = $this->getXPath("//lfm/results/albummatches/album");
        if ($albumsXPath === false) {
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("albummatches album xpath not found");
        }
        $results = [];
        if (count($albumsXPath) > 0) {
            foreach ($albumsXPath as $albumElement) {
                $results[] = new \aportela\LastFMWrapper\ParseHelpers\XML\AlbumHelper($albumElement);
            }
        }
        return ($results);
    }
}
