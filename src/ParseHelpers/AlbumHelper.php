<?php

namespace aportela\LastFMWrapper\ParseHelpers;

class AlbumHelper
{
    public ?string $mbId;
    public ?string $name;
    public mixed $artist;
    public ?string $url;
    /**
     * @var array<string>
     */
    public array $tags = [];
    /**
     * @var array<mixed>
     */
    public array $tracks = [];
}
