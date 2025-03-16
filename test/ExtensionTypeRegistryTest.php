<?php

namespace NeuronTests\Extensibility;

use Neuron\Extensibility\AbstractExtension;
use Neuron\Extensibility\ExtensionType;
use Neuron\Extensibility\ExtensionTypeRegistry;
use Neuron\Extensibility\Exceptions\DuplicateTypeException;
use Neuron\Extensibility\Exceptions\TypeAttributeMissingException;
use Neuron\Extensibility\Exceptions\TypeClassMissingException;
use Neuron\Extensibility\Exceptions\TypeInheritanceException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Tests for ExtensionTypeRegistry
 */
class ExtensionTypeRegistryTest extends TestCase
{
    private ExtensionTypeRegistry $registry;

    protected function setUp(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $this->registry = new ExtensionTypeRegistry($loggerMock);
    }

    public function testRegisterValidType(): void
    {
        $class = new class extends AbstractExtension {
        };

        // Create a dynamic class name with an ExtensionType attribute
        eval('
            #[\Neuron\Extensibility\ExtensionType]
            class ValidTypeTest extends \Neuron\Extensibility\AbstractExtension {}
        ');

        $this->registry->register('ValidTypeTest');
        $this->assertTrue($this->registry->has('ValidTypeTest'));
    }

    public function testRegisterDuplicateTypeThrowsException(): void
    {
        $this->expectException(DuplicateTypeException::class);

        eval('
            #[\Neuron\Extensibility\ExtensionType]
            class DuplicateTypeTest extends \Neuron\Extensibility\AbstractExtension {}
        ');
        $this->registry->register('DuplicateTypeTest');
        $this->registry->register('DuplicateTypeTest'); // Should throw
    }

    public function testRegisterNonexistentClassThrowsException(): void
    {
        $this->expectException(TypeClassMissingException::class);
        $this->registry->register('ClassThatDoesNotExistXYZ');
    }

    public function testRegisterInvalidTypeThrowsException(): void
    {
        $this->expectException(TypeInheritanceException::class);

        eval('class InvalidTypeTest {}'); // does not extend AbstractExtension
        $this->registry->register('InvalidTypeTest');
    }

    public function testRegisterMissingAttributeThrowsException(): void
    {
        $this->expectException(TypeAttributeMissingException::class);

        eval('class MissingAttributeTest extends \Neuron\Extensibility\AbstractExtension {}');
        $this->registry->register('MissingAttributeTest');
    }
}
