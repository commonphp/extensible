<?php

namespace Neuron\Extensibility;

/**
 * Provides an interface for managing the extension store
 */
interface ExtensionsInterface
{
    /**
     * Get the registry for extension types
     *
     * @return ExtensionTypeRegistry
     */
    public function getTypeRegistry(): ExtensionTypeRegistry;

    /**
     * Get the registry for extensions
     *
     * @return ExtensionRegistry
     */
    public function getRegistry(): ExtensionRegistry;

    /**
     * Checks if an extension exists in the registry.
     *
     * @param string $className The extension class name.
     * @return bool
     */
    public function has(string $className): bool;

    /**
     * Creates a new instance of an extension.
     *
     * @param string $className The class name of the extension.
     * @param array $parameters Parameters for instantiation.
     * @return object The instantiated extension.
     */
    public function create(string $className, array $parameters = []): object;

    /**
     * Retrieves a singleton extension instance.
     *
     * @template T
     * @param class-string<T> $className
     * @return T
     */
    public function get(string $className): object;

}