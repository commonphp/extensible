<?php

namespace Neuron\Extensibility\Exceptions;

use Neuron\Extensibility\AbstractExtension;
use Neuron\Extensibility\ExtensionType;
use Neuron\Extensibility\ExtensionException;
use Throwable;

/**
 * Exception thrown when an extension type class is missing the required attribute.
 *
 * @package Neuron\Extensibility\Exceptions
 */
class TypeAttributeMissingException extends ExtensionException
{
    public function __construct(string $type, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('The extension type class `'.$type.'` does not have the attribute '.ExtensionType::class, $code, $previous);
    }
}