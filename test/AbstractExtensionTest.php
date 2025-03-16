<?php

namespace NeuronTests\Extensibility;

use Neuron\Extensibility\AbstractExtension;
use PHPUnit\Framework\TestCase;

/**
 * Test case for the AbstractExtension class.
 */
class AbstractExtensionTest extends TestCase
{
    /**
     * Tests that metadata is stored and retrieved correctly.
     */
    public function testMetadataStorage(): void
    {
        $extension = new class(
            name: "TestExtension",
            version: "2.5.1",
            description: "A sample extension.",
            dependencies: ["DepA", "DepB"],
            preloaded: true,
            singletonParameters: ["param" => "value"]
        ) extends AbstractExtension {};

        $this->assertEquals("TestExtension", $extension->getName());
        $this->assertEquals("2.5.1", $extension->getVersion());
        $this->assertEquals("A sample extension.", $extension->getDescription());
        $this->assertEquals(["DepA", "DepB"], $extension->getDependencies());
        $this->assertTrue($extension->isPreloaded());
        $this->assertEquals(["param" => "value"], $extension->getSingletonParameters());
    }

    /**
     * Tests default constructor values.
     */
    public function testDefaultValues(): void
    {
        $extension = new class extends AbstractExtension {};

        $this->assertSame('', $extension->getName());
        $this->assertSame('1.0', $extension->getVersion());
        $this->assertSame('', $extension->getDescription());
        $this->assertSame([], $extension->getDependencies());
        $this->assertFalse($extension->isPreloaded());
        $this->assertSame([], $extension->getSingletonParameters());
    }
}
