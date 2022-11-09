<?php

declare(strict_types=1);

namespace Laminas\Mvc\View\Helper\Factory;

use ArrayAccess;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\Mvc\View\Exception\HostNameDetectionException;
use Laminas\Mvc\View\Helper\ServerUrl;
use Psr\Container\ContainerInterface;

use function assert;
use function is_array;
use function is_string;

final class ServerUrlFactory
{
    public function __invoke(ContainerInterface $container): ServerUrl
    {
        return new ServerUrl(
            $this->fetchConfiguredServerUrl($container) ?? $this->detectServerUrlFromEnvironment($container)
        );
    }

    /** @return non-empty-string|null */
    private function fetchConfiguredServerUrl(ContainerInterface $container): ?string
    {
        if (! $container->has('config')) {
            return null;
        }

        $config = $container->get('config');
        assert(is_array($config) || $config instanceof ArrayAccess);

        $helperConfig = $config['view_helper_config'] ?? [];
        assert(is_array($helperConfig));

        $serverUrl = $helperConfig['server_url'] ?? null;
        assert(is_string($serverUrl) || $serverUrl === null);

        return $serverUrl === '' ? null : $serverUrl;
    }

    /** @return non-empty-string */
    private function detectServerUrlFromEnvironment(ContainerInterface $container): string
    {
        /**
         * laminas-mvc uses laminas-http
         */
        $laminasRequest = $container->has(Request::class)
            ? $container->get(Request::class)
            : new Request();

        $uri = clone $laminasRequest->getUri();
        $uri->setFragment(null);
        $uri->setPath(null);
        $uri->setQuery(null);

        if (! $uri->getHost() || ! $uri->getScheme()) {
            throw HostNameDetectionException::withMissingHostOrScheme();
        }

        $uri = (string) $uri;
        assert($uri !== '');

        return $uri;
    }
}
