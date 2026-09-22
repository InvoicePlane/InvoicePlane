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
        $methodCountBefore = $this->databaseCount('ip_payment_methods');

        /* Act */
        $response = $this->get('/payment_methods');

        /* Assert: Error Semantics (C) */
        $this->assertResponseBodyContains($response, 'Bank Transfer');
        $this->assertResponseBodyContains($response, 'Cash On Delivery');
        $this->assertResponseStatusCode($response, 200);

        /* Assert: State Isolation (B) */
        $methodCountAfter = $this->databaseCount('ip_payment_methods');
        $this->assertSame($methodCountBefore, $methodCountAfter);

        /* Assert: Data Integrity (D) */
        $bankTransfer = $this->databaseFetchOne('ip_payment_methods', ['payment_method_name' => 'Bank Transfer']);
        $this->assertIsArray($bankTransfer);
        $this->assertGreaterThan(0, (int) $bankTransfer['payment_method_id']);

        /* Assert: Idempotency (E) */
        $response2 = $this->get('/payment_methods');
        $this->assertResponseStatusCode($response2, 200);
        $this->assertResponseBodyContains($response2, 'Bank Transfer');
    }

    // -------------------------------------------------------------------------
    // Create — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_a_payment_method(): void
    {
        /* Arrange */
        $methodCountBefore = $this->databaseCount('ip_payment_methods');

        /* Act */
        $response = $this->post('/payment_methods/form', [
            'payment_method_name' => 'Cheque',
            'is_update'           => '0',
            'btn_submit'          => '1',
        ]);

        /* Assert: Error Semantics (C) */
        $this->assertResponseRedirectsToRoute($response, 'payment_methods');

        /* Assert: Business Logic (A) */
        $this->assertDatabaseHas('ip_payment_methods', ['payment_method_name' => 'Cheque']);

        /* Assert: State Isolation (B) */
        $methodCountAfter = $this->databaseCount('ip_payment_methods');
        $this->assertGreaterThan($methodCountBefore, $methodCountAfter);
        $this->assertDatabaseCount('ip_payment_methods', 1);

        /* Assert: Data Integrity (D) */
        $method = $this->databaseFetchOne('ip_payment_methods', ['payment_method_name' => 'Cheque']);
        $this->assertGreaterThan(0, (int) $method['payment_method_id']);

        /* Assert: Idempotency (E) */
        $response2 = $this->post('/payment_methods/form', [
            'payment_method_name' => 'Cheque',
            'is_update'           => '0',
            'btn_submit'          => '1',
        ]);
        $this->assertResponseRedirectsToRoute($response2, 'payment_methods');
    }

    // -------------------------------------------------------------------------
    // Create — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_payment_method_name(): void
    {
        /* Arrange */
        $methodCountBefore = $this->databaseCount('ip_payment_methods');

        /* Act */
        $response = $this->post('/payment_methods/form', [
            'payment_method_name' => '',
            'is_update'           => '0',
            'btn_submit'          => '1',
        ]);

        /* Assert: Error Semantics (C) */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');

        /* Assert: State Isolation (B) */
        $methodCountAfter = $this->databaseCount('ip_payment_methods');
        $this->assertSame($methodCountBefore, $methodCountAfter);
        $this->assertDatabaseCount('ip_payment_methods', 0);

        /* Assert: Data Integrity (D) */
        $this->assertDatabaseCount('ip_payment_methods', 0);

        /* Assert: Boundary Cases (F) */
        $response2 = $this->post('/payment_methods/form', [
            'payment_method_name' => '   ',
            'is_update'           => '0',
            'btn_submit'          => '1',
        ]);
        self::assertFalse($response2->isRedirect());

        /* Assert: Idempotency (E) */
        $response3 = $this->post('/payment_methods/form', [
            'payment_method_name' => '',
            'is_update'           => '0',
            'btn_submit'          => '1',
        ]);
        self::assertFalse($response3->isRedirect());
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
        $other = $this->databaseInsert('ip_payment_methods', ['payment_method_name' => 'Other Method']);
        $methodCountBefore = $this->databaseCount('ip_payment_methods');

        /* Act */
        $response = $this->get('/payment_methods/form/' . $target);

        /* Assert: Error Semantics (C) */
        $this->assertResponseBodyContains($response, 'Editable Method');
        $this->assertResponseBodyNotContains($response, 'Other Method');
        $this->assertResponseStatusCode($response, 200);

        /* Assert: State Isolation (B) */
        $methodCountAfter = $this->databaseCount('ip_payment_methods');
        $this->assertSame($methodCountBefore, $methodCountAfter);

        /* Assert: Business Logic (A) */
        $this->assertResponseBodyContains($response, 'Editable Method');

        /* Assert: Data Integrity (D) */
        $targetMethod = $this->databaseFetchOne('ip_payment_methods', ['payment_method_id' => $target]);
        $this->assertSame('Editable Method', $targetMethod['payment_method_name']);

        /* Assert: Idempotency (E) */
        $response2 = $this->get('/payment_methods/form/' . $target);
        $this->assertResponseBodyContains($response2, 'Editable Method');
        $this->assertResponseBodyNotContains($response2, 'Other Method');
    }

    #[Test]
    public function it_updates_a_payment_method(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_payment_methods', ['payment_method_name' => 'Original Method']);
        $methodCountBefore = $this->databaseCount('ip_payment_methods');

        /* Act */
        $response = $this->post('/payment_methods/form/' . $id, [
            'payment_method_name' => 'Renamed Method',
            'is_update'           => '1',
            'btn_submit'          => '1',
        ]);

        /* Assert: Error Semantics (C) */
        $this->assertResponseRedirectsToRoute($response, 'payment_methods');

        /* Assert: Business Logic (A) */
        $this->assertDatabaseHas('ip_payment_methods', ['payment_method_id' => $id, 'payment_method_name' => 'Renamed Method']);
        $this->assertDatabaseMissing('ip_payment_methods', ['payment_method_name' => 'Original Method']);

        /* Assert: State Isolation (B) */
        $methodCountAfter = $this->databaseCount('ip_payment_methods');
        $this->assertSame($methodCountBefore, $methodCountAfter);

        /* Assert: Data Integrity (D) */
        $method = $this->databaseFetchOne('ip_payment_methods', ['payment_method_id' => $id]);
        $this->assertSame('Renamed Method', $method['payment_method_name']);

        /* Assert: Idempotency (E) */
        $response2 = $this->post('/payment_methods/form/' . $id, [
            'payment_method_name' => 'Renamed Method',
            'is_update'           => '1',
            'btn_submit'          => '1',
        ]);
        $this->assertResponseRedirectsToRoute($response2, 'payment_methods');
        $this->assertDatabaseCount('ip_payment_methods', 1);
    }

    // -------------------------------------------------------------------------
    // Update — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_update_without_payment_method_name(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_payment_methods', ['payment_method_name' => 'Keep This Method']);
        $methodCountBefore = $this->databaseCount('ip_payment_methods');

        /* Act */
        $response = $this->post('/payment_methods/form/' . $id, [
            'payment_method_name' => '',
            'is_update'           => '1',
            'btn_submit'          => '1',
        ]);

        /* Assert: Error Semantics (C) */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');

        /* Assert: Business Logic (A) */
        $this->assertDatabaseHas('ip_payment_methods', ['payment_method_id' => $id, 'payment_method_name' => 'Keep This Method']);

        /* Assert: State Isolation (B) */
        $methodCountAfter = $this->databaseCount('ip_payment_methods');
        $this->assertSame($methodCountBefore, $methodCountAfter);

        /* Assert: Data Integrity (D) */
        $method = $this->databaseFetchOne('ip_payment_methods', ['payment_method_id' => $id]);
        $this->assertSame('Keep This Method', $method['payment_method_name']);

        /* Assert: Idempotency (E) */
        $response2 = $this->post('/payment_methods/form/' . $id, [
            'payment_method_name' => '',
            'is_update'           => '1',
            'btn_submit'          => '1',
        ]);
        self::assertFalse($response2->isRedirect());
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
        $methodCountBefore = $this->databaseCount('ip_payment_methods');

        /* Act */
        $response = $this->post('/payment_methods/delete/' . $id, []);

        /* Assert: Error Semantics (C) */
        $this->assertResponseRedirectsToRoute($response, 'payment_methods');

        /* Assert: Business Logic (A) */
        $this->assertDatabaseMissing('ip_payment_methods', ['payment_method_id' => $id]);
        $this->assertDatabaseHas('ip_payment_methods', ['payment_method_id' => $keep]);

        /* Assert: State Isolation (B) */
        $methodCountAfter = $this->databaseCount('ip_payment_methods');
        $this->assertLessThan($methodCountBefore, $methodCountAfter);

        /* Assert: Data Integrity (D) */
        $keptMethod = $this->databaseFetchOne('ip_payment_methods', ['payment_method_id' => $keep]);
        $this->assertSame('Kept Method', $keptMethod['payment_method_name']);

        /* Assert: Idempotency (E) */
        $response2 = $this->post('/payment_methods/delete/' . $id, []);
        $this->assertResponseStatusCode($response2, 404);
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
        $methodCountBefore = $this->databaseCount('ip_payment_methods');

        /* Act */
        $response = $this->postWithValidCsrfToken('/payment_methods/delete/' . $id);

        /* Assert: Error Semantics (C) */
        $this->assertResponseRedirectsToRoute($response, 'payment_methods');

        /* Assert: Business Logic (A) */
        $this->assertDatabaseMissing('ip_payment_methods', ['payment_method_id' => $id]);

        /* Assert: State Isolation (B) */
        $methodCountAfter = $this->databaseCount('ip_payment_methods');
        $this->assertLessThan($methodCountBefore, $methodCountAfter);

        /* Assert: Data Integrity (D) */
        $this->assertDatabaseCount('ip_payment_methods', $methodCountAfter);

        /* Assert: Idempotency (E) */
        $response2 = $this->postWithValidCsrfToken('/payment_methods/delete/' . $id);
        $this->assertResponseStatusCode($response2, 404);
    }

    #[Test]
    public function it_does_not_delete_a_payment_method_when_the_csrf_token_is_missing(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->databaseInsert('ip_payment_methods', ['payment_method_name' => 'CSRF Method Kept']);
        $methodCountBefore = $this->databaseCount('ip_payment_methods');

        /* Act */
        $response = $this->postWithoutCsrfToken('/payment_methods/delete/' . $id);

        /* Assert: Error Semantics (C) */
        self::assertFalse($response->isRedirect(), 'A token-less delete must not reach the controller.');

        /* Assert: Business Logic (A) */
        $this->assertDatabaseHas('ip_payment_methods', ['payment_method_id' => $id, 'payment_method_name' => 'CSRF Method Kept']);

        /* Assert: State Isolation (B) */
        $methodCountAfter = $this->databaseCount('ip_payment_methods');
        $this->assertSame($methodCountBefore, $methodCountAfter);

        /* Assert: Data Integrity (D) */
        $method = $this->databaseFetchOne('ip_payment_methods', ['payment_method_id' => $id]);
        $this->assertSame('CSRF Method Kept', $method['payment_method_name']);

        /* Assert: Idempotency (E) */
        $response2 = $this->postWithoutCsrfToken('/payment_methods/delete/' . $id);
        self::assertFalse($response2->isRedirect());
        $this->assertDatabaseHas('ip_payment_methods', ['payment_method_id' => $id]);
    }

    // -------------------------------------------------------------------------
    // Edge cases
    // -------------------------------------------------------------------------

    #[Test]
    public function it_rejects_a_duplicate_payment_method_name_on_create(): void
    {
        /* Arrange */
        $id1 = $this->databaseInsert('ip_payment_methods', ['payment_method_name' => 'Duplicate Method']);
        $methodCountBefore = $this->databaseCount('ip_payment_methods');

        /* Act */
        $response = $this->post('/payment_methods/form', [
            'payment_method_name' => 'Duplicate Method',
            'is_update'           => '0',
            'btn_submit'          => '1',
        ]);

        /* Assert: Business Logic (A) */
        $this->assertDatabaseCount('ip_payment_methods', 1);
        $this->assertDatabaseCount('ip_payment_methods', 1, ['payment_method_name' => 'Duplicate Method']);

        /* Assert: State Isolation (B) */
        $methodCountAfter = $this->databaseCount('ip_payment_methods');
        $this->assertSame($methodCountBefore, $methodCountAfter);

        /* Assert: Error Semantics (C) */
        self::assertFalse($response->isRedirect(), 'Duplicate name must re-render the form, not redirect.');

        /* Assert: Data Integrity (D) */
        $original = $this->databaseFetchOne('ip_payment_methods', ['payment_method_id' => $id1]);
        $this->assertSame('Duplicate Method', $original['payment_method_name']);

        /* Assert: Boundary Cases (F) */
        $response2 = $this->post('/payment_methods/form', [
            'payment_method_name' => 'Duplicate Method',
            'is_update'           => '0',
            'btn_submit'          => '1',
        ]);
        $this->assertDatabaseCount('ip_payment_methods', 1);

        /* Assert: Idempotency (E) */
        $response3 = $this->post('/payment_methods/form', [
            'payment_method_name' => 'Duplicate Method',
            'is_update'           => '0',
            'btn_submit'          => '1',
        ]);
        $this->assertDatabaseCount('ip_payment_methods', 1);
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login_and_leaks_no_payment_method(): void
    {
        /* Arrange */
        $methodId = $this->databaseInsert('ip_payment_methods', ['payment_method_name' => 'Secret Method']);
        $methodCountBefore = $this->databaseCount('ip_payment_methods');
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/payment_methods');

        /* Assert: Error Semantics (C) */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
        $this->assertResponseStatusCode($response, 302);
        $this->assertResponseBodyNotContains($response, 'Secret Method');

        /* Assert: State Isolation (B) */
        $methodCountAfter = $this->databaseCount('ip_payment_methods');
        $this->assertSame($methodCountBefore, $methodCountAfter);

        /* Assert: Business Logic (A) */
        $this->assertDatabaseHas('ip_payment_methods', ['payment_method_id' => $methodId, 'payment_method_name' => 'Secret Method']);

        /* Assert: Data Integrity (D) */
        $method = $this->databaseFetchOne('ip_payment_methods', ['payment_method_id' => $methodId]);
        $this->assertGreaterThan(0, (int) $method['payment_method_id']);

        /* Assert: Boundary Cases (F) */
        $response2 = $this->get('/payment_methods/form/999');
        self::assertTrue($response2->isRedirect());

        /* Assert: Idempotency (E) */
        $response3 = $this->get('/payment_methods');
        self::assertTrue($response3->isRedirect());
    }
}
