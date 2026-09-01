<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Integrations module (e-invoicing / PDP provider admin) — the read-side HTTP
 * surface of application/modules/integrations/controllers/*.
 *
 * Since the last version of this test the module was split into per-concern
 * controllers, all still reachable under the same /integrations/* prefix:
 *   GET /integrations/settings        -> Settings::index   (provider table)
 *   GET /integrations/providers       -> Integrations::providers (JSON registry)
 *   GET /integrations/events          -> Events::index
 *   GET /integrations/incoming        -> Incoming::index
 *   GET /integrations/history/{id}    -> Integrations::history
 * All extend Admin_Controller, so every route redirects a guest to login.
 *
 * This coverage was deleted on prep/v180 in c4802ab9 ("remove einvoice
 * provider artifacts from prep") — a tests-only cleanup after an e-invoicing
 * merge was reverted. The provider *libraries* went; the module controllers
 * did not, so the deletion of their smoke test was collateral damage. Nothing
 * restored it until now.
 */
#[Group('integrations')]
class IntegrationsControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // Guest access — every route redirects to login
    // -------------------------------------------------------------------------

    /**
     * @return iterable<string, array{0: string}>
     */
    public static function guestRouteProvider(): iterable
    {
        yield 'settings' => ['/integrations/settings'];
        yield 'providers' => ['/integrations/providers'];
        yield 'events' => ['/integrations/events'];
        yield 'incoming' => ['/integrations/incoming'];
        yield 'history' => ['/integrations/history/1'];
    }

    // -------------------------------------------------------------------------
    // Settings — provider list
    // -------------------------------------------------------------------------

    #[Test]
    public function it_shows_a_configured_provider_on_the_settings_page(): void
    {
        /* Arrange */
        $this->seedProvider(['merchant_type' => 'superpdp', 'label' => 'My SuperPDP', 'enabled' => 0]);

        /* Act */
        $response = $this->get('/integrations/settings');

        /* Assert */
        self::assertFalse($response->isRedirect(), 'An authenticated admin must reach the settings page.');
        $this->assertResponseBodyContains($response, 'My SuperPDP');
    }

    // -------------------------------------------------------------------------
    // Providers — filesystem-driven JSON registry
    // -------------------------------------------------------------------------

    #[Test]
    public function it_lists_the_known_providers_as_json(): void
    {
        /* Arrange */
        /* (the registry is discovered from disk — no seeding needed) */

        /* Act */
        $response = $this->get('/integrations/providers');

        /* Assert */
        $this->assertResponseBodyContains($response, 'superpdp');
        $this->assertResponseBodyContains($response, 'qonto');
        $this->assertResponseBodyContains($response, 'letspeppol');
    }

    // -------------------------------------------------------------------------
    // Events / Incoming — sync dashboards
    // -------------------------------------------------------------------------

    #[Test]
    public function it_shows_an_enabled_provider_on_the_events_page(): void
    {
        /* Arrange */
        $this->seedProvider(['merchant_type' => 'superpdp', 'label' => 'My Events Provider', 'enabled' => 1]);

        /* Act */
        $response = $this->get('/integrations/events');

        /* Assert */
        self::assertFalse($response->isRedirect(), 'An authenticated admin must reach the events page.');
        $this->assertResponseBodyContains($response, 'My Events Provider');
    }

    #[Test]
    public function it_shows_an_enabled_provider_on_the_incoming_page(): void
    {
        /* Arrange */
        $this->seedProvider(['merchant_type' => 'superpdp', 'label' => 'My Incoming Provider', 'enabled' => 1]);

        /* Act */
        $response = $this->get('/integrations/incoming');

        /* Assert */
        self::assertFalse($response->isRedirect(), 'An authenticated admin must reach the incoming page.');
        $this->assertResponseBodyContains($response, 'My Incoming Provider');
    }

    // -------------------------------------------------------------------------
    // History — transmission log for one invoice
    // -------------------------------------------------------------------------

    #[Test]
    public function it_shows_the_outbound_transmission_history_for_an_invoice(): void
    {
        /* Arrange */
        $clientId         = $this->seedClient(['client_name' => 'History Client']);
        $invoiceId        = $this->seedInvoice($clientId);
        $merchantClientId = $this->seedProvider(['merchant_type' => 'superpdp', 'label' => 'History Provider', 'enabled' => 1]);
        $this->databaseInsert('ip_merchant_responses', [
            'invoice_id'                  => $invoiceId,
            'merchant_client_id'          => $merchantClientId,
            'merchant_response_date'      => date('Y-m-d'),
            'merchant_response_driver'    => 'superpdp',
            'merchant_response'           => 'accepted',
            'merchant_response_reference' => 'REF-HISTORY-001',
            'direction'                   => 'out',
            'record_type'                 => 'outbound_status',
            'status'                      => 'accepted',
            'created_at'                  => date('Y-m-d H:i:s'),
        ]);

        /* Act */
        $response = $this->get('/integrations/history/' . $invoiceId);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'An authenticated admin must reach the history page.');
        $this->assertResponseBodyContains($response, 'REF-HISTORY-001');
    }

    #[Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('guestRouteProvider')]
    public function it_redirects_a_guest_away_from_every_integrations_route(string $uri): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get($uri);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [%s] must redirect to login. Got [%d].', $uri, $response->statusCode())
        );
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function seedProvider(array $overrides = []): int
    {
        return $this->databaseInsert('ip_merchant_clients', $overrides + [
            'merchant_type' => 'superpdp',
            'label'         => 'Seeded Provider',
            'enabled'       => 0,
            'auth_type'     => 'oauth2',
            'settings_json' => '{}',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
    }
}
