<?php

namespace Tests\Unit\Integration;

use IntegrationClientInterface;
use IntegrationClientRegistry;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class IntegrationClientRegistryTest extends TestCase
{
    private function registry(): IntegrationClientRegistry
    {
        return new IntegrationClientRegistry();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_discovers_at_least_one_provider_on_construction(): void
    {
        /* Arrange */

        /* Act */
        $providers = $this->registry()->all();

        /* Assert */
        $this->assertIsArray($providers);
        $this->assertNotEmpty($providers);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_auto_discovers_the_qonto_provider(): void
    {
        /* Arrange */

        /* Act */
        $providers = $this->registry()->all();

        /* Assert */
        $this->assertArrayHasKey('qonto', $providers);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_auto_discovers_the_superpdp_provider(): void
    {
        /* Arrange */

        /* Act */
        $providers = $this->registry()->all();

        /* Assert */
        $this->assertArrayHasKey('superpdp', $providers);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_auto_discovers_the_letspeppol_provider(): void
    {
        /* Arrange */

        /* Act */
        $providers = $this->registry()->all();

        /* Assert */
        $this->assertArrayHasKey('letspeppol', $providers);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_registers_only_classes_that_implement_the_provider_interface(): void
    {
        /* Arrange */
        $providers = $this->registry()->all();

        /* Act */
        foreach ($providers as $code => $className) {
        /* Assert */
            $this->assertTrue(
                is_subclass_of($className, IntegrationClientInterface::class),
                "Provider '{$code}' ({$className}) must implement IntegrationClientInterface"
            );
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_a_provider_instance_for_a_known_code(): void
    {
        /* Arrange */
        $registry = $this->registry();

        /* Act */
        $provider = $registry->getClient('qonto');

        /* Assert */
        $this->assertInstanceOf(IntegrationClientInterface::class, $provider);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_a_new_instance_on_each_get_provider_call(): void
    {
        /* Arrange */
        $registry = $this->registry();

        /* Act */
        $a = $registry->getClient('qonto');
        $b = $registry->getClient('qonto');

        /* Assert */
        $this->assertNotSame($a, $b);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_for_an_unknown_provider_code(): void
    {
        /* Arrange */
        $registry = $this->registry();

        /* Act */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unknown e-invoicing provider: nonexistent');

        /* Assert */
        $registry->getClient('nonexistent');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_requires_every_discovered_provider_to_return_a_non_empty_code(): void
    {
        /* Arrange */
        $providers = $this->registry()->all();

        /* Act */
        foreach ($providers as $code => $className) {
        /* Assert */
            $this->assertNotEmpty(
                $className::clientCode(),
                "Provider class {$className} must return a non-empty clientCode()"
            );
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_requires_every_discovered_provider_to_return_a_non_empty_name(): void
    {
        /* Arrange */
        $providers = $this->registry()->all();

        /* Act */
        foreach ($providers as $code => $className) {
        /* Assert */
            $this->assertNotEmpty(
                $className::clientName(),
                "Provider class {$className} must return a non-empty clientName()"
            );
        }
    }
}
