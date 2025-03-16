<?php

namespace Neuron\Extensibility\Exceptions;

use Neuron\Extensibility\ExtensionException;
use Throwable;

/**
 * Exception thrown when an extension is not loaded.
 */
class ExtensionNotLoadedException extends ExtensionException
{
    public function __construct(string $className, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('The '.$className.' is not a loaded extension.', $code, $previous);
    }
}