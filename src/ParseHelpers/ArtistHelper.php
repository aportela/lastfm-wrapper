<?php

namespace aportela\LastFMWrapper\ParseHelpers;

class ArtistHelper extends \aportela\LastFMWrapper\ParseHelpers\BaseHelper
{
    public ?string $mbId = null;
    public ?string $name = null;
    public ?string $url = null;

    /**
     * @var array<\aportela\LastFMWrapper\ParseHelpers\ImageHelper>
     */
    public array $image = [];

    /**
     * @var array<string>
     */
    public array $tags = [];

    /**
     * @var array<\aportela\LastFMWrapper\ParseHelpers\ArtistHelper>
     */
    public array $similar = [];

    public ?\aportela\LastFMWrapper\ParseHelpers\ArtistBioHelper $bio = null;
}
