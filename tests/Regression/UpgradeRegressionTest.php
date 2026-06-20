<?php

namespace Tests\Regression;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

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
class UpgradeRegressionTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_produces_unchanged_output_for_the_invoices_index_after_an_upgrade(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/invoices/status/all');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseMatchesSnapshot($response, 'upgrade__invoices_index');
    }

    #[Test]
    public function it_produces_unchanged_output_for_the_clients_index_after_an_upgrade(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/clients/status/active');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseMatchesSnapshot($response, 'upgrade__clients_index');
    }

    #[Test]
    public function it_produces_unchanged_output_for_the_payments_index_after_an_upgrade(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/payments');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseMatchesSnapshot($response, 'upgrade__payments_index');
    }

    #[Test]
    public function it_produces_unchanged_output_for_the_quotes_index_after_an_upgrade(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/quotes/status/all');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseMatchesSnapshot($response, 'upgrade__quotes_index');
    }

    #[Test]
    public function it_produces_unchanged_output_for_the_products_index_after_an_upgrade(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/products');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseMatchesSnapshot($response, 'upgrade__products_index');
    }

    #[Test]
    public function it_produces_unchanged_output_for_the_dashboard_after_an_upgrade(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/dashboard');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseMatchesSnapshot($response, 'upgrade__dashboard');
    }

    #[Test]
    public function it_detects_a_loader_regression_if_namespaced_models_stop_binding(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/clients/status/active');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);

        self::assertStringNotContainsString(
            'Undefined property',
            $response->body(),
            'An Undefined property warning in the clients page suggests a model failed to bind — '
            . 'check MY_Loader::loadNamespacedClass().'
        );

        self::assertStringNotContainsString(
            'Call to a member function',
            $response->body(),
            'A "Call to a member function" error suggests a model or service was not loaded correctly.'
        );
    }
}
