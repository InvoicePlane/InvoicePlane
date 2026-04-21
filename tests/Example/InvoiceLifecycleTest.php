<?php

namespace Tests\Example;

use PHPUnit\Framework\Attributes\Test;
use Tests\Kernel\CiLifecycleKernel;

class InvoiceLifecycleTest extends CiLifecycleKernel
{
    #[Test]
    public function it_invoice_index_loads(): void
    {
        $output = $this->get('/invoices/index');

        $this->assertNotEmpty($output);
    }

    #[Test]
    public function it_invoice_create_flow(): void
    {
        $output = $this->post('/invoices/create', [
            'client_id' => 1,
            'amount'    => 100,
        ]);

        $this->assertNotEmpty($output);
    }
}
