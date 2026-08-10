<?php

namespace Tests\Unit\Integration;

use IntegrationClientInterface;
use IntegrationClientRegistry;
use LetsPeppolClient;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use QontoClient;
use RuntimeException;
use SuperPdpClient;

/**
 * Unit tests for IntegrationClientRegistry.
 *
 * The registry discovers e-invoicing providers by scanning the providers
 * directory and indexing them by their clientCode(). These tests pin down the
 * discovery result and the getClient() contract so a new provider file (or a
 * renamed one) can't silently drop out of the registry.
 */
#[Group('unit')]
class IntegrationClientRegistryTest extends TestCase
{
    private IntegrationClientRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry = new IntegrationClientRegistry();
    }

    #[Test]
    public function it_discovers_the_bundled_providers(): void
    {
        /* Arrange */
        $expectedProviders = [
            'qonto'      => QontoClient::class,
            'superpdp'   => SuperPdpClient::class,
            'letspeppol' => LetsPeppolClient::class,
        ];

        /* Act */
        $providers = $this->registry->all();

        /* Assert */
        foreach ($expectedProviders as $code => $providerClass) {
            self::assertSame($providerClass, $providers[$code]);
        }
    }

    #[Test]
    public function it_indexes_every_provider_under_its_own_client_code(): void
    {
        foreach ($this->registry->all() as $clientCode => $clientClass) {
            self::assertSame(
                $clientCode,
                $clientClass::clientCode(),
                "Provider {$clientClass} is registered under the wrong code"
            );
        }
    }

    #[Test]
    public function it_only_registers_integration_client_implementations(): void
    {
        /* Arrange */
        $providers = $this->registry->all();

        /* Act */
        $invalidProviders = array_filter(
            $providers,
            static fn (string $clientClass): bool => ! is_subclass_of($clientClass, IntegrationClientInterface::class)
        );

        /* Assert */
        self::assertSame(
            [],
            $invalidProviders,
            'Every registered provider must implement IntegrationClientInterface.'
        );
    }

    #[Test]
    public function it_resolves_a_provider_instance_by_client_code(): void
    {
        /* Act */
        $client = $this->registry->getClient('qonto');

        /* Assert */
        self::assertInstanceOf(QontoClient::class, $client);
        self::assertInstanceOf(IntegrationClientInterface::class, $client);
    }

    #[Test]
    public function it_returns_a_fresh_instance_on_each_resolution(): void
    {
        /* Arrange */
        $clientCode = 'qonto';

        /* Act */
        $firstClient  = $this->registry->getClient($clientCode);
        $secondClient = $this->registry->getClient($clientCode);

        /* Assert */
        self::assertNotSame(
            $firstClient,
            $secondClient
        );
    }

    #[Test]
    public function it_throws_for_an_unknown_provider_code(): void
    {
        /* Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unknown e-invoicing provider: does-not-exist');

        /* Act */
        $this->registry->getClient('does-not-exist');
    }
}
