<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Invoice_Groups controller — application/modules/invoice_groups/controllers/Invoice_groups.php.
 *
 * Required fields (Mdl_Invoice_Groups::validation_rules): invoice_group_name,
 * invoice_group_identifier_format, invoice_group_next_id, invoice_group_left_pad.
 * The baseline seed always creates one default group, so counts assert deltas
 * against that. Absorbs Issue1694InvoiceGroupsDeleteCsrfTest.
 */
#[Group('invoice_groups')]
#[CoversClass(\Invoice_Groups::class)]
class InvoiceGroupsControllerTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    private const REQUIRED = [
        'invoice_group_name'              => 'Payload Group',
        'invoice_group_identifier_format' => 'PL-{{{id}}}',
        'invoice_group_next_id'           => '1',
        'invoice_group_left_pad'          => '4',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    #[Test]
    public function it_lists_every_invoice_group(): void
    {
        /* Arrange */
        $this->seedGroup(['invoice_group_name' => 'Quarterly Group']);
        $this->seedGroup(['invoice_group_name' => 'Yearly Group']);

        /* Act */
        $response = $this->get('/invoice_groups');

        /* Assert */
        $this->assertResponseBodyContains($response, 'Quarterly Group');
        $this->assertResponseBodyContains($response, 'Yearly Group');
    }

    // -------------------------------------------------------------------------
    // Create — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_an_invoice_group(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/invoice_groups/form', array_merge(self::REQUIRED, [
            'invoice_group_name' => 'Yearly 2026',
            'btn_submit'         => '1',
        ]));

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'invoice_groups');
        $this->assertDatabaseHas('ip_invoice_groups', ['invoice_group_name' => 'Yearly 2026', 'invoice_group_identifier_format' => 'PL-{{{id}}}']);
        $this->assertDatabaseCount('ip_invoice_groups', 2);
    }

    // -------------------------------------------------------------------------
    // Create — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_invoice_group_name(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/invoice_groups/form', array_merge(self::REQUIRED, [
            'invoice_group_name'              => '',
            'invoice_group_identifier_format' => 'NONAME-{{{id}}}',
            'btn_submit'                      => '1',
        ]));

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseMissing('ip_invoice_groups', ['invoice_group_identifier_format' => 'NONAME-{{{id}}}']);
        $this->assertDatabaseCount('ip_invoice_groups', 1);
    }

    #[Test]
    public function it_fails_to_create_without_invoice_group_identifier_format(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/invoice_groups/form', array_merge(self::REQUIRED, [
            'invoice_group_name'              => 'Missing Format Group',
            'invoice_group_identifier_format' => '',
            'btn_submit'                      => '1',
        ]));

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseMissing('ip_invoice_groups', ['invoice_group_name' => 'Missing Format Group']);
        $this->assertDatabaseCount('ip_invoice_groups', 1);
    }

    #[Test]
    public function it_fails_to_create_without_invoice_group_next_id(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/invoice_groups/form', array_merge(self::REQUIRED, [
            'invoice_group_name'    => 'Missing Next Id Group',
            'invoice_group_next_id' => '',
            'btn_submit'            => '1',
        ]));

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseMissing('ip_invoice_groups', ['invoice_group_name' => 'Missing Next Id Group']);
        $this->assertDatabaseCount('ip_invoice_groups', 1);
    }

    #[Test]
    public function it_fails_to_create_without_invoice_group_left_pad(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/invoice_groups/form', array_merge(self::REQUIRED, [
            'invoice_group_name'     => 'Missing Pad Group',
            'invoice_group_left_pad' => '',
            'btn_submit'             => '1',
        ]));

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseMissing('ip_invoice_groups', ['invoice_group_name' => 'Missing Pad Group']);
        $this->assertDatabaseCount('ip_invoice_groups', 1);
    }

    // -------------------------------------------------------------------------
    // Update — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_edit_form_for_the_requested_invoice_group_only(): void
    {
        /* Arrange */
        $target = $this->seedGroup(['invoice_group_name' => 'Editable Group']);
        $this->seedGroup(['invoice_group_name' => 'Other Group']);

        /* Act */
        $response = $this->get('/invoice_groups/form/' . $target);

        /* Assert */
        $this->assertResponseBodyContains($response, 'Editable Group');
        $this->assertResponseBodyNotContains($response, 'Other Group');
    }

    #[Test]
    public function it_updates_an_invoice_group(): void
    {
        /* Arrange */
        $id = $this->seedGroup(['invoice_group_name' => 'Original Group']);

        /* Act */
        $response = $this->post('/invoice_groups/form/' . $id, array_merge(self::REQUIRED, [
            'invoice_group_name'    => 'Renamed Group',
            'invoice_group_next_id' => '5',
            'btn_submit'            => '1',
        ]));

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'invoice_groups');
        $this->assertDatabaseHas('ip_invoice_groups', ['invoice_group_id' => $id, 'invoice_group_name' => 'Renamed Group', 'invoice_group_next_id' => 5]);
        $this->assertDatabaseMissing('ip_invoice_groups', ['invoice_group_name' => 'Original Group']);
    }

    // -------------------------------------------------------------------------
    // Update — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_update_without_invoice_group_name(): void
    {
        /* Arrange */
        $id = $this->seedGroup(['invoice_group_name' => 'Keep This Group']);

        /* Act */
        $response = $this->post('/invoice_groups/form/' . $id, array_merge(self::REQUIRED, [
            'invoice_group_name' => '',
            'btn_submit'         => '1',
        ]));

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_invoice_groups', ['invoice_group_id' => $id, 'invoice_group_name' => 'Keep This Group']);
    }

    #[Test]
    public function it_fails_to_update_without_invoice_group_identifier_format(): void
    {
        /* Arrange */
        $id = $this->seedGroup(['invoice_group_name' => 'Format Kept Group', 'invoice_group_identifier_format' => 'FK-{{{id}}}']);

        /* Act */
        $response = $this->post('/invoice_groups/form/' . $id, array_merge(self::REQUIRED, [
            'invoice_group_name'              => 'Format Kept Group',
            'invoice_group_identifier_format' => '',
            'btn_submit'                      => '1',
        ]));

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_invoice_groups', ['invoice_group_id' => $id, 'invoice_group_identifier_format' => 'FK-{{{id}}}']);
    }

    #[Test]
    public function it_fails_to_update_without_invoice_group_next_id(): void
    {
        /* Arrange */
        $id = $this->seedGroup(['invoice_group_name' => 'Next Id Kept Group', 'invoice_group_next_id' => 7]);

        /* Act */
        $response = $this->post('/invoice_groups/form/' . $id, array_merge(self::REQUIRED, [
            'invoice_group_name'    => 'Next Id Kept Group',
            'invoice_group_next_id' => '',
            'btn_submit'            => '1',
        ]));

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_invoice_groups', ['invoice_group_id' => $id, 'invoice_group_next_id' => 7]);
    }

    #[Test]
    public function it_fails_to_update_without_invoice_group_left_pad(): void
    {
        /* Arrange */
        $id = $this->seedGroup(['invoice_group_name' => 'Pad Kept Group', 'invoice_group_left_pad' => 6]);

        /* Act */
        $response = $this->post('/invoice_groups/form/' . $id, array_merge(self::REQUIRED, [
            'invoice_group_name'     => 'Pad Kept Group',
            'invoice_group_left_pad' => '',
            'btn_submit'             => '1',
        ]));

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_invoice_groups', ['invoice_group_id' => $id, 'invoice_group_left_pad' => 6]);
    }

    // -------------------------------------------------------------------------
    // Delete — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_an_invoice_group(): void
    {
        /* Arrange */
        $id   = $this->seedGroup(['invoice_group_name' => 'Deletable Group']);
        $keep = $this->seedGroup(['invoice_group_name' => 'Kept Group']);

        /* Act */
        $response = $this->post('/invoice_groups/delete/' . $id, []);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'invoice_groups');
        $this->assertDatabaseMissing('ip_invoice_groups', ['invoice_group_id' => $id]);
        $this->assertDatabaseHas('ip_invoice_groups', ['invoice_group_id' => $keep]);
    }

    // -------------------------------------------------------------------------
    // Delete — CSRF regression (#1694)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_still_deletes_an_invoice_group_when_csrf_protection_is_on_and_the_token_is_valid(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->seedGroup(['invoice_group_name' => 'CSRF Group']);

        /* Act */
        $response = $this->postWithValidCsrfToken('/invoice_groups/delete/' . $id);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'invoice_groups');
        $this->assertDatabaseMissing('ip_invoice_groups', ['invoice_group_id' => $id]);
    }

    #[Test]
    public function it_does_not_delete_an_invoice_group_when_the_csrf_token_is_missing(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->seedGroup(['invoice_group_name' => 'CSRF Group Kept']);

        /* Act */
        $response = $this->postWithoutCsrfToken('/invoice_groups/delete/' . $id);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less delete must not reach the controller.');
        $this->assertDatabaseHas('ip_invoice_groups', ['invoice_group_id' => $id, 'invoice_group_name' => 'CSRF Group Kept']);
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login_and_leaks_no_invoice_group(): void
    {
        /* Arrange */
        $this->seedGroup(['invoice_group_name' => 'Secret Group']);
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/invoice_groups');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
        $this->assertResponseBodyNotContains($response, 'Secret Group');
    }

    /** @param array<string,string> $overrides */
    private function seedGroup(array $overrides = []): int
    {
        return $this->databaseInsert('ip_invoice_groups', array_merge([
            'invoice_group_name'              => 'Seeded Group',
            'invoice_group_identifier_format' => 'SG-{{{id}}}',
            'invoice_group_next_id'           => 1,
            'invoice_group_left_pad'          => 4,
        ], $overrides));
    }
}
