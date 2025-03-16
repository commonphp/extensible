<?php

namespace Neuron\Extensibility;

use Neuron\Extensibility\Exceptions\DuplicateTypeException;
use Neuron\Extensibility\Exceptions\DuplicateTypeInterfaceException;
use Neuron\Extensibility\Exceptions\NoMatchingExtensionTypeException;
use Neuron\Extensibility\Exceptions\TypeAttributeMissingException;
use Neuron\Extensibility\Exceptions\TypeClassMissingException;
use Neuron\Extensibility\Exceptions\TypeInheritanceException;
use Neuron\Extensibility\Exceptions\TypeAttributeNotRegisteredException;
use Neuron\Extensibility\Exceptions\TypeInterfaceNotRegisteredException;
use Psr\Log\LoggerInterface;
use ReflectionClass;

/**
 * Provides a registry for extension types.
 *
 * Manages extension types by ensuring type uniqueness, enforcing required interfaces,
 * and allowing retrieval of types by class or attribute.
 *
 * @package Neuron\Extensibility
 */
class ExtensionTypeRegistry
{
    /** @var LoggerInterface Logger instance */
    private LoggerInterface $log;

    /** @var array<string, ExtensionType> Registered extension types */
    private array $types = [];

    /** @var array<string, string> Registered interfaces mapped to extension types */
    private array $interfaces = [];

    /**
     * Constructs an ExtensionTypeRegistry.
     *
     * @param LoggerInterface $log The logger instance.
     */
    public function __construct(LoggerInterface $log)
    {
        $this->log = $log;
    }

    /**
     * Registers an extension type.
     *
     * @param string $typeAttributeClass The extension type class.
     * @throws DuplicateTypeException
     * @throws DuplicateTypeInterfaceException
     * @throws TypeAttributeMissingException
     * @throws TypeClassMissingException
     * @throws TypeInheritanceException
     */
    public function register(string $typeAttributeClass): void
    {
        if (isset($this->types[$typeAttributeClass])) {
            $this->log->error('There can only be one registered extension type for '.$typeAttributeClass);
            throw new DuplicateTypeException($typeAttributeClass);
        }
        if (!class_exists($typeAttributeClass)) {
            $this->log->error('The extension type class '.$typeAttributeClass.' does not exist');
            throw new TypeClassMissingException($typeAttributeClass);
        }
        if (!is_subclass_of($typeAttributeClass, AbstractExtension::class)) {
            $this->log->error('The extension type class '.$typeAttributeClass.' does not implement the '.AbstractExtension::class.' interface');
            throw new TypeInheritanceException($typeAttributeClass);
        }
        $class = new ReflectionClass($typeAttributeClass);
        $attrs = $class->getAttributes(ExtensionType::class);
        if (empty($attrs)) {
            $this->log->error('The extension type class '.$typeAttributeClass.' does not have the '.ExtensionType::class.' attribute');
            throw new TypeAttributeMissingException($typeAttributeClass);
        }
        /** @var ExtensionType $attr */
        $attr = $attrs[0]->newInstance();
        $interfaceClass = $attr->getRequiredInterface();
        if ($interfaceClass !== null)
        {
            if (isset($this->interfaces[$interfaceClass])) {
                $this->log->error('The extension type interface '.$interfaceClass.' cannot be used with '.$typeAttributeClass.' because it\'s already used by '.$this->interfaces[$interfaceClass]);
                throw new DuplicateTypeInterfaceException($interfaceClass, $typeAttributeClass, $this->interfaces[$interfaceClass]);
            }
            $this->interfaces[$interfaceClass] = $typeAttributeClass;
        }
        $this->types[$typeAttributeClass] = $attr;

        $this->log->debug('Registering extension type '.$typeAttributeClass);
    }

    /**
     * Checks if a type attribute is registered.
     *
     * @param string $attributeClass The attribute class name.
     * @return bool
     */
    public function has(string $attributeClass): bool
    {
        return isset($this->types[$attributeClass]);
    }

    /**
     * Retrieves an extension type by attribute class.
     *
     * @param string $attributeClass The attribute class name.
     * @return ExtensionType
     * @throws TypeAttributeNotRegisteredException If the attribute is not registered.
     */
    public function get(string $attributeClass): ExtensionType
    {
        if (!isset($this->types[$attributeClass])) {
            throw new TypeAttributeNotRegisteredException($attributeClass);
        }
        return $this->types[$attributeClass];
    }

    /**
     * Checks if an interface is registered.
     *
     * @param string $interfaceClass The interface class name.
     * @return bool
     */
    public function hasInterface(string $interfaceClass): bool
    {
        return isset($this->interfaces[$interfaceClass]);
    }

    /**
     * Retrieves an extension type by interface name.
     *
     * @param string $interfaceClass The interface class name.
     * @return ExtensionType
     * @throws TypeInterfaceNotRegisteredException If the interface is not registered.
     */
    public function getByInterface(string $interfaceClass): ExtensionType
    {
        if (!isset($this->interfaces[$interfaceClass])) {
            throw new TypeInterfaceNotRegisteredException($interfaceClass);
        }
        return $this->types[$this->interfaces[$interfaceClass]];
    }

    /**
     * Retrieves an extension type by class name.
     *
     * @param string $className The class name.
     * @return ExtensionType
     * @throws NoMatchingExtensionTypeException If no matching extension type is found.
     */
    public function getByClass(string $className): ExtensionType
    {
        foreach (class_implements($className) as $interface) {
            if ($this->hasInterface($interface)) {
                return $this->getByInterface($interface);
            }
        }
        if (!isset($reflection)) $reflection = new ReflectionClass($className);
        $attrs = $reflection->getAttributes();
        foreach ($attrs as $attr) {
            if ($this->has($attr->getName())) {
                return $this->get($attr->getName());
            }
        }
        throw new NoMatchingExtensionTypeException($className);
    }
}