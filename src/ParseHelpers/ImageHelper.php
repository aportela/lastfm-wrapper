<?php

namespace aportela\LastFMWrapper\ParseHelpers;

class ImageHelper extends \aportela\LastFMWrapper\ParseHelpers\BaseHelper
{
    public ?string $url = null;
    public \aportela\LastFMWrapper\ImageSize $size = \aportela\LastFMWrapper\ImageSize::NONE;
}
