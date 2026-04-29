<?php

namespace Tests\Unit\Quotes;

use Mdl_Quote_Amounts;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Quote_Amounts::class)]
class QuoteAmountModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('quotes/mdl_quote_amounts');
        $this->model = $this->CI->mdl_quote_amounts;
    }

    #[Test]
    public function it_has_calculate_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'calculate'));
    }

    #[Test]
    public function it_has_get_global_discount_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'get_global_discount'));
    }

    #[Test]
    public function it_has_get_total_quoted_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'get_total_quoted'));
    }

    #[Test]
    public function it_has_get_status_totals_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'get_status_totals'));
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_status_totals_as_array(): void
    {
        $this->skipWithoutDatabase();

        /* Act */
        $totals = $this->model->get_status_totals('');

        /* Assert */
        $this->assertIsArray($totals);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_total_quoted_as_numeric_or_null(): void
    {
        $this->skipWithoutDatabase();

        /* Act */
        $total = $this->model->get_total_quoted();

        /* Assert: NULL when table is empty, numeric otherwise */
        $this->assertTrue($total === null || is_numeric($total));
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_global_discount_for_quote(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $this->CI->db->insert('ip_clients', [
            'client_name'          => 'QAClient_' . uniqid(),
            'client_active'        => 1,
            'client_date_created'  => date('Y-m-d H:i:s'),
            'client_date_modified' => date('Y-m-d H:i:s'),
        ]);
        $client_id = $this->CI->db->insert_id();

        $this->CI->db->insert('ip_quotes', [
            'client_id'              => $client_id,
            'user_id'                => 1,
            'invoice_group_id'       => 1,
            'quote_status_id'        => 1,
            'quote_number'           => 'QUO-GD-' . uniqid(),
            'quote_date_created'     => date('Y-m-d'),
            'quote_date_expires'     => date('Y-m-d', strtotime('+30 days')),
            'quote_discount_amount'  => 0,
            'quote_discount_percent' => 0,
            'quote_password'         => '',
            'quote_url_key'          => bin2hex(random_bytes(16)),
        ]);
        $quote_id = $this->CI->db->insert_id();

        /* Act */
        $discount = $this->model->get_global_discount($quote_id);

        /* Assert */
        $this->assertIsNumeric($discount);
        $this->assertEquals(0.0, (float) $discount);

        /* Cleanup */
        $this->CI->db->delete('ip_quotes', ['quote_id' => $quote_id]);
        $this->CI->db->delete('ip_clients', ['client_id' => $client_id]);
    }
}
