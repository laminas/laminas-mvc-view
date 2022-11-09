<?php

declare(strict_types=1);

namespace Laminas\Mvc\View\Exception;

use RuntimeException;

final class HostNameDetectionException extends RuntimeException implements ExceptionInterface
{
    public static function withMissingHostOrScheme(): self
    {
        return new self('The hostname or scheme cannot be detected from the current environment');
    }
}
