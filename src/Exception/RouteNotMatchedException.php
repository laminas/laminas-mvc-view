<?php

declare(strict_types=1);

namespace Laminas\Mvc\View\Exception;

use RuntimeException;

final class RouteNotMatchedException extends RuntimeException implements ExceptionInterface
{
    public static function withEmptyRouteName(): self
    {
        return new self('A route name was not provided or RouteMatch does not contain a matched route name');
    }
}
