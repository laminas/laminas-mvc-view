<?php

declare(strict_types=1);

namespace Laminas\Mvc\View\Helper;

use Laminas\Mvc\ModuleRouteListener;
use Laminas\Mvc\View\Exception\RouteNotMatchedException;
use Laminas\Router\RouteInterface;
use Laminas\Router\RouteMatch;
use Laminas\Router\RouteStackInterface;
use Laminas\Stdlib\ArrayUtils;

use function array_merge;
use function assert;
use function func_num_args;
use function is_array;
use function is_bool;
use function is_object;
use function is_string;

final class Url
{
    private RouteMatch $routeMatch;
    private RouteStackInterface $router;

    public function __construct(
        RouteMatch $routeMatch,
        RouteStackInterface $router
    ) {
        $this->routeMatch = $routeMatch;
        $this->router     = $router;
    }

    /**
     * Generates an url given the name of a route.
     *
     * @see RouteInterface::assemble()
     *
     * @param string|null $name Name of the route
     * @param iterable<string, mixed> $params Parameters for the link
     * @param iterable<mixed>|bool $options Options for the route, or bool $reuseMatchedParams to skip the 4th argument
     * @param bool $reuseMatchedParams Whether to reuse matched parameters
     * @return string Url For the link href attribute
     * @throws RouteNotMatchedException If RouteMatch didn't contain a matched route name.
     */
    public function __invoke(
        ?string $name = null,
        iterable $params = [],
        $options = [],
        bool $reuseMatchedParams = false
    ): string {
        $name = $name ?? (string) $this->routeMatch->getMatchedRouteName();
        if ($name === '') {
            throw RouteNotMatchedException::withEmptyRouteName();
        }

        if (is_object($params)) {
            $params = ArrayUtils::iteratorToArray($params);
        }

        if (is_object($options)) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (func_num_args() === 3 && is_bool($options)) {
            $reuseMatchedParams = $options;
            $options            = [];
        }

        assert(is_array($options));

        if ($reuseMatchedParams) {
            $routeMatchParams = $this->routeMatch->getParams();

            /** @var mixed $controller */
            $controller = $routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER] ?? null;

            if (is_string($controller)) {
                $routeMatchParams['controller'] = $controller;
                unset($routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER]);
            }

            if (isset($routeMatchParams[ModuleRouteListener::MODULE_NAMESPACE])) {
                unset($routeMatchParams[ModuleRouteListener::MODULE_NAMESPACE]);
            }

            $params = array_merge($routeMatchParams, $params);
        }

        $options['name'] = $name;

        return (string) $this->router->assemble($params, $options);
    }
}
