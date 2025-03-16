<?php

namespace Neuron\Extensibility\Exceptions;

use Neuron\Extensibility\ExtensionException;
use Throwable;

/**
 * Exception thrown when a required extension interface is missing.
 */
class ExtensionInterfaceMissingException extends ExtensionException
{
    public function __construct(string $class, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('The extension interface `'.$class.'` does not exist.', $code, $previous);
    }
}