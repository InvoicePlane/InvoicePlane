<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Core Import Feature Tests.
 *
 * Tests the import page for authenticated admins.
 */
class ImportControllerTest extends AbstractTestCase
{
    private string $importDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();

        $this->importDir = dirname(__DIR__, 3) . '/uploads/import';
        if ( ! is_dir($this->importDir)) {
            mkdir($this->importDir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        foreach (['clients.csv', 'evil.php', 'invoice_items.csv', 'invoices.csv', 'payments.csv'] as $file) {
            $path = $this->importDir . '/' . $file;
            if (is_file($path)) {
                unlink($path);
            }
        }

        parent::tearDown();
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_or_redirect(): void
    {
        /* Arrange */
        /* (authenticated admin via setUp) */

        /* Act */
        $response = $this->get('/import');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<html');
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/import');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/import] must redirect. Got [%d].', $response->statusCode())
        );
    }

    #[Test]
    public function it_lists_only_allowed_import_files(): void
    {
        /* Arrange */
        file_put_contents($this->importDir . '/clients.csv', "client_name\nAllowed Client\n");
        file_put_contents($this->importDir . '/evil.php', '<?php echo "not allowed";');

        /* Act */
        $response = $this->get('/import/form');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'clients.csv');
        $this->assertResponseBodyNotContains($response, 'evil.php');
    }

    #[Test]
    public function it_ignores_unapproved_import_filenames_on_submit(): void
    {
        /* Arrange */
        file_put_contents($this->importDir . '/evil.php', '<?php echo "not allowed";');

        /* Act */
        $response = $this->post('/import/form', [
            'files'      => ['evil.php', '../../bootstrap/kernel.php'],
            'btn_submit' => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Import submit must redirect after processing.');
        $this->assertDatabaseCount('ip_imports', 1);
        $this->assertDatabaseCount('ip_import_details', 0);
    }
}
