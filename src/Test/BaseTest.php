<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\Test;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

class BaseTest extends \PHPUnit\Framework\TestCase
{
    protected static $logger;
    protected static ?string $lastFMAPIKey;

    protected static $jsonAPI;
    protected static $xmlAPI;

    /**
     * Called once just like normal constructor
     */
    public static function setUpBeforeClass(): void
    {
        self::$logger = new \Psr\Log\NullLogger("");
        self::$lastFMAPIKey = getenv('LASTFM_API_KEY', true) ? getenv('LASTFM_API_KEY') : null;
    }

    /**
     * Initialize the test case
     * Called for every defined test
     */
    public function setUp(): void
    {
        if (empty(self::$lastFMAPIKey)) {
            $this->markTestSkipped("LASTFM_API_KEY environment variable NOT FOUND");
        }
    }

    /**
     * Clean up the test case, called for every defined test
     */
    public function tearDown(): void
    {
    }

    /**
     * Clean up the whole test class
     */
    public static function tearDownAfterClass(): void
    {
    }

    public function testCheckEnvironmentAPIKey(): void
    {
        $this->assertNotEmpty(self::$lastFMAPIKey);
    }
}
