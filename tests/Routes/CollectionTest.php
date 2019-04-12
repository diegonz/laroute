<?php

namespace Lord\Laroute\Tests\Routes;

use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Lord\Laroute\Routes\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    protected $routeCollection;

    protected $routes;

    /**
     * @var Route
     */
    protected $testRoute;

    public function setUp()
    {
        parent::setUp();

        $this->testRoute = new Route('GET', 'test', ['controller' => 'laroute\\TestAction']);
        $this->routeCollection = new RouteCollection();
        $this->routeCollection->add($this->testRoute);
    }

    public function testParseRoutesAndGetRouteInformation(): void
    {
        $allCollection = new Collection($this->routeCollection, 'all', 'laroute');
        $allRoutes = $allCollection->toJson();
        $expectedAllJson = '[{"host":null,"methods":["GET","HEAD"],"uri":"test","name":null,"action":"TestAction"}]';
        $this->assertEquals($expectedAllJson, $allRoutes);

        $onlyCollection = new Collection($this->routeCollection, 'only', 'laroute');
        $onlyRoutes = $onlyCollection->toJson();
        $expectedOnlyJson = '[]';
        $this->assertEquals($expectedOnlyJson, $onlyRoutes);
    }
}
