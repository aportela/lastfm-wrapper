<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\Test;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

final class ArtistTest extends BaseTest
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
            self::$jsonAPI = new \aportela\LastFMWrapper\Artist(self::$logger, \aportela\LastFMWrapper\LastFM::API_FORMAT_JSON, $lasFMAPIKey);
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

    public function testInvalidAPIFormat(): void
    {
        $this->expectException(\aportela\LastFMWrapper\Exception\InvalidAPIFormatException::class);
        new \aportela\LastFMWrapper\Artist(self::$logger, "my_custom_format", "secret");
    }

    public function testSearchJSON(): void
    {
        $results = self::$jsonAPI->search("Roxette", 1);
        $this->assertCount(1, $results);
        $this->assertSame($results[0]->mbId, "d3b2711f-2baa-441a-be95-14945ca7e6ea");
        $this->assertSame($results[0]->name, "Roxette");
        $this->assertSame($results[0]->url, "https://www.last.fm/music/Roxette");
    }


    public function testGetJSON(): void
    {
        self::$jsonAPI->get("Roxette");
        $this->assertSame(self::$jsonAPI->mbId, "d3b2711f-2baa-441a-be95-14945ca7e6ea");
        $this->assertSame(self::$jsonAPI->name, "Roxette");
        $this->assertSame(self::$jsonAPI->url, "https://www.last.fm/music/Roxette");
        $this->assertIsArray(self::$jsonAPI->tags);
        $this->assertNotEmpty(self::$jsonAPI->bio->summary);
        $this->assertNotEmpty(self::$jsonAPI->bio->content);
    }
}
