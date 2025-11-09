<?php

declare(strict_types=1);

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
        if (is_array($albumsXPath) && $albumsXPath !== []) {
            foreach ($albumsXPath as $albumXPath) {
                $results[] = new \aportela\LastFMWrapper\ParseHelpers\XML\AlbumHelper($albumXPath);
            }
        }
        
        return ($results);
    }
}
