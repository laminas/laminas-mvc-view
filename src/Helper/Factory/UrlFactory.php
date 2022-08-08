<?php

declare(strict_types=1);

namespace Laminas\Mvc\View\Helper\Factory;

use Laminas\Mvc\Application;
use Laminas\Mvc\View\Assert;
use Laminas\Mvc\View\Helper\Url;
use Laminas\Router\RouteMatch;
use Laminas\Router\RouteStackInterface;
use Psr\Container\ContainerInterface;

use function assert;

final class UrlFactory
{
    public function __invoke(ContainerInterface $container): Url
    {
        /**
         * This is traditionally the alias we use in MVC to fetch the router in use
         */
        $router = $container->get('HttpRouter');
        Assert::isInstanceOf($router, RouteStackInterface::class);

        /**
         * The RouteMatch instance must be retrieved from the MVC Event
         */
        $mvcApplication = $container->get('Application');
        assert($mvcApplication instanceof Application);
        $routeMatch = $mvcApplication->getMvcEvent()->getRouteMatch();
        Assert::isInstanceOf($routeMatch, RouteMatch::class);

        return new Url(
            $routeMatch,
            $router
        );
    }
}
