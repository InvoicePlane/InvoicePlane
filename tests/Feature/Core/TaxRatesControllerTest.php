<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Tax_Rates controller — application/modules/tax_rates/controllers/Tax_rates.php.
 *
 * Required fields (Mdl_Tax_Rates::validation_rules): tax_rate_name, tax_rate_percent.
 * Absorbs the former TaxRatesServiceTest and Issue1694TaxRatesDeleteCsrfTest.
 */
#[Group('tax_rates')]
#[CoversClass(\Tax_Rates::class)]
class TaxRatesControllerTest extends AbstractTestCase
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
    public function it_lists_every_tax_rate(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_tax_rates', ['tax_rate_name' => 'Reduced VAT 9', 'tax_rate_percent' => '9.00']);
        $this->databaseInsert('ip_tax_rates', ['tax_rate_name' => 'Standard VAT 21', 'tax_rate_percent' => '21.00']);

        /* Act */
        $response = $this->get('/tax_rates');

        /* Assert */
        $this->assertResponseBodyContains($response, 'Reduced VAT 9');
        $this->assertResponseBodyContains($response, 'Standard VAT 21');
    }

    // -------------------------------------------------------------------------
    // Create — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_a_tax_rate(): void
    {
        /**
         * POST /tax_rates/form
         * {
         *   "tax_rate_name": "Standard VAT",
         *   "tax_rate_percent": "21.00",
         *   "btn_submit": "1"
         * }.
         */
        /* Arrange */

        /* Act */
        $response = $this->post('/tax_rates/form', [
            'tax_rate_name'    => 'Standard VAT',
            'tax_rate_percent' => '21.00',
            'btn_submit'       => '1',
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'tax_rates');
        $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_name' => 'Standard VAT', 'tax_rate_percent' => '21.00']);
        $this->assertDatabaseCount('ip_tax_rates', 1);
    }

    // -------------------------------------------------------------------------
    // Create — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_tax_rate_name(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/tax_rates/form', [
            'tax_rate_name'    => '',
            'tax_rate_percent' => '42.42',
            'btn_submit'       => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseMissing('ip_tax_rates', ['tax_rate_percent' => '42.42']);
        $this->assertDatabaseCount('ip_tax_rates', 0);
    }

    #[Test]
    public function it_fails_to_create_without_tax_rate_percent(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/tax_rates/form', [
            'tax_rate_name'    => 'Incomplete VAT',
            'tax_rate_percent' => '',
            'btn_submit'       => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseMissing('ip_tax_rates', ['tax_rate_name' => 'Incomplete VAT']);
        $this->assertDatabaseCount('ip_tax_rates', 0);
    }

    // -------------------------------------------------------------------------
    // Update — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_edit_form_for_the_requested_tax_rate_only(): void
    {
        /* Arrange */
        $target = $this->databaseInsert('ip_tax_rates', ['tax_rate_name' => 'Editable VAT', 'tax_rate_percent' => '9.00']);
        $this->databaseInsert('ip_tax_rates', ['tax_rate_name' => 'Other VAT', 'tax_rate_percent' => '3.00']);

        /* Act */
        $response = $this->get('/tax_rates/form/' . $target);

        /* Assert */
        $this->assertResponseBodyContains($response, 'Editable VAT');
        $this->assertResponseBodyNotContains($response, 'Other VAT');
    }

    #[Test]
    public function it_updates_a_tax_rate(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_tax_rates', ['tax_rate_name' => 'Original VAT', 'tax_rate_percent' => '9.00']);

        /* Act */
        $response = $this->post('/tax_rates/form/' . $id, [
            'tax_rate_name'    => 'Renamed VAT',
            'tax_rate_percent' => '15.00',
            'btn_submit'       => '1',
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'tax_rates');
        $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_id' => $id, 'tax_rate_name' => 'Renamed VAT', 'tax_rate_percent' => '15.00']);
        $this->assertDatabaseMissing('ip_tax_rates', ['tax_rate_name' => 'Original VAT']);
    }

    // -------------------------------------------------------------------------
    // Update — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_update_without_tax_rate_name(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_tax_rates', ['tax_rate_name' => 'Will Not Change', 'tax_rate_percent' => '9.00']);

        /* Act */
        $response = $this->post('/tax_rates/form/' . $id, [
            'tax_rate_name'    => '',
            'tax_rate_percent' => '21.00',
            'btn_submit'       => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_id' => $id, 'tax_rate_name' => 'Will Not Change', 'tax_rate_percent' => '9.00']);
    }

    #[Test]
    public function it_fails_to_update_without_tax_rate_percent(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_tax_rates', ['tax_rate_name' => 'Percent Kept', 'tax_rate_percent' => '7.00']);

        /* Act */
        $response = $this->post('/tax_rates/form/' . $id, [
            'tax_rate_name'    => 'Percent Kept',
            'tax_rate_percent' => '',
            'btn_submit'       => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_id' => $id, 'tax_rate_percent' => '7.00']);
    }

    // -------------------------------------------------------------------------
    // Delete — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_tax_rate(): void
    {
        /* Arrange */
        $id   = $this->databaseInsert('ip_tax_rates', ['tax_rate_name' => 'Deletable VAT', 'tax_rate_percent' => '5.00']);
        $keep = $this->databaseInsert('ip_tax_rates', ['tax_rate_name' => 'Kept VAT', 'tax_rate_percent' => '5.00']);

        /* Act */
        $response = $this->post('/tax_rates/delete/' . $id, []);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'tax_rates');
        $this->assertDatabaseMissing('ip_tax_rates', ['tax_rate_id' => $id]);
        $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_id' => $keep]);
    }

    // -------------------------------------------------------------------------
    // Delete — CSRF regression (#1694)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_still_deletes_a_tax_rate_when_csrf_protection_is_on_and_the_token_is_valid(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->databaseInsert('ip_tax_rates', ['tax_rate_name' => 'CSRF VAT', 'tax_rate_percent' => '10.00']);

        /* Act */
        $response = $this->postWithValidCsrfToken('/tax_rates/delete/' . $id);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'tax_rates');
        $this->assertDatabaseMissing('ip_tax_rates', ['tax_rate_id' => $id]);
    }

    #[Test]
    public function it_does_not_delete_a_tax_rate_when_the_csrf_token_is_missing(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->databaseInsert('ip_tax_rates', ['tax_rate_name' => 'CSRF VAT Kept', 'tax_rate_percent' => '10.00']);

        /* Act */
        $response = $this->postWithoutCsrfToken('/tax_rates/delete/' . $id);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less delete must not reach the controller.');
        $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_id' => $id, 'tax_rate_name' => 'CSRF VAT Kept']);
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login_and_leaks_no_tax_rate(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_tax_rates', ['tax_rate_name' => 'Secret VAT', 'tax_rate_percent' => '1.00']);
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/tax_rates');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
        $this->assertResponseBodyNotContains($response, 'Secret VAT');
    }
}
