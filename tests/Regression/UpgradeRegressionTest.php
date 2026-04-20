<?php

declare(strict_types=1);

namespace Tests\Regression;

use Tests\Hmvc\BaseHmvcTestCase;
use Tests\Hmvc\HmvcResponse;

/**
 * Snapshot-based regression suite.
 *
 * Purpose: detect silent regressions in rendered HTML output when:
 *   - CodeIgniter 3 is patched (pocketarc/codeigniter version bump)
 *   - InvoicePlane itself is upgraded to 1.7.2
 *   - MY_Router or MY_Loader are modified
 *   - MX (Wiredesign HMVC) is updated
 *
 * How it works:
 *   1. First run creates snapshot files in tests/__snapshots__/.
 *   2. Subsequent runs diff response bodies against the stored snapshots.
 *   3. If the diff is intentional, delete the .snap file and re-run.
 *
 * These tests are deliberately broad (full page output) because their
 * job is to notice ANY change, not to assert specific business rules.
 * Specific assertions live in the Feature/ tests.
 *
 * @group regression
 */
final class UpgradeRegressionTest extends BaseHmvcTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    public function it_produces_unchanged_output_for_the_invoices_index_after_an_upgrade(): void
    {
        $response = $this->get('/invoices');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseMatchesSnapshot($response, 'upgrade__invoices_index');
    }

    public function it_produces_unchanged_output_for_the_clients_index_after_an_upgrade(): void
    {
        $response = $this->get('/clients');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseMatchesSnapshot($response, 'upgrade__clients_index');
    }

    public function it_produces_unchanged_output_for_the_payments_index_after_an_upgrade(): void
    {
        $response = $this->get('/payments');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseMatchesSnapshot($response, 'upgrade__payments_index');
    }

    public function it_produces_unchanged_output_for_the_integrations_index_after_an_upgrade(): void
    {
        $response = $this->get('/integrations');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseMatchesSnapshot($response, 'upgrade__integrations_index');
    }

    public function it_produces_unchanged_output_for_the_quotes_index_after_an_upgrade(): void
    {
        $response = $this->get('/quotes');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseMatchesSnapshot($response, 'upgrade__quotes_index');
    }

    public function it_produces_unchanged_output_for_the_products_index_after_an_upgrade(): void
    {
        $response = $this->get('/products');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseMatchesSnapshot($response, 'upgrade__products_index');
    }

    public function it_produces_unchanged_output_for_the_dashboard_after_an_upgrade(): void
    {
        $response = $this->get('/dashboard');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseMatchesSnapshot($response, 'upgrade__dashboard');
    }

    public function it_detects_a_routing_regression_if_mx_router_stops_resolving_integrations(): void
    {
        $response = $this->get('/integrations');

        $this->assertResponseStatusCode($response, 200);

        self::assertStringNotContainsString(
            'Unable to load your default controller',
            $response->body(),
            'MX router returned the CI3 default 404 page for /integrations — ' .
            'MY_Router::aliasPsr4Controller() or $moduleAliases may have regressed.'
        );

        self::assertStringNotContainsString(
            '404 Page Not Found',
            $response->body(),
            'The integrations route returned a 404 — check MY_Router and the module controller filename.'
        );
    }

    public function it_detects_a_loader_regression_if_namespaced_models_stop_binding(): void
    {
        $response = $this->get('/clients');

        $this->assertResponseStatusCode($response, 200);

        self::assertStringNotContainsString(
            'Undefined property',
            $response->body(),
            'An Undefined property warning in the clients page suggests a model failed to bind — ' .
            'check MY_Loader::loadNamespacedClass().'
        );

        self::assertStringNotContainsString(
            'Call to a member function',
            $response->body(),
            'A "Call to a member function" error suggests a model or service was not loaded correctly.'
        );
    }
}
