<?php

namespace Neuron\Extensibility;

use Attribute;
use Neuron\Extensibility\Exceptions\ExtensionInterfaceMissingException;

/**
 * Defines an extension type with metadata and constraints.
 *
 * @package Neuron\Extensibility
 */
#[Attribute(Attribute::TARGET_CLASS)]
class ExtensionType
{

    /** @var bool Indicates if the extension should be treated as a singleton */
    private bool $singleton;

    /** @var bool Indicates if preloading is allowed for this type */
    private bool $preloadingAllowed;

    /** @var string|null The required interface for the extension type */
    private ?string $requiredInterface;

    /**
     * Constructs an ExtensionType attribute.
     *
     * @param bool $singleton Whether the extension type is a singleton.
     * @param bool $allowPreloading Whether preloading is allowed (only applicable with a singleton!)
     * @param string|null $requireInterface The required interface for the extension type.
     * @throws ExtensionInterfaceMissingException If the required interface does not exist.
     */
    public function __construct(bool $singleton = true, bool $allowPreloading = false, ?string $requireInterface = null)
    {
        $this->singleton = $singleton;
        $this->preloadingAllowed = $singleton && $allowPreloading;
        $this->requiredInterface = $requireInterface;

        if ($this->hasRequiredInterface() && !interface_exists($this->requiredInterface)) {
            throw new ExtensionInterfaceMissingException($this->requiredInterface);
        }
    }

    /**
     * Checks if the extension type is a singleton.
     *
     * @return bool
     */
    public function isSingleton(): bool
    {
        return $this->singleton;
    }

    /**
     * Checks if preloading is allowed for this extension type.
     *
     * @return bool
     */
    public function isPreloadedAllowed(): bool
    {
        return $this->preloadingAllowed;
    }

    /**
     * Checks if the extension type has a required interface.
     *
     * @return bool
     */
    public function hasRequiredInterface(): bool
    {
        return $this->requiredInterface !== null;
    }

    /**
     * Gets the required interface for the extension type.
     *
     * @return string|null
     */
    public function getRequiredInterface(): ?string
    {
        return $this->requiredInterface;
    }
}