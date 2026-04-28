<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Unit\Clients;

use Mdl_Client_Notes;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\AbstractTestCase;

#[CoversClass(Mdl_Client_Notes::class)]
class ClientNotesModelTest extends AbstractTestCase
{
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new ClientNotesService();
    }

    public function test_service_has_correct_table(): void
    {
        $this->assertEquals('ip_client_notes', $this->model->table);
    }

    public function test_service_has_correct_primary_key(): void
    {
        $this->assertStringContainsString('client_note_id', $this->model->primary_key);
    }
}
