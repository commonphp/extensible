<?php

namespace Neuron\Extensibility\Exceptions;

use Neuron\Extensibility\AbstractExtension;
use Neuron\Extensibility\ExtensionException;
use Throwable;

/**
 * Exception thrown when an extension type class does not extend the required base class.
 *
 * @package Neuron\Extensibility\Exceptions
 */
class TypeInheritanceException extends ExtensionException
{
    public function __construct(string $type, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('The extension type class `'.$type.'` does not extend '.AbstractExtension::class, $code, $previous);
    }
}