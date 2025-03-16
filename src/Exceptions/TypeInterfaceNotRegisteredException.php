<?php

namespace Neuron\Extensibility\Exceptions;

use Neuron\Extensibility\AbstractExtension;
use Neuron\Extensibility\ExtensionException;
use Throwable;

/**
 * Exception thrown when an extension type interface is not registered.
 *
 * @package Neuron\Extensibility\Exceptions
 */
class TypeInterfaceNotRegisteredException extends ExtensionException
{
    public function __construct(string $interface, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('The extension interface `'.$interface.'` is not registered', $code, $previous);
    }
}