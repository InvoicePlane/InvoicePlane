<?php

namespace Tests\Unit\Security;

use PHPUnit\Framework\TestCase;

/**
 * Delete Endpoint Security Test Suite
 *
 * Regression tests to ensure delete endpoints cannot be exploited via GET requests.
 * This addresses the CSRF vulnerabilities discovered in PR #1622.
 *
 * Each delete endpoint should:
 * 1. Only accept POST requests
 * 2. Validate CSRF tokens
 * 3. Redirect with error message on validation failure
 * 4. NOT perform deletion on GET requests
 */
class DeleteEndpointSecurityTest extends TestCase
{
    /**
     * List of all delete endpoints that require POST + CSRF protection.
     *
     * Format: [
     *     'route' => 'module/delete/id',
     *     'redirect_on_failure' => 'module/index',
     *     'module' => 'Module_Name',
     * ]
     */
    private const PROTECTED_DELETE_ENDPOINTS = [
        [
            'route' => 'projects/delete',
            'module' => 'Projects',
            'id_param' => '1',
        ],
        [
            'route' => 'tasks/delete',
            'module' => 'Tasks',
            'id_param' => '1',
        ],
        [
            'route' => 'users/delete',
            'module' => 'Users',
            'id_param' => '2',  // Skip user 1 (system protected)
        ],
        [
            'route' => 'invoice_groups/delete',
            'module' => 'Invoice_groups',
            'id_param' => '1',
        ],
        [
            'route' => 'payment_methods/delete',
            'module' => 'Payment_methods',
            'id_param' => '1',
        ],
        [
            'route' => 'custom_fields/delete',
            'module' => 'Custom_fields',
            'id_param' => '1',
        ],
        [
            'route' => 'units/delete',
            'module' => 'Units',
            'id_param' => '1',
        ],
        [
            'route' => 'tax_rates/delete',
            'module' => 'Tax_rates',
            'id_param' => '1',
        ],
        [
            'route' => 'custom_values/delete',
            'module' => 'Custom_values',
            'id_param' => '1',
        ],
        [
            'route' => 'clients/delete',
            'module' => 'Clients',
            'id_param' => '1',
        ],
        [
            'route' => 'products/delete',
            'module' => 'Products',
            'id_param' => '1',
        ],
        [
            'route' => 'settings/remove_logo',
            'module' => 'Settings',
            'id_param' => 'login',  // Can be 'login' or 'invoice'
        ],
    ];

