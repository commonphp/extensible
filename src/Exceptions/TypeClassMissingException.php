<?php

namespace Neuron\Extensibility\Exceptions;

use Neuron\Extensibility\ExtensionException;
use Throwable;

/**
 * Exception thrown when an extension type class does not exist.
 *
 * @package Neuron\Extensibility\Exceptions
 */
class TypeClassMissingException extends ExtensionException
{
    public function __construct(string $type, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('The extension type class `'.$type.'` does not exist.', $code, $previous);
    }
}