<?php
namespace Lyra\Tests;

use PHPUnit\Framework\TestCase;

/**
 * ./vendor/bin/phpunit src/Lyra/Tests/AutoloaderTest.php
 */
class AutoloaderTest extends TestCase
{
    /**
     * ./vendor/bin/phpunit --filter testAutoloader src/Lyra/Tests/AutoloaderTest.php
     * Test standalone autoloader (not the composer one)
     */
    public function testAutoloader()
    {
        require(__DIR__ . '/../../autoload.php');
    }
}
