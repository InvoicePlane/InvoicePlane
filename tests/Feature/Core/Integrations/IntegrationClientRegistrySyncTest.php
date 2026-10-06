<?php

namespace Tests\Feature\Core\Integrations;

use IntegrationClientRegistry;
use IntegrationSettingsCipher;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\UsesCodeIgniterModels;

/**
 * syncDatabaseProviders() seeds ip_merchant_clients with one row per discovered provider. A new
 * provider must arrive disabled with its defaults encrypted, and an operator's existing row must
 * never be touched.
 */
#[Group('integration')]
final class IntegrationClientRegistrySyncTest extends AbstractTestCase
{
    use UsesCodeIgniterModels;

    private const FIXTURES = __DIR__ . '/../../../Fixtures/integrations/providers';

    protected function tearDown(): void
    {
        $this->tearDownCodeIgniter();
        $this->databaseDelete('ip_merchant_clients', ['merchant_type' => 'demopdp']);
        parent::tearDown();
    }

    #[Test]
    public function it_registers_a_newly_discovered_provider_disabled_with_encrypted_defaults(): void
    {
        /* Arrange */
        $this->databaseDelete('ip_merchant_clients', ['merchant_type' => 'demopdp']);
        $this->bootCodeIgniter();

        /* Act */
        (new IntegrationClientRegistry(self::FIXTURES))->syncDatabaseProviders();

        /* Assert */
        $row = $this->databaseFetchOne('ip_merchant_clients', ['merchant_type' => 'demopdp']);
        self::assertNotNull($row, 'A discovered provider must get a ledger row.');
        self::assertSame('Demo PDP', $row['label']);
        self::assertSame('bearer', $row['auth_type']);
        self::assertSame(0, (int) $row['enabled'], 'A new provider must never start enabled.');
        self::assertNotSame('', (string) $row['created_at']);

        $cipher = new IntegrationSettingsCipher();
        self::assertTrue($cipher->isEncrypted($row['settings_json']), 'Settings must be stored encrypted.');
        self::assertSame(
            ['access_token' => '', 'api_base_url' => 'https://api.demo-pdp.test', 'invoice_endpoint' => '/v1/invoices', 'invoice_status_endpoint' => '/v1/invoices/{id}'],
            $cipher->decrypt($row['settings_json'], 'demopdp')
        );
    }

    #[Test]
    public function it_leaves_an_existing_provider_row_untouched(): void
    {
        /* Arrange */
        $this->databaseDelete('ip_merchant_clients', ['merchant_type' => 'demopdp']);
        $this->databaseInsert('ip_merchant_clients', [
            'merchant_type' => 'demopdp',
            'label'         => 'Operator label',
            'enabled'       => 1,
            'auth_type'     => 'bearer',
            'settings_json' => (new IntegrationSettingsCipher())->encrypt(['access_token' => 'operator-token'], 'demopdp'),
            'created_at'    => '2026-01-01 00:00:00',
            'updated_at'    => '2026-01-01 00:00:00',
        ]);
        $idBefore = (int) $this->databaseFetchOne('ip_merchant_clients', ['merchant_type' => 'demopdp'])['id'];
        $this->bootCodeIgniter();

        /* Act */
        (new IntegrationClientRegistry(self::FIXTURES))->syncDatabaseProviders();

        /* Assert */
        $row = $this->databaseFetchOne('ip_merchant_clients', ['merchant_type' => 'demopdp']);
        self::assertSame($idBefore, (int) $row['id']);
        self::assertSame('Operator label', $row['label']);
        self::assertSame(1, (int) $row['enabled']);
        self::assertSame('2026-01-01 00:00:00', $row['created_at']);
        self::assertSame(1, $this->databaseCount('ip_merchant_clients', ['merchant_type' => 'demopdp']), 'No duplicate row may be created.');
        self::assertSame(['access_token' => 'operator-token'], (new IntegrationSettingsCipher())->decrypt($row['settings_json'], 'demopdp'));
    }
}
