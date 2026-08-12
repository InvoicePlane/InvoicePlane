<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Static regression coverage for delete endpoints that must remain POST-only
 * and CSRF-protected.
 */
#[Group('security')]
class CsrfDeleteSecurityTest extends TestCase
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

    #[Test]
    public function it_requires_post_validation_for_delete_endpoints(): void
    {
        /* Arrange */
        $failures = [];

        /* Act */
        foreach (self::DELETE_ENDPOINTS as $endpoint) {
            [$module, $action] = explode('/', $endpoint);
            $controllerFile    = $this->findControllerFile($module, $action);

            if ($controllerFile === null) {
                $failures[] = "{$module}/{$action}: controller not found";
                continue;
            }

            $content = (string) file_get_contents($controllerFile);
            if (
                preg_match(
                    '/public\s+function\s+' . preg_quote($action, '/') . '\s*\([^)]*\)\s*(?::\s*\w+)?\s*\{[^}]*ensure_valid_post_request/s',
                    $content
                ) !== 1
            ) {
                $failures[] = "{$module}/{$action}: missing ensure_valid_post_request() call";
            }
        }

        /* Assert */
        self::assertSame([], $failures, implode(PHP_EOL, $failures));
    }

    #[Test]
    public function it_does_not_link_to_delete_endpoints_with_get_anchors(): void
    {
        /* Arrange */
        $violations = [];

        /* Act */
        foreach ($this->moduleViewFiles() as $file) {
            $content = (string) file_get_contents($file);

            if (preg_match_all('/anchor\s*\(\s*[\'"]([^\'"]*\/delete[^\'"]*)[\'"]/', $content, $matches)) {
                $violations[] = basename($file) . ' has anchor() link to delete: ' . implode(', ', $matches[1]);
            }
        }

        /* Assert */
        self::assertSame([], $violations, implode(PHP_EOL, $violations));
    }

    #[Test]
    public function it_includes_csrf_tokens_in_post_forms(): void
    {
        /* Arrange */
        $failures      = [];
        $postFormCount = 0;

        /* Act */
        foreach ($this->moduleViewFiles() as $file) {
            $content = (string) file_get_contents($file);

            if (preg_match_all('/method\s*=\s*["\']POST["\']/i', $content) === 0) {
                continue;
            }

            $postFormCount++;
            if (preg_match('/_csrf_field\s*\(\s*\)/', $content) !== 1) {
                $failures[] = basename($file);
            }
        }

        /* Assert */
        self::assertGreaterThan(0, $postFormCount);
        self::assertSame([], $failures, implode(PHP_EOL, $failures));
    }

    private function findControllerFile(string $module, string $action): ?string
    {
        $controllerDir = $this->moduleDir() . '/' . $module . '/controllers';
        if ( ! is_dir($controllerDir)) {
            return null;
        }

        foreach (glob($controllerDir . '/*.php') ?: [] as $file) {
            $content = (string) file_get_contents($file);
            if (
                preg_match('/class\s+\w+\s+extends/', $content) === 1
                && preg_match('/public\s+function\s+' . preg_quote($action, '/') . '\s*\(/', $content) === 1
            ) {
                return $file;
            }
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    private function moduleViewFiles(): array
    {
        $files = [];

        foreach (glob($this->moduleDir() . '/*/views/*.php') ?: [] as $file) {
            $files[] = $file;
        }

        sort($files);

        return $files;
    }

    private function moduleDir(): string
    {
        return dirname(__DIR__, 3) . '/application/modules';
    }
}
