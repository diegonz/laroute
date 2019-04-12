<?php

namespace Lord\Laroute\Routes;

use Illuminate\Support\Arr;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;

class Collection extends \Illuminate\Support\Collection
{
    /**
     * Collection constructor.
     *
     * @param  RouteCollection  $routes
     * @param $filter
     * @param $namespace
     */
    public function __construct(RouteCollection $routes, $filter, $namespace)
    {
        parent::__construct($routes);
        $this->items = $this->parseRoutes($routes, $filter, $namespace);
    }

    /**
     * Parse the routes into a jsonable output.
     *
     * @param  RouteCollection  $routes
     * @param  string  $filter
     * @param  string  $namespace
     *
     * @return array
     */
    protected function parseRoutes(RouteCollection $routes, $filter, $namespace): array
    {
        $results = [];
        if ($routes->count() > 0) {
            foreach ($routes as $route) {
                $results[] = $this->getRouteInformation($route, $filter, $namespace);
            }
        }

        return array_values(array_filter($results));
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
        $host = $route->domain();
        $methods = $route->methods();
        $uri = $route->uri();
        $name = $route->getName();
        $action = $route->getActionName();
        $laroute = Arr::get($route->getAction(), 'laroute', null);

        if (! empty($namespace)) {
            $a = $route->getAction();

            if (isset($a['controller'])) {
                $action = str_replace($namespace.'\\', '', $action);
            }
        }

        $hasAnyRoutes = true;

        switch ($filter) {
            case 'all':
                $hasAnyRoutes = $laroute === false;
                break;
            case 'only':
                $hasAnyRoutes = $laroute !== true;
                break;
        }

        return $hasAnyRoutes ? [] : compact('host', 'methods', 'uri', 'name', 'action');
    }
}
