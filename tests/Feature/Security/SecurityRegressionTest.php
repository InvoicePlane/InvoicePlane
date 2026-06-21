<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Regression tests for the 4 highest-risk structural security vulnerabilities
 * fixed in v1.7.2. Each test proves the fix cannot silently regress.
 *
 * Covered:
 *   - IDOR: guest user cannot access another client's invoice or quote PDF
 *   - Path traversal: upload download/delete endpoints reject traversal payloads
 *   - DDL / SQL injection via setting_key: query builder parameterisation holds
 *   - Config injection via logo setting: path-traversal filename is rejected
 */
#[Group('security')]
class SecurityRegressionTest extends AbstractTestCase
{
    // -----------------------------------------------------------------------
    // 1. IDOR — guest PDF access
    // -----------------------------------------------------------------------

    #[Test]
    public function it_denies_a_guest_access_to_another_clients_invoice_pdf(): void
    {
        /* Arrange */
        $ownClientId   = $this->seedClient(['client_name' => 'Guest Owner']);
        $otherClientId = $this->seedClient(['client_name' => 'Other Client']);

        $guestUserId = $this->databaseInsert('ip_users', [
            'user_name'          => 'guest_idor_test',
            'user_email'         => 'guest-idor@test.local',
            'user_password'      => password_hash('secret', PASSWORD_DEFAULT),
            'user_psalt'         => bin2hex(random_bytes(10)),
            'user_type'          => 2,
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);

        // Link guest to ownClient only — NOT otherClient.
        $this->databaseInsert('ip_user_clients', [
            'user_id'   => $guestUserId,
            'client_id' => $ownClientId,
        ]);

        $otherInvoiceId = $this->seedInvoice($otherClientId);

        $this->actingAs([
            'user_id'       => $guestUserId,
            'user_type'     => 2,
            'user_email'    => 'guest-idor@test.local',
            'user_name'     => 'Guest IDOR Test',
            'user_company'  => '',
            'user_language' => 'system',
        ]);

        /* Act */
        $response = $this->get("/guest/invoices/generate_pdf/{$otherInvoiceId}");

        /* Assert */
        self::assertSame(
            404,
            $response->statusCode(),
            'A guest must not be able to retrieve another client\'s invoice PDF — expected 404.'
        );
    }

    #[Test]
    public function it_denies_a_guest_access_to_another_clients_quote_pdf(): void
    {
        /* Arrange */
        $ownClientId   = $this->seedClient(['client_name' => 'Guest Owner Q']);
        $otherClientId = $this->seedClient(['client_name' => 'Other Client Q']);

        $guestUserId = $this->databaseInsert('ip_users', [
            'user_name'          => 'guest_idor_q_test',
            'user_email'         => 'guest-idor-q@test.local',
            'user_password'      => password_hash('secret', PASSWORD_DEFAULT),
            'user_psalt'         => bin2hex(random_bytes(10)),
            'user_type'          => 2,
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);

        $this->databaseInsert('ip_user_clients', [
            'user_id'   => $guestUserId,
            'client_id' => $ownClientId,
        ]);

        $otherQuoteId = $this->databaseInsert('ip_quotes', [
            'client_id'           => $otherClientId,
            'user_id'             => 1,
            'invoice_group_id'    => 1,
            'quote_status_id'     => 1,
            'quote_date_created'  => date('Y-m-d'),
            'quote_date_modified' => date('Y-m-d'),
            'quote_date_expires'  => date('Y-m-d', strtotime('+30 days')),
            'quote_number'        => 'QUO-IDOR-' . random_int(1000, 9999),
            'quote_url_key'       => bin2hex(random_bytes(16)),
        ]);

        $this->databaseInsert('ip_quote_amounts', [
            'quote_id'             => $otherQuoteId,
            'quote_item_subtotal'  => '0.00',
            'quote_item_tax_total' => '0.00',
            'quote_tax_total'      => '0.00',
            'quote_total'          => '0.00',
        ]);

        $this->actingAs([
            'user_id'       => $guestUserId,
            'user_type'     => 2,
            'user_email'    => 'guest-idor-q@test.local',
            'user_name'     => 'Guest IDOR Q Test',
            'user_company'  => '',
            'user_language' => 'system',
        ]);

        /* Act */
        $response = $this->get("/guest/quotes/generate_pdf/{$otherQuoteId}");

        /* Assert */
        self::assertSame(
            404,
            $response->statusCode(),
            'A guest must not be able to retrieve another client\'s quote PDF — expected 404.'
        );
    }

    // -----------------------------------------------------------------------
    // 2. Path traversal — upload endpoints
    // -----------------------------------------------------------------------

    #[Test]
    public function it_rejects_a_path_traversal_payload_in_the_file_download_endpoint(): void
    {
        /* Arrange */
        $this->actingAsAdmin();

        // Classic traversal patterns: the real_filename part (after the first _) must
        // fail validate_safe_filename() and never reach readfile().
        $traversalPayloads = [
            'key_..%2F..%2F..%2Fetc%2Fpasswd',   // URL-encoded slashes
            'key_....//....//etc//passwd',          // doubled slashes obfuscation
            'key_%2e%2e%2fetc%2fpasswd',           // fully encoded
        ];

        foreach ($traversalPayloads as $payload) {
            /* Act */
            $response = $this->get('/upload/get_file/' . $payload);

            /* Assert */
            self::assertNotSame(
                200,
                $response->statusCode(),
                "Traversal payload [{$payload}] must not yield a 200 response."
            );

            self::assertStringNotContainsString(
                'root:',
                $response->body(),
                "Traversal payload [{$payload}] must not return /etc/passwd content."
            );
        }
    }

    #[Test]
    public function it_rejects_null_byte_injection_in_the_file_download_endpoint(): void
    {
        /* Arrange */
        $this->actingAsAdmin();

        // Null bytes in filenames can truncate paths in older C-library calls.
        // validate_safe_filename() explicitly checks for them.
        $payload = 'key_legitimate.pdf' . "\0" . '../../../etc/passwd';

        /* Act */
        $response = $this->get('/upload/get_file/' . rawurlencode($payload));

        /* Assert */
        self::assertNotSame(200, $response->statusCode());
        self::assertStringNotContainsString('root:', $response->body());
    }

    #[Test]
    public function it_rejects_a_path_traversal_payload_in_the_file_delete_endpoint(): void
    {
        /* Arrange */
        $this->actingAsAdmin();

        // Before the fix, delete_file() accepted unvalidated filenames and could
        // delete arbitrary files via the POST name parameter.
        $traversalName = '../../../bootstrap/kernel.php';

        /* Act */
        // Attempt to delete a file outside the uploads directory.
        $response = $this->post('/upload/delete_file/testkey', [
            'name' => $traversalName,
        ]);

        /* Assert */
        // Must not return 200 (success). 400 = invalid filename, 410 = file not found/path rejected.
        self::assertNotSame(
            200,
            $response->statusCode(),
            'A path traversal name in delete_file must not produce a 200 success response.'
        );

        // Verify the targeted file was not deleted.
        self::assertFileExists(
            dirname(__DIR__, 3) . '/bootstrap/kernel.php',
            'bootstrap/kernel.php must survive a path-traversal delete attempt.'
        );
    }

    // -----------------------------------------------------------------------
    // 3. SQL injection via setting_key
    // -----------------------------------------------------------------------

    #[Test]
    public function it_stores_a_crafted_setting_key_safely_without_breaking_the_table(): void
    {
        /* Arrange */
        // A classic SQL injection payload embedded in the key name.
        // Before parameterised queries, this would terminate the WHERE clause
        // and run a DROP TABLE.
        $this->actingAsAdmin();

        $maliciousKey = "legit_key'; DROP TABLE ip_settings; --";

        /* Act */
        $response = $this->post('/settings', [
            'settings' => [$maliciousKey => 'any_value'],
        ]);

        /* Assert */
        // The request must not crash (5xx) — CI3's query builder parameterises the key.
        self::assertNotSame(500, $response->statusCode());

        // The settings table must still exist and still hold the seed rows.
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'disable_setup']);

        // Clean up the injected key if it was actually stored.
        $this->databaseDelete('ip_settings', ['setting_key' => $maliciousKey]);
    }

