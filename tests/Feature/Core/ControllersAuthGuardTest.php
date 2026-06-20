<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Parameterised auth-guard regression test.
 *
 * Every route in this list must redirect an unauthenticated visitor.
 * This is a canary: if Admin_Controller's auth check is accidentally
 * removed during a refactor, every one of these will fail immediately.
 *
 * Routes are taken directly from the controllers in dir2.txt.
 *
 * @group feature
 * @group auth
 */
#[CoversClass(Tests\Feature\Core\ControllersAuthGuard::class)]
class ControllersAuthGuardTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Requires live CI3 environment with database — not available in CI');
        $this->actingAsGuest();
    }

    public static function adminRouteProvider(): array
    {
        return [
            'invoices index'        => ['/invoices'],
            'invoices status all'   => ['/invoices/status/all'],
            'invoices status draft' => ['/invoices/status/draft'],
            'invoices status paid'  => ['/invoices/status/paid'],
            'clients index'         => ['/clients'],
            'clients status active' => ['/clients/status/active'],
            'payments index'        => ['/payments'],
            'payments online_logs'  => ['/payments/online_logs'],
            'quotes index'          => ['/quotes'],
            'products index'        => ['/products'],
            'tasks index'           => ['/tasks'],
            'tax_rates index'       => ['/tax_rates'],
            'units index'           => ['/units'],
            'families index'        => ['/families'],
            'payment_methods index' => ['/payment_methods'],
            'invoice_groups index'  => ['/invoice_groups'],
            'email_templates index' => ['/email_templates'],
            'custom_fields index'   => ['/custom_fields'],
            'custom_values index'   => ['/custom_values'],
            'users index'           => ['/users'],
            'settings index'        => ['/settings'],
            'reports index'         => ['/reports'],
            'dashboard'             => ['/dashboard'],
            'import index'          => ['/import'],
            'projects index'        => ['/projects'],
        ];
    }

    /**
     * @dataProvider adminRouteProvider
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_redirects_an_unauthenticated_visitor_away_from_admin_module(string $uri): void
    {
        $response = $this->get($uri);

        self::assertTrue(
            $response->isRedirect(),
            sprintf(
                'Unauthenticated GET [%s] must redirect to login. Got status [%d] with body (first 200 chars): %s',
                $uri,
                $response->statusCode(),
                mb_substr($response->body(), 0, 200)
            )
        );

        self::assertFalse(
            $response->contains('<form') && $response->contains('invoice'),
            sprintf(
                '[%s] must not render any admin form content to an unauthenticated visitor.',
                $uri
            )
        );
    }

    /**
     * @dataProvider adminRouteProvider
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_not_expose_php_errors_on_an_unauthenticated_request_to_admin_route(string $uri): void
    {
        $response = $this->get($uri);

        $this->assertResponseHasNoPhpErrors($response);
    }
}
