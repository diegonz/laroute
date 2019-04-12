<?php

namespace Lord\Laroute\Tests\Console\Commands;

use Lord\Laroute\LarouteServiceProvider;
use Lord\Laroute\Routes\Exceptions\ZeroRoutesException;
use Orchestra\Testbench\TestCase;

class LarouteGeneratorCommandEmptyRoutesTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LarouteServiceProvider::class,
        ];
    }

    public function testTheConsoleCommandThrowsAnExceptionWithZeroRoutes(): void
    {
        $this->expectException(ZeroRoutesException::class);
        $this->artisan('laroute:generate');
    }

    public function testZeroRoutesExceptionProvidesAMessage(): void
    {
        $emptyExceptionMessage = '';
        try {
            $this->artisan('laroute:generate');
        } /** @noinspection PhpRedundantCatchClauseInspection */
        catch (ZeroRoutesException $e) {
            $emptyExceptionMessage = $e->getMessage();
        }
        $this->assertEquals("You don't have any routes!", $emptyExceptionMessage);
    }
}
