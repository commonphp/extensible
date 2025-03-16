<?php

namespace Neuron\Extensibility\Exceptions;

use Neuron\Extensibility\ExtensionException;
use Throwable;

/**
 * Exception thrown when an extension class does not exist.
 */
class ExtensionClassMissingException extends ExtensionException
{
    public function __construct(string $class, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('The extension class `'.$class.'` does not exist.', $code, $previous);
    }
}