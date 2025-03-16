<?php

namespace Neuron\Extensibility\Exceptions;

use Neuron\Extensibility\AbstractExtension;
use Neuron\Extensibility\ExtensionException;
use Throwable;

/**
 * Exception thrown when an extension does not implement the required interface.
 */
class ExtensionInheritanceException extends ExtensionException
{
    public function __construct(string $type, string $interface, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('Could not load '.$type.': does not implement '.$interface, $code, $previous);
    }
}