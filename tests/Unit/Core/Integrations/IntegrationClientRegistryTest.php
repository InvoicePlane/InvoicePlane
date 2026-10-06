<?php

namespace Tests\Unit\Core;

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

        // IntegrationClientRegistry::__construct() calls
        // get_instance()->load->helper('file_security'). In the isolated unit
        // context there is no CI super-object, so stand up the minimal seam the
        // constructor touches (UnitCodeIgniter.php's get_instance() returns this
        // global by reference).
        $GLOBALS['unitCiInstance'] ??= new class () {
            public object $load;

            public function __construct()
            {
                $this->load = new class () {
                    /**
                     * @param string|string[] $helpers
                     */
                    public function helper($helpers): void {}
                };
            }
        };

        $this->registry = new IntegrationClientRegistry();
    }

    #[Test]
    public function it_discovers_the_bundled_providers(): void
    {
        /* Act */
        $providers = $this->registry->all();

        /* Assert */
        self::assertSame(QontoClient::class, $providers['qonto']);
        self::assertSame(SuperPdpClient::class, $providers['superpdp']);
        self::assertSame(LetsPeppolClient::class, $providers['letspeppol']);
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
        foreach ($this->registry->all() as $clientClass) {
            self::assertTrue(
                is_subclass_of($clientClass, IntegrationClientInterface::class),
                "{$clientClass} must implement IntegrationClientInterface"
            );
        }
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
        self::assertNotSame(
            $this->registry->getClient('qonto'),
            $this->registry->getClient('qonto')
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

    #[Test]
    public function it_scans_the_providers_directory_only_once_per_process(): void
    {
        /* Arrange */
        $before = IntegrationClientRegistry::scanCount();

        /* Act */
        new IntegrationClientRegistry();
        new IntegrationClientRegistry();

        /* Assert */
        self::assertSame($before, IntegrationClientRegistry::scanCount(), 'Constructing the registry again must reuse the cached map.');
    }

    #[Test]
    public function it_rescans_after_the_cache_is_flushed(): void
    {
        /* Arrange */
        IntegrationClientRegistry::flushCache();

        /* Act */
        $registry = new IntegrationClientRegistry();

        /* Assert */
        self::assertSame(1, IntegrationClientRegistry::scanCount());
        self::assertArrayHasKey('qonto', $registry->all());
    }

    #[Test]
    public function it_registers_a_provider_dropped_into_a_directory_and_skips_helper_classes(): void
    {
        /* Arrange */
        $dir = $this->providerDirectory([
            'RegFixtureAlphaClient.php'  => $this->providerSource('RegFixtureAlphaClient', 'reg-alpha'),
            'RegFixtureHelperClient.php' => '<?php class RegFixtureHelperClient {}',
        ]);

        /* Act */
        $providers = (new IntegrationClientRegistry($dir))->all();

        /* Assert */
        self::assertSame(['reg-alpha' => 'RegFixtureAlphaClient'], $providers);
    }

    #[Test]
    public function it_registers_a_provider_that_lives_in_its_own_subdirectory(): void
    {
        /* Arrange */
        $dir = $this->providerDirectory([
            'RegFixtureNested/RegFixtureNestedClient.php' => $this->providerSource('RegFixtureNestedClient', 'reg-nested'),
        ]);

        /* Act */
        $providers = (new IntegrationClientRegistry($dir))->all();

        /* Assert */
        self::assertSame(['reg-nested' => 'RegFixtureNestedClient'], $providers);
    }

    #[Test]
    public function it_rejects_two_providers_with_the_same_code(): void
    {
        /* Arrange */
        $dir = $this->providerDirectory([
            'RegFixtureOneClient.php' => $this->providerSource('RegFixtureOneClient', 'reg-dup'),
            'RegFixtureTwoClient.php' => $this->providerSource('RegFixtureTwoClient', 'reg-dup'),
        ]);

        /* Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Duplicate e-invoicing provider code: reg-dup');

        /* Act */
        new IntegrationClientRegistry($dir);
    }

    #[Test]
    public function it_rejects_a_provider_code_with_unsafe_characters(): void
    {
        /* Arrange */
        $dir = $this->providerDirectory([
            'RegFixtureBadClient.php' => $this->providerSource('RegFixtureBadClient', '../etc'),
        ]);

        /* Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid e-invoicing provider code.');

        /* Act */
        new IntegrationClientRegistry($dir);
    }

    #[Test]
    public function it_loads_the_helper_classes_that_sit_beside_a_nested_provider(): void
    {
        /* Arrange */
        $dir = $this->providerDirectory([
            'RegCompanion/RegCompanionClient.php' => '<?php class RegCompanionClient implements IntegrationClientInterface {'
                . ' public static function clientCode(): string { return RegCompanionHelper::CODE . RegCompanionEndpoint::SUFFIX; }'
                . substr($this->providerSource('X', 'x'), strpos($this->providerSource('X', 'x'), 'public static function clientName')),
            'RegCompanion/RegCompanionHelper.php'             => '<?php class RegCompanionHelper { public const CODE = "reg-comp"; }',
            'RegCompanion/Endpoints/RegCompanionEndpoint.php' => '<?php class RegCompanionEndpoint { public const SUFFIX = "-ep"; }',
        ]);

        /* Act */
        $providers = (new IntegrationClientRegistry($dir))->all();

        /* Assert */
        self::assertSame(['reg-comp-ep' => 'RegCompanionClient'], $providers);
    }

    #[Test]
    public function it_does_not_load_unrelated_siblings_of_a_top_level_provider(): void
    {
        /* Arrange */
        $dir = $this->providerDirectory([
            'RegTopClient.php' => $this->providerSource('RegTopClient', 'reg-top'),
            'RegUnrelated.php' => '<?php define("REG_UNRELATED_LOADED", true);',
        ]);

        /* Act */
        (new IntegrationClientRegistry($dir))->all();

        /* Assert */
        self::assertFalse(defined('REG_UNRELATED_LOADED'), 'Only a provider file itself (and its own folder) may be loaded.');
    }

    #[Test]
    public function it_refuses_a_provider_with_an_unsupported_auth_type_when_building_its_settings(): void
    {
        /* Arrange */
        $dir      = $this->providerDirectory(['RegAuthClient.php' => str_replace('return "none"', 'return "kerberos"', $this->providerSource('RegAuthClient', 'reg-auth'))]);
        $registry = new IntegrationClientRegistry($dir);

        /* Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unsupported provider authentication type: reg-auth');

        /* Act */
        $registry->getSettingsDefinition('reg-auth');
    }

    #[Test]
    public function it_exposes_auth_type_defaults_and_a_normalized_schema_for_a_supported_provider(): void
    {
        /* Arrange */
        $registry = new IntegrationClientRegistry(dirname(__DIR__, 3) . '/Fixtures/integrations/providers');

        /* Act */
        $definition = $registry->getSettingsDefinition('demopdp');

        /* Assert */
        self::assertSame('bearer', $definition['auth_type']);
        self::assertSame('https://api.demo-pdp.test', $definition['defaults']['api_base_url']);
        self::assertArrayHasKey('access_token', $definition['schema']);
    }

    /**
     * @param array<string, string> $files
     */
    private function providerDirectory(array $files): string
    {
        $dir = sys_get_temp_dir() . '/ip-registry-' . bin2hex(random_bytes(4));
        foreach ($files as $relative => $source) {
            $path = $dir . '/' . $relative;
            if ( ! is_dir(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }
            file_put_contents($path, $source);
        }

        return $dir;
    }

    private function providerSource(string $class, string $code): string
    {
        return '<?php class ' . $class . ' implements IntegrationClientInterface {'
            . ' public static function clientCode(): string { return ' . var_export($code, true) . '; }'
            . ' public static function clientName(): string { return "Fixture"; }'
            . ' public static function authType(): string { return "none"; }'
            . ' public static function defaultSettings(): array { return []; }'
            . ' public static function settingsSchema(): array { return []; }'
            . ' public function authenticate(array $settings): bool { return true; }'
            . ' public function sendInvoice(string $documentPath, array $metadata): array { return []; }'
            . ' public function getInvoiceStatus(string $externalId): array { return []; }'
            . ' public function receiveInvoices(array $filters = []): array { return []; }'
            . ' public function downloadInvoiceDocument(array $invoice): array { return []; }'
            . ' public function getInvoiceEvents(array $filters = []): array { return []; }'
            . ' public function buildInvoicePayload($invoice, array $items, array $metadata = []): array { return []; }'
            . ' public function fetchToken(array $settings): string { return ""; }'
            . ' public function ping(array $settings): array { return []; }'
            . ' }';
    }
}
