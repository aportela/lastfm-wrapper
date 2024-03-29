<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\Test;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

final class AlbumTest extends BaseTest
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
        self::$jsonAPI = new \aportela\LastFMWrapper\Album(self::$logger, \aportela\LastFMWrapper\APIFormat::JSON, self::$lastFMAPIKey ?? "");
        self::$xmlAPI = new \aportela\LastFMWrapper\Album(self::$logger, \aportela\LastFMWrapper\APIFormat::XML, self::$lastFMAPIKey ?? "");
    }

    public function testSearchJSON(): void
    {
        $results = self::$jsonAPI->search("Roxette", "Tourism", 1);
        $this->assertCount(1, $results);
        $this->assertSame($results[0]->artist, "Roxette");
        $this->assertSame($results[0]->name, "Tourism");
        $this->assertSame($results[0]->url, "https://www.last.fm/music/Roxette/Tourism");
    }

    public function testSearchXML(): void
    {
        $results = self::$xmlAPI->search("Roxette", "Tourism", 1);
        $this->assertCount(1, $results);
        $this->assertSame($results[0]->artist, "Roxette");
        $this->assertSame($results[0]->name, "Tourism");
        $this->assertSame($results[0]->url, "https://www.last.fm/music/Roxette/Tourism");
    }

    public function testGetJSON(): void
    {
        self::$jsonAPI->get("Roxette", "Tourism");
        $this->assertSame(self::$jsonAPI->mbId, "1031f9e1-d9b0-39d6-a983-d3b552da054d");
        $this->assertSame(self::$jsonAPI->name, "Roxette");
        $this->assertSame(self::$jsonAPI->url, "https://www.last.fm/music/Roxette/Tourism");
        $this->assertIsArray(self::$jsonAPI->tags);
        $this->assertIsArray(self::$jsonAPI->tracks);
    }

    public function testGetXML(): void
    {
        self::$xmlAPI->get("Roxette", "Tourism");
        $this->assertSame(self::$xmlAPI->mbId, "1031f9e1-d9b0-39d6-a983-d3b552da054d");
        $this->assertSame(self::$xmlAPI->name, "Roxette");
        $this->assertSame(self::$xmlAPI->url, "https://www.last.fm/music/Roxette/Tourism");
        $this->assertIsArray(self::$xmlAPI->tags);
        $this->assertIsArray(self::$xmlAPI->tracks);
    }
}
