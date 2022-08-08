<?php

declare(strict_types=1);

namespace Laminas\Mvc\View\Helper\Factory;

use ArrayAccess;
use Laminas\View\Helper\Doctype;
use Psr\Container\ContainerInterface;

use function assert;
use function is_array;
use function is_string;

final class DoctypeFactory
{
    public function __invoke(ContainerInterface $container): Doctype
    {
        $helper = new Doctype();

        if (! $container->has('config')) {
            return $helper;
        }

        $config = $container->get('config');
        assert(is_array($config) || $config instanceof ArrayAccess);

        $options = $config['view_manager'] ?? [];
        assert(is_array($options));

        /** @var mixed $doctype */
        $doctype = $options['doctype'] ?? null;
        if (is_string($doctype)) {
            $helper->setDoctype($doctype);
        }

        return $helper;
    }
}
