<?php

declare(strict_types=1);

namespace Laminas\Mvc\View;

final class Module
{
    public function getConfig(): array
    {
        $provider = new ConfigProvider();
        $config   = $provider();
        unset($config['dependencies']);

        return $config;
    }
}
