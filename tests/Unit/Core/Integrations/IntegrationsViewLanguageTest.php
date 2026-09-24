<?php

namespace Tests\Unit\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Guards against the integrations module views referencing translation keys
 * that are not defined in the English language file.
 *
 * The einvoice -> integrations refactor previously dropped a batch of keys
 * (einvoice, incoming_invoices, providers, …) that the module's own views
 * still referenced, so those pages rendered raw keys. This test scans every
 * `trans()` / `_trans()` call in the module views and the navigation entry
 * points that link to it, and asserts each key resolves.
 */
#[Group('unit')]
class IntegrationsViewLanguageTest extends TestCase
{
    #[Test]
    public function it_defines_every_translation_key_used_by_the_integration_views(): void
    {
        $lang    = $this->englishLang();
        $missing = [];

        foreach ($this->viewFiles() as $file) {
            foreach ($this->translationKeysIn($file) as $key) {
                if ( ! array_key_exists($key, $lang)) {
                    $missing[] = basename($file) . ' => ' . $key;
                }
            }
        }

        self::assertSame([], $missing, 'Undefined translation keys: ' . implode(', ', $missing));
    }

    #[Test]
    public function it_defines_the_navigation_entry_point_labels(): void
    {
        $lang = $this->englishLang();

        foreach (['incoming_invoices', 'einvoice_providers', 'einvoice', 'einvoice_history'] as $key) {
            self::assertArrayHasKey($key, $lang, "Navigation label '{$key}' must be defined");
        }
    }

    /** @return array<string, mixed> */
    private function englishLang(): array
    {
        $lang = [];
        require dirname(__DIR__, 3) . '/application/language/english/ip_lang.php';

        return $lang;
    }

    /** @return list<string> */
    private function viewFiles(): array
    {
        $base = dirname(__DIR__, 3) . '/application';

        return [
            ...glob($base . '/modules/integrations/views/*.php'),
            $base . '/modules/layout/views/includes/navbar.php',
            $base . '/modules/layout/views/includes/sidebar.php',
            $base . '/modules/invoices/views/partial_invoice_table.php',
        ];
    }

    /** @return list<string> */
    private function translationKeysIn(string $file): array
    {
        $source = (string) file_get_contents($file);

        preg_match_all("/\\b_?trans\\(\\s*'([a-z0-9_]+)'/", $source, $matches);

        return array_values(array_unique($matches[1]));
    }
}
