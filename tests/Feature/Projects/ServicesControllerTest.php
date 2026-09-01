<?php

namespace Tests\Feature\Projects;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Services controller — application/modules/services/controllers/Services.php.
 *
 * A service is a named line-item template that can also be pinned to a client
 * (ip_client_services). Required field (Mdl_Services::validation_rules):
 * service_name (also is_unique[ip_services.service_name]). Routes:
 *   list     GET  /services                       (Services::index)
 *   create   POST /services/form                  -> /services
 *   edit     POST /services/form/{id}             -> /services
 *   assign   POST /services/form_client/{client_id} -> /clients/form/{client_id}
 *   delete   POST /services/delete/{id}           -> /services
 * delete carries no ensure_valid_post_request(); it is guarded only by
 * Base_Controller's blanket GET-to-delete 404 gate.
 *
 * The services module was added by PR #1520 (feat/service-for-company) as a
 * pure code addition with no test of its own — this class is that missing
 * coverage, not a restoration.
 */
#[Group('services')]
class ServicesControllerTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    #[Test]
    public function it_lists_every_service(): void
    {
        /* Arrange */
        $this->seedService('On-site Installation');
        $this->seedService('Annual Maintenance');

        /* Act */
        $response = $this->get('/services');

        /* Assert */
        $this->assertResponseBodyContains($response, 'On-site Installation');
        $this->assertResponseBodyContains($response, 'Annual Maintenance');
    }

    // -------------------------------------------------------------------------
    // Create — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_a_service(): void
    {
        /**
         * POST /services/form
         * { "service_name": "Consulting Hour", "btn_submit": "1" }.
         */
        /* Arrange */

        /* Act */
        $response = $this->post('/services/form', [
            'service_name' => 'Consulting Hour',
            'btn_submit'   => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A successful create redirects back to the service list.');
        $this->assertDatabaseHas('ip_services', ['service_name' => 'Consulting Hour']);
    }

    // -------------------------------------------------------------------------
    // Create — validation
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_a_service_without_a_name(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/services/form', [
            'service_name' => '',
            'btn_submit'   => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseCount('ip_services', 0);
    }

    #[Test]
    public function it_fails_to_create_a_service_whose_name_is_already_taken(): void
    {
        /* Arrange */
        $this->seedService('Duplicate Service');

        /* Act */
        $response = $this->post('/services/form', [
            'service_name' => 'Duplicate Service',
            'btn_submit'   => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A non-unique name must re-render the form, not redirect.');
        $this->assertDatabaseCount('ip_services', 1);
    }

    // -------------------------------------------------------------------------
    // Update — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_updates_a_service(): void
    {
        /* Arrange */
        $id = $this->seedService('Original Name');

        /* Act */
        $response = $this->post('/services/form/' . $id, [
            'service_name' => 'Renamed Service',
            'btn_submit'   => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A successful update redirects back to the service list.');
        $this->assertDatabaseHas('ip_services', ['service_id' => $id, 'service_name' => 'Renamed Service']);
        $this->assertDatabaseMissing('ip_services', ['service_name' => 'Original Name']);
    }

    #[Test]
    public function it_fails_to_update_a_service_without_a_name(): void
    {
        /* Arrange */
        $id = $this->seedService('Keep This Name');

        /* Act */
        $response = $this->post('/services/form/' . $id, [
            'service_name' => '',
            'btn_submit'   => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_services', ['service_id' => $id, 'service_name' => 'Keep This Name']);
    }

    // -------------------------------------------------------------------------
    // Assign to a client (form_client)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_a_service_and_pins_it_to_a_client(): void
    {
        /**
         * POST /services/form_client/{client_id}
         * { "service_name": "Client Retainer", "btn_submit": "1" }.
         */
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Retainer Client']);

        /* Act */
        $response = $this->post('/services/form_client/' . $clientId, [
            'service_name' => 'Client Retainer',
            'btn_submit'   => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A successful assignment redirects back to the client form.');
        $serviceId = (int) $this->databaseFetchOne('ip_services', ['service_name' => 'Client Retainer'])['service_id'];
        $this->assertDatabaseHas('ip_client_services', ['client_id' => $clientId, 'service_id' => $serviceId]);
    }

    #[Test]
    public function it_404s_when_pinning_a_service_to_a_client_that_does_not_exist(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/services/form_client/999999', [
            'service_name' => 'Orphan Service',
            'btn_submit'   => '1',
        ]);

        /* Assert */
        self::assertSame(404, $response->statusCode(), 'An unknown client id must 404 before anything is written.');
        $this->assertDatabaseMissing('ip_services', ['service_name' => 'Orphan Service']);
    }

    // -------------------------------------------------------------------------
    // Downstream flow — services feed the invoice/quote service picker
    // -------------------------------------------------------------------------

    #[Test]
    public function it_resolves_a_tagged_invoices_service_name_in_the_filtered_invoice_table(): void
    {
        /* Arrange */
        // The cross-module flow: an invoice carries ip_invoices.service_id, and
        // filter/Ajax::filter_invoices resolves it to a name via
        // mdl_services->get_names_by_ids(); partial_invoice_table renders it,
        // but only when the enable_services setting is on.
        $this->databaseInsert('ip_settings', ['setting_key' => 'enable_services', 'setting_value' => '1']);
        $taggedServiceId = $this->seedService('Flowed Service Name');
        $clientId        = $this->seedClient(['client_name' => 'Service Flow Client']);
        $this->seedInvoice($clientId, ['invoice_number' => 'SVCFLOW-001', 'service_id' => $taggedServiceId]);

        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_invoices', ['filter_query' => 'SVCFLOW-001']);

        /* Assert */
        $this->assertResponseBodyContains($response, 'SVCFLOW-001');
        $this->assertResponseBodyContains($response, 'Flowed Service Name');
    }

    // -------------------------------------------------------------------------
    // Delete — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_service_and_its_client_links(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Linked Client']);
        $id       = $this->seedService('Deletable Service');
        $keepId   = $this->seedService('Kept Service');
        $this->databaseInsert('ip_client_services', ['client_id' => $clientId, 'service_id' => $id]);

        /* Act */
        $response = $this->post('/services/delete/' . $id, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A successful delete redirects back to the service list.');
        $this->assertDatabaseMissing('ip_services', ['service_id' => $id]);
        $this->assertDatabaseMissing('ip_client_services', ['service_id' => $id]);
        $this->assertDatabaseHas('ip_services', ['service_id' => $keepId]);
    }

    #[Test]
    public function it_does_not_delete_a_service_on_a_plain_get_request(): void
    {
        /* Arrange */
        // Base_Controller::__construct() 404s any GET whose URL contains
        // "delete" before the controller ever runs.
        $id = $this->seedService('GET Service Kept');

        /* Act */
        $response = $this->get('/services/delete/' . $id);

        /* Assert */
        self::assertSame(404, $response->statusCode(), 'The global GET-to-delete gate in Base_Controller must reject this before it is acted on.');
        $this->assertDatabaseHas('ip_services', ['service_id' => $id, 'service_name' => 'GET Service Kept']);
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login_and_leaks_no_service(): void
    {
        /* Arrange */
        $this->seedService('Secret Service Name');
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/services');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
        $this->assertResponseBodyNotContains($response, 'Secret Service Name');
    }

    private function seedService(string $name): int
    {
        return $this->databaseInsert('ip_services', ['service_name' => $name]);
    }
}
