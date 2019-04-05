<?php

namespace Lord\Laroute\Routes;

use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Lord\Laroute\Routes\Exceptions\ZeroRoutesException;

class Collection extends \Illuminate\Support\Collection
{
    /**
     * Collection constructor.
     * @param  RouteCollection  $routes
     * @param $filter
     * @param $namespace
     * @throws ZeroRoutesException
     */
    public function __construct(RouteCollection $routes, $filter, $namespace)
    {
        parent::__construct($routes);
        $this->items = $this->parseRoutes($routes, $filter, $namespace);
    }

    /**
     * Parse the routes into a jsonable output.
     *
     * @param RouteCollection $routes
     * @param string $filter
     * @param string $namespace
     *
     * @return array
     * @throws ZeroRoutesException
     */
    protected function parseRoutes(RouteCollection $routes, $filter, $namespace): array
    {
        $this->guardAgainstZeroRoutes($routes);

        $results = [];

        foreach ($routes as $route) {
            $results[] = $this->getRouteInformation($route, $filter, $namespace);
        }

        return array_values(array_filter($results));
    }

    /**
     * Throw an exception if there aren't any routes to process
     *
     * @param RouteCollection $routes
     *
     * @throws ZeroRoutesException
     */
    protected function guardAgainstZeroRoutes(RouteCollection $routes): void
    {
        if (count($routes) < 1) {
            throw new ZeroRoutesException("You don't have any routes!");
        }
    }

    /**
     * Get the route information for a given route.
     *
     * @param $route Route
     * @param $filter string
     * @param $namespace string
     *
     * @return array
     */
    protected function getRouteInformation(Route $route, $filter, $namespace): array
    {
        $host    = $route->domain();
        $methods = $route->methods();
        $uri     = $route->uri();
        $name    = $route->getName();
        $action  = $route->getActionName();
        $laroute = array_get($route->getAction(), 'laroute', null);

        if(!empty($namespace)) {
            $a = $route->getAction();

            if(isset($a['controller'])) {
                $action = str_replace($namespace.'\\', '', $action);
            }
        }

        switch ($filter) {
            case 'all':
                if($laroute === false) {
                    return null;
                }
                break;
            case 'only':
                if($laroute !== true) {
                    return null;
                }
                break;
        }

        return compact('host', 'methods', 'uri', 'name', 'action');
    }

}
