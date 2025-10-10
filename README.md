# lastfm-wrapper

Custom last.fm api wrapper

## Requirements

- mininum php version 8.4
- curl extension must be enabled (aportela/httprequest-wrapper)
- a valid last.fm [api key](https://www.last.fm/api)

## Install (composer) dependencies:

```
composer require aportela/lastfm-wrapper
```

## Code example:

```
<?php

require "vendor/autoload.php";

$logger = new \Psr\Log\NullLogger();

$lastFMAPIKey = "YOUR_SECRET_API_KEY";

// JSON format (for api endpoints & raw responses)
$lastFMArtist = new \aportela\LastFMWrapper\Artist($logger, \aportela\LastFMWrapper\APIFormat::JSON, $lastFMAPIKey);
// get artist object from LastFM API
$lastFMArtist->get("Roxette");
/*
echo sprintf("- Artist: %s", $lastFMArtist->name) . PHP_EOL;
echo sprintf("- MBId: %s", $lastFMArtist->mbId) . PHP_EOL;
echo sprintf("- URL: %s", $lastFMArtist->url) . PHP_EOL;
echo sprintf("- %d tags", count($lastFMArtist->tags)) . PHP_EOL;
foreach ($lastFMArtist->tags as $tag) {
    echo sprintf("\t- %s", $tag) . PHP_EOL;
}
echo sprintf("- %d similar artists", count($lastFMArtist->similar)) . PHP_EOL;
foreach ($lastFMArtist->similar as $similarArtist) {
    echo sprintf("\t- %s", $similarArtist->name) . PHP_EOL;
}
echo sprintf("- Biography (summary): %s", $lastFMArtist->bio->summary) . PHP_EOL;
echo sprintf("- Biography (content) length: %d bytes", mb_strlen($lastFMArtist->bio->content)) . PHP_EOL;
// get Image from LastFM Artist page
if (! empty($lastFMArtist->url)) {
    $artistImageURL = $lastFMArtist->getImageFromArtistPageURL($lastFMArtist->url);
    echo sprintf("- Image URL: %s", $artistImageURL) . PHP_EOL;
}
*/
// search artists (limited to 2 matches) from LastFM API
$matchedArtists = $lastFMArtist->search("Roxette", 5);
/*
echo sprintf("- %d matches searching for artists with name %s: ", count($matchedArtists), "Roxette") . PHP_EOL;
foreach ($matchedArtists as $matchedArtist) {
    echo sprintf("\t- %s (mbId: %s)", $matchedArtist->name, $matchedArtist->mbId) . PHP_EOL;
}
*/

// XML format (for api endpoints & raw responses)
$lastFMArtist = new \aportela\LastFMWrapper\Artist($logger, \aportela\LastFMWrapper\APIFormat::XML, $lastFMAPIKey);
// get artist object from LastFM API
$lastFMArtist->get("Roxette");
/*
echo sprintf("- Artist: %s", $lastFMArtist->name) . PHP_EOL;
echo sprintf("- MBId: %s", $lastFMArtist->mbId) . PHP_EOL;
echo sprintf("- URL: %s", $lastFMArtist->url) . PHP_EOL;
echo sprintf("- %d tags", count($lastFMArtist->tags)) . PHP_EOL;
foreach ($lastFMArtist->tags as $tag) {
    echo sprintf("\t- %s", $tag) . PHP_EOL;
}
echo sprintf("- %d similar artists", count($lastFMArtist->similar)) . PHP_EOL;
foreach ($lastFMArtist->similar as $similarArtist) {
    echo sprintf("\t- %s", $similarArtist->name) . PHP_EOL;
}
echo sprintf("- Biography (summary): %s", $lastFMArtist->bio->summary) . PHP_EOL;
echo sprintf("- Biography (content) length: %d bytes", mb_strlen($lastFMArtist->bio->content)) . PHP_EOL;
// get Image from LastFM Artist page
if (! empty($lastFMArtist->url)) {
    $artistImageURL = $lastFMArtist->getImageFromArtistPageURL($lastFMArtist->url);
    echo sprintf("- Image URL: %s", $artistImageURL) . PHP_EOL;
}

// get Image from LastFM Artist page
if (! empty($lastFMArtist->url)) {
    $artistImageURL = $lastFMArtist->getImageFromArtistPageURL($lastFMArtist->url);
}
*/
// search artists (limited to 2 matches) from LastFM API
$artists = $lastFMArtist->search("Roxette", 5);
/*
echo sprintf("- %d matches searching for artists with name %s: ", count($matchedArtists), "Roxette") . PHP_EOL;
foreach ($matchedArtists as $matchedArtist) {
    echo sprintf("\t- %s (mbId: %s)", $matchedArtist->name, $matchedArtist->mbId) . PHP_EOL;
}
*/

/*
// JSON format (for api endpoints & raw responses)
$lastFMAlbum = new \aportela\LastFMWrapper\Album($logger, \aportela\LastFMWrapper\APIFormat::JSON, $lastFMAPIKey);
// get album object from LastFM API
$lastFMAlbum->get("Roxette", "Tourism");
// search albums (limited to 2 matches) from LastFM API
$albums = $lastFMAlbum->search("Roxette", "Tourism", 2);

// XML format (for api endpoints & raw responses)
$lastFMAlbum = new \aportela\LastFMWrapper\Album($logger, \aportela\LastFMWrapper\APIFormat::XML, $lastFMAPIKey);
// get album object from LastFM API
$lastFMAlbum->get("Roxette", "Tourism");
// search albums (limited to 2 matches) from LastFM API
$albums = $lastFMAlbum->search("Roxette", "Tourism", 2);


// JSON format (for api endpoints & raw responses)
$lastFMTrack = new \aportela\LastFMWrapper\Track($logger, \aportela\LastFMWrapper\APIFormat::JSON, $lastFMAPIKey);
// get track object from LastFM API
$lastFMTrack->get("Roxette", "Silver Blue");
// search tracks (limited to 2 matches) from LastFM API
$tracks = $lastFMTrack->search("Roxette", "Silver Blue", 2);

// XML format (for api endpoints & raw responses)
$lastFMTrack = new \aportela\LastFMWrapper\Track($logger, \aportela\LastFMWrapper\APIFormat::XML, $lastFMAPIKey);
// get track object from LastFM API
$lastFMTrack->get("Roxette", "Silver Blue");
// search tracks (limited to 2 matches) from LastFM API
$tracks = $lastFMTrack->search("Roxette", "Silver Blue", 2);

*/
```

![PHP Composer](https://github.com/aportela/lastfm-wrapper/actions/workflows/php.yml/badge.svg)
