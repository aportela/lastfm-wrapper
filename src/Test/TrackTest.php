<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\Test;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

final class TrackTest extends BaseTest
{
    private const string TEST_ARTIST_NAME = "Roxette";
    private const string TEST_ARTIST_MBID = "d3b2711f-2baa-441a-be95-14945ca7e6ea";
    private const string TEST_ARTIST_URL = "https://www.last.fm/music/Roxette";

    private const string TEST_ARTIST_ALBUM_NAME = "Tourism";
    private const string TEST_ARTIST_ALBUM_URL = "https://www.last.fm/music/Roxette/Tourism";

    private const string TEST_ARTIST_TRACK_TITLE = "Silver Blue";
    private const string TEST_ARTIST_TRACK_URL = "https://www.last.fm/music/Roxette/_/Silver+Blue";

    /**
     * Called once just like normal constructor
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$jsonAPI = new \aportela\LastFMWrapper\Track(self::$logger, \aportela\LastFMWrapper\APIFormat::JSON, self::$lastFMAPIKey ?? "", self::THROTTLE_MS, self::$cachePath);
        self::$xmlAPI = new \aportela\LastFMWrapper\Track(self::$logger, \aportela\LastFMWrapper\APIFormat::XML, self::$lastFMAPIKey ?? "", self::THROTTLE_MS, self::$cachePath);
    }

    public function testSearchJson(): void
    {
        $results = self::$jsonAPI->search(self::TEST_ARTIST_NAME, self::TEST_ARTIST_TRACK_TITLE, 1);
        $this->assertCount(1, $results);
        $this->assertSame(self::TEST_ARTIST_NAME, $results[0]->artist);
        $this->assertSame(self::TEST_ARTIST_TRACK_TITLE, $results[0]->name);
        $this->assertSame(self::TEST_ARTIST_TRACK_URL, $results[0]->url);
    }

    public function testSearchXml(): void
    {
        $results = self::$xmlAPI->search(self::TEST_ARTIST_NAME, self::TEST_ARTIST_TRACK_TITLE, 1);
        $this->assertCount(1, $results);
        $this->assertSame(self::TEST_ARTIST_NAME, $results[0]->artist);
        $this->assertSame(self::TEST_ARTIST_TRACK_TITLE, $results[0]->name);
        $this->assertSame(self::TEST_ARTIST_TRACK_URL, $results[0]->url);
    }

    public function testGetJson(): void
    {
        $track = null;
        try {
            $track = self::$jsonAPI->get(self::TEST_ARTIST_NAME, self::TEST_ARTIST_TRACK_TITLE);
        } catch (\aportela\LastFMWrapper\Exception\RemoteAPIServerConnectionException $e) {
            $this->markTestSkipped('API server connection error: ' . $e->getMessage());
        }
        $this->assertSame(self::TEST_ARTIST_TRACK_TITLE, $track->name);
        $this->assertSame(self::TEST_ARTIST_TRACK_URL, $track->url);
        $this->assertSame(self::TEST_ARTIST_NAME, $track->artist->name);
        $this->assertSame(self::TEST_ARTIST_MBID, $track->artist->mbId);
        $this->assertSame(self::TEST_ARTIST_URL, $track->artist->url);
        $this->assertSame(self::TEST_ARTIST_NAME, $track->album->artist);
        $this->assertSame(self::TEST_ARTIST_ALBUM_NAME, $track->album->name);
        $this->assertSame(self::TEST_ARTIST_ALBUM_URL, $track->album->url);
        $this->assertIsArray($track->tags);
    }

    public function testGetXml(): void
    {
        $track = null;
        try {
            $track = self::$xmlAPI->get(self::TEST_ARTIST_NAME, self::TEST_ARTIST_TRACK_TITLE);
        } catch (\aportela\LastFMWrapper\Exception\RemoteAPIServerConnectionException $e) {
            $this->markTestSkipped('API server connection error: ' . $e->getMessage());
        }
        $this->assertSame(self::TEST_ARTIST_TRACK_TITLE, $track->name);
        $this->assertSame(self::TEST_ARTIST_TRACK_URL, $track->url);
        $this->assertSame(self::TEST_ARTIST_NAME, $track->artist->name);
        $this->assertSame(self::TEST_ARTIST_MBID, $track->artist->mbId);
        $this->assertSame(self::TEST_ARTIST_URL, $track->artist->url);
        $this->assertSame(self::TEST_ARTIST_NAME, $track->album->artist);
        $this->assertSame(self::TEST_ARTIST_ALBUM_NAME, $track->album->name);
        $this->assertSame(self::TEST_ARTIST_ALBUM_URL, $track->album->url);
        $this->assertIsArray($track->tags);
    }
}
