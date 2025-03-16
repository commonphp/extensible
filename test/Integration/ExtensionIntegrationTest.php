<?php

namespace NeuronTests\Extensibility\Integration;

use Attribute;
use Neuron\Extensibility\AbstractExtension;
use Neuron\Extensibility\Exceptions\ExtensionSingletonException;
use Neuron\Extensibility\ExtensionStore;
use Neuron\Extensibility\ExtensionType;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

#[Attribute]
#[ExtensionType(singleton: false)]
class IntegrationTestType extends AbstractExtension {}

#[IntegrationTestType]
class IntegrationTestExtensionA extends AbstractExtension {}

#[IntegrationTestType]
class IntegrationTestExtensionB extends AbstractExtension {}

class ExtensionIntegrationTest extends TestCase
{
    private ExtensionStore $store;

    protected function setUp(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $instantiator = new class implements \Neuron\Extensibility\InstantiatorInterface {
            public function instantiate(string $className, array $parameters): object
            {
                return new $className(...$parameters);
            }
        };

        $this->store = new ExtensionStore($instantiator, $logger, $eventDispatcher);
        $this->store->typeRegistry->register(IntegrationTestType::class);
        $this->store->registry->register(IntegrationTestType::class, IntegrationTestExtensionA::class);
        $this->store->registry->register(IntegrationTestType::class, IntegrationTestExtensionB::class);
    }

    public function testBasicIntegration(): void
    {
        $this->expectException(ExtensionSingletonException::class);
        $instanceA = $this->store->get(IntegrationTestExtensionA::class);
        $this->assertInstanceOf(IntegrationTestExtensionA::class, $instanceA);

        $instanceB = $this->store->create(IntegrationTestExtensionB::class);
        $this->assertInstanceOf(IntegrationTestExtensionB::class, $instanceB);
    }
}
