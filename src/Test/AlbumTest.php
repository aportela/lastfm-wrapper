<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\Test;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

final class AlbumTest extends BaseTest
{
    private const string TEST_ALBUM_MBID = "1031f9e1-d9b0-39d6-a983-d3b552da054d";
    private const string TEST_ALBUM_NAME = "Tourism";
    private const int TEST_ALBUM_TRACK_COUNT = 16;
    private const string TEST_ALBUM_URL = "https://www.last.fm/music/Roxette/Tourism";
    private const string TEST_ALBUM_ARTIST_NAME = "Roxette";

    /**
     * Called once just like normal constructor
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$jsonAPI = new \aportela\LastFMWrapper\Album(self::$logger, \aportela\LastFMWrapper\APIFormat::JSON, self::$lastFMAPIKey ?? "", self::THROTTLE_MS, self::$cachePath);
        self::$xmlAPI = new \aportela\LastFMWrapper\Album(self::$logger, \aportela\LastFMWrapper\APIFormat::XML, self::$lastFMAPIKey ?? "", self::THROTTLE_MS, self::$cachePath);
    }

    public function testSearchJson(): void
    {
        $results = self::$jsonAPI->search(self::TEST_ALBUM_ARTIST_NAME, self::TEST_ALBUM_NAME, 1);
        $this->assertCount(1, $results);
        $this->assertSame(self::TEST_ALBUM_MBID, $results[0]->mbId);
        $this->assertSame(self::TEST_ALBUM_NAME, $results[0]->name);
        $this->assertSame(self::TEST_ALBUM_ARTIST_NAME, $results[0]->artist->name);
        $this->assertSame(self::TEST_ALBUM_URL, $results[0]->url);
    }

    public function testSearchXml(): void
    {
        $results = self::$xmlAPI->search(self::TEST_ALBUM_ARTIST_NAME, self::TEST_ALBUM_NAME, 1);
        $this->assertCount(1, $results);
        $this->assertSame(self::TEST_ALBUM_MBID, $results[0]->mbId);
        $this->assertSame(self::TEST_ALBUM_NAME, $results[0]->name);
        $this->assertSame(self::TEST_ALBUM_ARTIST_NAME, $results[0]->artist->name);
        $this->assertSame(self::TEST_ALBUM_URL, $results[0]->url);
    }

    public function testGetJson(): void
    {
        $album = null;
        try {
            $album = self::$jsonAPI->get(self::TEST_ALBUM_ARTIST_NAME, self::TEST_ALBUM_NAME);
        } catch (\aportela\LastFMWrapper\Exception\RemoteAPIServerConnectionException $e) {
            $this->markTestSkipped('API server connection error: ' . $e->getMessage());
        }
        $this->assertSame(self::TEST_ALBUM_MBID, $album->mbId);
        $this->assertSame(self::TEST_ALBUM_NAME, $album->name);
        $this->assertSame(self::TEST_ALBUM_ARTIST_NAME, $album->artist->name);
        $this->assertSame(self::TEST_ALBUM_URL, $album->url);
        $this->assertTrue(count($album->tags) > 0);
        $this->assertCount(self::TEST_ALBUM_TRACK_COUNT, $album->tracks);
    }

    public function testGetXml(): void
    {
        $album = null;
        try {
            $album = self::$xmlAPI->get(self::TEST_ALBUM_ARTIST_NAME, self::TEST_ALBUM_NAME);
        } catch (\aportela\LastFMWrapper\Exception\RemoteAPIServerConnectionException $e) {
            $this->markTestSkipped('API server connection error: ' . $e->getMessage());
        }
        $this->assertSame(self::TEST_ALBUM_MBID, $album->mbId);
        $this->assertSame(self::TEST_ALBUM_NAME, $album->name);
        $this->assertSame(self::TEST_ALBUM_ARTIST_NAME, $album->artist->name);
        $this->assertSame(self::TEST_ALBUM_URL, $album->url);
        $this->assertTrue(count($album->tags) > 0);
        $this->assertCount(self::TEST_ALBUM_TRACK_COUNT, $album->tracks);
    }
}
