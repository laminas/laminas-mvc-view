<?php

declare(strict_types=1);

namespace LaminasTest\Mvc\View\Helper\Factory;

use Laminas\Http\PhpEnvironment\Request;
use Laminas\Mvc\View\Exception\HostNameDetectionException;
use Laminas\Mvc\View\Helper\Factory\ServerUrlFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ServerUrlFactoryTest extends TestCase
{
    private ServerUrlFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new ServerUrlFactory();
    }

    public function testThatWhenAUriIsConfiguredItWillBeUsedToSeedTheHelper(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);
        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn([
                'view_helper_config' => [
                    'server_url' => 'https://muppets.com',
                ],
            ]);

        self::assertSame(
            'https://muppets.com',
            $this->factory->__invoke($container)->__invoke()
        );
    }

    /**
     * @backupGlobals enabled
     */
    public function testThatWhenThereIsNoConfigAndNoRequestInTheContainerANewRequestWillBeCreatedFromServerVars(): void
    {
        $_SERVER['SERVER_NAME'] = 'goats.com';
        $_SERVER['HTTPS']       = 'on';

        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::exactly(2))
            ->method('has')
            ->willReturn(false);

        self::assertSame(
            'https://goats.com',
            $this->factory->__invoke($container)->__invoke()
        );
    }

    public function testAnExceptionWillBeThrownWhenTheUrlCannotBeDeterminedViaSuperGlobals(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::exactly(2))
            ->method('has')
            ->willReturn(false);

        $this->expectException(HostNameDetectionException::class);
        $this->factory->__invoke($container);
    }

    public function testTheRequestWillBeRetrievedFromTheContainerWhenAvailable(): void
    {
        $request = new Request();
        $request->setUri('https://elephants.com/foo?bar=baz');

        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::exactly(2))
            ->method('has')
            ->willReturnMap([
                ['config', false],
                [Request::class, true],
            ]);
        $container->expects(self::once())
            ->method('get')
            ->with(Request::class)
            ->willReturn($request);

        self::assertSame(
            'https://elephants.com',
            $this->factory->__invoke($container)->__invoke()
        );
    }
}
