<?php

declare(strict_types=1);

namespace Laminas\Mvc\View;

use function array_merge;

final class Module
{
    public function getConfig(): array
    {
        $provider = new ConfigProvider();
        $config   = $provider();
        unset($config['dependencies']);

        return array_merge([
            'service_manager' => $provider->getDependencyConfig(),
        ], $config);
    }
}
