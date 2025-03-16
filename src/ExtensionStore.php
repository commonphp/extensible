<?php

namespace Neuron\Extensibility;

use Neuron\Extensibility\Exceptions\ExtensionSingletonException;
use Neuron\Extensibility\Exceptions\ExtensionNotLoadedException;
use Neuron\Extensibility\Exceptions\MissingDependenciesException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

/**
 * Manages the instantiation and lifecycle of extensions.
 *
 * Handles singleton enforcement, dependency resolution, and provides
 * a unified interface for retrieving extension instances.
 *
 * @package Neuron\Extensibility
 */
class ExtensionStore implements ExtensionsInterface
{
    /** @var ExtensionTypeRegistry Registry for extension types */
    public readonly ExtensionTypeRegistry $typeRegistry;

    /** @var ExtensionRegistry Registry for extensions */
    public readonly ExtensionRegistry $registry;

    /** @var InstantiatorInterface The instantiator */
    private InstantiatorInterface $instantiator;

    /** @var LoggerInterface The logger */
    private LoggerInterface $log;

    /** @var EventDispatcherInterface The event dispatcher */
    private EventDispatcherInterface $eventDispatcher;

    /** @var array<string, object> Instantiated singleton extensions */
    private array $extensions = [];

    /** @var array<string, bool> A list of classes which have been validated */
    private array $validatedDependencies = [];

    /**
     * Constructs an ExtensionStore.
     *
     * @param InstantiatorInterface $instantiator The instantiator instance.
     * @param LoggerInterface $log The logger instance.
     * @param EventDispatcherInterface $eventDispatcher The event dispatcher.
     */
    public function __construct(InstantiatorInterface $instantiator, LoggerInterface $log, EventDispatcherInterface $eventDispatcher)
    {
        $this->typeRegistry = new ExtensionTypeRegistry($log);
        $this->registry = new ExtensionRegistry($this, $log);
        $this->instantiator = $instantiator;
        $this->log = $log;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritDoc
     */
    public function getRegistry(): ExtensionRegistry
    {
        return $this->registry;
    }

    /**
     * @inheritDoc
     */
    public function getTypeRegistry(): ExtensionTypeRegistry
    {
        return $this->typeRegistry;
    }

    /**
     * @inheritDoc
     */
    public function has(string $className): bool
    {
        return $this->registry->has($className);
    }

    /**
     * Retrieves an extension from the registry.
     *
     * @param string $className The extension class name.
     * @return Extension
     * @throws ExtensionNotLoadedException If the extension is not loaded.
     */
    private function getExtension(string $className): Extension
    {
        if (!$this->has($className)) {
            throw new ExtensionNotLoadedException($className);
        }
        return $this->registry->get($className);
    }

    /**
     * Instantiates a new extension instance.
     *
     * @param Extension $extension The extension object.
     * @param array $parameters The instantiation parameters.
     * @return object The instantiated extension.
     * @throws MissingDependenciesException If required dependencies are missing.
     */
    private function createExtension(Extension $extension, array $parameters): object
    {
        $className = $extension->getClassName();

        if (!isset($this->validatedDependencies[$className])) {
            $missingDependencies = [];
            foreach ($extension->getExtensionDependencies() as $dependency) {
                if (!$this->registry->has($dependency)) {
                    $missingDependencies[] = $dependency;
                }
            }
            if (!empty($missingDependencies)) {
                throw new MissingDependenciesException($className, $missingDependencies);
            }
            $this->validatedDependencies[$className] = true;
        }

        $instance = $this->instantiator->instantiate($className, $parameters);
        $this->eventDispatcher->dispatch(new ExtensionInstantiatedEvent($extension, $instance));
        return $instance;
    }

    /**
     * @inheritDoc
     * @throws ExtensionNotLoadedException
     * @throws ExtensionSingletonException
     * @throws MissingDependenciesException
     */
    public function create(string $className, array $parameters = []): object
    {
        $extension = $this->getExtension($className);

        if ($extension->isSingleton()) {
            $this->log->error('Cannot create an instance of '.$className.' because it is a singleton and must be managed using get(...)');
            throw new ExtensionSingletonException($className, false);
        }

        $this->log->debug('Creating an instance of '.$className);

        return $this->createExtension($extension, $parameters);
    }

    /**
     * @inheritDoc
     * @throws ExtensionNotLoadedException
     * @throws ExtensionSingletonException
     * @throws MissingDependenciesException
     */
    public function get(string $className): object
    {

        if (!isset($this->extensions[$className])) {
            $extension = $this->getExtension($className);

            if (!$extension->isSingleton()) {
                $this->log->error('Cannot use '.$className.' as a singleton so it must be created using create(...)');
                throw new ExtensionSingletonException($className, true);
            }

            $instance = $this->createExtension($extension, $this->registry->getInstantiationParameters($extension->getTypeClass()));

            $this->log->debug('Creating an singleton instance of '.$className);

            $this->extensions[$className] = $instance;
        }

        return $this->extensions[$className];
    }
}