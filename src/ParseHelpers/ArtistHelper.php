<?php

namespace aportela\LastFMWrapper\ParseHelpers;

class ArtistHelper
{
    public ?string $mbId;
    public string $name;
    public string $url;

    /**
     * @var array<\aportela\LastFMWrapper\ParseHelpers\ImageHelper>
     */
    public array $image = [];

    /**
     * @var array<string>
     */
    public array $tags = [];
    /**
     * @var array<mixed>
     */
    public array $similar = [];
    public mixed $bio;
}
