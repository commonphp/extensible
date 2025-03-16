<?php

namespace Neuron\Extensibility\Exceptions;

use Neuron\Extensibility\ExtensionException;
use Throwable;

/**
 * Exception thrown when an extension interface is already bound to another type.
 */
class DuplicateTypeInterfaceException extends ExtensionException
{
    public function __construct(string $interface, string $newType, string $oldType, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('The extension interface `'.$interface.'` cannot be bound to '.$newType.' because it is already bound to '.$oldType, $code, $previous);
    }
}