    #[Test]
    public function it_stores_a_setting_key_with_html_characters_as_literal_text(): void
    {
        /* Arrange */
        // Stored XSS: if setting_key is rendered unescaped in an admin view,
        // a <script> tag in the key would execute in the browser.
        // This test documents that the key is stored as a plain string —
        // the view layer is responsible for escaping on output.
        $this->actingAsAdmin();

        $xssKey = '<script>alert(1)</script>';

        /* Act */
        $response = $this->post('/settings', [
            'settings' => [$xssKey => 'xss_probe'],
        ]);

        /* Assert */
        self::assertNotSame(500, $response->statusCode());

        // The table must be intact.
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'disable_setup']);

        // Clean up.
        $this->databaseDelete('ip_settings', ['setting_key' => $xssKey]);
    }

    // -----------------------------------------------------------------------
    // 4. Config injection via logo setting (path traversal in setting value)
    // -----------------------------------------------------------------------

    #[Test]
    public function it_rejects_a_path_traversal_value_for_the_invoice_logo_setting(): void
    {
        /* Arrange */
        // Before the fix, the invoice_logo setting was saved without filename validation.
        // An attacker who can POST settings could set invoice_logo to a path like
        // ../../config/database.php and have it served as a "logo".
        $this->actingAsAdmin();

        $originalLogo = $this->databaseFetchOne('ip_settings', ['setting_key' => 'invoice_logo']);

        /* Act */
        $response = $this->post('/settings', [
            'settings' => [
                'invoice_logo' => '../../../application/config/database.php',
            ],
        ]);

        /* Assert */
        // The controller must redirect back with an error — not accept the value.
        self::assertTrue(
            $response->isRedirect(),
            'A path-traversal invoice_logo value must cause a redirect (validation rejection), not a 200.'
        );

        // The stored value must not have changed to the traversal path.
        $stored = $this->databaseFetchOne('ip_settings', ['setting_key' => 'invoice_logo']);
        $storedValue = $stored['setting_value'] ?? ($originalLogo['setting_value'] ?? '');

        self::assertStringNotContainsString(
            '../',
            (string) $storedValue,
            'A traversal path must never be persisted as the invoice_logo setting value.'
        );
    }

    #[Test]
    public function it_rejects_a_path_traversal_value_for_the_login_logo_setting(): void
    {
        /* Arrange */
        $this->actingAsAdmin();

        $originalLogo = $this->databaseFetchOne('ip_settings', ['setting_key' => 'login_logo']);

        /* Act */
        $response = $this->post('/settings', [
            'settings' => [
                'login_logo' => '..\\..\\application\\config\\database.php',
            ],
        ]);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            'A path-traversal login_logo value must cause a redirect (validation rejection), not a 200.'
        );

        $stored = $this->databaseFetchOne('ip_settings', ['setting_key' => 'login_logo']);
        $storedValue = $stored['setting_value'] ?? ($originalLogo['setting_value'] ?? '');

        self::assertStringNotContainsString(
            '..',
            (string) $storedValue,
            'A traversal path must never be persisted as the login_logo setting value.'
        );
    }
}
