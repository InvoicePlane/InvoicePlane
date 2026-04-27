<?php

namespace Tests\Regression\Invoices;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class InvoicesTest extends AbstractTestCase
{
    #[Test]
    public function it_can_load_invoice_index(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $response = $this->get('invoices/index');

        $this->assertNotNull($response);
    }

    #[Test]
    public function it_can_create_invoice(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $response = $this->post('invoices/create', [
            'client_id' => 1,
            'amount'    => 100,
        ]);

        $this->assertNotNull($response);
    }
}
