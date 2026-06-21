<?php

namespace Tests\Unit\Invoices;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Smoke test for the InvoiceGroupsServiceTest module via CI3 HTTP harness.
 */
class InvoiceGroupsServiceTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_or_redirect(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_invoice_groups', [
            'invoice_group_name'              => 'Service Group Omicron',
            'invoice_group_next_id'           => 1,
            'invoice_group_identifier_format' => 'SVC-{number}',
            'invoice_group_left_pad'          => 4,
        ]);

        /* Act */
        $response = $this->get('/invoice_groups');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertDatabaseHas('ip_invoice_groups', ['invoice_group_name' => 'Service Group Omicron']);
        $this->assertResponseBodyContains($response, '<html');
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/invoice_groups');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/invoice_groups] must redirect. Got [%d].', $response->statusCode())
        );
    }
}
