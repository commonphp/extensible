<?php

namespace Neuron\Extensibility\Exceptions;

use Neuron\Extensibility\ExtensionException;
use Throwable;

/**
 * Exception thrown when required dependencies are missing for an extension.
 */
class MissingDependenciesException extends ExtensionException
{
    public function __construct(string $className, array $dependencies, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('Could not create '.$className.' extension: Missing dependencies ('.implode(', ', $dependencies).')', $code, $previous);
    }
}