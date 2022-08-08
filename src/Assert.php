<?php

declare(strict_types=1);

namespace Laminas\Mvc\View;

use Laminas\Mvc\View\Exception\AssertionFailedException;
use Webmozart\Assert\Assert as WebmozartAssert;

/** @internal  */
final class Assert extends WebmozartAssert
{
    /**
     * @param string $message
     * @throws AssertionFailedException
     * @psalm-pure
     * @psalm-return never
     */
    protected static function reportInvalidArgument($message)
    {
        throw new AssertionFailedException($message);
    }
}
