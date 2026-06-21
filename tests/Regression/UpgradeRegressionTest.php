<?php

namespace Tests\Regression;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Upgrade regression suite.
 *
 * Purpose: detect silent regressions when:
 *   - CodeIgniter 3 is patched (pocketarc/codeigniter version bump)
 *   - InvoicePlane itself is upgraded to 1.7.2
 *   - MY_Router or MY_Loader are modified
 *   - MX (Wiredesign HMVC) is updated
 *
 */
#[Group('regression')]
class UpgradeRegressionTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_renders_the_invoices_index_without_errors(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/invoices/status/all');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseBodyContains($response, '<html');
    }

    #[Test]
    public function it_renders_the_clients_index_without_errors(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/clients/status/active');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseBodyContains($response, '<html');
    }

    #[Test]
    public function it_renders_the_payments_index_without_errors(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/payments');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseBodyContains($response, '<html');
    }

    #[Test]
    public function it_renders_the_quotes_index_without_errors(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/quotes/status/all');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseBodyContains($response, '<html');
    }

    #[Test]
    public function it_renders_the_products_index_without_errors(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/products');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseBodyContains($response, '<html');
    }

    #[Test]
    public function it_renders_the_dashboard_without_errors(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/dashboard');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseBodyContains($response, '<html');
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
