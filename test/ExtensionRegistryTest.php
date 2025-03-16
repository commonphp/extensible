<?php

namespace NeuronTests\Extensibility;

use ArrayAccess;
use Attribute;
use Neuron\Extensibility\AbstractExtension;
use Neuron\Extensibility\ExtensionRegistry;
use Neuron\Extensibility\ExtensionStore;
use Neuron\Extensibility\Exceptions\ExtensionClassMissingException;
use Neuron\Extensibility\Exceptions\ExtensionInheritanceException;
use Neuron\Extensibility\ExtensionType;
use Neuron\Extensibility\InstantiatorInterface;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

#[ExtensionType]
#[Attribute]
class MyTypeForTest extends AbstractExtension {}

#[MyTypeForTest]
class MyExtensionForTest extends AbstractExtension {}

#[ExtensionType(requireInterface: ArrayAccess::class)]
#[Attribute]
class SomeTypeClass extends AbstractExtension
{}

#[SomeTypeClass]
class SomeExtension extends AbstractExtension
{}

/**
 * Tests for ExtensionRegistry
 */
class ExtensionRegistryTest extends TestCase
{
    private ExtensionRegistry $registry;
    private ExtensionStore $store;

    protected function setUp(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        // Create a partial, real store to hold typeRegistry
        $this->store = new ExtensionStore(
            instantiator: $this->createMock(InstantiatorInterface::class),
            log: $logger,
            eventDispatcher: $this->createMock(EventDispatcherInterface::class)
        );

        $this->registry = $this->store->registry; // real instance
        $this->store->typeRegistry->register(SomeTypeClass::class);

    }

    public function testRegisterNonexistentExtensionClassThrowsException(): void
    {
        $this->expectException(ExtensionClassMissingException::class);


        $this->registry->register(SomeTypeClass::class, 'ThisClassDoesNotExist');
    }

    public function testRegisterExtensionWithoutImplementingRequiredInterfaceThrowsException(): void
    {
        $this->expectException(ExtensionInheritanceException::class);

        $this->registry->register(SomeTypeClass::class, SomeExtension::class);
    }

    public function testRegisterAndCheckHas(): void
    {
        $this->store->typeRegistry->register(MyTypeForTest::class);

        $this->registry->register(MyTypeForTest::class, MyExtensionForTest::class);

        $this->assertTrue($this->registry->has(MyExtensionForTest::class));
        $this->assertNotNull($this->registry->get(MyExtensionForTest::class));
    }
}
