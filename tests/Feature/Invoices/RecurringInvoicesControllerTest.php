<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class RecurringInvoicesControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('smoke')]
    public function it_lists_recurring_invoices_for_authenticated_admin(): void
    {
        /* Arrange */
        /* (authenticated admin via setUp) */

        /* Act */
        $response = $this->get('/invoices/recurring');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<html');
    }
}
