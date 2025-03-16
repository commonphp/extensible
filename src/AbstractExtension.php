<?php

namespace Neuron\Extensibility;

/**
 * Base class for defining an extension with metadata.
 * Provides properties for name, version, description, dependencies, and singleton parameters.
 */
abstract class AbstractExtension
{
    /** @var string The name of the extension */
    private string $name;

    /** @var string The version of the extension */
    private string $version;

    /** @var string A brief description of the extension */
    private string $description;

    /** @var array A list of dependencies required by the extension */
    private array $dependencies;

    /** @var bool Indicates whether the extension should be preloaded */
    private bool $preloaded;

    /** @var array Parameters for singleton instances */
    private array $singletonParameters;

    /**
     * Constructs an extension instance.
     *
     * @param string $name Name of the extension.
     * @param string $version Version of the extension.
     * @param string $description Description of the extension.
     * @param array $dependencies Dependencies required by the extension.
     * @param bool $preloaded Whether the extension is preloaded.
     * @param array $singletonParameters Parameters for singleton instances.
     */
    public function __construct(string $name = '', string $version = '1.0', string $description = '', array $dependencies = [], bool $preloaded = false, array $singletonParameters = [])
    {
        $this->name = $name;
        $this->version = $version;
        $this->description = $description;
        $this->dependencies = $dependencies;
        $this->preloaded = $preloaded;
        $this->singletonParameters = $singletonParameters;
    }

    /**
     * Gets the name of the extension.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets the version of the extension.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Gets the description of the extension.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Gets the dependencies required by the extension.
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * Checks if the extension is preloaded.
     *
     * @return bool
     */
    public function isPreloaded(): bool
    {
        return $this->preloaded;
    }

    /**
     * Gets the singleton parameters.
     *
     * @return array
     */
    public function getSingletonParameters(): array
    {
        return $this->singletonParameters;
    }
}