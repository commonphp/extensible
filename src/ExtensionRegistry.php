<?php

/** @noinspection PhpUnused */

namespace Neuron\Extensibility;

use Neuron\Extensibility\Exceptions\ExtensionAttributeMissingException;
use Neuron\Extensibility\Exceptions\ExtensionClassMissingException;
use Neuron\Extensibility\Exceptions\ExtensionInheritanceException;
use Neuron\Extensibility\Exceptions\ExtensionSingletonException;
use Neuron\Extensibility\Exceptions\TypeAttributeNotRegisteredException;
use Psr\Log\LoggerInterface;
use ReflectionClass;

/**
 * Provides a registry of registered extensions.
 */
final class ExtensionRegistry
{
    /** @var ExtensionStore The extension store */
    private ExtensionStore $store;

    /** @var LoggerInterface Logger interface for logging */
    private LoggerInterface $log;

    /** @var array<class-string<AbstractExtension>, Extension> Registered extensions */
    private array $extensions = [];

    /** @var array<class-string, array<class-string<AbstractExtension>>> Registered interfaces */
    private array $interfaces = [];

    /** @var array<class-string<ExtensionType>, array<class-string<AbstractExtension>>> Registered types */
    private array $types = [];

    /**
     * Constructs an ExtensionRegistry.
     *
     * @param ExtensionStore $store The extension store.
     * @param LoggerInterface $log The logger instance.
     */
    public function __construct(ExtensionStore $store, LoggerInterface $log)
    {
        $this->store = $store;
        $this->log = $log;
    }


    /**
     * Registers an extension.
     *
     * @param class-string<AbstractExtension> $typeClass The type class.
     * @param class-string $extensionClass The extension class.
     * @throws ExtensionSingletonException
     * @throws TypeAttributeNotRegisteredException
     * @throws ExtensionAttributeMissingException
     * @throws ExtensionClassMissingException
     * @throws ExtensionInheritanceException
     */
    public function register(string $typeClass, string $extensionClass): void
    {
        $type = $this->store->typeRegistry->get($typeClass);

        if (!class_exists($extensionClass)) {
            $this->log->error('Extension class '.$extensionClass.' does not exist');
            throw new ExtensionClassMissingException($extensionClass);
        }

        $reflection = new ReflectionClass($extensionClass);
        $interface = $type->getRequiredInterface();
        if ($type->hasRequiredInterface()) {
            if (!$reflection->implementsInterface($interface))
            {
                $this->log->error('Extension class '.$extensionClass.' does not implement '.$interface);
                throw new ExtensionInheritanceException($extensionClass, $interface);
            }
        }

        $attrs = $reflection->getAttributes($typeClass);
        if (empty($attrs)) {
            $this->log->error('Extension class '.$extensionClass.' does not have '.$typeClass.' attribute');
            throw new ExtensionAttributeMissingException($extensionClass, $typeClass);
        }
        /** @var AbstractExtension $attr */
        $attr = $attrs[0]->newInstance();

        $extension = new Extension($extensionClass, $attr, $type);

        $this->extensions[$extensionClass] = $extension;

        if (!isset($this->types[$typeClass])) {
            $this->types[$typeClass] = [];
        }
        $this->types[$typeClass][] = $extensionClass;

        if ($extension->hasRequiredInterface()) {
            if (!isset($this->interfaces[$interface])) {
                $this->interfaces[$interface] = [];
            }
            $this->interfaces[$interface][] = $extensionClass;
        }

        if ($extension->isExtensionPreloaded()) {
            $this->store->get($extensionClass);
        }

        $this->log->debug('Registered extension: '.$extensionClass);
    }

    /**
     * Checks if an extension exists.
     *
     * @param string $className The extension class name.
     * @return bool
     */
    public function has(string $className): bool
    {
        return isset($this->extensions[$className]);
    }

    /**
     * Checks if an extension exists by interface
     *
     * @param string $interfaceName The interface name.
     * @return bool
     */
    public function hasInterface(string $interfaceName): bool
    {
        return isset($this->interfaces[$interfaceName]);
    }

    /**
     * Checks if an extension exists by type attribute
     *
     * @param string $attributeName The attribute name.
     * @return bool
     */
    public function hasType(string $attributeName): bool
    {
        return isset($this->types[$attributeName]);
    }

    /**
     * Gets an extension by class name.
     *
     * @param string $className The extension class name.
     * @return Extension|null
     */
    public function get(string $className): ?Extension
    {
        return $this->has($className) ? $this->extensions[$className] : null;
    }

    /**
     * Gets all loaded extensions by interface name
     *
     * @param string $interfaceName The interface name.
     * @return array<Extension>
     */
    public function ofInterface(string $interfaceName): array
    {
        $result = [];
        if (!$this->hasInterface($interfaceName)) {
            foreach ($this->interfaces[$interfaceName] as $extensionClass) {
                $result[] = $this->extensions[$extensionClass];
            }
        }
        return $result;
    }

    /**
     * Gets all loaded extensions by type attribute
     *
     * @param string $attributeName The attribute name.
     * @return array<Extension>
     */
    public function ofType(string $attributeName): array
    {
        $result = [];
        if (!$this->hasType($attributeName)) {
            foreach ($this->types[$attributeName] as $extensionClass) {
                $result[] = $this->extensions[$extensionClass];
            }
        }
        return $result;
    }

    /**
     * Get the default instantiation parameters for the extension
     *
     * @param string $className The attribute name
     * @return array
     */
    public function getInstantiationParameters(string $className): array
    {
        return $this->extensions[$className]?->getSingletonParameters() ?? [];
    }
}