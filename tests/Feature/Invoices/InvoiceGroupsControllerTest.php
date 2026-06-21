<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Invoice_Groups Controller Feature Tests.
 *
 * Tests invoice group management (index, form, delete).
 */
class InvoiceGroupsControllerTest extends AbstractTestCase
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
            'invoice_group_name'                => 'Monthly 2024',
            'invoice_group_next_id'             => 1,
            'invoice_group_prefix'              => 'INV',
            'invoice_group_identifier_format'   => '{number}',
        ]);

        /* Act */
        $response = $this->get('/invoice_groups');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'Monthly 2024');
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
