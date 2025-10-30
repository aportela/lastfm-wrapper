<?php

namespace aportela\LastFMWrapper\ParseHelpers\XML\Search;

class Album extends \aportela\LastFMWrapper\ParseHelpers\ParseXMLHelper
{
    /**
     * @return array<\aportela\LastFMWrapper\ParseHelpers\XML\AlbumHelper>
     */
    public function parse(): array
    {
        $albumsXPath = $this->getXPath("//lfm/results/albummatches/album");
        if ($albumsXPath === false) {
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("albummatches album xpath not found");
        }
        $results = [];
        if (is_array($albumsXPath) && count($albumsXPath) > 0) {
            foreach ($albumsXPath as $albumElement) {
                $results[] = new \aportela\LastFMWrapper\ParseHelpers\XML\AlbumHelper($albumElement);
            }
        }
        return ($results);
    }
}
