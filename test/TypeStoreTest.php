<?php

namespace NeuronTests\Extensibility;

use Attribute;
use Neuron\Extensibility\AbstractExtension;
use Neuron\Extensibility\ExtensionStore;
use Neuron\Extensibility\Exceptions\ExtensionNotLoadedException;
use Neuron\Extensibility\Exceptions\ExtensionSingletonException;
use Neuron\Extensibility\Exceptions\MissingDependenciesException;
use Neuron\Extensibility\ExtensionType;
use Neuron\Extensibility\InstantiatorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

#[ExtensionType(singleton: true)]
#[Attribute]
class SingletonTypeTest extends AbstractExtension
{}

#[SingletonTypeTest]
class SingletonExtensionTest
{}

#[ExtensionType(singleton: false)]
#[Attribute]
class NonSingletonTypeTest extends AbstractExtension
{}

#[NonSingletonTypeTest]
class NonSingletonExtensionTest
{}


// This extension depends on SomeDependencyClass which we won't register
#[ExtensionType(singleton: false)]
#[Attribute]
class DependentTypeTest extends AbstractExtension
{
    public function __construct() {
        parent::__construct(
            name: "",
            version: "1.0",
            description: "",
            dependencies: ["SomeDependencyClass"]
        );
    }
}

#[DependentTypeTest]
class DependentExtensionTest
{}

/**
 * Tests for ExtensionStore
 */
class TypeStoreTest extends TestCase
{
    private ExtensionStore $store;

    protected function setUp(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $instantiator = new class implements InstantiatorInterface {
            public function instantiate(string $className, array $parameters): object
            {
                return new $className(...$parameters);
            }
        };

        $this->store = new ExtensionStore($instantiator, $logger, $eventDispatcher);
    }

    public function testGetUnregisteredExtensionThrowsException(): void
    {
        $this->expectException(ExtensionNotLoadedException::class);
        $this->store->get('FakeUnregisteredClass');
    }

    public function testCreateUnregisteredExtensionThrowsException(): void
    {
        $this->expectException(ExtensionNotLoadedException::class);
        $this->store->create('FakeUnregisteredClass');
    }

    public function testCreateSingletonExtensionThrowsException(): void
    {

        // Register the extension type
        $this->store->typeRegistry->register(SingletonTypeTest::class);

        // Register the extension
        $this->store->registry->register(SingletonTypeTest::class, SingletonExtensionTest::class);

        // Attempt to create while it is a singleton
        $this->expectException(ExtensionSingletonException::class);
        $this->store->create(SingletonExtensionTest::class);
    }

    public function testGetReturnsSingletonInstance(): void
    {

        // Register
        $this->store->typeRegistry->register(SingletonTypeTest::class);
        $this->store->registry->register(SingletonTypeTest::class, SingletonExtensionTest::class);

        $instance1 = $this->store->get(SingletonExtensionTest::class);
        $instance2 = $this->store->get(SingletonExtensionTest::class);
        $this->assertSame($instance1, $instance2, "Expected a single (singleton) instance.");
    }

    public function testCreateReturnsDifferentInstances(): void
    {

        // Register
        $this->store->typeRegistry->register(NonSingletonTypeTest::class);
        $this->store->registry->register(NonSingletonTypeTest::class, NonSingletonExtensionTest::class);

        $inst1 = $this->store->create(NonSingletonExtensionTest::class);
        $inst2 = $this->store->create(NonSingletonExtensionTest::class);

        $this->assertNotSame($inst1, $inst2, "Expected two distinct instances for non-singletons.");
    }

    public function testMissingDependenciesThrowsException(): void
    {

        $this->store->typeRegistry->register(DependentTypeTest::class);
        $this->store->registry->register(DependentTypeTest::class, DependentExtensionTest::class);

        $this->expectException(MissingDependenciesException::class);
        $this->store->create(DependentExtensionTest::class);
    }
}
