<?php

namespace Neuron\Extensibility;

/**
 * Interface for extension instantiation mechanisms.
 *
 * @package Neuron\Extensibility
 */
interface InstantiatorInterface
{
    /**
     * Instantiates an extension.
     *
     * @param string $className The class name of the extension.
     * @param array $parameters Parameters for instantiation.
     * @return object The instantiated extension.
     */
    public function instantiate(string $className, array $parameters): object;
}