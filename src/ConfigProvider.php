<?php

declare(strict_types=1);

namespace Laminas\Mvc\View;

use Laminas\ServiceManager\ServiceManager;
use Laminas\View\Helper as ViewHelper;

/**
 * @psalm-import-type ServiceManagerConfiguration from ServiceManager
 * @psalm-type ViewHelperConfiguration = array{
 *     server_url?: non-empty-string|null,
 * }
 */
final class ConfigProvider
{
    /** @return array<string, mixed> */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
            'view_helpers' => $this->getViewHelperConfig(),
            /**
             * Configuration for Individual View Helpers
             */
            'view_helper_config' => [
                /**
                 * The server url can be statically configured - this is useful when you want to render views in
                 * processes outside a traditional web server environment.
                 *
                 * The value should be an absolute url, or left blank, in which case, it will be detected at runtime.
                 */
                'server_url' => null, // i.e 'https://example.com'
            ],
            /**
             * MVC has historically used the `view_manager` top-level key for a range of configuration options
             */
            'view_manager' => [
                /**
                 * Configure a doctype for HTML views by selecting one of the available constants in the
                 * {@link ViewHelper\Doctype} helper class.
                 */
                'doctype' => null,
            ],
        ];
    }

    /** @return ServiceManagerConfiguration */
    public function getDependencyConfig(): array
    {
        return [];
    }

    /** @return ServiceManagerConfiguration */
    public function getViewHelperConfig(): array
    {
        return [
            'factories' => [
                Helper\ServerUrl::class => Helper\Factory\ServerUrlFactory::class,
                Helper\Url::class       => Helper\Factory\UrlFactory::class,
                /**
                 * Factories for helpers in Laminas\View
                 */
                ViewHelper\Doctype::class => Helper\Factory\DoctypeFactory::class,
            ],
            'aliases'   => [
                'serverUrl' => Helper\ServerUrl::class,
                'url'       => Helper\Url::class,
            ],
        ];
    }
}
