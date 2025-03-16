<?php

namespace Neuron\Extensibility\Exceptions;

use Neuron\Extensibility\AbstractExtension;
use Neuron\Extensibility\ExtensionException;
use Throwable;

/**
 * Exception thrown when no matching extension type is found for a given class.
 *
 * @package Neuron\Extensibility\Exceptions
 */
class NoMatchingExtensionTypeException extends ExtensionException
{
    public function __construct(string $className, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('There is no loaded extension type that matches the class '.$className, $code, $previous);
    }
}