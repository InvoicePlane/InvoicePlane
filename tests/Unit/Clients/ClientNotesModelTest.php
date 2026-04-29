<?php

namespace Tests\Unit\Clients;

use Mdl_Client_Notes;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Client_Notes::class)]
class ClientNotesModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('clients/mdl_client_notes');
        $this->model = $this->CI->mdl_client_notes;
    }

    #[Test]
    public function it_has_correct_table(): void
    {
        $this->assertEquals('ip_client_notes', $this->model->table);
    }

    #[Test]
    public function it_has_correct_primary_key(): void
    {
        $this->assertStringContainsString('client_note_id', $this->model->primary_key);
    }
}
