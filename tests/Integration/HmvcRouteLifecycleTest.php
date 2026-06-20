<?php

namespace Tests\Integration;

use Modules\Clients\Controllers\Clients;
use Modules\Invoices\Controllers\Invoices;
use Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(Clients::class)]
#[CoversClass(Invoices::class)]
class HmvcRouteLifecycleTest extends CiIntegrationTestCase
{
    #[Test]
    public function it_executes_clients_index_through_full_ci_and_mx_lifecycle(): void
    {
        $this->actingAsAdmin();

        $response = $this->get('/clients/status/active');

        $this->assertResponseOk($response);
        $this->assertNotSame('', mb_trim($response->body()));
    }

    #[Test]
    public function it_executes_invoices_index_through_full_ci_and_mx_lifecycle(): void
    {
        $this->actingAsAdmin();

        $response = $this->get('/invoices/index');

        $this->assertResponseOk($response);
        $this->assertNotSame('', mb_trim($response->body()));
    }
}
