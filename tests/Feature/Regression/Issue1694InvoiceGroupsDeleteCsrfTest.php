<?php

namespace Tests\Feature\Regression;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * #1694 regression — Controller: Invoice_groups::delete() (application/modules/invoice_groups).
 */
#[Group('security')]
class Issue1694InvoiceGroupsDeleteCsrfTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function it_deletes_an_invoice_group_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $groupId = (int) $this->seedModel('InvoiceGroup')->invoice_group_id;

        /* Act */
        $response = $this->postWithValidCsrfToken('/invoice_groups/delete/' . $groupId);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('invoice_groups/delete must redirect. Got [%d].', $response->statusCode())
        );
        $this->assertDatabaseMissing('ip_invoice_groups', ['invoice_group_id' => $groupId]);
    }

    #[Test]
    public function it_rejects_the_delete_without_a_csrf_token(): void
    {
        /* Arrange */
        $groupId = (int) $this->seedModel('InvoiceGroup')->invoice_group_id;

        /* Act */
        $response = $this->postWithoutCsrfToken('/invoice_groups/delete/' . $groupId);

        /* Assert */
        self::assertGreaterThanOrEqual(400, $response->statusCode());
        $this->assertDatabaseHas('ip_invoice_groups', ['invoice_group_id' => $groupId]);
    }
}
