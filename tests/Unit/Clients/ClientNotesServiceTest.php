<?php

namespace Tests\Unit\Clients;

use Mdl_Client_Notes;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\AbstractTestCase;

#[CoversClass(Mdl_Client_Notes::class)]
class ClientNotesServiceTest extends AbstractTestCase
{
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('ClientNotesService does not exist — CI3 model tested directly via Mdl_Client_Notes');
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
