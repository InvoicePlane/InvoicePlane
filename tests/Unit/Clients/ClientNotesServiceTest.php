<?php

namespace Tests\Unit\Clients;

use Modules\Crm\app\Services\ClientNotesService;
use Tests\AbstractTestCase;

class ClientNotesServiceTest extends AbstractTestCase
{
    private ClientNotesService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ClientNotesService();
    }

    public function test_service_has_correct_table(): void
    {
        $this->assertEquals('ip_client_notes', $this->service->table);
    }

    public function test_service_has_correct_primary_key(): void
    {
        $this->assertStringContainsString('client_note_id', $this->service->primary_key);
    }
}
