<?php

namespace Neuron\Extensibility;

use Neuron\Events\AbstractEvent;

/**
 * Provides event information when an extension is instantiated
 */
class ExtensionInstantiatedEvent extends AbstractEvent
{
    public function __construct(Extension $extension, object $instance)
    {
        parent::__construct([
            'extension' => $extension,
            'instance' => $instance
        ]);
    }
}