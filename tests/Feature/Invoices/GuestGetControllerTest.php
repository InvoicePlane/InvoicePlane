<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * guest/controllers/Get.php — file download for guest-visible invoices/quotes.
 * url_key must be exactly 32 alphanumeric characters and belong to a
 * guest_visible() invoice or quote before any file access is attempted.
 */
class GuestGetControllerTest extends AbstractTestCase
{
    private string $uploadDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uploadDir = dirname(__DIR__, 3) . '/uploads/customer_files';
        if ( ! is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        foreach (glob($this->uploadDir . '/*_guesttest*') ?: [] as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        parent::tearDown();
    }

    // -------------------------------------------------------------------------
    // show_files
    // -------------------------------------------------------------------------

    #[Test]
    public function it_returns_an_empty_response_for_show_files_with_no_key(): void
    {
        /* Act */
        $response = $this->get('/guest/get/show_files');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        self::assertSame('{}', trim($response->body()));
    }

    #[Test]
    public function it_returns_an_empty_response_for_show_files_on_a_draft_invoice(): void
    {
        /* Arrange: draft (status 1) is never guest_visible() */
        $urlKey = $this->seedVisibleInvoiceUrlKey(1);

        /* Act */
        $response = $this->get('/guest/get/show_files/' . $urlKey);

        /* Assert */
        self::assertSame('{}', trim($response->body()));
    }

    #[Test]
    public function it_returns_an_empty_response_for_show_files_with_no_uploads(): void
    {
        /* Arrange */
        $urlKey = $this->seedVisibleInvoiceUrlKey();

        /* Act */
        $response = $this->get('/guest/get/show_files/' . $urlKey);

        /* Assert */
        self::assertSame('{}', trim($response->body()));
    }

    #[Test]
    public function it_lists_uploaded_files_for_a_guest_visible_invoice(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $urlKey   = bin2hex(random_bytes(16));
        $this->seedInvoice($clientId, ['invoice_url_key' => $urlKey, 'invoice_status_id' => 2]);
        $this->databaseInsert('ip_uploads', [
            'client_id'          => $clientId,
            'url_key'            => $urlKey,
            'file_name_original' => 'attachment.pdf',
            'file_name_new'      => $urlKey . '_guesttest_attachment.pdf',
            'uploaded_date'      => date('Y-m-d'),
        ]);
        file_put_contents($this->uploadDir . '/' . $urlKey . '_guesttest_attachment.pdf', 'attachment-bytes');

        /* Act */
        $response = $this->get('/guest/get/show_files/' . $urlKey);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'attachment.pdf');
    }

    // -------------------------------------------------------------------------
    // get_file / attachment
    // -------------------------------------------------------------------------

    #[Test]
    public function it_returns_400_for_get_file_with_no_filename(): void
    {
        /* Act */
        $response = $this->get('/guest/get/get_file');

        /* Assert */
        $this->assertResponseStatusCode($response, 400);
    }

    #[Test]
    public function it_returns_404_for_get_file_with_a_malformed_url_key_prefix(): void
    {
        /* Act */
        $response = $this->get('/guest/get/get_file/not-a-valid-key_file.pdf');

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    #[Test]
    public function it_returns_404_for_get_file_whose_url_key_is_not_guest_visible(): void
    {
        /* Arrange: well-formed 32-char key, but no invoice/quote owns it */
        $fakeKey = bin2hex(random_bytes(16));

        /* Act */
        $response = $this->get('/guest/get/get_file/' . $fakeKey . '_file.pdf');

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    #[Test]
    public function it_returns_404_for_get_file_whose_url_key_belongs_to_a_draft_invoice(): void
    {
        /* Arrange */
        $urlKey = $this->seedVisibleInvoiceUrlKey(1);

        /* Act */
        $response = $this->get('/guest/get/get_file/' . $urlKey . '_file.pdf');

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    #[Test]
    public function it_returns_404_for_a_visible_invoice_whose_file_does_not_exist_on_disk(): void
    {
        /* Arrange: valid, guest-visible invoice, but no file was ever written */
        $urlKey = $this->seedVisibleInvoiceUrlKey();

        /* Act */
        $response = $this->get('/guest/get/get_file/' . $urlKey . '_missing.pdf');

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    #[Test]
    public function it_downloads_an_existing_file_for_a_guest_visible_invoice(): void
    {
        /* Arrange */
        $urlKey   = $this->seedVisibleInvoiceUrlKey();
        $filename = $urlKey . '_guesttest.pdf';
        file_put_contents($this->uploadDir . '/' . $filename, 'pdf-bytes');

        /* Act */
        $response = $this->get('/guest/get/get_file/' . $filename);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        self::assertSame('pdf-bytes', $response->body());
    }

    #[Test]
    public function it_rejects_a_path_traversal_attempt_in_the_filename(): void
    {
        /* Arrange */
        $urlKey = $this->seedVisibleInvoiceUrlKey();

        /* Act */
        $response = $this->get('/guest/get/get_file/' . rawurlencode($urlKey . '_../../../../etc/passwd'));

        /* Assert */
        self::assertNotSame(200, $response->statusCode());
    }

    #[Test]
    public function it_serves_attachment_route_the_same_as_get_file(): void
    {
        /* Arrange */
        $urlKey   = $this->seedVisibleInvoiceUrlKey();
        $filename = $urlKey . '_guesttest2.pdf';
        file_put_contents($this->uploadDir . '/' . $filename, 'attachment-bytes');

        /* Act */
        $response = $this->get('/guest/get/attachment/' . $filename);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        self::assertSame('attachment-bytes', $response->body());
    }

    private function seedVisibleInvoiceUrlKey(int $statusId = 2): string
    {
        $clientId = $this->seedClient();
        $urlKey   = bin2hex(random_bytes(16)); // 32 hex chars
        $this->seedInvoice($clientId, ['invoice_url_key' => $urlKey, 'invoice_status_id' => $statusId]);

        return $urlKey;
    }
}
