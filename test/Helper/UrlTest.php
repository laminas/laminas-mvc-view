<?php

declare(strict_types=1);

namespace LaminasTest\Mvc\View\Helper;

use ArrayObject;
use Laminas\Mvc\ModuleRouteListener;
use Laminas\Mvc\View\Exception\RouteNotMatchedException;
use Laminas\Mvc\View\Helper\Url;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\RouteMatch;
use Laminas\Router\Http\Segment;
use Laminas\Router\Http\TreeRouteStack;
use PHPUnit\Framework\TestCase;

final class UrlTest extends TestCase
{
    private TreeRouteStack $router;
    private RouteMatch $routeMatch;
    private Url $helper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->router = new TreeRouteStack();
        $this->router->addRoute('home', [
            'type'    => Literal::class,
            'options' => [
                'route' => '/',
            ],
        ]);
        $this->router->addRoute('default', [
            'type'    => Segment::class,
            'options' => [
                'route' => '/:controller[/:action]',
            ],
        ]);

        $this->routeMatch = new RouteMatch([]);
        $this->helper     = new Url($this->routeMatch, $this->router);
    }

    public function testNamedStaticRouteCanBeRetrieved(): void
    {
        self::assertEquals('/', $this->helper->__invoke('home'));
    }

    public function testItIsExceptionalToCallTheHelperWithoutARouteName(): void
    {
        $this->expectException(RouteNotMatchedException::class);
        $this->expectExceptionMessage(
            'A route name was not provided or RouteMatch does not contain a matched route name'
        );
        $this->helper->__invoke();
    }

    public function testDefaultModuleRouting(): void
    {
        self::assertEquals(
            '/my/route',
            $this->helper->__invoke('default', ['controller' => 'my', 'action' => 'route'])
        );
    }

    public function testDefaultModuleRoutingWithTraversable(): void
    {
        $arrayObject = new ArrayObject(['controller' => 'my', 'action' => 'route']);
        self::assertEquals(
            '/my/route',
            $this->helper->__invoke('default', $arrayObject)
        );
    }

    public function testThatMatchedRouteParametersCanBeReused(): void
    {
        $this->routeMatch->setMatchedRouteName('replace');
        $this->routeMatch->setParam('controller', 'groovy');

        $this->router->addRoute('replace', [
            'type'    => Segment::class,
            'options' => [
                'route'    => '/:controller/:action',
                'defaults' => [
                    'controller' => 'default',
                ],
            ],
        ]);

        self::assertEquals(
            '/groovy/bar',
            $this->helper->__invoke('replace', ['action' => 'bar'], [], true)
        );
    }

    public function testThatOptionsAreUsed(): void
    {
        self::assertEquals(
            '/#foo',
            $this->helper->__invoke('home', [], ['fragment' => 'foo'], false)
        );
    }

    public function testThatOptionsCanBeTraversable(): void
    {
        $options = new ArrayObject(['fragment' => 'foo']);
        self::assertEquals(
            '/#foo',
            $this->helper->__invoke('home', [], $options, false)
        );
    }

    public function testThatReuseMatchedRouteParamsCanBeProvidedAsTheThirdArgument(): void
    {
        $this->routeMatch->setMatchedRouteName('replace');
        $this->routeMatch->setParam('controller', 'groovy');

        $this->router->addRoute('replace', [
            'type'    => Segment::class,
            'options' => [
                'route'    => '/:controller/:action',
                'defaults' => [
                    'controller' => 'default',
                ],
            ],
        ]);

        self::assertEquals(
            '/groovy/bar',
            $this->helper->__invoke('replace', ['action' => 'bar'], true)
        );
    }

    public function testThatMatchedRouteParamsAreNotReusedByDefault(): void
    {
        $this->routeMatch->setMatchedRouteName('replace');
        $this->routeMatch->setParam('controller', 'groovy');

        $this->router->addRoute('replace', [
            'type'    => Segment::class,
            'options' => [
                'route'    => '/:controller/:action',
                'defaults' => [
                    'controller' => 'default',
                ],
            ],
        ]);

        self::assertEquals(
            '/default/bar',
            $this->helper->__invoke('replace', ['action' => 'bar'])
        );
    }

    public function testThatControllerIsSourcedFromModuleRouteListenerWhenAvailable(): void
    {
        $this->routeMatch->setMatchedRouteName('default');
        $this->routeMatch->setParam(ModuleRouteListener::ORIGINAL_CONTROLLER, 'groovy');

        self::assertEquals(
            '/groovy/bar',
            $this->helper->__invoke('default', ['action' => 'bar'], [], true)
        );
    }

    public function testThatGivenControllerOverridesControllerFoundInModuleRouteListener(): void
    {
        $this->routeMatch->setMatchedRouteName('default');
        $this->routeMatch->setParam(ModuleRouteListener::ORIGINAL_CONTROLLER, 'groovy');

        self::assertEquals(
            '/bing/bar',
            $this->helper->__invoke('default', ['controller' => 'bing', 'action' => 'bar'], [], true)
        );
    }

    public function testModuleNamespaceIsStrippedFromRouteParams(): void
    {
        $this->router->addRoute('replace', [
            'type'    => Segment::class,
            'options' => [
                'route'    => '/:controller/:action[/:' . ModuleRouteListener::MODULE_NAMESPACE . ']',
                'defaults' => [
                    'controller' => 'default',
                ],
            ],
        ]);

        $this->routeMatch->setMatchedRouteName('replace');
        $this->routeMatch->setParam('controller', 'groovy');
        $this->routeMatch->setParam(ModuleRouteListener::MODULE_NAMESPACE, 'Muppets');

        self::assertEquals(
            '/groovy/bar',
            $this->helper->__invoke('default', ['action' => 'bar'], [], true)
        );
    }
}
