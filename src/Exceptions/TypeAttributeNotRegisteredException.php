<?php

namespace Neuron\Extensibility\Exceptions;

use Neuron\Extensibility\AbstractExtension;
use Neuron\Extensibility\ExtensionException;
use Throwable;

/**
 * Exception thrown when an extension type attribute is not registered.
 *
 * @package Neuron\Extensibility\Exceptions
 */
class TypeAttributeNotRegisteredException extends ExtensionException
{
    public function __construct(string $type, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('The extension type `'.$type.'` is not registered', $code, $previous);
    }
}