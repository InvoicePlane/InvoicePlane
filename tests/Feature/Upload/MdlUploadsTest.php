<?php

namespace Tests\Feature\Upload;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class MdlUploadsTest extends AbstractTestCase
{
    private string $uploadDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();

        $this->uploadDir = dirname(__DIR__, 3) . '/uploads/customer_files';
        if ( ! is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        foreach (glob($this->uploadDir . '/testkey_*') ?: [] as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        parent::tearDown();
    }

    #[Test]
    public function it_lists_upload_metadata_for_existing_files(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Upload Metadata Client']);
        file_put_contents($this->uploadDir . '/testkey_invoice.pdf', 'pdf-bytes');
        $this->databaseInsert('ip_uploads', [
            'client_id'          => $clientId,
            'url_key'            => 'testkey',
            'file_name_original' => 'invoice.pdf',
            'file_name_new'      => 'testkey_invoice.pdf',
            'uploaded_date'      => date('Y-m-d'),
        ]);

        /* Act */
        $response = $this->get('/upload/show_files/testkey');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $payload = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame([['name' => 'invoice.pdf', 'size' => 9]], $payload);
    }

    #[Test]
    public function it_skips_upload_rows_with_unsafe_stored_paths(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Unsafe Upload Client']);
        file_put_contents($this->uploadDir . '/testkey_safe.pdf', 'safe');
        $this->databaseInsert('ip_uploads', [
            'client_id'          => $clientId,
            'url_key'            => 'testkey',
            'file_name_original' => 'safe.pdf',
            'file_name_new'      => 'testkey_safe.pdf',
            'uploaded_date'      => date('Y-m-d'),
        ]);
        $this->databaseInsert('ip_uploads', [
            'client_id'          => $clientId,
            'url_key'            => 'testkey',
            'file_name_original' => 'passwd',
            'file_name_new'      => '../../etc/passwd',
            'uploaded_date'      => date('Y-m-d'),
        ]);

        /* Act */
        $response = $this->get('/upload/show_files/testkey');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyNotContains($response, 'passwd');
        $payload = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame([['name' => 'safe.pdf', 'size' => 4]], $payload);
        $this->assertDatabaseHas('ip_uploads', ['file_name_new' => '../../etc/passwd']);
    }
}
