<?php

namespace aportela\LastFMWrapper\ParseHelpers;

class AlbumHelper extends \aportela\LastFMWrapper\ParseHelpers\BaseHelper
{
    public ?string $mbId = null;
    public ?string $name = null;
    public ?string $artist = null;
    public ?string $url = null;

    /**
     * @var array<string>
     */
    public array $tags = [];

    /**
     * @var array<\aportela\LastFMWrapper\ParseHelpers\TrackHelper>
     */
    public array $tracks = [];
}
