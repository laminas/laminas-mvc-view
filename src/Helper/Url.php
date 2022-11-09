<?php

declare(strict_types=1);

namespace Laminas\Mvc\View\Helper;

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

final class Url
{
    public function __construct(
        private RouteMatch $routeMatch,
        private RouteStackInterface $router
    ) {
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
        /** @psalm-suppress RedundantCastGivenDocblockType */
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
            $params = array_merge($this->routeMatch->getParams(), $params);
        }

        $options['name'] = $name;

        return (string) $this->router->assemble($params, $options);
    }
}
