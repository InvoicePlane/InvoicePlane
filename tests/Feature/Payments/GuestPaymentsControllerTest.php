<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * guest/controllers/Payments.php (the guest portal's own payment history
 * listing — distinct from application/modules/payments/controllers/Payments.php,
 * the admin one, and from the guest/gateways/* callback controllers).
 */
class GuestPaymentsControllerTest extends AbstractTestCase
{
    #[Test]
    public function it_redirects_an_unauthenticated_request_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/guest/payments');

        /* Assert */
        self::assertTrue($response->isRedirect());
    }

    #[Test]
    public function it_denies_an_admin_session_guest_type_access(): void
    {
        /* Arrange: an admin (user_type 1) is not a guest (user_type 2) */
        $this->actingAsAdmin();

        /* Act */
        $response = $this->get('/guest/payments');

        /* Assert */
        self::assertTrue($response->isRedirect());
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

        /* Act */
        $response = $this->get('/guest/payments');

        /* Assert */
        $this->assertResponseStatusCode($response, 403);
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

        /* Act */
        $response = $this->get('/guest/payments');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'own-payment-marker');
        $this->assertResponseBodyNotContains($response, 'other-payment-marker');
    }

    #[Test]
    public function it_does_not_expose_php_errors(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $this->actingAsGuestUser($clientId);

        /* Act */
        $response = $this->get('/guest/payments');

        /* Assert */
        $this->assertResponseHasNoPhpErrors($response);
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
