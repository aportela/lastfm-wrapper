<?php

namespace aportela\LastFMWrapper\ParseHelpers;

class TrackHelper
{
    public ?string $mbId;
    public ?string $name;
    public ?string $url;
    public \aportela\LastFMWrapper\ParseHelpers\ArtistHelper $artist;
    public mixed $album;

    /**
     * @var array<string>
     */
    public array $tags = [];
}
