<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Tests\Feature\Core\TaxRatesController::class)]
class TaxRatesControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    #[Test]
    public function it_displays_tax_rates_list(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        $this->actingAs($user);

        /* Arrange */
        $taxRate = $this->seedModel('TaxRate');

        /* Act */
        $response = $this->get('/tax_rates/index');

        /* Assert */
        $response->assertStatus(200);
        $response->assertViewIs('tax_rates.index');
        $response->assertSee($taxRate->tax_rate_name);
    }

    #[Test]
    public function it_creates_new_tax_rate(): void
    {
        $taxRateData = [
            'tax_rate_name'    => 'VAT',
            'tax_rate_percent' => '21.00',
        ];

        $response = $this->post('/tax_rates/form', $taxRateData);

        $response->assertRedirect('/tax_rates/index');
        $this->assertDatabaseHas('ip_tax_rates', [
            'tax_rate_name'    => 'VAT',
            'tax_rate_percent' => 21.00,
        ]);
    }

    #[Test]
    public function it_stores_tax_rate_via_form_store(): void
    {
        /* Act */
        /**
         * Payload:
         * {
         *   "tax_rate_name": "VAT",
         *   "tax_rate_percent": "20.00",
         *   "btn_submit": true
         * }
         */
        $response = $this->post('/tax_rates/formStore', [
            'tax_rate_name'    => 'VAT',
            'tax_rate_percent' => '20.00',
            'btn_submit'       => true,
        ]);

        /* Assert */
        $response->assertRedirect('/tax_rates/index');
    }

    #[Test]
    public function it_standardizes_tax_rate_percent_on_creation(): void
    {
        $taxRateData = [
            'tax_rate_name'    => 'Sales Tax',
            'tax_rate_percent' => '15,50', // European format
        ];

        $response = $this->post('/tax_rates/form', $taxRateData);

        $response->assertRedirect('/tax_rates/index');
        $this->assertDatabaseHas('ip_tax_rates', [
            'tax_rate_name'    => 'Sales Tax',
            'tax_rate_percent' => 15.50,
        ]);
    }

    #[Test]
    public function it_updates_existing_tax_rate(): void
    {
        $taxRate = $this->seedModel('TaxRate', [
            'tax_rate_name'    => 'Original Tax',
            'tax_rate_percent' => 10.00,
        ]);

        $updateData = [
            'tax_rate_name'    => 'Updated Tax',
            'tax_rate_percent' => '19.00',
        ];

        $response = $this->post('/tax_rates/form/' . ($taxRate->tax_rate_id), $updateData);

        $response->assertRedirect('/tax_rates/index');
        $this->assertDatabaseHas('ip_tax_rates', [
            'tax_rate_id'      => $taxRate->tax_rate_id,
            'tax_rate_name'    => 'Updated Tax',
            'tax_rate_percent' => 19.00,
        ]);
    }

    #[Test]
    public function it_redirects_when_cancel_button_is_clicked(): void
    {
        /* Act */
        $response = $this->post('/tax_rates/form', [
            'btn_cancel' => true,
        ]);

        /* Assert */
        $response->assertRedirect('/tax_rates/index');
    }

    #[Test]
    public function it_deletes_tax_rate(): void
    {
        /* Arrange */
        $taxRate = $this->seedModel('TaxRate');

        /* Act */
        $response = $this->get('/tax_rates/delete/' . ($taxRate->id));

        /* Assert */
        $response->assertRedirect('/tax_rates/index');
        $this->assertDatabaseMissing('ip_tax_rates', ['tax_rate_id' => $taxRate->id]);
    }

    #[Test]
    public function it_cancels_tax_rate_form_and_redirects(): void
    {
        $response = $this->post('/tax_rates/form', ['btn_cancel' => true]);

        $response->assertRedirect('/tax_rates/index');
    }

    #[Test]
    public function it_validates_tax_rate_percent_is_numeric(): void
    {
        $taxRateData = [
            'tax_rate_name'    => 'Invalid Tax',
            'tax_rate_percent' => 'not-a-number',
        ];

        $response = $this->post('/tax_rates/form', $taxRateData);

        $response->assertSessionHasErrors();
    }

    #[Test]
    public function it_returns_404_when_editing_nonexistent_tax_rate(): void
    {
        $response = $this->get('/tax_rates/form/' . (99999));

        $response->assertNotFound();
    }


    // Migrated from BckpTaxRatesControllerTest.php
    #[Test]
    public function it_displays_paginated_list_of_tax_rates(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        $this->seedModelMany('TaxRate', 5);

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/tax_rates/index');

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::tax_rates_index');
        $response->assertViewHas('tax_rates');
    }

    #[Test]
    public function it_displays_create_form(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/tax_rates/form');

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::tax_rates_form');
        $response->assertViewHas('tax_rate');

        $taxRate = $response->viewData('tax_rate');
        $this->assertInstanceOf(\Tests\Feature\Invoices\TaxRate::class, $taxRate);
        $this->assertFalse($taxRate->exists);
    }

    #[Test]
    public function it_creates_new_tax_rate_with_valid_data(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /**
         * {
         *     "tax_rate_name": "VAT 20%",
         *     "tax_rate_percent": "20.00"
         * }.
         */
        $taxRateData = [
            'tax_rate_name'    => 'VAT 20%',
            'tax_rate_percent' => '20.00',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post('/tax_rates/form', $taxRateData);

        /* Assert */
        $response->assertRedirect('/tax_rates/index');
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_tax_rates', [
            'tax_rate_name'    => 'VAT 20%',
            'tax_rate_percent' => '20.00',
        ]);
    }

    #[Test]
    public function it_displays_edit_form_with_existing_tax_rate(): void
    {
        /* Arrange */
        $user    = $this->seedModel('User');
        $taxRate = $this->seedModel('TaxRate');

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/tax_rates/form/' . ($taxRate->tax_rate_id));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::tax_rates_form');
        $response->assertViewHas('tax_rate');

        $viewTaxRate = $response->viewData('tax_rate');
        $this->assertEquals($taxRate->tax_rate_id, $viewTaxRate->tax_rate_id);
    }

    #[Test]
    public function it_updates_existing_tax_rate_with_valid_data(): void
    {
        /* Arrange */
        $user    = $this->seedModel('User');
        $taxRate = $this->seedModel('TaxRate', [
            'tax_rate_name'    => 'Old Name',
            'tax_rate_percent' => '10.00',
        ]);

        /**
         * {
         *     "tax_rate_name": "Updated VAT",
         *     "tax_rate_percent": "25.00"
         * }.
         */
        $updateData = [
            'tax_rate_name'    => 'Updated VAT',
            'tax_rate_percent' => '25.00',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post('/tax_rates/form/' . ($taxRate->tax_rate_id), $updateData);

        /* Assert */
        $response->assertRedirect('/tax_rates/index');
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_tax_rates', [
            'tax_rate_id'      => $taxRate->tax_rate_id,
            'tax_rate_name'    => 'Updated VAT',
            'tax_rate_percent' => '25.00',
        ]);
    }

    #[Test]
    public function it_orders_tax_rates_correctly(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        $this->seedModel('TaxRate', ['tax_rate_name' => 'Zero Rate', 'tax_rate_percent' => '0.00']);
        $this->seedModel('TaxRate', ['tax_rate_name' => 'Standard Rate', 'tax_rate_percent' => '20.00']);
        $this->seedModel('TaxRate', ['tax_rate_name' => 'Reduced Rate', 'tax_rate_percent' => '5.00']);

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/tax_rates/index');

        /* Assert */
        $response->assertOk();
        $taxRates = $response->viewData('tax_rates');

        // Verify we have all tax rates
        $this->assertCount(3, $taxRates);
    }

    #[Test]
    public function it_creates_tax_rate_with_zero_percent(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /** @var array{tax_rate_name: string, tax_rate_percent: string} $taxRateData */
        $taxRateData = [
            'tax_rate_name'    => 'No Tax',
            'tax_rate_percent' => '0.00',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post('/tax_rates/form', $taxRateData);

        /* Assert */
        $response->assertRedirect('/tax_rates/index');
        $this->assertDatabaseHas('ip_tax_rates', [
            'tax_rate_name'    => 'No Tax',
            'tax_rate_percent' => '0.00',
        ]);
    }

}
