<?php

namespace Tests\Feature\Clients;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class ClientsControllerTest extends AbstractTestCase
{
    #[Test]
    public function it_shows_seeded_client_on_clients_page(): void
    {
        dump(class_exists('MX_Controller'));
        dump(class_exists('MX_Loader'));
        dump(class_exists('MX_Modules'));
        die();

        $response = $this->get('/clients/index');

        $this->assertStringContainsString(
            'Test Client',
            $response
        );
    }
}
