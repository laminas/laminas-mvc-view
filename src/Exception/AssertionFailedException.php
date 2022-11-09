<?php

declare(strict_types=1);

namespace Laminas\Mvc\View\Exception;

use InvalidArgumentException;

final class AssertionFailedException extends InvalidArgumentException implements ExceptionInterface
{
}
