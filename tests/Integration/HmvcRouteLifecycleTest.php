<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Test;

class HmvcRouteLifecycleTest extends CiIntegrationTestCase
{
    #[Test]
    public function it_executes_clients_index_through_full_ci_and_mx_lifecycle(): void
    {
        $response = $this->get('/clients/index');

        $this->assertNotSame('', trim($response->body));
    }

    #[Test]
    public function it_executes_invoices_index_through_full_ci_and_mx_lifecycle(): void
    {
        $response = $this->get('/invoices/index');

        $this->assertNotSame('', trim($response->body));
    }
}
