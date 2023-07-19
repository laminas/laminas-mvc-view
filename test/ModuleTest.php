<?php

declare(strict_types=1);

namespace LaminasTest\Mvc\View;

use Laminas\Mvc\View\Module;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function gettype;

class ModuleTest extends TestCase
{
    public static function expectedKeys(): array
    {
        return [
            ['view_helpers', 'array'],
            ['view_helper_config', 'array'],
            ['view_manager', 'array'],
        ];
    }

    #[DataProvider('expectedKeys')]
    public function testExpectedKeysArePresent(string $key, string $expectedType): void
    {
        $config = (new Module())->getConfig();
        self::assertArrayHasKey($key, $config);
        self::assertSame($expectedType, gettype($config[$key]));
    }
}
