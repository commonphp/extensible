<?php

namespace Neuron\Extensibility;

use Exception;
use Throwable;

/**
 * Base exception class for extension-related errors.
 */
class ExtensionException extends Exception
{
    /**
     * Constructs an ExtensionException.
     *
     * @param string $message Exception message.
     * @param int $code Exception code.
     * @param Throwable|null $previous Previous exception.
     */
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}