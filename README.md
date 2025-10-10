# lastfm-wrapper

Custom last.fm api wrapper

## Requirements

- mininum php version 8.4
- curl extension must be enabled (aportela/httprequest-wrapper)
- a valid last.fm [api key](https://www.last.fm/api)

## Install (composer) dependencies:

```Shell
composer require aportela/lastfm-wrapper
```

## Code example:

### Artist

```php
<?php

require "vendor/autoload.php";

$logger = new \Psr\Log\NullLogger();

$lastFMAPIKey = "YOUR_SECRET_API_KEY";

// JSON format (for api endpoints & raw responses)
$lastFMArtist = new \aportela\LastFMWrapper\Artist($logger, \aportela\LastFMWrapper\APIFormat::JSON, $lastFMAPIKey);
// get artist object from LastFM API
$lastFMArtist->get("Roxette");
// search artists (limited to 2 matches) on LastFM API
$matchedArtists = $lastFMArtist->search("Roxette", 5);


// XML format (for api endpoints & raw responses)
$lastFMArtist = new \aportela\LastFMWrapper\Artist($logger, \aportela\LastFMWrapper\APIFormat::XML, $lastFMAPIKey);
// get artist object from LastFM API
$lastFMArtist->get("Roxette");
// search artists (limited to 2 matches) on LastFM API
$artists = $lastFMArtist->search("Roxette", 5);
```

### Album

```php
<?php

require "vendor/autoload.php";

$logger = new \Psr\Log\NullLogger();

$lastFMAPIKey = "YOUR_SECRET_API_KEY";

// JSON format (for api endpoints & raw responses)
$lastFMAlbum = new \aportela\LastFMWrapper\Album($logger, \aportela\LastFMWrapper\APIFormat::JSON, $lastFMAPIKey);
// get album object from LastFM API
$lastFMAlbum->get("Roxette", "Tourism");
// search albums (limited to 5 matches) on LastFM API
$albums = $lastFMAlbum->search("Roxette", "Tourism", 5);

// XML format (for api endpoints & raw responses)
$lastFMAlbum = new \aportela\LastFMWrapper\Album($logger, \aportela\LastFMWrapper\APIFormat::XML, $lastFMAPIKey);
// get album object from LastFM API
$lastFMAlbum->get("Roxette", "Tourism");
// search albums (limited to 5 matches) on LastFM API
$albums = $lastFMAlbum->search("Roxette", "Tourism", 5);
```

### Track

```php
<?php

require "vendor/autoload.php";

$logger = new \Psr\Log\NullLogger();

$lastFMAPIKey = "YOUR_SECRET_API_KEY";

// JSON format (for api endpoints & raw responses)
$lastFMTrack = new \aportela\LastFMWrapper\Track($logger, \aportela\LastFMWrapper\APIFormat::JSON, $lastFMAPIKey);
// get track object from LastFM API
$lastFMTrack->get("Roxette", "Silver Blue");
// search tracks (limited to 5 matches) on LastFM API
$tracks = $lastFMTrack->search("Roxette", "Silver Blue", 5);

// XML format (for api endpoints & raw responses)
$lastFMTrack = new \aportela\LastFMWrapper\Track($logger, \aportela\LastFMWrapper\APIFormat::XML, $lastFMAPIKey);
// get track object from LastFM API
$lastFMTrack->get("Roxette", "Silver Blue");
// search tracks (limited to 5 matches) on LastFM API
$tracks = $lastFMTrack->search("Roxette", "Silver Blue", 5);
```

![PHP Composer](https://github.com/aportela/lastfm-wrapper/actions/workflows/php.yml/badge.svg)
