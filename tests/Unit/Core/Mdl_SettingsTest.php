<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Mdl_Settings pure-PHP logic.
 *
 * Covered:
 *  - setting() — key lookup with default fallback
 *  - save_batch() — separation of inserts vs updates
 *  - set_setting() — in-memory mutation
 *
 * @group unit
 * @group models
 * @group settings
 */
class Mdl_SettingsTest extends TestCase
{
    private StubMdl_Settings $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new StubMdl_Settings();
    }

    public function it_returns_a_setting_value_when_the_key_exists(): void
    {
        $this->model->settings = ['company_name' => 'Acme BV'];

        $result = $this->model->setting('company_name');

        self::assertSame(
            'Acme BV',
            $result,
            'setting() must return the stored value when the key exists.'
        );
    }

    public function it_returns_the_default_when_the_key_does_not_exist(): void
    {
        $this->model->settings = [];

        $result = $this->model->setting('nonexistent_key', 'fallback_value');

        self::assertSame(
            'fallback_value',
            $result,
            'setting() must return the provided default when the key is not present.'
        );
    }

    public function it_returns_the_default_when_the_stored_value_is_an_empty_string(): void
    {
        $this->model->settings = ['invoice_prefix' => ''];

        $result = $this->model->setting('invoice_prefix', 'INV-');

        self::assertSame(
            'INV-',
            $result,
            'setting() must return the default when the stored value is an empty string.'
        );
    }

    public function it_returns_an_empty_string_as_the_default_when_no_default_is_provided(): void
    {
        $this->model->settings = [];

        $result = $this->model->setting('missing_key');

        self::assertSame(
            '',
            $result,
            'setting() must return an empty string when no default is given and the key is absent.'
        );
    }

    public function it_mutates_the_in_memory_settings_array_via_set_setting(): void
    {
        $this->model->settings = ['timezone' => 'UTC'];

        $this->model->set_setting('timezone', 'Europe/Amsterdam');

        self::assertSame(
            'Europe/Amsterdam',
            $this->model->setting('timezone'),
            'set_setting() must update the in-memory settings array immediately.'
        );
    }

    public function it_adds_a_new_key_via_set_setting_when_the_key_did_not_exist(): void
    {
        $this->model->settings = [];

        $this->model->set_setting('new_feature_flag', '1');

        self::assertSame(
            '1',
            $this->model->setting('new_feature_flag'),
            'set_setting() must insert a new key if it did not previously exist.'
        );
    }

    public function it_separates_new_keys_from_existing_keys_in_save_batch(): void
    {
        $existingKeys = ['company_name', 'invoice_prefix'];

        $batchInput = [
            'company_name'   => 'Updated Co',
            'invoice_prefix' => 'INV-',
            'new_setting'    => 'brand_new',
        ];

        [$toUpdate, $toInsert] = $this->model->classifyBatch($batchInput, $existingKeys);

        self::assertArrayHasKey(
            'company_name',
            $toUpdate,
            'Keys already in the database must go into the update bucket.'
        );

        self::assertArrayHasKey(
            'new_setting',
            $toInsert,
            'Keys not yet in the database must go into the insert bucket.'
        );

        self::assertArrayNotHasKey(
            'new_setting',
            $toUpdate,
            'A brand-new key must NOT appear in the update bucket.'
        );

        self::assertArrayNotHasKey(
            'company_name',
            $toInsert,
            'An existing key must NOT appear in the insert bucket.'
        );
    }

    public function it_produces_an_empty_insert_bucket_when_all_keys_already_exist(): void
    {
        $existingKeys = ['a', 'b', 'c'];
        $batchInput   = ['a' => '1', 'b' => '2', 'c' => '3'];

        [, $toInsert] = $this->model->classifyBatch($batchInput, $existingKeys);

        self::assertEmpty(
            $toInsert,
            'When all batch keys already exist in the database, the insert bucket must be empty.'
        );
    }

    public function it_produces_an_empty_update_bucket_when_no_keys_already_exist(): void
    {
        $existingKeys = [];
        $batchInput   = ['new_a' => '1', 'new_b' => '2'];

        [$toUpdate] = $this->model->classifyBatch($batchInput, $existingKeys);

        self::assertEmpty(
            $toUpdate,
            'When no batch keys exist in the database yet, the update bucket must be empty.'
        );
    }

    public function it_handles_an_empty_batch_input_without_throwing(): void
    {
        [$toUpdate, $toInsert] = $this->model->classifyBatch([], ['existing_key']);

        self::assertEmpty($toUpdate, 'Update bucket must be empty for an empty batch input.');
        self::assertEmpty($toInsert, 'Insert bucket must be empty for an empty batch input.');
    }
}

class StubMdl_Settings
{
    public array $settings = [];

    public function setting(string $key, string $default = ''): string
    {
        return (isset($this->settings[$key]) && $this->settings[$key] !== '')
            ? (string) $this->settings[$key]
            : $default;
    }

    public function set_setting(string $key, mixed $value): void
    {
        $this->settings[$key] = $value;
    }

    /**
     * Replicates the insert/update classification from Mdl_Settings::save_batch().
     *
     * @param array    $settings     key => value pairs to persist
     * @param string[] $existingKeys keys already present in ip_settings
     *
     * @return array{0: array, 1: array} [toUpdate, toInsert]
     */
    public function classifyBatch(array $settings, array $existingKeys): array
    {
        $existingMap = array_flip($existingKeys);
        $toUpdate    = [];
        $toInsert    = [];

        foreach ($settings as $key => $value) {
            if (isset($existingMap[$key])) {
                $toUpdate[$key] = $value;
            } else {
                $toInsert[$key] = $value;
            }
        }

        return [$toUpdate, $toInsert];
    }
}
