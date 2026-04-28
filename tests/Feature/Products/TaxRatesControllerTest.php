<?php

namespace Tests\Feature\Products;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tax_Rates;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

/**
 * TaxRates Controller Feature Tests.
 *
 * Tests tax rate management including list, create, update, and delete.
 */
#[CoversClass(Tax_Rates::class)]

class TaxRatesControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    /**
     * Test index displays paginated list of tax rates.
     */
    #[Group('smoke')]
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

    /**
     * Test create displays tax rate form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_create_form(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/tax_rates/create');

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::tax_rates_form');
        $response->assertViewHas('tax_rate');

        $taxRate = $response->viewData('tax_rate');
        $this->assertNotNull($taxRate);
    }

    /**
     * Test store creates new tax rate with valid data.
     */
    #[Group('crud')]
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
        $response = $this->post('/tax_rates/store', $taxRateData);

        /* Assert */
        $response->assertRedirect('/tax_rates/index');
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_tax_rates', [
            'tax_rate_name'    => 'VAT 20%',
            'tax_rate_percent' => '20.00',
        ]);
    }

    /**
     * Test edit displays tax rate form with existing data.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_edit_form_with_existing_tax_rate(): void
    {
        /* Arrange */
        $user    = $this->seedModel('User');
        $taxRate = $this->seedModel('TaxRate');

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/tax_rates/form/' . $taxRate->tax_rate_id);

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::tax_rates_form');
        $response->assertViewHas('tax_rate');

        $viewTaxRate = $response->viewData('tax_rate');
        $this->assertEquals($taxRate->tax_rate_id, $viewTaxRate->tax_rate_id);
    }

    /**
     * Test update modifies existing tax rate.
     */
    #[Group('crud')]
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
        $response = $this->post('/tax_rates/form/' . $taxRate->tax_rate_id, $updateData);

        /* Assert */
        $response->assertRedirect('/tax_rates/index');
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_tax_rates', [
            'tax_rate_id'      => $taxRate->tax_rate_id,
            'tax_rate_name'    => 'Updated VAT',
            'tax_rate_percent' => '25.00',
        ]);
    }

    /**
     * Test destroy deletes tax rate.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_tax_rate(): void
    {
        /* Arrange */
        $user    = $this->seedModel('User');
        $taxRate = $this->seedModel('TaxRate');

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/tax_rates/delete/' . $taxRate->tax_rate_id);

        /* Assert */
        $response->assertRedirect('/tax_rates/index');
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseMissing('ip_tax_rates', [
            'tax_rate_id' => $taxRate->tax_rate_id,
        ]);
    }

    /**
     * Test tax rates are ordered correctly.
     */
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
        $taxRates     = $response->viewData('tax_rates');
        $taxRatesArray = is_array($taxRates) ? $taxRates : iterator_to_array($taxRates);

        $this->assertCount(3, $taxRatesArray);

        // Verify tax rates are ordered by percentage ascending
        $percents       = array_map(static fn ($rate): float => (float) $rate->tax_rate_percent, $taxRatesArray);
        $sortedPercents = $percents;
        sort($sortedPercents);
        $this->assertSame($sortedPercents, $percents, 'Tax rates must be ordered by percentage ascending.');
    }

    /**
     * Test tax rate with zero percent.
     */
    #[Group('crud')]
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
        $response = $this->post('/tax_rates/store', $taxRateData);

        /* Assert */
        $response->assertRedirect('/tax_rates/index');
        $this->assertDatabaseHas('ip_tax_rates', [
            'tax_rate_name'    => 'No Tax',
            'tax_rate_percent' => '0.00',
        ]);
    }
}
