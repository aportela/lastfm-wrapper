<?php

namespace aportela\LastFMWrapper\ParseHelpers;

class TrackHelper
{
    public ?string $name;
    public ?string $url;
    public mixed $artist;
    public mixed $album;
    /**
     * @var array<string>
     */
    public array $tags = [];
}
