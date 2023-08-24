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
        self::$jsonAPI = new \aportela\LastFMWrapper\Artist(self::$logger, \aportela\LastFMWrapper\APIFormat::JSON, self::$lastFMAPIKey ?? "");
        self::$xmlAPI = new \aportela\LastFMWrapper\Artist(self::$logger, \aportela\LastFMWrapper\APIFormat::XML, self::$lastFMAPIKey ?? "");
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
        $results = self::$xmlAPI->search("Roxette", 1);
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
        $this->assertIsArray(self::$jsonAPI->similar);
        $this->assertNotEmpty(self::$jsonAPI->bio->summary);
        $this->assertNotEmpty(self::$jsonAPI->bio->content);
    }

    public function testGetXML(): void
    {
        self::$xmlAPI->get("Roxette");
        $this->assertSame(self::$xmlAPI->mbId, "d3b2711f-2baa-441a-be95-14945ca7e6ea");
        $this->assertSame(self::$xmlAPI->name, "Roxette");
        $this->assertSame(self::$xmlAPI->url, "https://www.last.fm/music/Roxette");
        $this->assertIsArray(self::$xmlAPI->tags);
        $this->assertIsArray(self::$xmlAPI->similar);
        $this->assertNotEmpty(self::$xmlAPI->bio->summary);
        $this->assertNotEmpty(self::$xmlAPI->bio->content);
    }

    public function testGetImageFromArtistPageURL(): void
    {
        $this->assertNotEmpty(self::$xmlAPI->getImageFromArtistPageURL("https://www.last.fm/music/Roxette"));
    }
}
