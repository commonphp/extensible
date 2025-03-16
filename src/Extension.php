<?php

/** @noinspection PhpUnused */

namespace Neuron\Extensibility;

/**
 * Represents an extension instance with type and metadata.
 */
final class Extension
{
    /** @var string Fully qualified class name of the extension */
    private string $className;

    /** @var AbstractExtension The actual extension instance */
    private AbstractExtension $extension;

    /** @var ExtensionType The extension type */
    private ExtensionType $type;

    /**
     * Constructs an extension.
     *
     * @param string $className Fully qualified class name.
     * @param AbstractExtension $extension The extension instance.
     * @param ExtensionType $type The type of the extension.
     */
    public function __construct(string $className, AbstractExtension $extension, ExtensionType $type)
    {
        $this->className = $className;
        $this->extension = $extension;
        $this->type = $type;
    }

    /**
     * Gets the fully qualified class name of the extension.
     *
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * Gets the name of the extension.
     *
     * @return string
     */
    public function getExtensionName(): string
    {
        return $this->extension->getName();
    }

    /**
     * Gets the version of the extension.
     *
     * @return string
     */
    public function getExtensionVersion(): string
    {
        return $this->extension->getVersion();
    }

    /**
     * Gets the description of the extension.
     *
     * @return string
     */
    public function getExtensionDescription(): string
    {
        return $this->extension->getDescription();
    }

    /**
     * Gets the dependencies of the extension.
     *
     * @return array
     */
    public function getExtensionDependencies(): array
    {
        return $this->extension->getDependencies();
    }

    /**
     * Checks if the extension is preloaded.
     *
     * @return bool
     */
    public function isExtensionPreloaded(): bool
    {
        return $this->type->isPreloadedAllowed() && $this->extension->isPreloaded();
    }

    /**
     * Checks if the extension is a singleton.
     *
     * @return bool
     */
    public function isSingleton(): bool
    {
        return $this->type->isSingleton();
    }

    /**
     * Get the default parameters if the extension is a singleton
     *
     * @return array
     */
    public function getSingletonParameters(): array
    {
        return ($this->isSingleton() ? $this->extension->getSingletonParameters() : []);
    }

    /**
     * Check if the extension requires an interface
     *
     * @return bool
     */
    public function hasRequiredInterface(): bool
    {
        return $this->type->hasRequiredInterface();
    }

    /**
     * Gets the required interface for the extension.
     *
     * @return string|null
     */
    public function getRequiredInterface(): ?string
    {
        return $this->type->getRequiredInterface();
    }

    /**
     * Gets the class type of the extension.
     *
     * @return string
     */
    public function getTypeClass(): string
    {
        return get_class($this->type);
    }
}