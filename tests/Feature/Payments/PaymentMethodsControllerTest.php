<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Payment_Methods controller — application/modules/payment_methods/controllers/Payment_methods.php.
 *
 * Required fields (Mdl_Payment_Methods::validation_rules): payment_method_name.
 * Absorbs Issue1694PaymentMethodsDeleteCsrfTest.
 */
#[Group('payment_methods')]
#[CoversClass(\Payment_Methods::class)]
class PaymentMethodsControllerTest extends AbstractTestCase
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
    public function it_lists_every_payment_method(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_payment_methods', ['payment_method_name' => 'Bank Transfer']);
        $this->databaseInsert('ip_payment_methods', ['payment_method_name' => 'Cash On Delivery']);

        /* Act */
        $response = $this->get('/payment_methods');

        /* Assert */
        $this->assertResponseBodyContains($response, 'Bank Transfer');
        $this->assertResponseBodyContains($response, 'Cash On Delivery');
    }

    // -------------------------------------------------------------------------
    // Create — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_a_payment_method(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/payment_methods/form', [
            'payment_method_name' => 'Cheque',
            'is_update'           => '0',
            'btn_submit'          => '1',
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'payment_methods');
        $this->assertDatabaseHas('ip_payment_methods', ['payment_method_name' => 'Cheque']);
        $this->assertDatabaseCount('ip_payment_methods', 1);
    }

    // -------------------------------------------------------------------------
    // Create — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_payment_method_name(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/payment_methods/form', [
            'payment_method_name' => '',
            'is_update'           => '0',
            'btn_submit'          => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseCount('ip_payment_methods', 0);
    }

    // -------------------------------------------------------------------------
    // Update — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_edit_form_for_the_requested_payment_method_only(): void
    {
        /* Arrange */
        $target = $this->databaseInsert('ip_payment_methods', ['payment_method_name' => 'Editable Method']);
        $this->databaseInsert('ip_payment_methods', ['payment_method_name' => 'Other Method']);

        /* Act */
        $response = $this->get('/payment_methods/form/' . $target);

        /* Assert */
        $this->assertResponseBodyContains($response, 'Editable Method');
        $this->assertResponseBodyNotContains($response, 'Other Method');
    }

    #[Test]
    public function it_updates_a_payment_method(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_payment_methods', ['payment_method_name' => 'Original Method']);

        /* Act */
        $response = $this->post('/payment_methods/form/' . $id, [
            'payment_method_name' => 'Renamed Method',
            'is_update'           => '1',
            'btn_submit'          => '1',
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'payment_methods');
        $this->assertDatabaseHas('ip_payment_methods', ['payment_method_id' => $id, 'payment_method_name' => 'Renamed Method']);
        $this->assertDatabaseMissing('ip_payment_methods', ['payment_method_name' => 'Original Method']);
    }

    // -------------------------------------------------------------------------
    // Update — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_update_without_payment_method_name(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_payment_methods', ['payment_method_name' => 'Keep This Method']);

        /* Act */
        $response = $this->post('/payment_methods/form/' . $id, [
            'payment_method_name' => '',
            'is_update'           => '1',
            'btn_submit'          => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_payment_methods', ['payment_method_id' => $id, 'payment_method_name' => 'Keep This Method']);
    }

    // -------------------------------------------------------------------------
    // Delete — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_payment_method(): void
    {
        /* Arrange */
        $id   = $this->databaseInsert('ip_payment_methods', ['payment_method_name' => 'Deletable Method']);
        $keep = $this->databaseInsert('ip_payment_methods', ['payment_method_name' => 'Kept Method']);

        /* Act */
        $response = $this->post('/payment_methods/delete/' . $id, []);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'payment_methods');
        $this->assertDatabaseMissing('ip_payment_methods', ['payment_method_id' => $id]);
        $this->assertDatabaseHas('ip_payment_methods', ['payment_method_id' => $keep]);
    }

    // -------------------------------------------------------------------------
    // Delete — CSRF regression (#1694)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_still_deletes_a_payment_method_when_csrf_protection_is_on_and_the_token_is_valid(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->databaseInsert('ip_payment_methods', ['payment_method_name' => 'CSRF Method']);

        /* Act */
        $response = $this->postWithValidCsrfToken('/payment_methods/delete/' . $id);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'payment_methods');
        $this->assertDatabaseMissing('ip_payment_methods', ['payment_method_id' => $id]);
    }

    #[Test]
    public function it_does_not_delete_a_payment_method_when_the_csrf_token_is_missing(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->databaseInsert('ip_payment_methods', ['payment_method_name' => 'CSRF Method Kept']);

        /* Act */
        $response = $this->postWithoutCsrfToken('/payment_methods/delete/' . $id);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less delete must not reach the controller.');
        $this->assertDatabaseHas('ip_payment_methods', ['payment_method_id' => $id, 'payment_method_name' => 'CSRF Method Kept']);
    }

    // -------------------------------------------------------------------------
    // Edge cases
    // -------------------------------------------------------------------------

    #[Test]
    public function it_rejects_a_duplicate_payment_method_name_on_create(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_payment_methods', ['payment_method_name' => 'Duplicate Method']);

        /* Act */
        $response = $this->post('/payment_methods/form', [
            'payment_method_name' => 'Duplicate Method',
            'is_update'           => '0',
            'btn_submit'          => '1',
        ]);

        /* Assert */
        $this->assertDatabaseCount('ip_payment_methods', 1);
        $this->assertDatabaseCount('ip_payment_methods', 1, ['payment_method_name' => 'Duplicate Method']);
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login_and_leaks_no_payment_method(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_payment_methods', ['payment_method_name' => 'Secret Method']);
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/payment_methods');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
        $this->assertResponseBodyNotContains($response, 'Secret Method');
    }
}
