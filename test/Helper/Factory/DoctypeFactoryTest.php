<?php

declare(strict_types=1);

namespace LaminasTest\Mvc\View\Helper\Factory;

use Laminas\Mvc\View\Helper\Factory\DoctypeFactory;
use Laminas\View\Helper\Doctype;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class DoctypeFactoryTest extends TestCase
{
    private DoctypeFactory $factory;
    /** @var MockObject&ContainerInterface */
    private ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory   = new DoctypeFactory();
        $this->container = $this->createMock(ContainerInterface::class);
        Doctype::unsetDoctypeRegistry(); // Overcome nasty static state
    }

    public function testAHelperWillBeReturnedWhenNoConfigIsFound(): void
    {
        $this->container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(false);

        $this->container->expects(self::never())
            ->method('get');

        $this->factory->__invoke($this->container);
    }

    public function testThatTheDoctypeWillBeAsConfiguredWhenAppropriateConfigCanBeFound(): void
    {
        $config = [
            'view_manager' => [
                'doctype' => Doctype::HTML4_FRAMESET,
            ],
        ];

        $this->container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);

        $this->container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $helper = $this->factory->__invoke($this->container);

        self::assertEquals(Doctype::HTML4_FRAMESET, $helper->getDoctype());
    }

    /** @return array<string, array{0: iterable<mixed>, 1: string}> */
    public function configProvider(): array
    {
        $helper  = new Doctype();
        $default = $helper->getDoctype();

        return [
            'Empty Config'          => [[], $default],
            'Missing Doctype Key'   => [['view_manager' => []], $default],
            'Doctype Configured'    => [['view_manager' => ['doctype' => Doctype::XHTML1_RDFA]], Doctype::XHTML1_RDFA],
            'Doctype Explicit Null' => [['view_manager' => ['doctype' => null]], $default],
        ];
    }

    /** @dataProvider configProvider */
    public function testConfigScenarios(iterable $config, string $expectedDoctype): void
    {
        $this->container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);

        $this->container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $helper = $this->factory->__invoke($this->container);

        self::assertEquals($expectedDoctype, $helper->getDoctype());
    }
}
