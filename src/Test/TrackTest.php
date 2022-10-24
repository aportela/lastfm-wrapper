<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\Test;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

final class TrackTest extends BaseTest
{
    private static $jsonAPI;

    /*
    private static $mbXML;
    */

    /**
     * Called once just like normal constructor
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $lasFMAPIKey = getenv('LASTFM_API_KEY');
        if (!empty($lasFMAPIKey)) {
            self::$jsonAPI = new \aportela\LastFMWrapper\Track(self::$logger, \aportela\LastFMWrapper\LastFM::API_FORMAT_JSON, $lasFMAPIKey);
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidAPIKeyException("");
        }
    }

    /**
     * Initialize the test case
     * Called for every defined test
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Clean up the test case, called for every defined test
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Clean up the whole test class
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
    }


    public function testSearchJSON(): void
    {
        $results = self::$jsonAPI->search("Roxette", "The Look", 1);
        $this->assertCount(1, $results);
        $this->assertSame($results[0]->mbId, "e42e2e0f-d320-47cf-948d-3ba9add0e2e3");
        $this->assertSame($results[0]->artist, "Roxette");
        $this->assertSame($results[0]->name, "The Look");
        $this->assertSame($results[0]->url, "https://www.last.fm/music/Roxette/_/The+Look");
    }

    public function testGetJSON(): void
    {
        self::$jsonAPI->get("Roxette", "The Look");
        $this->assertSame(self::$jsonAPI->mbId, "e42e2e0f-d320-47cf-948d-3ba9add0e2e3");
        $this->assertSame(self::$jsonAPI->name, "The Look");
        $this->assertSame(self::$jsonAPI->url, "https://www.last.fm/music/Roxette/_/The+Look");
        $this->assertSame(self::$jsonAPI->artist->name, "Roxette");
        $this->assertSame(self::$jsonAPI->artist->mbId, "d3b2711f-2baa-441a-be95-14945ca7e6ea");
        $this->assertSame(self::$jsonAPI->artist->url, "https://www.last.fm/music/Roxette");
        $this->assertSame(self::$jsonAPI->album->artist, "Roxette");
        $this->assertSame(self::$jsonAPI->album->title, "Look Sharp!");
        $this->assertSame(self::$jsonAPI->album->mbId, "7f73dca2-79e7-302d-bb09-1a5db381e7f2");
        $this->assertSame(self::$jsonAPI->album->url, "https://www.last.fm/music/Roxette/Look+Sharp%21");
        $this->assertIsArray(self::$jsonAPI->tags);
    }
}
