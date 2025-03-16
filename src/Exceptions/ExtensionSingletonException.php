<?php

namespace Neuron\Extensibility\Exceptions;

use Neuron\Extensibility\ExtensionException;
use Throwable;

/**
 * Exception thrown when a singleton extension is improperly instantiated.
 */
class ExtensionSingletonException extends ExtensionException
{
    public function __construct(string $class, bool $singleton, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('You must use the '.($singleton ? 'get' : 'create').' method for the `'.$class.'` extension because it is'.($singleton ? ' ' : ' not ').'a singleton.', $code, $previous);
    }
}