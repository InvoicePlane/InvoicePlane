<?php

namespace Tests\Integration;

use Modules\Clients\Controllers\Clients;
use Modules\Invoices\Controllers\Invoices;
use Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\TestUris;

#[CoversClass(Clients::class)]
#[CoversClass(Invoices::class)]
class HmvcRouteLifecycleTest extends CiIntegrationTestCase
{
    #[Test]
    public function it_executes_clients_index_through_full_ci_and_mx_lifecycle(): void
    {
        $response = $this->get(TestUris::CLIENTS_INDEX);

        $this->assertResponseOk($response);
        $this->assertNotSame('', mb_trim($response->body()));
    }

    #[Test]
    public function it_executes_invoices_index_through_full_ci_and_mx_lifecycle(): void
    {
        $response = $this->get(TestUris::INVOICES_INDEX);

        $this->assertResponseOk($response);
        $this->assertNotSame('', mb_trim($response->body()));
    }
}
