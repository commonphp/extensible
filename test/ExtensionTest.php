<?php

namespace NeuronTests\Extensibility;

use Neuron\Extensibility\AbstractExtension;
use Neuron\Extensibility\Extension;
use Neuron\Extensibility\ExtensionType;
use PHPUnit\Framework\TestCase;

/**
 * Test case for the Extension wrapper class.
 */
class ExtensionTest extends TestCase
{
    public function testExtensionMetadata(): void
    {
        // Mock extension
        $extensionClass = new class(
            name: "Mock Extension",
            version: "1.0",
            description: "Testing Extension wrapper.",
            dependencies: [],
            preloaded: true
        ) extends AbstractExtension {
        };

        // Mock extension type with no required interface
        $extType = new ExtensionType(
            singleton: true,
            allowPreloading: true,
            requireInterface: null
        );

        $extension = new Extension(
            className: 'MockExtensionClass',
            extension: $extensionClass,
            type: $extType
        );

        $this->assertEquals('MockExtensionClass', $extension->getClassName());
        $this->assertEquals("Mock Extension", $extension->getExtensionName());
        $this->assertEquals("1.0", $extension->getExtensionVersion());
        $this->assertEquals("Testing Extension wrapper.", $extension->getExtensionDescription());
        $this->assertEquals([], $extension->getExtensionDependencies());
        $this->assertTrue($extension->isExtensionPreloaded());
        $this->assertTrue($extension->isSingleton());
    }

    public function testRequiredInterface(): void
    {
        // Mock extension
        $extensionClass = new class extends AbstractExtension {
        };

        // Mock extension type requiring some interface
        $extType = new ExtensionType(
            singleton: false,
            allowPreloading: false,
            requireInterface: \ArrayAccess::class
        );

        $extension = new Extension(
            className: 'MockExtensionClass',
            extension: $extensionClass,
            type: $extType
        );

        $this->assertTrue($extension->hasRequiredInterface());
        $this->assertSame(\ArrayAccess::class, $extension->getRequiredInterface());
    }
}
