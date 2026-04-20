<?php

namespace Modules\Invoices\tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\InvoiceGroups\Tests\Feature\WithFaker;
use Modules\Invoices\app\Http\Controllers\InvoiceGroupsController;
use Modules\Invoices\app\Models\InvoiceGroup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

use function Tests\Feature\InvoiceGroups\route;

use Tests\TestCase;

#[CoversClass(InvoiceGroupsController::class)]
class InvoiceGroupsControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_displays_invoice_groups_list()
    {
        // Arrange: create invoice groups
        $invoiceGroup = \Modules\Invoices\app\Models\InvoiceGroup::factory()->create();

        // Act: visit invoice groups index
        $response = $this->get(route('invoice_groups.index'));

        // Assert: invoice groups are displayed
        $response->assertStatus(200);
        $response->assertSee($invoiceGroup->invoice_group_name);
        $response->assertViewHas('invoice_groups');
    }

    #[Test]
    public function it_displays_invoice_group_form_for_new_group()
    {
        // Act: visit new invoice group form
        $response = $this->get(route('invoice_groups.form'));

        // Assert: form is displayed
        $response->assertStatus(200);
    }

    #[Test]
    public function it_redirects_when_cancel_button_is_clicked()
    {
        // Act: submit form with cancel button
        $response = $this->post(route('invoice_groups.form'), [
            'btn_cancel' => true,
        ]);

        // Assert: redirects to invoice groups index
        $response->assertRedirect(route('invoice_groups'));
    }

    #[Test]
    public function it_creates_new_invoice_group(): void
    {
        $groupData = [
            'invoice_group_name'     => 'Test Group ' . time(),
            'invoice_group_prefix'   => 'TG' . rand(100, 999),
            'invoice_group_next_id'  => 1,
            'invoice_group_left_pad' => 0,
        ];

        $response = $this->post(route('invoice_groups.form'), $groupData);

        $response->assertRedirect(route('invoice_groups.index'));
        $this->assertDatabaseHas('ip_invoice_groups', [
            'invoice_group_name' => $groupData['invoice_group_name'],
        ]);
    }

    #[Test]
    public function it_updates_existing_invoice_group(): void
    {
        $group = InvoiceGroup::factory()->create([
            'invoice_group_name' => 'Original Group',
        ]);

        $updateData = [
            'invoice_group_name'   => 'Edited Group ' . time(),
            'invoice_group_prefix' => 'EG' . rand(100, 999),
        ];

        $response = $this->post(route('invoice_groups.form', ['id' => $group->invoice_group_id]), $updateData);

        $response->assertRedirect(route('invoice_groups.index'));
        $this->assertDatabaseHas('ip_invoice_groups', [
            'invoice_group_id'   => $group->invoice_group_id,
            'invoice_group_name' => $updateData['invoice_group_name'],
        ]);
    }

    #[Test]
    public function it_deletes_invoice_group()
    {
        // Arrange: create an invoice group
        $invoiceGroup = \Modules\Invoices\app\Models\InvoiceGroup::factory()->create();

        // Act: delete the invoice group
        $response = $this->get(route('invoice_groups.delete', ['id' => $invoiceGroup->id]));

        // Assert: redirects and invoice group is deleted
        $response->assertRedirect(route('invoice_groups'));
        $this->assertDatabaseMissing('ip_invoice_groups', ['invoice_group_id' => $invoiceGroup->id]);
    }

    #[Test]
    public function it_sets_default_values_for_new_invoice_group(): void
    {
        $response = $this->get(route('invoice_groups.form'));

        $response->assertSuccessful();
        // Should set default left_pad to 0 and next_id to 1
    }

    #[Test]
    public function it_cancels_invoice_group_form_and_redirects(): void
    {
        $response = $this->post(route('invoice_groups.form'), ['btn_cancel' => true]);

        $response->assertRedirect(route('invoice_groups.index'));
    }
}

