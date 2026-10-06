<?php

namespace Tests\Unit\Core\Integrations;

use IntegrationClientRegistry;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * The stored settings_json and the settings form of each provider are a persistence contract:
 * keys, defaults, field types and required/sensitive flags must not drift when provider code is
 * refactored. The fixture was recorded from the provider classes before they were moved onto the
 * shared base.
 */
#[Group('unit')]
final class ProviderSettingsContractTest extends TestCase
{
    /** @var array<string, array<string, mixed>> */
    private static array $contract;

    private IntegrationClientRegistry $registry;

    public static function setUpBeforeClass(): void
    {
        self::$contract = json_decode((string) file_get_contents(dirname(__DIR__, 3) . '/Fixtures/integrations/provider_settings.json'), true, 512, JSON_THROW_ON_ERROR);
    }

    protected function setUp(): void
    {
        $GLOBALS['unitCiInstance'] ??= new class () {
            public object $load;

            public function __construct()
            {
                $this->load = new class () {
                    public function helper(mixed $helpers): void {}
                };
            }
        };
        $this->registry = new IntegrationClientRegistry();
    }

    /** @return array<string, array{string}> */
    public static function providers(): array
    {
        return ['letspeppol' => ['letspeppol'], 'qonto' => ['qonto'], 'superpdp' => ['superpdp']];
    }

    #[Test]
    #[DataProvider('providers')]
    public function it_keeps_the_provider_name_and_auth_type(string $code): void
    {
        $class = $this->registry->all()[$code];

        self::assertSame(self::$contract[$code]['name'], $class::clientName());
        self::assertSame(self::$contract[$code]['auth_type'], $class::authType());
    }

    #[Test]
    #[DataProvider('providers')]
    public function it_keeps_the_default_settings(string $code): void
    {
        $class = $this->registry->all()[$code];

        self::assertSame(self::$contract[$code]['defaults'], $class::defaultSettings());
    }

    #[Test]
    #[DataProvider('providers')]
    public function it_keeps_the_settings_form_schema(string $code): void
    {
        $class = $this->registry->all()[$code];

        self::assertEquals(self::$contract[$code]['schema'], $class::settingsSchema());
    }
}
