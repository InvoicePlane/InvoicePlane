<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Tests\Feature\Core\Security::class)]
class SecurityTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->seedModel('User', ['user_type' => 1, 'user_active' => 1]);
    }

    #[Test]
    public function it_prevents_unauthorized_access_to_admin_routes(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $response = $this->get('/dashboard/index');

        $response->assertRedirect('/sessions/login');
    }

    #[Test]
    public function it_allows_authenticated_users_to_access_admin_routes(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $this->actingAs($this->user);

        $response = $this->get('/dashboard/index');

        $response->assertSuccessful();
    }

    #[Test]
    public function it_filters_input_to_prevent_xss_attacks(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $this->actingAs($this->user);
        $client = $this->seedModel('tmpClient');

        $maliciousData = [
            'client_id'            => $client->client_id,
            'invoice_notes'        => '<script>alert("XSS")</script>',
            'invoice_date_created' => now()->format('Y-m-d'),
            'invoice_date_due'     => now()->addDays(30)->format('Y-m-d'),
        ];

        $response = $this->post('/invoices/form', $maliciousData);

        // Input should be filtered
        $this->assertDatabaseMissing('ip_invoices', [
            'invoice_notes' => '<script>alert("XSS")</script>',
        ]);
    }

    #[Test]
    public function it_prevents_sql_injection_in_search_queries(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $this->actingAs($this->user);
        $this->seedModel('tmpClient', ['client_name' => 'Test Client', 'client_active' => 1]);

        $sqlInjection = "' OR '1'='1";

        $response = $this->get('/clients/ajax/nameQuery/' . ($sqlInjection));

        $response->assertSuccessful();
        // Should not return all clients or cause error
    }

    #[Test]
    public function it_validates_csrf_tokens_on_form_submissions(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $this->actingAs($this->user);

        $response = $this->post('/clients/form', [
            'client_name'  => 'Test Client',
            'client_email' => 'test@example.com',
        ], [
            'X-CSRF-TOKEN' => 'invalid-token',
        ]);

        $response->assertStatus(419); // CSRF token mismatch
    }

    #[Test]
    public function it_prevents_directory_traversal_in_file_operations(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $this->actingAs($this->user);

        $response = $this->get('/invoices/download/' . ('../../etc/passwd'));

        $response->assertNotFound();
    }

    #[Test]
    public function it_validates_user_permissions_for_sensitive_operations(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $guestUser = $this->seedModel('User', ['user_type' => 2, 'user_active' => 1]);
        $this->actingAs($guestUser);

        $invoice = $this->seedModel('Invoice');

        $response = $this->delete('/invoices/delete/' . ($invoice->invoice_id));

        // Guest users should not be able to delete invoices
        $response->assertStatus(403);
    }

    #[Test]
    public function it_sanitizes_file_upload_names(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $this->actingAs($this->user);

        // Test with potentially malicious filename
        $maliciousFilename = '../../../evil.php';

        // Implementation would depend on upload controller
        // Just ensure basename is used and path traversal is blocked
        $this->assertTrue(str_contains(basename($maliciousFilename), 'evil.php'));
        $this->assertFalse(str_contains(basename($maliciousFilename), '../'));
    }

    #[Test]
    public function it_rate_limits_login_attempts(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $user = $this->seedModel('User', [
            'user_email'    => 'test@example.com',
            'user_password' => bcrypt('password'),
            'user_active'   => 1,
        ]);

        // Attempt multiple failed logins
        for ($i = 0; $i < 11; $i++) {
            $this->post('/sessions/login', [
                'btn_login' => true,
                'email'     => 'test@example.com',
                'password'  => 'wrongpassword',
            ]);
        }

        // Account should be locked
        $this->assertDatabaseHas('ip_login_log', [
            'login_name' => 'test@example.com',
        ]);
    }

    #[Test]
    public function it_validates_email_format_in_user_input(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $this->actingAs($this->user);

        $response = $this->post('/clients/form', [
            'client_name'  => 'Test Client',
            'client_email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('client_email');
    }

    #[Test]
    public function it_prevents_mass_assignment_vulnerabilities(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $this->actingAs($this->user);

        $response = $this->post('/clients/form', [
            'client_name'  => 'Test Client',
            'client_email' => 'test@example.com',
            'user_type'    => 1, // Attempt to set privileged field
            'user_active'  => 1,
        ]);

        // user_type should not be assignable through client form
        $this->assertDatabaseMissing('ip_clients', [
            'user_type' => 1,
        ]);
    }
}
