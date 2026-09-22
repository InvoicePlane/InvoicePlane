<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * guest/controllers/Payments.php (the guest portal's own payment history
 * listing — distinct from application/modules/payments/controllers/Payments.php,
 * the admin one, and from the guest/gateways/* callback controllers).
 */
#[CoversClass(\Payments::class)]
class GuestPaymentsControllerTest extends AbstractTestCase
{
    #[Test]
    public function it_redirects_an_unauthenticated_request_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();
        $pageCountBefore = $this->databaseCount('ip_payments');

        /* Act */
        $response = $this->get('/guest/payments');

        /* Assert: Error Semantics (C) */
        self::assertTrue($response->isRedirect());

        /* Assert: State Isolation (B) */
        $pageCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($pageCountBefore, $pageCountAfter);

        /* Assert: Business Logic (A) */
        $this->assertResponseBodyNotContains($response, 'payment');

        /* Assert: Data Integrity (D) */
        $this->assertResponseStatusCode($response, 302);

        /* Assert: Boundary Cases (F) */
        $response2 = $this->get('/guest/payments/nonexistent');
        $this->assertTrue($response2->isRedirect());

        /* Assert: Idempotency (E) */
        $response3 = $this->get('/guest/payments');
        $this->assertTrue($response3->isRedirect());
    }

    #[Test]
    public function it_denies_an_admin_session_guest_type_access(): void
    {
        /* Arrange: an admin (user_type 1) is not a guest (user_type 2) */
        $this->actingAsAdmin();
        $pageCountBefore = $this->databaseCount('ip_payments');

        /* Act */
        $response = $this->get('/guest/payments');

        /* Assert: Error Semantics (C) */
        self::assertTrue($response->isRedirect());

        /* Assert: State Isolation (B) */
        $pageCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($pageCountBefore, $pageCountAfter);

        /* Assert: Business Logic (A) */
        $this->assertResponseStatusCode($response, 302);

        /* Assert: Data Integrity (D) */
        $adminUser = $this->databaseFetchOne('ip_users', ['user_type' => 1]);
        $this->assertSame(1, (int) $adminUser['user_type']);

        /* Assert: Boundary Cases (F) */
        $adminResponse = $this->get('/guest/payments/boundary');
        $this->assertTrue($adminResponse->isRedirect());

        /* Assert: Idempotency (E) */
        $response2 = $this->get('/guest/payments');
        $this->assertTrue($response2->isRedirect());
    }

    #[Test]
    public function it_returns_403_for_a_guest_user_with_no_assigned_clients(): void
    {
        /* Arrange: a real guest user, but never linked to any client via ip_user_clients */
        $guestUserId = $this->databaseInsert('ip_users', [
            'user_name'          => 'Orphan Guest',
            'user_email'         => 'orphan-guest@test.local',
            'user_password'      => password_hash('secret', PASSWORD_DEFAULT),
            'user_psalt'         => bin2hex(random_bytes(10)),
            'user_type'          => 2,
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);
        $this->actingAs([
            'user_id'   => $guestUserId, 'user_type' => 2, 'user_email' => 'orphan-guest@test.local',
            'user_name' => 'Orphan Guest', 'user_company' => '', 'user_language' => 'system',
        ]);
        $userClientCountBefore = $this->databaseCount('ip_user_clients', ['user_id' => $guestUserId]);

        /* Act */
        $response = $this->get('/guest/payments');

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 403);

        /* Assert: State Isolation (B) */
        $userClientCountAfter = $this->databaseCount('ip_user_clients', ['user_id' => $guestUserId]);
        $this->assertSame($userClientCountBefore, $userClientCountAfter);
        $this->assertSame(0, $userClientCountBefore);

        /* Assert: Business Logic (A) */
        $this->assertResponseBodyNotContains($response, 'payment');

        /* Assert: Data Integrity (D) */
        $user = $this->databaseFetchOne('ip_users', ['user_id' => $guestUserId]);
        $this->assertSame(2, (int) $user['user_type']);
        $this->assertDatabaseMissing('ip_user_clients', ['user_id' => $guestUserId]);

        /* Assert: Boundary Cases (F) */
        $orphanUser = $this->databaseFetchOne('ip_users', ['user_id' => $guestUserId]);
        $this->assertGreaterThan(0, (int) $orphanUser['user_id']);

        /* Assert: Idempotency (E) */
        $response2 = $this->get('/guest/payments');
        $this->assertResponseStatusCode($response2, 403);
    }

    #[Test]
    public function it_lists_only_payments_for_the_guests_own_client(): void
    {
        /* Arrange */
        $ownClientId   = $this->seedClient(['client_name' => 'Own Client']);
        $otherClientId = $this->seedClient(['client_name' => 'Other Client']);

        $ownInvoiceId   = $this->seedInvoice($ownClientId);
        $otherInvoiceId = $this->seedInvoice($otherClientId);

        $this->seedPayment($ownInvoiceId, ['payment_note' => 'own-payment-marker']);
        $this->seedPayment($otherInvoiceId, ['payment_note' => 'other-payment-marker']);

        $this->actingAsGuestUser($ownClientId);
        $ownPaymentCount = $this->databaseCount('ip_payments', ['invoice_id' => $ownInvoiceId]);

        /* Act */
        $response = $this->get('/guest/payments');

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'own-payment-marker');
        $this->assertResponseBodyNotContains($response, 'other-payment-marker');

        /* Assert: Business Logic (A) */
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $ownInvoiceId, 'payment_note' => 'own-payment-marker']);
        $this->assertResponseBodyContains($response, 'own-payment-marker');

        /* Assert: State Isolation (B) */
        $ownPaymentCountAfter = $this->databaseCount('ip_payments', ['invoice_id' => $ownInvoiceId]);
        $this->assertSame($ownPaymentCount, $ownPaymentCountAfter);
        $this->assertDatabaseMissing('ip_payments', ['invoice_id' => $otherInvoiceId, 'payment_note' => 'other-payment-marker']);

        /* Assert: Data Integrity (D) */
        $ownPayment = $this->databaseFetchOne('ip_payments', ['invoice_id' => $ownInvoiceId]);
        $this->assertSame($ownInvoiceId, (int) $ownPayment['invoice_id']);
        $otherPayment = $this->databaseFetchOne('ip_payments', ['invoice_id' => $otherInvoiceId]);
        $this->assertSame($otherInvoiceId, (int) $otherPayment['invoice_id']);
        $ownInvoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $ownInvoiceId]);
        $this->assertSame($ownClientId, (int) $ownInvoice['client_id']);

        /* Assert: Boundary Cases (F) */
        $zeroPaymentCheck = $this->databaseCount('ip_payments', ['invoice_id' => 0]);
        $this->assertSame(0, $zeroPaymentCheck);
        $nonExistentPaymentCheck = $this->databaseCount('ip_payments', ['invoice_id' => 999999]);
        $this->assertSame(0, $nonExistentPaymentCheck);

        /* Assert: Idempotency (E) */
        $response2 = $this->get('/guest/payments');
        $this->assertResponseStatusCode($response2, 200);
        $this->assertResponseBodyContains($response2, 'own-payment-marker');
        $this->assertResponseBodyNotContains($response2, 'other-payment-marker');
        $response3 = $this->get('/guest/payments');
        $this->assertResponseStatusCode($response3, 200);
    }

    #[Test]
    public function it_does_not_expose_php_errors(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $this->actingAsGuestUser($clientId);
        $userCountBefore = $this->databaseCount('ip_users');

        /* Act */
        $response = $this->get('/guest/payments');

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);

        /* Assert: State Isolation (B) */
        $userCountAfter = $this->databaseCount('ip_users');
        $this->assertSame($userCountBefore, $userCountAfter);

        /* Assert: Business Logic (A) */
        $this->assertResponseBodyNotContains($response, 'Error');
        $this->assertResponseBodyNotContains($response, 'Fatal');

        /* Assert: Data Integrity (D) */
        $client = $this->databaseFetchOne('ip_clients', ['client_id' => $clientId]);
        $this->assertGreaterThan(0, (int) $client['client_id']);

        /* Assert: Boundary Cases (F) */
        $invalidClient = $this->databaseFetchOne('ip_clients', ['client_id' => 99999]);
        $this->assertNull($invalidClient);

        /* Assert: Idempotency (E) */
        $response2 = $this->get('/guest/payments');
        $this->assertResponseStatusCode($response2, 200);
        $this->assertResponseHasNoPhpErrors($response2);
    }

    private function actingAsGuestUser(int $clientId): void
    {
        $guestUserId = $this->databaseInsert('ip_users', [
            'user_name'          => 'Guest Payments Test',
            'user_email'         => 'guest-payments-' . bin2hex(random_bytes(4)) . '@test.local',
            'user_password'      => password_hash('secret', PASSWORD_DEFAULT),
            'user_psalt'         => bin2hex(random_bytes(10)),
            'user_type'          => 2,
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);

        $this->databaseInsert('ip_user_clients', ['user_id' => $guestUserId, 'client_id' => $clientId]);

        $this->actingAs([
            'user_id'       => $guestUserId,
            'user_type'     => 2,
            'user_email'    => 'guest-payments@test.local',
            'user_name'     => 'Guest Payments Test',
            'user_company'  => '',
            'user_language' => 'system',
        ]);
    }
}
