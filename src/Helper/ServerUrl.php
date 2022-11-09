<?php

declare(strict_types=1);

namespace Laminas\Mvc\View\Helper;

use function assert;
use function is_string;
use function ltrim;
use function rtrim;

final class ServerUrl
{
    /** @var non-empty-string */
    private string $serverUrl;

    /** @param non-empty-string $serverUrl */
    public function __construct(string $serverUrl)
    {
        $serverUrl = rtrim($serverUrl, '/');
        assert($serverUrl !== '');

        $this->serverUrl = $serverUrl;
    }

    /**
     * Return an absolute URL, optionally prepended to the given path
     *
     * @param string|null $path Optional path to be appended to the base server url
     * @return non-empty-string
     */
    public function __invoke(?string $path = null): string
    {
        $path = is_string($path) && $path !== ''
            ? '/' . ltrim($path, '/')
            : '';

        return $this->serverUrl . $path;
    }
}
