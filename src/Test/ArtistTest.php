<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\Test;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

final class ArtistTest extends BaseTest
{
    /*
    private static $mbXML;
    */

    /**
     * Called once just like normal constructor
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$jsonAPI = new \aportela\LastFMWrapper\Artist(self::$logger, \aportela\LastFMWrapper\APIFormat::JSON, self::$lastFMAPIKey);
        self::$xmlAPI = new \aportela\LastFMWrapper\Artist(self::$logger, \aportela\LastFMWrapper\APIFormat::XML, self::$lastFMAPIKey);
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
        $results = self::$jsonAPI->search("Roxette", 1);
        $this->assertCount(1, $results);
        $this->assertSame($results[0]->mbId, "d3b2711f-2baa-441a-be95-14945ca7e6ea");
        $this->assertSame($results[0]->name, "Roxette");
        $this->assertSame($results[0]->url, "https://www.last.fm/music/Roxette");
    }

    public function testSearchXML(): void
    {
        $this->expectException(\aportela\LastFMWrapper\Exception\InvalidAPIFormatException::class);
        $this->expectExceptionMessage(\aportela\LastFMWrapper\APIFormat::XML->value);
        $results = self::$xmlAPI->search("Roxette", 1);
        // TODO
        /*
        $this->assertCount(1, $results);
        $this->assertSame($results[0]->mbId, "d3b2711f-2baa-441a-be95-14945ca7e6ea");
        $this->assertSame($results[0]->name, "Roxette");
        $this->assertSame($results[0]->url, "https://www.last.fm/music/Roxette");
        */
    }


    public function testGetJSON(): void
    {
        self::$jsonAPI->get("Roxette");
        $this->assertSame(self::$jsonAPI->mbId, "d3b2711f-2baa-441a-be95-14945ca7e6ea");
        $this->assertSame(self::$jsonAPI->name, "Roxette");
        $this->assertSame(self::$jsonAPI->url, "https://www.last.fm/music/Roxette");
        $this->assertIsArray(self::$jsonAPI->tags);
        $this->assertIsArray(self::$jsonAPI->similar);
        $this->assertNotEmpty(self::$jsonAPI->bio->summary);
        $this->assertNotEmpty(self::$jsonAPI->bio->content);
    }

    public function testGetXML(): void
    {
        $this->expectException(\aportela\LastFMWrapper\Exception\InvalidAPIFormatException::class);
        $this->expectExceptionMessage(\aportela\LastFMWrapper\APIFormat::XML->value);
        self::$xmlAPI->get("Roxette");
        // TODO
        /*
        $this->assertSame(self::$jsonAPI->mbId, "d3b2711f-2baa-441a-be95-14945ca7e6ea");
        $this->assertSame(self::$jsonAPI->name, "Roxette");
        $this->assertSame(self::$jsonAPI->url, "https://www.last.fm/music/Roxette");
        $this->assertIsArray(self::$jsonAPI->tags);
        $this->assertIsArray(self::$jsonAPI->similar);
        $this->assertNotEmpty(self::$jsonAPI->bio->summary);
        $this->assertNotEmpty(self::$jsonAPI->bio->content);
        */
    }
}
