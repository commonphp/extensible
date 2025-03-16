<?php

namespace Neuron\Extensibility\Exceptions;

use Neuron\Extensibility\ExtensionException;
use Throwable;

/**
 * Exception thrown when attempting to register a duplicate extension type.
 */
class DuplicateTypeException extends ExtensionException
{
    public function __construct(string $type, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('The extension type `'.$type.'` is already defined.', $code, $previous);
    }
}