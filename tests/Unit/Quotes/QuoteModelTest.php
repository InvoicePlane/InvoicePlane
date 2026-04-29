<?php

namespace Tests\Unit\Quotes;

use Mdl_Quotes;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Quotes::class)]
class QuoteModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('quotes/mdl_quotes');
        $this->model = $this->CI->mdl_quotes;
    }

    #[Test]
    public function it_has_correct_table_name(): void
    {
        $this->assertEquals('ip_quotes', $this->model->table);
    }

    #[Test]
    public function it_has_correct_primary_key(): void
    {
        $this->assertStringContainsString('quote_id', $this->model->primary_key);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_quote_statuses(): void
    {
        $statuses = $this->model->statuses();

        $this->assertIsArray($statuses);
        $this->assertCount(6, $statuses);
        $this->assertArrayHasKey('1', $statuses); // Draft
        $this->assertArrayHasKey('2', $statuses); // Sent
        $this->assertArrayHasKey('3', $statuses); // Viewed
        $this->assertArrayHasKey('4', $statuses); // Approved
        $this->assertArrayHasKey('5', $statuses); // Rejected
        $this->assertArrayHasKey('6', $statuses); // Canceled
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('client_id', $rules);
        $this->assertArrayHasKey('quote_date_created', $rules);
        $this->assertArrayHasKey('invoice_group_id', $rules);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_save_validation_rules_for_new_quote(): void
    {
        $rules = $this->model->validation_rules_save_quote();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('quote_number', $rules);
        $this->assertArrayHasKey('quote_date_created', $rules);
        $this->assertArrayHasKey('quote_date_expires', $rules);
    }

    #[Test]
    public function it_generates_url_key(): void
    {
        $urlKey = $this->model->get_url_key();

        $this->assertIsString($urlKey);
        $this->assertEquals(32, strlen($urlKey));
    }

    #[Group('smoke')]
    #[Test]
    public function it_is_draft_returns_self(): void
    {
        $this->assertInstanceOf(\Mdl_Quotes::class, $this->model->is_draft());
    }

    #[Group('smoke')]
    #[Test]
    public function it_is_sent_returns_self(): void
    {
        $this->assertInstanceOf(\Mdl_Quotes::class, $this->model->is_sent());
    }

    #[Group('smoke')]
    #[Test]
    public function it_is_approved_returns_self(): void
    {
        $this->assertInstanceOf(\Mdl_Quotes::class, $this->model->is_approved());
    }

    #[Test]
    public function it_calculates_expiry_date_from_created_date(): void
    {
        $created = '2024-01-01';
        $expires = $this->model->get_date_due($created);

        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $expires);
        $this->assertGreaterThanOrEqual($created, $expires);
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_and_retrieves_quote(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $client_id    = $this->seedModel('Client')->client_id;
        $quote_number = 'QUO-' . uniqid();
        $quote_id     = $this->seedModel('Quote', ['client_id' => $client_id, 'quote_number' => $quote_number])->quote_id;

        /* Act */
        $row = $this->databaseFetchOne('ip_quotes', ['quote_id' => $quote_id]);

        /* Assert */
        $this->assertNotNull($row);
        $this->assertEquals($quote_number, $row['quote_number']);
        $this->assertEquals(1, (int) $row['quote_status_id']);

        /* Cleanup */
        $this->databaseDelete('ip_quotes', ['quote_id' => $quote_id]);
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }
}
