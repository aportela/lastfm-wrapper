<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper;

enum APIFormat: string
{
    case JSON = "json";
    case XML = "xml";
}
