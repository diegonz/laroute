<?php

namespace Lord\Laroute\Tests\Routes;

use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    protected $routeCollection;

    protected $routes;

    public function setUp()
    {
        parent::setUp();

        $this->routeCollection = $this->mock(RouteCollection::class);
        $this->routes = $this->createInstance();
    }

    protected function createInstance()
    {
        $this->routeCollection
            ->shouldReceive('count')
            ->once()
            ->andReturn(1)
            ->shouldReceive('getIterator')
            ->once()
            ->andReturn(['Huh?']);

        return new Collection($this->routeCollection);
    }

    public function testItIsAProperInstance()
    {
        /** @noinspection PhpParamsInspection */
        $this->assertInstanceOf(RouteCollection::class, $this->routeCollection);
    }

    public function testIFailedAtTestingACollection()
    {
        $this->assertTrue(true);
    }


    public function tearDown()
    {
        Mockery::close();
    }

    protected function mock($class, $app = [])
    {
        return Mockery::mock($class, $app);
    }
}
