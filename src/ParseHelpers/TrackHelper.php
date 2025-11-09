<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers;

class TrackHelper extends \aportela\LastFMWrapper\ParseHelpers\BaseHelper
{
    public ?int $rank = null;
    
    public ?string $name = null;
    
    public ?string $url = null;
    
    public ?\aportela\LastFMWrapper\ParseHelpers\ArtistHelper $artist = null;
    
    public ?\aportela\LastFMWrapper\ParseHelpers\AlbumHelper $album = null;

    /**
     * @var array<string>
     */
    public array $tags = [];
}
