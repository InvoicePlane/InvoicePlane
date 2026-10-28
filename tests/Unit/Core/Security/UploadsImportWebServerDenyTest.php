<?php

namespace Tests\Unit\Core\Security;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Support\NginxLocationResolver;

/**
 * GHSA-wv2c-c285-9hrq (PR #1623): import CSVs hold bulk client and invoice data and must not be fetchable.
 *
 * NginxDenyRulesTest and DevelopMergeSecurityTest only str_contains() the words `deny all` / `Deny from all`
 * anywhere in the file, so they stay green when the import block is deleted but a sibling block still says
 * `deny all`, or when the directive is commented out. These tests parse the real config files and answer
 * the question that matters: which block would nginx pick for a request to the import folder, and does
 * that block refuse it?
 *
 * Static by nature: no web server runs here. What is proven is what the shipped config says, not what a
 * deployed nginx or Apache answers.
 */
#[Group('security')]
final class UploadsImportWebServerDenyTest extends TestCase
{
    private const IMPORT_PATHS = [
        'the clients csv'          => '/uploads/import/clients.csv',
        'the invoices csv'         => '/uploads/import/invoices.csv',
        'the invoice items csv'    => '/uploads/import/invoice_items.csv',
        'the payments csv'         => '/uploads/import/payments.csv',
        'the folder itself'        => '/uploads/import/',
        'a php file in the folder' => '/uploads/import/shell.php',
        'a dotfile in the folder'  => '/uploads/import/.htaccess',
    ];

    /** @return array<string, array{string}> */
    public static function importPaths(): array
    {
        $cases = [];
        foreach (self::IMPORT_PATHS as $label => $path) {
            $cases[$label] = [$path];
        }

        return $cases;
    }

    #[Test]
    #[DataProvider('importPaths')]
    public function it_resolves_every_import_path_to_a_nginx_location_that_refuses_it(string $path): void
    {
        /* Arrange */
        $resolver = $this->nginx();

        /* Act */
        $location = $resolver->resolve($path);

        /* Assert */
        self::assertNotNull($location, 'No nginx location matches ' . $path);
        self::assertTrue(
            NginxLocationResolver::refuses($location),
            $path . ' is handled by `location ' . trim($location['modifier'] . ' ' . $location['pattern']) . '` which does not refuse it.'
        );
    }

    #[Test]
    public function it_does_not_refuse_ordinary_public_paths_so_the_check_can_tell_the_difference(): void
    {
        /* Arrange */
        $resolver = $this->nginx();

        /* Act */
        $root = $resolver->resolve('/invoices/view/abc');
        $logo = $resolver->resolve('/uploads/logo_300x150.jpg');

        /* Assert: positive control, a resolver that refuses everything would pass the test above */
        self::assertFalse(NginxLocationResolver::refuses($root));
        self::assertFalse(NginxLocationResolver::refuses($logo));
    }

    #[Test]
    public function it_refuses_the_import_folder_through_the_explicit_prefix_block_alone(): void
    {
        /* Arrange: only the ^~ prefix locations (plus the catch-all), as if the regex block were deleted */
        $resolver = $this->nginx()->onlyModifiers(['^~']);

        /* Act */
        $location = $resolver->resolve('/uploads/import/clients.csv');

        /* Assert */
        self::assertSame('/uploads/import/', $location['pattern']);
        self::assertTrue(NginxLocationResolver::refuses($location));
    }

    #[Test]
    public function it_ships_an_import_htaccess_whose_only_effective_directive_is_deny_from_all(): void
    {
        /* Arrange */
        $file = ROOT_PATH . '/uploads/import/.htaccess';

        /* Act */
        $raw        = is_file($file) ? (string) file_get_contents($file) : '';
        $directives = array_values(array_filter(
            array_map('trim', explode("\n", $raw)),
            static fn (string $line): bool => $line !== '' && $line[0] !== '#'
        ));

        /* Assert: one live directive, so it is not commented out and no Allow/Satisfy/Order line can reopen the folder */
        self::assertFileExists($file);
        self::assertSame(['Deny from all'], $directives);
    }

    private function nginx(): NginxLocationResolver
    {
        $file = ROOT_PATH . '/resources/docker/nginx/invoiceplane.conf';
        self::assertFileExists($file);

        return NginxLocationResolver::fromConfig((string) file_get_contents($file));
    }
}
