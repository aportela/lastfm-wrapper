<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\Test;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

final class AlbumTest extends BaseTest
{
    private const string TEST_ARTIST_NAME = "Roxette";
    private const string TEST_ARTIST_ALBUM_MBID = "1031f9e1-d9b0-39d6-a983-d3b552da054d";
    private const string TEST_ARTIST_ALBUM_NAME = "Tourism";
    private const string TEST_ARTIST_ALBUM_URL = "https://www.last.fm/music/Roxette/Tourism";

    /*
    private static $mbXML;
    */

    /**
     * Called once just like normal constructor
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$jsonAPI = new \aportela\LastFMWrapper\Album(self::$logger, \aportela\LastFMWrapper\APIFormat::JSON, self::$lastFMAPIKey ?? "");
        self::$xmlAPI = new \aportela\LastFMWrapper\Album(self::$logger, \aportela\LastFMWrapper\APIFormat::XML, self::$lastFMAPIKey ?? "");
    }

    public function testSearchJson(): void
    {
        $results = self::$jsonAPI->search(self::TEST_ARTIST_NAME, self::TEST_ARTIST_ALBUM_NAME, 1);
        $this->assertCount(1, $results);
        $this->assertSame(self::TEST_ARTIST_NAME, $results[0]->artist);
        $this->assertSame(self::TEST_ARTIST_ALBUM_NAME, $results[0]->name);
        $this->assertSame(self::TEST_ARTIST_ALBUM_URL, $results[0]->url);
    }

    public function testSearchXml(): void
    {
        $results = self::$xmlAPI->search(self::TEST_ARTIST_NAME, self::TEST_ARTIST_ALBUM_NAME, 1);
        $this->assertCount(1, $results);
        $this->assertSame(self::TEST_ARTIST_NAME, $results[0]->artist);
        $this->assertSame(self::TEST_ARTIST_ALBUM_NAME, $results[0]->name);
        $this->assertSame(self::TEST_ARTIST_ALBUM_URL, $results[0]->url);
    }

    public function testGetJson(): void
    {
        self::$jsonAPI->get(self::TEST_ARTIST_NAME, self::TEST_ARTIST_ALBUM_NAME);
        $this->assertSame(self::TEST_ARTIST_ALBUM_MBID, self::$jsonAPI->mbId);
        $this->assertSame(self::TEST_ARTIST_NAME, self::$jsonAPI->name);
        $this->assertSame(self::TEST_ARTIST_ALBUM_URL, self::$jsonAPI->url);
        $this->assertIsArray(self::$jsonAPI->tags);
        $this->assertIsArray(self::$jsonAPI->tracks);
    }

    public function testGetXml(): void
    {
        self::$xmlAPI->get(self::TEST_ARTIST_NAME, self::TEST_ARTIST_ALBUM_NAME);
        $this->assertSame(self::TEST_ARTIST_ALBUM_MBID, self::$xmlAPI->mbId);
        $this->assertSame(self::TEST_ARTIST_NAME, self::$xmlAPI->name);
        $this->assertSame(self::TEST_ARTIST_ALBUM_URL, self::$xmlAPI->url);
        $this->assertIsArray(self::$xmlAPI->tags);
        $this->assertIsArray(self::$xmlAPI->tracks);
    }
}
