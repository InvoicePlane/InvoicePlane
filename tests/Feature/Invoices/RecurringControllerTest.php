<?php

namespace Tests\Feature\Invoices;

use Modules\Crm\Controllers\InvoicesController as GuestInvoicesController;
use Modules\Invoices\Models\Invoice;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;
use Tests\Feature\Core\FeatureTestCase;

/**
 * InvoicesController (CRM/Guest) Feature Tests.
 *
 * Tests guest portal invoice viewing.
 */
#[CoversClass(GuestInvoicesController::class)]

class RecurringControllerTest extends FeatureTestCase
{
    use InteractsWithDatabase;

    /**
     * Test recurring invoices index displays all recurring configurations.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_list_of_recurring_invoices(): void
    {
        /* Arrange */
        $user       = $this->seedModel('User');
        $recurring1 = $this->seedModel('InvoicesRecurring');
        $recurring2 = $this->seedModel('InvoicesRecurring');

        /* Act */
        $response = $this->actingAs($user)->get(route('invoices.recurring'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('invoices::recurring_index');
        $response->assertViewHas('recurring_invoices');
        $recurringInvoices = $response->viewData('recurring_invoices');
        $this->assertGreaterThanOrEqual(2, $recurringInvoices->count());
    }

    /**
     * Test recurring invoices index includes frequency options.
     */
    #[Group('smoke')]
    #[Test]
    public function it_includes_recur_frequencies_in_view_data(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $response = $this->actingAs($user)->get(route('invoices.recurring'));

        /* Assert */
        $response->assertOk();
        $response->assertViewHas('recur_frequencies');
        $recurFrequencies = $response->viewData('recur_frequencies');
        $this->assertIsArray($recurFrequencies);
    }

    /**
     * Test pagination works correctly for recurring invoices.
     */
    #[Test]
    public function it_paginates_recurring_invoices_correctly(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        $this->seedModelMany('InvoicesRecurring', 20);

        /* Act */
        $response = $this->actingAs($user)->get(route('invoices.recurring'));

        /* Assert */
        $response->assertOk();
        $recurringInvoices = $response->viewData('recurring_invoices');
        $this->assertLessThanOrEqual(15, $recurringInvoices->count());
    }

    /**
     * Test stopping a recurring invoice sets status to 0.
     */
    #[Test]
    public function it_stops_recurring_invoice_and_sets_status_to_zero(): void
    {
        /* Arrange */
        $user      = $this->seedModel('User');
        $recurring = $this->seedModel('InvoicesRecurring', ['recur_status' => 1]);

        /* Act */
        $response = $this->actingAs($user)->post(
            route('invoices.recurring.stop', ['id' => $recurring->invoice_recurring_id])
        );

        /* Assert */
        $response->assertRedirect();
        $recurring->refresh();
        $this->assertEquals(0, $recurring->recur_status);
    }

    /**
     * Test stopping recurring invoice redirects to index.
     */
    #[Group('smoke')]
    #[Test]
    public function it_redirects_to_index_after_stopping_recurring_invoice(): void
    {
        /* Arrange */
        $user      = $this->seedModel('User');
        $recurring = $this->seedModel('InvoicesRecurring');

        /* Act */
        $response = $this->actingAs($user)->post(
            route('invoices.recurring.stop', ['id' => $recurring->invoice_recurring_id])
        );

        /* Assert */
        $response->assertRedirect();
    }

    /**
     * Test stopping non-existent recurring invoice throws 404.
     */
    #[Test]
    public function it_throws_404_when_stopping_non_existent_recurring_invoice(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /** @var array{id: int} $stopParams */
        $stopParams = [
            'id' => 99999,
        ];

        /* Act */
        $response = $this->actingAs($user)->post(route('invoices.recurring.stop', $stopParams));

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test deleting a recurring invoice removes it from database.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_recurring_invoice_from_database(): void
    {
        /* Arrange */
        $user        = $this->seedModel('User');
        $recurring   = $this->seedModel('InvoicesRecurring');
        $recurringId = $recurring->invoice_recurring_id;

        /** @var array{id: int} $deleteParams */
        $deleteParams = [
            'id' => $recurringId,
        ];

        /* Act */
        $response = $this->actingAs($user)->post(route('invoices.recurring.delete', $deleteParams));

        /* Assert */
        $response->assertRedirect();
        $this->assertNull(InvoicesRecurring::find($recurringId));
    }

    /**
     * Test deleting recurring invoice redirects to index.
     */
    #[Group('smoke')]
    #[Test]
    public function it_redirects_to_index_after_deleting_recurring_invoice(): void
    {
        /* Arrange */
        $user      = $this->seedModel('User');
        $recurring = $this->seedModel('InvoicesRecurring');

        /** @var array{id: int} $deleteParams */
        $deleteParams = [
            'id' => $recurring->invoice_recurring_id,
        ];

        /* Act */
        $response = $this->actingAs($user)->post(route('invoices.recurring.delete', $deleteParams));

        /* Assert */
        $response->assertRedirect();
    }

    /**
     * Test deleting non-existent recurring invoice throws 404.
     */
    #[Test]
    public function it_throws_404_when_deleting_non_existent_recurring_invoice(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /** @var array{id: int} $deleteParams */
        $deleteParams = [
            'id' => 99999,
        ];

        /* Act */
        $response = $this->actingAs($user)->post(route('invoices.recurring.delete', $deleteParams));

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test index includes filter display configuration.
     */
    #[Group('smoke')]
    #[Test]
    public function it_includes_filter_configuration_in_view_data(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $response = $this->actingAs($user)->get(route('invoices.recurring'));

        /* Assert */
        $response->assertOk();
        $response->assertViewHas('filter_display');
        $response->assertViewHas('filter_placeholder');
        $response->assertViewHas('filter_method');
        $filterDisplay = $response->viewData('filter_display');
        $filterMethod  = $response->viewData('filter_method');
        $this->assertTrue($filterDisplay);
        $this->assertEquals('filter_invoices_recuring', $filterMethod);
    }
}
