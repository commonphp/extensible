<?php

namespace Neuron\Extensibility\Exceptions;

use Neuron\Extensibility\AbstractExtension;
use Neuron\Extensibility\ExtensionType;
use Neuron\Extensibility\ExtensionException;
use Throwable;

/**
 * Exception thrown when an extension is missing a required attribute.
 */
class ExtensionAttributeMissingException extends ExtensionException
{
    public function __construct(string $type, string $attribute, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('Could not load '.$type.': Missing '.$attribute.' attribute', $code, $previous);
    }
}