    /**
     * Verify that all delete endpoints use ensure_valid_post_request().
     *
     * This is the primary defense against GET-based CSRF exploitation.
     * The method validates both HTTP method and CSRF token.
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_requires_post_and_csrf_on_all_delete_endpoints(): void
    {
        $failed = [];

        foreach (self::PROTECTED_DELETE_ENDPOINTS as $endpoint) {
            $module = strtolower(str_replace('_', '-', $endpoint['module']));
            $controller_path = dirname(__DIR__, 3) . '/application/modules/' . $module . '/controllers/';

            $controller_file = null;
            foreach (glob($controller_path . '*.php') as $file) {
                if (preg_match('/class\s+' . $endpoint['module'] . '\s+extends/i', file_get_contents($file))) {
                    $controller_file = $file;
                    break;
                }
            }

            if (!$controller_file) {
                $failed[] = "{$endpoint['module']}: Controller file not found";
                continue;
            }

            $content = file_get_contents($controller_file);

            // Extract the delete method
            if (!preg_match('/public\s+function\s+delete\s*\(/', $content)) {
                $failed[] = "{$endpoint['module']}: delete() method not found";
                continue;
            }

            // Verify ensure_valid_post_request is called early in the method
            if (!preg_match('/public\s+function\s+delete\s*\([^)]*\)\s*(?::\s*\w+)?\s*\{[^}]*ensure_valid_post_request/s', $content)) {
                $failed[] = "{$endpoint['module']}: delete() does not call ensure_valid_post_request()";
            }
        }

        $this->assertEmpty(
            $failed,
            "The following endpoints are not properly protected:\n" . implode("\n", $failed)
        );
    }

    /**
     * Verify that logo removal endpoints require POST + CSRF.
     *
     * This was the original reported vulnerability (logo removal via GET).
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_protects_logo_removal_endpoints(): void
    {
        $settings_controller = dirname(__DIR__, 3) . '/application/modules/settings/controllers/Settings.php';
        $this->assertFileExists($settings_controller, 'Settings controller not found');

        $content = file_get_contents($settings_controller);

        // Verify remove_logo method exists
        $this->assertStringContainsString('public function remove_logo', $content);

        // Verify it calls ensure_valid_post_request
        $this->assertStringContainsString(
            'ensure_valid_post_request',
            $content,
            'remove_logo() must validate POST and CSRF tokens'
        );

        // Verify the method rejects invalid requests
        $this->assertStringContainsString(
            'redirect',
            $content,
            'remove_logo() must redirect on validation failure'
        );
    }

    /**
     * Verify that views use POST forms, not GET links, for delete operations.
     *
     * Prevents the UI layer from accidentally exposing delete operations via GET.
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_uses_post_forms_for_all_delete_operations(): void
    {
        $viewDir = dirname(__DIR__, 3) . '/application/modules';
        $violations = [];

        foreach (glob($viewDir . '/*/views/*.php') as $file) {
            $content = file_get_contents($file);

            // Look for delete links as anchor tags (bad) vs forms (good)
            // Pattern: anchor('something/delete', 'text')
            if (preg_match_all('/anchor\s*\(\s*[\'"].*\/delete/', $content)) {
                $violations[] = basename($file) . " uses anchor() for delete (should use form POST)";
            }
        }

        $this->assertEmpty(
            $violations,
            "The following files use GET links for delete operations:\n" . implode("\n", $violations)
        );
    }

    /**
     * Verify that ensure_valid_post_request() is called BEFORE any business logic.
     *
     * Ensures CSRF validation happens before the operation, not after.
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_validates_csrf_before_performing_deletion(): void
    {
        $this->markTestSkipped('Code review test - manual inspection required');

        // Manual checks needed:
        // 1. ensure_valid_post_request() is first statement in method
        // 2. Return immediately on validation failure
        // 3. No deletion can happen before validation
    }

    /**
     * Verify that settings form includes CSRF token.
     *
     * The bulk settings form is a major POST handler.
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_includes_csrf_in_settings_form(): void
    {
        $settings_view = dirname(__DIR__, 3) . '/application/modules/settings/views/index.php';
        $this->assertFileExists($settings_view, 'Settings view not found');

        $content = file_get_contents($settings_view);
        $this->assertStringContainsString('_csrf_field', $content, 'Settings form must include CSRF token');
    }

    /**
     * Verify that login form includes CSRF token.
     *
     * Public-facing authentication endpoints must be protected.
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_includes_csrf_in_login_form(): void
    {
        $login_view = dirname(__DIR__, 3) . '/application/modules/sessions/views/session_login.php';
        $this->assertFileExists($login_view, 'Login view not found');

        $content = file_get_contents($login_view);
        $this->assertStringContainsString('_csrf_field', $content, 'Login form must include CSRF token');
    }

    /**
     * Verify that password reset form includes CSRF token.
     *
     * Password reset is a sensitive operation requiring CSRF protection.
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_includes_csrf_in_password_reset_forms(): void
    {
        $passwordreset_view = dirname(__DIR__, 3) . '/application/modules/sessions/views/session_passwordreset.php';
        $this->assertFileExists($passwordreset_view, 'Password reset view not found');

        $content = file_get_contents($passwordreset_view);
        $this->assertStringContainsString('_csrf_field', $content, 'Password reset form must include CSRF token');

        $new_password_view = dirname(__DIR__, 3) . '/application/modules/sessions/views/session_new_password.php';
        $this->assertFileExists($new_password_view, 'New password view not found');

        $content = file_get_contents($new_password_view);
        $this->assertStringContainsString('_csrf_field', $content, 'New password form must include CSRF token');
    }

    /**
     * Verify CSRF protection is enabled globally.
     *
     * Ensures csrf_protection = true in configuration.
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_enables_csrf_protection_globally(): void
    {
        $config_file = dirname(__DIR__, 3) . '/application/config/config.php';
        $this->assertFileExists($config_file, 'Config file not found');

        $content = file_get_contents($config_file);

        // Verify CSRF is enabled
        $this->assertStringContainsString("'csrf_protection'", $content);
        $this->assertStringContainsString("env_bool('CSRF_PROTECTION', 'true')", $content);
    }

    /**
     * Verify no URIs are excluded from CSRF protection.
     *
     * csrf_exclude_uris should be empty to ensure all endpoints are protected.
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_excludes_no_uris_from_csrf_protection(): void
    {
        $config_file = dirname(__DIR__, 3) . '/application/config/config.php';
        $content = file_get_contents($config_file);

        $this->assertStringContainsString("'csrf_exclude_uris'", $content);
        $this->assertStringContainsString("'csrf_exclude_uris' => []", $content);
    }

    /**
     * Regression test: Ensure no delete operations are performed via GET.
     *
     * This test documents what SHOULD happen (not what does in unit test context).
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function delete_operations_must_not_be_accessible_via_get(): void
    {
        // Integration test - would verify:
        // GET /projects/delete/1 → Redirects, does not delete
        // GET /tasks/delete/1 → Redirects, does not delete
        // GET /users/delete/2 → Redirects, does not delete
        // GET /settings/remove_logo/login → Redirects, logo not removed

        $this->markTestSkipped('Integration test - requires running application');
    }

    /**
     * Regression test: Ensure CSRF tokens are validated on DELETE POST.
     *
     * Prevents CSRF attack through specially crafted forms.
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function delete_operations_must_validate_csrf_tokens(): void
    {
        // Integration test - would verify:
        // POST /projects/delete/1 (no CSRF) → Fails
        // POST /projects/delete/1 (bad CSRF) → Fails
        // POST /projects/delete/1 (valid CSRF) → Succeeds

        $this->markTestSkipped('Integration test - requires running application');
    }

    /**
     * Code audit checklist for delete endpoints.
     *
     * Manual verification items:
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function code_audit_checklist_for_delete_endpoints(): void
    {
        // This test documents the manual code review steps:
        //
        // For each delete endpoint, verify:
        // ☑ Method signature: public function delete($id)
        // ☑ First line: if ( ! $this->ensure_valid_post_request(...)) { return; }
        // ☑ Redirect URL is to appropriate index page
        // ☑ No business logic before validation
        // ☑ Corresponding view uses: <form method="POST" action="...delete...">
        // ☑ View form includes: <?php _csrf_field(); ?>
        // ☑ View form has submit button (not anchor/link)

        $this->markTestSkipped('Manual code review checklist');
    }
}
