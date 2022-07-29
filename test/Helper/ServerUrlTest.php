<?php

declare(strict_types=1);

namespace LaminasTest\Mvc\View\Helper;

use Laminas\Mvc\View\Helper\ServerUrl;
use PHPUnit\Framework\TestCase;

class ServerUrlTest extends TestCase
{
    private ServerUrl $helper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->helper = new ServerUrl('https://example.com');
    }

    public function testHelperWillReturnTheConfiguredUrlWhenCalledWithoutArgument(): void
    {
        self::assertSame('https://example.com', $this->helper->__invoke());
    }

    /** @return list<array{0: non-empty-string, 1: string}> */
    public function pathProvider(): array
    {
        return [
            ['/foo', 'https://example.com/foo'],
            ['foo', 'https://example.com/foo'],
            ['/foo/bar', 'https://example.com/foo/bar'],
            ['///foo', 'https://example.com/foo'],
            ['/foo/', 'https://example.com/foo/'],
            ['/', 'https://example.com/'],
        ];
    }

    /**
     * @param non-empty-string $path
     * @dataProvider pathProvider
     */
    public function testPathArgumentsYieldTheExpectedUrl(string $path, string $expect): void
    {
        self::assertSame($expect, $this->helper->__invoke($path));
    }

    public function testAnEmptyStringCanBeGivenAsAPathWithTheSameResultAsNoPathArgument(): void
    {
        /** @psalm-suppress InvalidArgument */
        self::assertSame('https://example.com', $this->helper->__invoke(''));
    }

    public function testThatTheBaseUrlHasTrailingSlashesTrimmed(): void
    {
        $helper = new ServerUrl('https://foo.com////');
        self::assertSame('https://foo.com', $helper->__invoke());
    }
}
