<?php

namespace Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Quotes;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Quotes::class)]

class QuotesControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    protected $user;

    protected $client;

    protected $quoteGroup;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user       = $this->seedModel('User', ['user_type' => 1, 'user_active' => 1]);
        $this->client     = $this->seedModel('tmpClient', ['client_active' => 1]);
        $this->quoteGroup = $this->seedModel('InvoiceGroup');
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_redirects_to_status_all(): void
    {
        /* Act */
        $response = $this->get(\Tests\Feature\Invoices\route('quotes.index'));

        /* Assert */
        $response->assertRedirect(\Tests\Feature\Invoices\route('quotes.status', ['status' => 'all']));
    }

    #[Test]
    public function it_displays_quotes_by_status(): void
    {
        /* Arrange */
        $draftQuote    = $this->seedModel('\Modules\Quotes\Models\Quote', ['status' => 'draft']);
        $sentQuote     = $this->seedModel('\Modules\Quotes\Models\Quote', ['status' => 'sent']);
        $approvedQuote = $this->seedModel('\Modules\Quotes\Models\Quote', ['status' => 'approved']);

        /* Act */
        $response = $this->get(\Tests\Feature\Invoices\route('quotes.status', ['status' => 'draft']));
        $response->assertSee($draftQuote->title);
        $response->assertDontSee($sentQuote->title);
        $response->assertDontSee($approvedQuote->title);
        $response->assertStatus(200);

        /* Act */
        $response = $this->get(\Tests\Feature\Invoices\route('quotes.status', ['status' => 'sent']));
        $response->assertSee($sentQuote->title);
        $response->assertDontSee($draftQuote->title);
        $response->assertDontSee($approvedQuote->title);
        $response->assertStatus(200);
    }

    #[Test]
    public function it_displays_quotes_index(): void
    {
        $response = $this->get(\Tests\Feature\Invoices\route('quotes.index'));

        $response->assertSuccessful();
        $response->assertViewHas('quotes');
    }

    #[Test]
    public function it_creates_new_quote_with_single_item(): void
    {
        $quoteData = [
            'client_id'          => $this->client->client_id,
            'quote_date_created' => now()->format('Y-m-d'),
            'quote_date_expires' => now()->addDays(30)->format('Y-m-d'),
            'quote_status_id'    => 1,
            'quote_group_id'     => $this->quoteGroup->invoice_group_id,
            'items'              => [
                0 => [
                    'item_name'        => 'Quoted Service',
                    'item_description' => 'Service description for quote',
                    'item_quantity'    => 1,
                    'item_price'       => 250.00,
                ],
            ],
        ];

        $response = $this->post(\Tests\Feature\Invoices\route('quotes.form'), $quoteData);

        $response->assertRedirect();
        $this->assertDatabaseHas('ip_quotes', [
            'client_id'       => $this->client->client_id,
            'quote_status_id' => 1,
        ]);
        $this->assertDatabaseHas('ip_quote_items', [
            'item_name'  => 'Quoted Service',
            'item_price' => 250.00,
        ]);
    }

    #[Test]
    public function it_creates_quote_with_multiple_items(): void
    {
        $quoteData = [
            'client_id'          => $this->client->client_id,
            'quote_date_created' => now()->format('Y-m-d'),
            'quote_date_expires' => now()->addDays(30)->format('Y-m-d'),
            'items'              => [
                0 => [
                    'item_name'     => 'Consultation',
                    'item_quantity' => 2,
                    'item_price'    => 150.00,
                ],
                1 => [
                    'item_name'     => 'Implementation',
                    'item_quantity' => 1,
                    'item_price'    => 500.00,
                ],
                2 => [
                    'item_name'     => 'Training',
                    'item_quantity' => 3,
                    'item_price'    => 100.00,
                ],
            ],
        ];

        $response = $this->post(\Tests\Feature\Invoices\route('quotes.form'), $quoteData);

        $response->assertRedirect();
        $quote = \Tests\Feature\Invoices\Quote::where('client_id', $this->client->client_id)->latest()->first();
        $this->assertCount(3, $quote->items);
    }

    #[Test]
    public function it_creates_quote_with_tax_rates(): void
    {
        $taxRate = $this->seedModel('TaxRate', ['tax_rate_percent' => 21.00]);

        $quoteData = [
            'client_id'          => $this->client->client_id,
            'quote_date_created' => now()->format('Y-m-d'),
            'quote_date_expires' => now()->addDays(15)->format('Y-m-d'),
            'items'              => [
                0 => [
                    'item_name'        => 'Taxable Service',
                    'item_quantity'    => 1,
                    'item_price'       => 200.00,
                    'item_tax_rate_id' => $taxRate->tax_rate_id,
                ],
            ],
        ];

        $response = $this->post(\Tests\Feature\Invoices\route('quotes.form'), $quoteData);

        $response->assertRedirect();
        $this->assertDatabaseHas('ip_quote_items', [
            'item_name'        => 'Taxable Service',
            'item_tax_rate_id' => $taxRate->tax_rate_id,
        ]);
    }

    #[Test]
    public function it_views_quote_details(): void
    {
        $quote = $this->seedModel('Quote', ['client_id' => $this->client->client_id]);
        $this->seedModelMany('QuoteItem', 2, ['quote_id' => $quote->quote_id]);

        $response = $this->get(\Tests\Feature\Invoices\route('quotes.view', ['quote_id' => $quote->quote_id]));

        $response->assertSuccessful();
        $response->assertViewHas('quote', function ($viewQuote) use ($quote): bool {
            return $viewQuote->quote_id === $quote->quote_id;
        });
        $response->assertViewHas('items', function ($items): bool {
            return count($items) === 2;
        });
    }

    #[Test]
    public function it_loads_quote_form(): void
    {
        $response = $this->get(\Tests\Feature\Invoices\route('quotes.form'));

        $response->assertSuccessful();
    }

    #[Test]
    public function it_generates_quote_pdf(): void
    {
        $quote = $this->seedModel('Quote');

        $response = $this->get(\Tests\Feature\Invoices\route('quotes.generatePdf', ['quote_id' => $quote->quote_id]));

        $response->assertSuccessful();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    #[Test]
    public function it_loads_quote_edit_form(): void
    {
        $quote = $this->seedModel('Quote');

        $response = $this->get(\Tests\Feature\Invoices\route('quotes.form', ['id' => $quote->quote_id]));

        $response->assertSuccessful();
        $response->assertViewHas('quote');
    }

    #[Test]
    public function it_updates_quote_details(): void
    {
        $quote = $this->seedModel('Quote', [
            'client_id'       => $this->client->client_id,
            'quote_status_id' => 1,
        ]);

        $updateData = [
            'quote_date_expires' => now()->addDays(60)->format('Y-m-d'),
            'quote_status_id'    => 2,
            'quote_notes'        => 'Updated quote with extended expiry',
        ];

        $response = $this->post(\Tests\Feature\Invoices\route('quotes.form', ['id' => $quote->quote_id]), $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id'        => $quote->quote_id,
            'quote_status_id' => 2,
            'quote_notes'     => 'Updated quote with extended expiry',
        ]);
    }

    #[Test]
    public function it_updates_quote_items_and_pricing(): void
    {
        $quote = $this->seedModel('Quote');
        $item  = $this->seedModel('QuoteItem', [
            'quote_id'  => $quote->quote_id,
            'item_name' => 'Original Name',
        ]);

        $updateData = [
            'items' => [
                $item->item_id => [
                    'item_name'        => 'Updated Service Name',
                    'item_description' => 'Updated service description',
                    'item_quantity'    => 2,
                    'item_price'       => 300.00,
                ],
            ],
        ];

        $response = $this->post(\Tests\Feature\Invoices\route('quotes.form', ['id' => $quote->quote_id]), $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('ip_quote_items', [
            'item_id'    => $item->item_id,
            'item_name'  => 'Updated Service Name',
            'item_price' => 300.00,
        ]);
    }

    #[Test]
    public function it_marks_quote_as_sent(): void
    {
        $quote = $this->seedModel('Quote', ['quote_status_id' => 1]);

        $response = $this->post(\Tests\Feature\Invoices\route('quotes.form', ['id' => $quote->quote_id]), [
            'quote_status_id' => 2,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id'        => $quote->quote_id,
            'quote_status_id' => 2,
        ]);
    }

    #[Test]
    public function it_marks_quote_as_approved(): void
    {
        $quote = $this->seedModel('Quote', ['quote_status_id' => 2]);

        $response = $this->post(\Tests\Feature\Invoices\route('quotes.form', ['id' => $quote->quote_id]), [
            'quote_status_id' => 3,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id'        => $quote->quote_id,
            'quote_status_id' => 3,
        ]);
    }

    #[Test]
    public function it_marks_quote_as_rejected(): void
    {
        $quote = $this->seedModel('Quote', ['quote_status_id' => 2]);

        $response = $this->post(\Tests\Feature\Invoices\route('quotes.form', ['id' => $quote->quote_id]), [
            'quote_status_id' => 4,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id'        => $quote->quote_id,
            'quote_status_id' => 4,
        ]);
    }

    #[Test]
    public function it_converts_quote_to_invoice(): void
    {
        $quote = $this->seedModel('Quote', ['client_id' => $this->client->client_id]);
        $this->seedModelMany('QuoteItem', 2, ['quote_id' => $quote->quote_id]);

        $convertData = [
            'invoice_date_created' => now()->format('Y-m-d'),
            'invoice_date_due'     => now()->addDays(30)->format('Y-m-d'),
        ];

        $response = $this->post(\Tests\Feature\Invoices\route('quotes.convertToInvoice', ['quote_id' => $quote->quote_id]), $convertData);

        $response->assertRedirect();
        $this->assertDatabaseHas('ip_invoices', [
            'client_id' => $this->client->client_id,
        ]);
    }

    #[Test]
    public function it_copies_existing_quote(): void
    {
        $originalQuote = $this->seedModel('Quote', ['client_id' => $this->client->client_id]);
        $this->seedModelMany('QuoteItem', 2, ['quote_id' => $originalQuote->quote_id]);

        $response = $this->post(\Tests\Feature\Invoices\route('quotes.copy', ['quote_id' => $originalQuote->quote_id]), [
            'quote_date_created' => now()->format('Y-m-d'),
            'quote_date_expires' => now()->addDays(45)->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        $this->assertEquals(2, \Tests\Feature\Invoices\Quote::where('client_id', $this->client->client_id)->count());
    }

    #[Test]
    public function it_deletes_quote(): void
    {
        $quote = $this->seedModel('Quote');

        $response = $this->delete(\Tests\Feature\Invoices\route('quotes.delete', ['quote_id' => $quote->quote_id]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('ip_quotes', ['quote_id' => $quote->quote_id]);
    }
}
