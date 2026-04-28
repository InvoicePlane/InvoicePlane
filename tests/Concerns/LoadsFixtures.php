<?php

namespace Modules\Core\Testing\Traits;

/**
 * LoadsFixtures Trait.
 *
 * Provides a SOLID, DRY way to load test fixtures without duplicating
 * loadFixtures() methods across every test class.
 *
 * Usage:
 *   protected function fixtureTypes(): array
 *   {
 *       return ['users', 'clients', 'invoices'];
 *   }
 */
trait LoadsFixtures
{
    /**
     * Define which fixture types this test needs.
     *
     * Override in your test class to specify fixtures.
     *
     * @return array<string> List of fixture types (e.g., ['users', 'clients'])
     */
    protected function fixtureTypes(): array
    {
        return [];
    }

    /**
     * Define which specific fixtures to load for each type.
     *
     * Override to customize which fixtures are loaded.
     *
     * @return array<string, array<string>> Map of fixture type to keys
     */
    protected function fixtureKeys(): array
    {
        return [
            'users'           => ['admin', 'guest', 'inactive'],
            'clients'         => ['active_client', 'inactive_client'],
            'invoices'        => ['draft_invoice', 'sent_invoice', 'paid_invoice'],
            'quotes'          => ['draft_quote', 'sent_quote', 'approved_quote'],
            'projects'        => ['active_project', 'completed_project'],
            'products'        => ['product_1', 'product_2'],
            'families'        => ['family_1', 'family_2'],
            'units'           => ['unit_1', 'unit_2'],
            'payments'        => ['payment_1', 'payment_2'],
            'payment_methods' => ['payment_method_1', 'payment_method_2'],
            'tax_rates'       => ['tax_rate_1', 'tax_rate_2'],
            'tasks'           => ['task_1', 'task_2'],
            'custom_fields'   => ['invoice_text_field', 'client_dropdown_field', 'quote_textarea_field', 'user_checkbox_field'],
            'custom_values'   => ['industry_technology', 'industry_healthcare', 'industry_finance', 'industry_education'],
        ];
    }

    /**
     * Load fixtures into fake database.
     *
     * Called automatically by setUp() via loadFixtures().
     * This replaces the duplicated loadFixtures() method.
     */
    protected function loadAllFixtures(): void
    {
        $fixtureTypes = $this->fixtureTypes();
        $fixtureKeys  = $this->fixtureKeys();

        foreach ($fixtureTypes as $type) {
            $this->loadFixtureType($type, $fixtureKeys[$type] ?? []);
        }
    }

    /**
     * Load a specific fixture type.
     *
     * @param string        $type Fixture type (e.g., 'users', 'clients')
     * @param array<string> $keys Specific fixture keys to load
     */
    protected function loadFixtureType(string $type, array $keys): void
    {
        if (empty($keys)) {
            return;
        }

        $fixtures  = $this->fixtures->all($type);
        $tableName = $this->getTableNameForFixture($type);

        foreach ($keys as $key) {
            if (isset($fixtures[$key])) {
                $this->fakeDb->insert($tableName, $fixtures[$key]);
            }
        }
    }

    /**
     * Get database table name for fixture type.
     *
     * @param string $type Fixture type
     *
     * @return string Database table name
     */
    protected function getTableNameForFixture(string $type): string
    {
        $tableMap = [
            'users'           => 'ip_users',
            'clients'         => 'ip_clients',
            'invoices'        => 'ip_invoices',
            'quotes'          => 'ip_quotes',
            'projects'        => 'ip_projects',
            'products'        => 'ip_products',
            'families'        => 'ip_families',
            'units'           => 'ip_units',
            'payments'        => 'ip_payments',
            'payment_methods' => 'ip_payment_methods',
            'tax_rates'       => 'ip_tax_rates',
            'tasks'           => 'ip_tasks',
            'custom_fields'   => 'ip_custom_fields',
            'custom_values'   => 'ip_custom_values',
        ];

        return $tableMap[$type] ?? 'ip_' . $type;
    }
}
