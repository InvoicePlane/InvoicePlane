<?php

namespace Tests\Feature\Upload;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Upload;

#[CoversClass(Upload::class)]
final class UploadControllerTest extends AbstractTestCase
{
    /** @var list<string> */
    private array $createdFiles = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    protected function tearDown(): void
    {
        array_map('unlink', array_filter($this->createdFiles, 'is_file'));
        parent::tearDown();
    }

    #[Test]
    public function it_lists_the_files_stored_under_a_url_key(): void
    {
        /* Arrange */
        $urlKey = $this->urlKey();
        $this->storeFile($urlKey, 'contract.pdf', '%PDF-1.4 contract');

        /* Act */
        $response = $this->get('/upload/show_files/' . $urlKey);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        self::assertSame([['name' => 'contract.pdf', 'size' => strlen('%PDF-1.4 contract')]], json_decode($response->body(), true));
    }

    #[Test]
    public function it_does_not_list_files_stored_under_another_url_key(): void
    {
        /* Arrange */
        $ownKey   = $this->urlKey();
        $otherKey = $this->urlKey();
        $this->storeFile($ownKey, 'mine.pdf', '%PDF mine');
        $this->storeFile($otherKey, 'secret.pdf', '%PDF secret');

        /* Act */
        $response = $this->get('/upload/show_files/' . $ownKey);

        /* Assert */
        $this->assertResponseBodyContains($response, 'mine.pdf');
        $this->assertResponseBodyNotContains($response, 'secret.pdf');
    }

    #[Test]
    public function it_prunes_metadata_for_a_file_that_no_longer_exists_on_disk(): void
    {
        /* Arrange */
        $urlKey = $this->urlKey();
        $this->databaseInsert('ip_uploads', [
            'client_id' => 1, 'url_key' => $urlKey, 'file_name_original' => 'ghost.pdf',
            'file_name_new' => $urlKey . '_ghost.pdf', 'uploaded_date' => date('Y-m-d'),
        ]);

        /* Act */
        $response = $this->get('/upload/show_files/' . $urlKey);

        /* Assert: nothing listed, and the stale row is cleaned up */
        self::assertSame([], json_decode($response->body(), true) ?? []);
        $this->assertDatabaseMissing('ip_uploads', ['url_key' => $urlKey, 'file_name_original' => 'ghost.pdf']);
    }

    #[Test]
    public function it_serves_an_uploaded_file_by_its_url_key_and_name(): void
    {
        /* Arrange */
        $urlKey = $this->urlKey();
        $this->storeFile($urlKey, 'invoice.pdf', '%PDF-1.4 exact-bytes');

        /* Act */
        $response = $this->get('/upload/get_file/' . rawurlencode($urlKey . '_invoice.pdf'));

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        self::assertSame('%PDF-1.4 exact-bytes', $response->body());
    }

    #[Test]
    public function it_returns_404_for_a_file_that_does_not_exist(): void
    {
        /* Act */
        $response = $this->get('/upload/get_file/' . rawurlencode($this->urlKey() . '_missing.pdf'));

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    #[Test]
    public function it_deletes_the_file_and_its_metadata(): void
    {
        /* Arrange */
        $urlKey = $this->urlKey();
        $path   = $this->storeFile($urlKey, 'old.pdf', '%PDF old');

        /* Act */
        $response = $this->post('/upload/delete_file/' . $urlKey, ['name' => 'old.pdf']);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        self::assertFileDoesNotExist($path);
        $this->assertDatabaseMissing('ip_uploads', ['url_key' => $urlKey, 'file_name_original' => 'old.pdf']);
    }

    #[Test]
    public function it_does_not_delete_a_file_that_belongs_to_another_url_key(): void
    {
        /* Arrange */
        $ownKey   = $this->urlKey();
        $otherKey = $this->urlKey();
        $victim   = $this->storeFile($otherKey, 'keep.pdf', '%PDF keep');

        /* Act: name matches the victim's file, but under the wrong key */
        $response = $this->post('/upload/delete_file/' . $ownKey, ['name' => 'keep.pdf']);

        /* Assert */
        self::assertFileExists($victim);
        $this->assertDatabaseHas('ip_uploads', ['url_key' => $otherKey, 'file_name_original' => 'keep.pdf']);
        self::assertNotSame(500, $response->statusCode());
    }

    #[Test]
    public function it_rejects_a_traversal_name_on_delete_and_leaves_other_files_alone(): void
    {
        /* Arrange */
        $urlKey = $this->urlKey();
        $canary = ROOT_PATH . '/ipconfig.php';

        /* Act */
        $response = $this->post('/upload/delete_file/' . $urlKey, ['name' => '../../ipconfig.php']);

        /* Assert */
        $this->assertResponseStatusCode($response, 400);
        self::assertFileExists($canary);
    }

    #[Test]
    public function it_rejects_an_upload_request_that_carries_no_file(): void
    {
        /* Arrange */
        $urlKey = $this->urlKey();

        /* Act */
        $response = $this->post('/upload/upload_file/1/' . $urlKey);

        /* Assert */
        $this->assertResponseStatusCode($response, 400);
        $this->assertDatabaseMissing('ip_uploads', ['url_key' => $urlKey]);
    }

    private function urlKey(): string
    {
        return bin2hex(random_bytes(16));
    }

    private function storeFile(string $urlKey, string $name, string $content): string
    {
        $path = rtrim(UPLOADS_CFILES_FOLDER, '/') . '/' . $urlKey . '_' . $name;
        file_put_contents($path, $content);
        $this->createdFiles[] = $path;
        $this->databaseInsert('ip_uploads', [
            'client_id' => 1, 'url_key' => $urlKey, 'file_name_original' => $name,
            'file_name_new' => $urlKey . '_' . $name, 'uploaded_date' => date('Y-m-d'),
        ]);

        return $path;
    }
}
