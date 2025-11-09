<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\Test;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

final class ArtistTest extends BaseTest
{
    private const string TEST_ARTIST_NAME = "Roxette";
    private const string TEST_ARTIST_MBID = "d3b2711f-2baa-441a-be95-14945ca7e6ea";
    private const string TEST_ARTIST_URL = "https://www.last.fm/music/Roxette";

    private static \aportela\LastFMWrapper\Artist $jsonAPI;
    private static \aportela\LastFMWrapper\Artist $xmlAPI;

    /**
     * Called once just like normal constructor
     */
    #[\Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (! empty(self::$lastFMAPIKey)) {
            self::$JSONCache = new \aportela\SimpleFSCache\Cache(self::$logger, self::$cachePath, null, \aportela\SimpleFSCache\CacheFormat::JSON, );
            self::$XMLCache = new \aportela\SimpleFSCache\Cache(self::$logger, self::$cachePath, null, \aportela\SimpleFSCache\CacheFormat::XML);

            self::$jsonAPI = new \aportela\LastFMWrapper\Artist(self::$logger, \aportela\LastFMWrapper\APIFormat::JSON, self::$lastFMAPIKey ?? "", \aportela\LastFMWrapper\Entity::DEFAULT_THROTTLE_DELAY_MS, self::$JSONCache);
            self::$xmlAPI = new \aportela\LastFMWrapper\Artist(self::$logger, \aportela\LastFMWrapper\APIFormat::XML, self::$lastFMAPIKey ?? "", \aportela\LastFMWrapper\Entity::DEFAULT_THROTTLE_DELAY_MS, self::$XMLCache);
        }
    }

    public function testSearchJson(): void
    {
        $results = self::$jsonAPI->search(self::TEST_ARTIST_NAME, 1);
        $this->assertCount(1, $results);
        $this->assertSame(self::TEST_ARTIST_MBID, $results[0]->mbId);
        $this->assertSame(self::TEST_ARTIST_NAME, $results[0]->name);
        $this->assertSame(self::TEST_ARTIST_URL, $results[0]->url);
    }

    public function testSearchXml(): void
    {
        $results = self::$xmlAPI->search(self::TEST_ARTIST_NAME, 1);
        $this->assertCount(1, $results);
        $this->assertSame(self::TEST_ARTIST_MBID, $results[0]->mbId);
        $this->assertSame(self::TEST_ARTIST_NAME, $results[0]->name);
        $this->assertSame(self::TEST_ARTIST_URL, $results[0]->url);
    }

    public function testGetJson(): void
    {
        $artist = null;
        try {
            $artist = self::$jsonAPI->get(self::TEST_ARTIST_NAME);
        } catch (\aportela\LastFMWrapper\Exception\RemoteAPIServerConnectionException $e) {
            $this->markTestSkipped('API server connection error: ' . $e->getMessage());
        }
        $this->assertSame(self::TEST_ARTIST_MBID, $artist->mbId);
        $this->assertSame(self::TEST_ARTIST_NAME, $artist->name);
        $this->assertSame(self::TEST_ARTIST_URL, $artist->url);
        $this->assertNotEmpty($artist->image);
        $this->assertTrue(count($artist->tags) > 0);
        $this->assertTrue(count($artist->similar) > 0);
        $this->assertNotNull($artist->bio);
        $this->assertNotEmpty($artist->bio->summary);
        $this->assertNotEmpty($artist->bio->content);
    }

    public function testGetXml(): void
    {
        $artist = null;
        try {
            $artist = self::$xmlAPI->get(self::TEST_ARTIST_NAME);
        } catch (\aportela\LastFMWrapper\Exception\RemoteAPIServerConnectionException $e) {
            $this->markTestSkipped('API server connection error: ' . $e->getMessage());
        }
        $this->assertSame(self::TEST_ARTIST_MBID, $artist->mbId);
        $this->assertSame(self::TEST_ARTIST_NAME, $artist->name);
        $this->assertSame(self::TEST_ARTIST_URL, $artist->url);
        $this->assertNotEmpty($artist->image);
        $this->assertTrue(count($artist->tags) > 0);
        $this->assertTrue(count($artist->similar) > 0);
        $this->assertNotNull($artist->bio);
        $this->assertNotEmpty($artist->bio->summary);
        $this->assertNotEmpty($artist->bio->content);
    }

    public function testGetImageFromArtistPageUrl(): void
    {
        $this->assertNotEmpty(self::$xmlAPI->getImageFromArtistPageURL(self::TEST_ARTIST_URL));
    }
}
