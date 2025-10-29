<?php

namespace aportela\LastFMWrapper\ParseHelpers;

class AlbumHelper
{
    public ?string $mbId;
    public string $name;
    public string $artist;
    public string $url;
    /**
     * @var array<string>
     */
    public array $tags = [];
    /**
     * @var array<\aportela\LastFMWrapper\ParseHelpers\TrackHelper>
     */
    public array $tracks = [];
}
