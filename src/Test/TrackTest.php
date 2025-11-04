<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\Test;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

final class TrackTest extends BaseTest
{
    private const string TEST_TRACK_MBID = "459fe686-82a2-4f36-b932-b62822c44bdc";
    private const string TEST_TRACK_TITLE = "Silver Blue";
    private const string TEST_TRACK_URL = "https://www.last.fm/music/Roxette/_/Silver+Blue";

    private const string TEST_TRACK_ARTIST_NAME = "Roxette";
    private const string TEST_TRACK_ARTIST_MBID = "d3b2711f-2baa-441a-be95-14945ca7e6ea";
    private const string TEST_TRACK_ARTIST_URL = "https://www.last.fm/music/Roxette";

    private const string TEST_TRACK_ALBUM_NAME = "Tourism";
    private const string TEST_TRACK_ALBUM_URL = "https://www.last.fm/music/Roxette/Tourism";

    private static \aportela\LastFMWrapper\Track $jsonAPI;
    private static \aportela\LastFMWrapper\Track $xmlAPI;

    /**
     * Called once just like normal constructor
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if (! empty(self::$lastFMAPIKey)) {
            self::$JSONCache = new \aportela\SimpleFSCache\Cache(self::$logger, \aportela\SimpleFSCache\CacheFormat::JSON, self::$cachePath, false);
            self::$XMLCache = new \aportela\SimpleFSCache\Cache(self::$logger, \aportela\SimpleFSCache\CacheFormat::XML, self::$cachePath, false);

            self::$jsonAPI = new \aportela\LastFMWrapper\Track(self::$logger, \aportela\LastFMWrapper\APIFormat::JSON, self::$lastFMAPIKey ?? "", \aportela\LastFMWrapper\Entity::DEFAULT_THROTTLE_DELAY_MS, self::$JSONCache);
            self::$xmlAPI = new \aportela\LastFMWrapper\Track(self::$logger, \aportela\LastFMWrapper\APIFormat::XML, self::$lastFMAPIKey ?? "", \aportela\LastFMWrapper\Entity::DEFAULT_THROTTLE_DELAY_MS, self::$XMLCache);
        }
    }

    public function testSearchJson(): void
    {
        $results = self::$jsonAPI->search(self::TEST_TRACK_ARTIST_NAME, self::TEST_TRACK_TITLE, 1);
        $this->assertCount(1, $results);
        $this->assertSame(self::TEST_TRACK_MBID, $results[0]->mbId);
        $this->assertSame(self::TEST_TRACK_TITLE, $results[0]->name);
        $this->assertSame(self::TEST_TRACK_URL, $results[0]->url);
        $this->assertNotNull($results[0]->artist);
        $this->assertSame(self::TEST_TRACK_ARTIST_NAME, $results[0]->artist->name);
    }

    public function testSearchXml(): void
    {
        $results = self::$xmlAPI->search(self::TEST_TRACK_ARTIST_NAME, self::TEST_TRACK_TITLE, 1);
        $this->assertCount(1, $results);
        $this->assertSame(self::TEST_TRACK_MBID, $results[0]->mbId);
        $this->assertSame(self::TEST_TRACK_TITLE, $results[0]->name);
        $this->assertSame(self::TEST_TRACK_URL, $results[0]->url);
        $this->assertNotNull($results[0]->artist);
        $this->assertSame(self::TEST_TRACK_ARTIST_NAME, $results[0]->artist->name);
    }

    public function testGetJson(): void
    {
        $track = null;
        try {
            $track = self::$jsonAPI->get(self::TEST_TRACK_ARTIST_NAME, self::TEST_TRACK_TITLE);
        } catch (\aportela\LastFMWrapper\Exception\RemoteAPIServerConnectionException $e) {
            $this->markTestSkipped('API server connection error: ' . $e->getMessage());
        }
        $this->assertSame(self::TEST_TRACK_MBID, $track->mbId);
        $this->assertSame(self::TEST_TRACK_TITLE, $track->name);
        $this->assertSame(self::TEST_TRACK_URL, $track->url);
        $this->assertNotNull($track->artist);
        $this->assertSame(self::TEST_TRACK_ARTIST_MBID, $track->artist->mbId);
        $this->assertSame(self::TEST_TRACK_ARTIST_NAME, $track->artist->name);
        $this->assertSame(self::TEST_TRACK_ARTIST_URL, $track->artist->url);
        $this->assertNotNull($track->album);
        $this->assertSame(self::TEST_TRACK_ALBUM_NAME, $track->album->name);
        $this->assertNotNull($track->album->artist);
        $this->assertSame(self::TEST_TRACK_ARTIST_NAME, $track->album->artist->name);
        $this->assertSame(self::TEST_TRACK_ALBUM_URL, $track->album->url);
    }

    public function testGetXml(): void
    {
        $track = null;
        try {
            $track = self::$xmlAPI->get(self::TEST_TRACK_ARTIST_NAME, self::TEST_TRACK_TITLE);
        } catch (\aportela\LastFMWrapper\Exception\RemoteAPIServerConnectionException $e) {
            $this->markTestSkipped('API server connection error: ' . $e->getMessage());
        }
        $this->assertSame(self::TEST_TRACK_MBID, $track->mbId);
        $this->assertSame(self::TEST_TRACK_TITLE, $track->name);
        $this->assertSame(self::TEST_TRACK_URL, $track->url);
        $this->assertNotNull($track->artist);
        $this->assertSame(self::TEST_TRACK_ARTIST_MBID, $track->artist->mbId);
        $this->assertSame(self::TEST_TRACK_ARTIST_NAME, $track->artist->name);
        $this->assertSame(self::TEST_TRACK_ARTIST_URL, $track->artist->url);
        $this->assertNotNull($track->album);
        $this->assertSame(self::TEST_TRACK_ALBUM_NAME, $track->album->name);
        $this->assertNotNull($track->album->artist);
        $this->assertSame(self::TEST_TRACK_ARTIST_NAME, $track->album->artist->name);
        $this->assertSame(self::TEST_TRACK_ALBUM_URL, $track->album->url);
    }
}
