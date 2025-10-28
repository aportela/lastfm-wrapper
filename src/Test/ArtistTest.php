<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\Test;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

final class ArtistTest extends BaseTest
{
    private const string TEST_ARTIST_NAME = "Roxette";
    private const string TEST_ARTIST_MBID = "d3b2711f-2baa-441a-be95-14945ca7e6ea";
    private const string TEST_ARTIST_URL = "https://www.last.fm/music/Roxette";

    /*
    private static $mbXML;
    */

    /**
     * Called once just like normal constructor
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$jsonAPI = new \aportela\LastFMWrapper\Artist(self::$logger, \aportela\LastFMWrapper\APIFormat::JSON, self::$lastFMAPIKey ?? "", self::THROTTLE_MS, self::$cachePath);
        self::$xmlAPI = new \aportela\LastFMWrapper\Artist(self::$logger, \aportela\LastFMWrapper\APIFormat::XML, self::$lastFMAPIKey ?? "", self::THROTTLE_MS, self::$cachePath);
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
        self::$jsonAPI->get(self::TEST_ARTIST_NAME);
        $this->assertSame(self::TEST_ARTIST_MBID, self::$jsonAPI->mbId);
        $this->assertSame(self::TEST_ARTIST_NAME, self::$jsonAPI->name);
        $this->assertSame(self::TEST_ARTIST_URL, self::$jsonAPI->url);
        $this->assertIsArray(self::$jsonAPI->tags);
        $this->assertIsArray(self::$jsonAPI->similar);
        $this->assertNotEmpty(self::$jsonAPI->bio->summary);
        $this->assertNotEmpty(self::$jsonAPI->bio->content);
    }

    public function testGetXml(): void
    {
        self::$xmlAPI->get(self::TEST_ARTIST_NAME);
        $this->assertSame(self::TEST_ARTIST_MBID, self::$xmlAPI->mbId);
        $this->assertSame(self::TEST_ARTIST_NAME, self::$xmlAPI->name);
        $this->assertSame(self::TEST_ARTIST_URL, self::$xmlAPI->url);
        $this->assertIsArray(self::$xmlAPI->tags);
        $this->assertIsArray(self::$xmlAPI->similar);
        $this->assertNotEmpty(self::$xmlAPI->bio->summary);
        $this->assertNotEmpty(self::$xmlAPI->bio->content);
    }

    public function testGetImageFromArtistPageUrl(): void
    {
        $this->assertNotEmpty(self::$xmlAPI->getImageFromArtistPageURL(self::TEST_ARTIST_URL));
    }
}
