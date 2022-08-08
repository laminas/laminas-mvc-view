<?php

declare(strict_types=1);

namespace LaminasTest\Mvc\View\Helper\Factory;

use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\View\Helper\Factory\UrlFactory;
use Laminas\Router\Http\RouteMatch;
use Laminas\Router\Http\TreeRouteStack;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class UrlFactoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testTheFactoryWillComposeTheCorrectDependencies(): void
    {
        $router = new TreeRouteStack();
        $match  = new RouteMatch([]);
        $event  = new MvcEvent();
        $event->setRouteMatch($match);

        $application = $this->createMock(Application::class);
        $application->expects(self::once())
            ->method('getMvcEvent')
            ->willReturn($event);

        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::exactly(2))
            ->method('get')
            ->willReturnMap([
                ['HttpRouter', $router],
                ['Application', $application],
            ]);

        (new UrlFactory())->__invoke($container);
    }
}