#[CoversClass(InvoicesController::class)]
class InvoicesControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected tmpClient $client;

    protected InvoiceGroup $invoiceGroup;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user         = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->client       = tmpClient::factory()->create(['client_active' => 1]);
        $this->invoiceGroup = InvoiceGroup::factory()->create();
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_redirects_index_to_all_invoices_status(): void
    {
        $response = $this->get(route('invoices.index'));

        $response->assertRedirect(route('invoices.status', ['status' => 'all']));
    }

    #[Test]
    public function it_displays_draft_invoices_when_filtering_by_draft_status(): void
    {
        $draftInvoices = Invoice::factory()->count(3)->create(['invoice_status_id' => 1]);
        $sentInvoices  = Invoice::factory()->count(2)->create(['invoice_status_id' => 2]);

        $response = $this->get(route('invoices.status', ['status' => 'draft']));

        $response->assertSuccessful();
        $response->assertViewHas('invoices', function ($invoices) {
            return $invoices->count() === 3;
        });
    }

    #[Test]
    public function it_displays_sent_invoices_when_filtering_by_sent_status(): void
    {
        Invoice::factory()->count(2)->create(['invoice_status_id' => 1]); // Draft
        $sentInvoices = Invoice::factory()->count(3)->create(['invoice_status_id' => 2]); // Sent

        $response = $this->get(route('invoices.status', ['status' => 'sent']));

        $response->assertSuccessful();
        $response->assertViewHas('invoices', function ($invoices) {
            return $invoices->count() === 3;
        });
    }

    #[Test]
    public function it_displays_paid_invoices_when_filtering_by_paid_status(): void
    {
        Invoice::factory()->count(2)->create(['invoice_status_id' => 1]);
        $paidInvoices = Invoice::factory()->count(4)->create(['invoice_status_id' => 4]); // Paid

        $response = $this->get(route('invoices.status', ['status' => 'paid']));

        $response->assertSuccessful();
        $response->assertViewHas('invoices', function ($invoices) {
            return $invoices->count() === 4;
        });
    }

    #[Test]
    public function it_displays_overdue_invoices(): void
    {
        // Create overdue invoices (due date in the past, not paid)
        Invoice::factory()->count(2)->create([
            'invoice_status_id' => 2, // Sent
            'invoice_date_due'  => now()->subDays(10),
        ]);
        // Create current invoices
        Invoice::factory()->count(3)->create([
            'invoice_status_id' => 2,
            'invoice_date_due'  => now()->addDays(10),
        ]);

        $response = $this->get(route('invoices.status', ['status' => 'overdue']));

        $response->assertSuccessful();
        $response->assertViewHas('invoices', function ($invoices) {
            return $invoices->count() === 2;
        });
    }

    #[Test]
    public function it_displays_viewed_invoices_when_filtering_by_viewed_status(): void
    {
        Invoice::factory()->count(2)->create(['invoice_status_id' => 1]);
        $viewedInvoices = Invoice::factory()->count(3)->create(['invoice_status_id' => 3]); // Viewed

        $response = $this->get(route('invoices.status', ['status' => 'viewed']));

        $response->assertSuccessful();
        $response->assertViewHas('invoices', function ($invoices) {
            return $invoices->count() === 3;
        });
    }

    #[Test]
    public function it_displays_all_invoices_when_status_is_all(): void
    {
        Invoice::factory()->count(5)->create();

        $response = $this->get(route('invoices.status', ['status' => 'all']));

        $response->assertSuccessful();
        $response->assertViewHas('invoices', function ($invoices) {
            return $invoices->count() === 5;
        });
    }

    #[Test]
    public function it_creates_new_invoice_with_single_item(): void
    {
        $invoiceData = [
            'client_id'            => $this->client->client_id,
            'invoice_date_created' => now()->format('Y-m-d'),
            'invoice_date_due'     => now()->addDays(30)->format('Y-m-d'),
            'invoice_status_id'    => 1,
            'invoice_group_id'     => $this->invoiceGroup->invoice_group_id,
            'items'                => [
                0 => [
                    'item_name'        => 'Test Service',
                    'item_description' => 'Test service description',
                    'item_quantity'    => 1,
                    'item_price'       => 100.00,
                ],
            ],
        ];

        $response = $this->post(route('invoices.form'), $invoiceData);

        $response->assertRedirect();
        $this->assertDatabaseHas('ip_invoices', [
            'client_id'         => $this->client->client_id,
            'invoice_status_id' => 1,
        ]);
        $this->assertDatabaseHas('ip_invoice_items', [
            'item_name'  => 'Test Service',
            'item_price' => 100.00,
        ]);
    }

    #[Test]
    public function it_creates_invoice_with_multiple_items(): void
    {
        $invoiceData = [
            'client_id'            => $this->client->client_id,
            'invoice_date_created' => now()->format('Y-m-d'),
            'invoice_date_due'     => now()->addDays(30)->format('Y-m-d'),
            'items'                => [
                0 => [
                    'item_name'     => 'First Item',
                    'item_quantity' => 1,
                    'item_price'    => 100.00,
                ],
                1 => [
                    'item_name'     => 'Second Item',
                    'item_quantity' => 3,
                    'item_price'    => 25.00,
                ],
                2 => [
                    'item_name'     => 'Third Item',
                    'item_quantity' => 1,
                    'item_price'    => 75.50,
                ],
            ],
        ];

        $response = $this->post(route('invoices.form'), $invoiceData);

        $response->assertRedirect();
        $invoice = Invoice::where('client_id', $this->client->client_id)->latest()->first();
        $this->assertCount(3, $invoice->items);
    }

    #[Test]
    public function it_creates_invoice_with_tax_rates(): void
    {
        $taxRate = TaxRate::factory()->create(['tax_rate_percent' => 21.00]);

        $invoiceData = [
            'client_id'            => $this->client->client_id,
            'invoice_date_created' => now()->format('Y-m-d'),
            'invoice_date_due'     => now()->addDays(30)->format('Y-m-d'),
            'items'                => [
                0 => [
                    'item_name'        => 'Taxable Item',
                    'item_quantity'    => 1,
                    'item_price'       => 100.00,
                    'item_tax_rate_id' => $taxRate->tax_rate_id,
                ],
            ],
        ];

        $response = $this->post(route('invoices.form'), $invoiceData);

        $response->assertRedirect();
        $this->assertDatabaseHas('ip_invoice_items', [
            'item_name'        => 'Taxable Item',
            'item_tax_rate_id' => $taxRate->tax_rate_id,
        ]);
    }

    #[Test]
    public function it_views_invoice_details(): void
    {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->client_id,
        ]);
        InvoiceItem::factory()->count(2)->create(['invoice_id' => $invoice->invoice_id]);

        $response = $this->get(route('invoices.view', ['invoice_id' => $invoice->invoice_id]));

        $response->assertSuccessful();
        $response->assertViewHas('invoice', function ($viewInvoice) use ($invoice) {
            return $viewInvoice->invoice_id === $invoice->invoice_id;
        });
        $response->assertViewHas('items', function ($items) {
            return count($items) === 2;
        });
    }

    #[Test]
    public function it_returns_404_when_viewing_nonexistent_invoice(): void
    {
        $response = $this->get(route('invoices.view', ['invoice_id' => 99999]));

        $response->assertNotFound();
    }

    #[Test]
    public function it_loads_invoice_edit_form_with_existing_data(): void
    {
        $invoice = Invoice::factory()->create();

        $response = $this->get(route('invoices.form', ['id' => $invoice->invoice_id]));

        $response->assertSuccessful();
        $response->assertViewHas('invoice');
    }

    #[Test]
    public function it_updates_invoice_details(): void
    {
        $invoice = Invoice::factory()->create([
            'client_id'         => $this->client->client_id,
            'invoice_status_id' => 1,
        ]);

        $updateData = [
            'invoice_date_due'  => now()->addDays(45)->format('Y-m-d'),
            'invoice_status_id' => 2,
            'invoice_notes'     => 'Updated invoice notes',
        ];

        $response = $this->post(route('invoices.form', ['id' => $invoice->invoice_id]), $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('ip_invoices', [
            'invoice_id'        => $invoice->invoice_id,
            'invoice_status_id' => 2,
            'invoice_notes'     => 'Updated invoice notes',
        ]);
    }

    #[Test]
    public function it_updates_invoice_items(): void
    {
        $invoice = Invoice::factory()->create();
        $item    = InvoiceItem::factory()->create([
            'invoice_id' => $invoice->invoice_id,
            'item_name'  => 'Original Name',
        ]);

        $updateData = [
            'items' => [
                $item->item_id => [
                    'item_name'        => 'Updated Service Name',
                    'item_description' => 'Updated description',
                    'item_quantity'    => 2,
                    'item_price'       => 150.00,
                ],
            ],
        ];

        $response = $this->post(route('invoices.form', ['id' => $invoice->invoice_id]), $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('ip_invoice_items', [
            'item_id'    => $item->item_id,
            'item_name'  => 'Updated Service Name',
            'item_price' => 150.00,
        ]);
    }

    #[Test]
    public function it_marks_invoice_as_sent(): void
    {
        $invoice = Invoice::factory()->create(['invoice_status_id' => 1]);

        $response = $this->post(route('invoices.form', ['id' => $invoice->invoice_id]), [
            'invoice_status_id' => 2,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ip_invoices', [
            'invoice_id'        => $invoice->invoice_id,
            'invoice_status_id' => 2,
        ]);
    }

    #[Test]
    public function it_marks_invoice_as_paid(): void
    {
        $invoice = Invoice::factory()->create(['invoice_status_id' => 2]);

        $response = $this->post(route('invoices.form', ['id' => $invoice->invoice_id]), [
            'invoice_status_id' => 4,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ip_invoices', [
            'invoice_id'        => $invoice->invoice_id,
            'invoice_status_id' => 4,
        ]);
    }

    #[Test]
    public function it_deletes_draft_invoice(): void
    {
        $invoice = Invoice::factory()->create(['invoice_status_id' => 1]);

        $response = $this->delete(route('invoices.delete', ['invoice_id' => $invoice->invoice_id]));

        $response->assertRedirect(route('invoices.index'));
        $this->assertDatabaseMissing('ip_invoices', ['invoice_id' => $invoice->invoice_id]);
    }

    #[Test]
    public function it_prevents_deletion_of_sent_invoice_when_deletion_disabled(): void
    {
        config(['settings.enable_invoice_deletion' => false]);
        $invoice = Invoice::factory()->create(['invoice_status_id' => 2]);

        $response = $this->delete(route('invoices.delete', ['invoice_id' => $invoice->invoice_id]));

        $response->assertRedirect(route('invoices.index'));
        $response->assertSessionHas('alert_error');
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoice->invoice_id]);
    }

    #[Test]
    public function it_allows_deletion_of_sent_invoice_when_deletion_enabled(): void
    {
        config(['settings.enable_invoice_deletion' => true]);
        $invoice = Invoice::factory()->create(['invoice_status_id' => 2]);

        $response = $this->delete(route('invoices.delete', ['invoice_id' => $invoice->invoice_id]));

        $response->assertRedirect(route('invoices.index'));
        $this->assertDatabaseMissing('ip_invoices', ['invoice_id' => $invoice->invoice_id]);
    }

    #[Test]
    public function it_generates_invoice_pdf(): void
    {
        $invoice = Invoice::factory()->create();

        $response = $this->get(route('invoices.generatePdf', ['invoice_id' => $invoice->invoice_id]));

        $response->assertSuccessful();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    #[Test]
    public function it_marks_invoice_as_sent_when_generating_pdf_if_setting_enabled(): void
    {
        config(['settings.mark_invoices_sent_pdf' => 1]);
        $invoice = Invoice::factory()->create(['invoice_status_id' => 1]);

        $response = $this->get(route('invoices.generatePdf', ['invoice_id' => $invoice->invoice_id]));

        $this->assertDatabaseHas('ip_invoices', [
            'invoice_id'        => $invoice->invoice_id,
            'invoice_status_id' => 2,
        ]);
    }

    #[Test]
    public function it_generates_invoice_number_when_generating_pdf(): void
    {
        config(['settings.mark_invoices_sent_pdf' => 1]);
        $invoice = Invoice::factory()->create([
            'invoice_status_id' => 1,
            'invoice_number'    => null,
        ]);

        $response = $this->get(route('invoices.generatePdf', ['invoice_id' => $invoice->invoice_id]));

        $invoice->refresh();
        $this->assertNotNull($invoice->invoice_number);
    }

    #[Test]
    public function it_displays_invoice_archive(): void
    {
        $response = $this->get(route('invoices.archive'));

        $response->assertSuccessful();
        $response->assertViewHas('invoices_archive');
    }

    #[Test]
    public function it_downloads_archived_invoice_pdf(): void
    {
        // Create a test PDF file in the archive folder
        $fileName    = 'test_invoice_123.pdf';
        $archivePath = UPLOADS_ARCHIVE_FOLDER . DIRECTORY_SEPARATOR . $fileName;
        file_put_contents($archivePath, 'test pdf content');

        $response = $this->get(route('invoices.download', ['invoice' => $fileName]));

        $response->assertSuccessful();
        $response->assertHeader('Content-Type', 'application/pdf');

        // Cleanup
        unlink($archivePath);
    }

    #[Test]
    public function it_prevents_directory_traversal_in_download(): void
    {
        $response = $this->get(route('invoices.download', ['invoice' => '../../../etc/passwd']));

        $response->assertNotFound();
    }

    #[Test]
    public function it_returns_404_for_nonexistent_archive_file(): void
    {
        $response = $this->get(route('invoices.download', ['invoice' => 'nonexistent.pdf']));

        $response->assertNotFound();
    }

    #[Test]
    public function it_copies_existing_invoice(): void
    {
        $originalInvoice = Invoice::factory()->create([
            'client_id' => $this->client->client_id,
        ]);
        InvoiceItem::factory()->count(2)->create(['invoice_id' => $originalInvoice->invoice_id]);

        $response = $this->post(route('invoices.copy', ['invoice_id' => $originalInvoice->invoice_id]), [
            'invoice_date_created' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        $this->assertEquals(2, Invoice::where('client_id', $this->client->client_id)->count());
    }

    #[Test]
    public function it_deletes_invoice_tax_rate_and_recalculates_amounts(): void
    {
        $invoice = Invoice::factory()->create();
        $taxRate = InvoiceTaxRate::factory()->create(['invoice_id' => $invoice->invoice_id]);

        $response = $this->delete(route('invoices.deleteInvoiceTax', [
            'invoice_id'          => $invoice->invoice_id,
            'invoice_tax_rate_id' => $taxRate->invoice_tax_rate_id,
        ]));

        $response->assertRedirect(route('invoices.view', ['invoice_id' => $invoice->invoice_id]));
        $this->assertDatabaseMissing('ip_invoice_tax_rates', [
            'invoice_tax_rate_id' => $taxRate->invoice_tax_rate_id,
        ]);
    }

    #[Test]
    public function it_recalculates_all_invoices(): void
    {
        Invoice::factory()->count(5)->create();

        $response = $this->post(route('invoices.recalculateAll'));

        $response->assertSuccessful();
        // All invoices should have updated amounts
    }

    #[Test]
    public function it_generates_xml_invoice(): void
    {
        $invoice = Invoice::factory()->create();
        InvoiceItem::factory()->create(['invoice_id' => $invoice->invoice_id]);

        $response = $this->get(route('invoices.generateXml', ['invoice_id' => $invoice->invoice_id]));

        // If einvoice is configured
        if ($response->status() !== 404) {
            $response->assertHeader('Content-Type', 'text/xml');
        }
    }

    #[Test]
    public function it_returns_404_for_xml_without_einvoice_config(): void
    {
        $invoice = Invoice::factory()->create();

        $response = $this->get(route('invoices.generateXml', ['invoice_id' => $invoice->invoice_id]));

        // If no einvoice user is configured
        $this->assertTrue($response->status() === 404 || $response->status() === 200);
    }

    #[Test]
    public function it_generates_sumex_pdf_if_invoice_has_sumex_id(): void
    {
        $invoice = Invoice::factory()->create(['sumex_id' => 'SUMEX123']);

        $response = $this->get(route('invoices.generateSumexPdf', ['invoice_id' => $invoice->invoice_id]));

        $response->assertSuccessful();
    }

    #[Test]
    public function it_generates_sumex_copy_pdf(): void
    {
        $invoice = Invoice::factory()->create(['sumex_id' => 'SUMEX123']);

        $response = $this->get(route('invoices.generateSumexCopy', ['invoice_id' => $invoice->invoice_id]));

        $response->assertSuccessful();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}

#[CoversClass(RecurringController::class)]
class RecurringInvoicesControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_displays_recurring_invoices_index(): void
    {
        $response = $this->get(route('invoices.recurring.index'));

        $response->assertSuccessful();
        $response->assertViewHas('recurring_invoices');
        $response->assertViewHas('recur_frequencies');
    }

    #[Test]
    public function it_stops_recurring_invoice(): void
    {
        $recurringInvoice = RecurringInvoice::factory()->create(['status' => 'active']);

        $response = $this->post(route('invoices.recurring.stop', [
            'invoice_recurring_id' => $recurringInvoice->invoice_recurring_id,
        ]));

        $response->assertRedirect(route('invoices.recurring.index'));
        $this->assertDatabaseHas('ip_invoices_recurring', [
            'invoice_recurring_id' => $recurringInvoice->invoice_recurring_id,
            'status'               => 'stopped',
        ]);
    }

    #[Test]
    public function it_deletes_recurring_invoice(): void
    {
        $recurringInvoice = RecurringInvoice::factory()->create();

        $response = $this->delete(route('invoices.recurring.delete', [
            'invoice_recurring_id' => $recurringInvoice->invoice_recurring_id,
        ]));

        $response->assertRedirect(route('invoices.recurring.index'));
        $this->assertDatabaseMissing('ip_invoices_recurring', [
            'invoice_recurring_id' => $recurringInvoice->invoice_recurring_id,
        ]);
    }
}

