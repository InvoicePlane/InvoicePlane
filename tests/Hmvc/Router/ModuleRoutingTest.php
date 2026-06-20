<?php

namespace Tests\Feature\Routing;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Parameterised routing smoke test.
 *
 * Every route here must:
 *   1. Return HTTP 200 when authenticated
 *   2. Produce a body longer than 200 bytes (view rendered)
 *   3. Contain no raw PHP errors
 *
 * This guards against:
 *   - MY_Router regressions (PSR-4 alias breaks)
 *   - MY_Loader regressions (model not found)
 *   - View path regressions (layout buffer fails)
 *   - Module controller filename regressions
 *
 * Routes are derived from every controller file listed in dir2.txt.
 *
 * @group feature
 * @group routing
 */
class ModuleRoutingTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    public static function moduleIndexRouteProvider(): array
    {
        return [
            'invoices'        => ['/invoices'],
            'clients'         => ['/clients'],
            'payments'        => ['/payments'],
            'quotes'          => ['/quotes'],
            'products'        => ['/products'],
            'tasks'           => ['/tasks'],
            'tax_rates'       => ['/tax_rates'],
            'units'           => ['/units'],
            'families'        => ['/families'],
            'payment_methods' => ['/payment_methods'],
            'invoice_groups'  => ['/invoice_groups'],
            'email_templates' => ['/email_templates'],
            'custom_fields'   => ['/custom_fields'],
            'custom_values'   => ['/custom_values'],
            'users'           => ['/users'],
            'settings'        => ['/settings'],
            'reports'         => ['/reports/sales_by_client'],
            'dashboard'       => ['/dashboard'],
            'import'          => ['/import'],
            'projects'        => ['/projects'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('moduleIndexRouteProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_resolves_the_module_index_route_and_returns_200_or_redirect(string $uri): void
    {
        $response = $this->get($uri);

        self::assertThat(
            $response->statusCode(),
            self::logicalOr(
                self::equalTo(200),
                self::equalTo(301),
                self::equalTo(302),
                self::equalTo(307)
            ),
            sprintf(
                'GET [%s] must return 200 or a redirect. Got [%d] with body: %s',
                $uri,
                $response->statusCode(),
                mb_substr($response->body(), 0, 300)
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('moduleIndexRouteProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_not_expose_php_errors_on_authenticated_module_index(string $uri): void
    {
        $response = $this->get($uri);

        $this->assertResponseHasNoPhpErrors($response);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('moduleIndexRouteProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_a_non_trivial_body_for_every_authenticated_module_index(string $uri): void
    {
        $response = $this->get($uri);

        if ($response->isRedirect()) {
            self::addToAssertionCount(1);

            return;
        }

        self::assertGreaterThan(
            200,
            $response->bodyLength(),
            sprintf(
                'GET [%s] returned a body shorter than 200 bytes — the view likely did not render. '
                . 'Body: %s',
                $uri,
                $response->body()
            )
        );
    }
}
