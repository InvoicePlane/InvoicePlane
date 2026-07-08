<?php

/**
 * CSRF Delete Security Regression Tests.
 *
 * These tests verify that the CSRF vulnerabilities found in PR #1622
 * remain fixed and don't regress in future updates.
 *
 * Each delete endpoint should:
 * 1. Require POST (reject GET requests)
 * 2. Require valid CSRF tokens
 * 3. Not perform any deletion before validation
 */
class CsrfDeleteSecurityTest
{
    private const DELETE_ENDPOINTS = [
        'projects/delete',
        'tasks/delete',
        'users/delete',
        'invoice_groups/delete',
        'payment_methods/delete',
        'custom_fields/delete',
        'units/delete',
        'tax_rates/delete',
        'custom_values/delete',
        'clients/delete',
        'products/delete',
        'settings/remove_logo',
    ];

    /**
     * Verify all delete endpoints require POST + CSRF validation.
     */
    public static function test_all_delete_endpoints_require_csrf_validation()
    {
        echo "\n=== Verifying CSRF Protection on Delete Endpoints ===\n";

        $module_dir = dirname(__DIR__, 3) . '/application/modules';
        $failures   = [];

        foreach (self::DELETE_ENDPOINTS as $endpoint) {
            list($module, $action) = explode('/', $endpoint);

            // Find controller file
            $controller_file = null;
            $module_path     = $module_dir . '/' . $module;

            if (is_dir($module_path . '/controllers')) {
                foreach (scandir($module_path . '/controllers') as $file) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }

                    $path    = $module_path . '/controllers/' . $file;
                    $content = file_get_contents($path);

                    if (preg_match('/class\s+\w+\s+extends/', $content) && preg_match('/public\s+function\s+' . preg_quote($action) . '\s*\(/', $content)) {
                        $controller_file = $path;
                        break;
                    }
                }
            }

            if ( ! $controller_file) {
                $failures[] = "{$module}/{$action}: Controller not found";
                continue;
            }

            $content = file_get_contents($controller_file);

            // Verify ensure_valid_post_request is called
            if ( ! preg_match('/public\s+function\s+' . preg_quote($action) . '\s*\([^)]*\)\s*(?::\s*\w+)?\s*\{[^}]*ensure_valid_post_request/s', $content)) {
                $failures[] = "{$module}/{$action}: Missing ensure_valid_post_request() call";
            } else {
                echo "✓ {$module}/{$action} has CSRF protection\n";
            }
        }

        if ( ! empty($failures)) {
            echo "\n❌ FAILURES:\n";
            foreach ($failures as $failure) {
                echo "  - {$failure}\n";
            }

            return false;
        }

        echo "\n✓ All delete endpoints are properly protected!\n";

        return true;
    }

    /**
     * Verify no delete operations are exposed via anchor links.
     */
    public static function test_no_get_links_to_delete_endpoints()
    {
        echo "\n=== Verifying No GET Links to Delete Endpoints ===\n";

        $module_dir = dirname(__DIR__, 3) . '/application/modules';
        $violations = [];

        // Iterate through all modules
        foreach (scandir($module_dir) as $module) {
            if ($module === '.' || $module === '..') {
                continue;
            }

            $views_dir = $module_dir . '/' . $module . '/views';
            if ( ! is_dir($views_dir)) {
                continue;
            }

            foreach (glob($views_dir . '/*.php') as $file) {
                $content = file_get_contents($file);

                // Look for anchor() calls to delete endpoints
                if (preg_match_all('/anchor\s*\(\s*[\'"]([^\'"]*\/delete[^\'"]*)[\'"]/', $content, $matches)) {
                    $violations[] = basename($file) . ' has anchor() link to delete: ' . implode(', ', $matches[1]);
                }
            }
        }

        if ( ! empty($violations)) {
            echo "\n❌ VIOLATIONS FOUND:\n";
            foreach ($violations as $violation) {
                echo "  - {$violation}\n";
            }

            return false;
        }

        echo "✓ No GET links to delete endpoints found!\n";

        return true;
    }

    /**
     * Verify CSRF tokens are included in all POST forms.
     */
    public static function test_csrf_tokens_in_post_forms()
    {
        echo "\n=== Verifying CSRF Tokens in POST Forms ===\n";

        $module_dir      = dirname(__DIR__, 3) . '/application/modules';
        $failures        = [];
        $post_form_count = 0;

        // Iterate through all modules
        foreach (scandir($module_dir) as $module) {
            if ($module === '.' || $module === '..') {
                continue;
            }

            $views_dir = $module_dir . '/' . $module . '/views';
            if ( ! is_dir($views_dir)) {
                continue;
            }

            foreach (glob($views_dir . '/*.php') as $file) {
                $content = file_get_contents($file);

                // Find POST forms
                if (preg_match_all('/method\s*=\s*["\']POST["\']/i', $content)) {
                    $post_form_count++;

                    // Check for CSRF token
                    if ( ! preg_match('/_csrf_field\s*\(\s*\)/', $content)) {
                        $failures[] = basename($file);
                    }
                }
            }
        }

        if ( ! empty($failures)) {
            echo "\n❌ POST forms without CSRF tokens:\n";
            foreach ($failures as $file) {
                echo "  - {$file}\n";
            }

            return false;
        }

        echo "✓ All {$post_form_count} POST forms include CSRF tokens!\n";

        return true;
    }

    /**
     * Run all tests.
     */
    public static function run_all()
    {
        echo "\n╔════════════════════════════════════════════════════════════╗\n";
        echo "║ CSRF DELETE ENDPOINT SECURITY REGRESSION TESTS           ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n";

        $results = [
            self::test_all_delete_endpoints_require_csrf_validation(),
            self::test_no_get_links_to_delete_endpoints(),
            self::test_csrf_tokens_in_post_forms(),
        ];

        echo "\n╔════════════════════════════════════════════════════════════╗\n";
        if (in_array(false, $results)) {
            echo "║ ❌ SOME TESTS FAILED                                       ║\n";

            return false;
        }
        echo "║ ✓ ALL CSRF SECURITY TESTS PASSED                         ║\n";

        echo "╚════════════════════════════════════════════════════════════╝\n";

        return true;
    }
}

// Run tests if executed directly
if (basename(__FILE__) === basename($_SERVER['PHP_SELF'] ?? '')) {
    $result = CsrfDeleteSecurityTest::run_all();
    exit($result ? 0 : 1);
}
