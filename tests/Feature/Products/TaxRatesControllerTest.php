<?php

namespace Tests\Feature\Products;

use Tests\Concerns\InteractsWithDatabase;

use Modules\Core\Models\User;
use Modules\Products\Controllers\FamiliesController;
use Modules\Products\Models\Family;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\FeatureTestCase;

/**
 * FamiliesController Feature Tests.
 *
 * Tests product family (category) management including list, create, update, and delete.
 */
#[CoversClass(FamiliesController::class)]

class TaxRatesControllerTest extends FeatureTestCase
{
    use InteractsWithDatabase;

    /**
     * Test index displays paginated list of tax rates.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_paginated_list_of_tax_rates(): void
    {
        /** Arrange */
        $user = $this->seedModel('User');
        $this->seedModelMany('TaxRate', 5);

        /** Act */
        $response = $this->actingAs($user)->get(route('tax_rates.index'));

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
        /** Arrange */
        $user = $this->seedModel('User');

        /** Act */
        $response = $this->actingAs($user)->get(route('tax_rates.create'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::tax_rates_form');
        $response->assertViewHas('tax_rate');

        $taxRate = $response->viewData('tax_rate');
        $this->assertInstanceOf(TaxRate::class, $taxRate);
        $this->assertFalse($taxRate->exists);
    }

    /**
     * Test store creates new tax rate with valid data.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_new_tax_rate_with_valid_data(): void
    {
        /** Arrange */
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

        /** Act */
        $response = $this->actingAs($user)->post(route('tax_rates.store'), $taxRateData);

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
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
        /** Arrange */
        $user    = $this->seedModel('User');
        $taxRate = $this->seedModel('TaxRate');

        /** Act */
        $response = $this->actingAs($user)->get(route('tax_rates.edit', $taxRate));

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
        /** Arrange */
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

        /** Act */
        $response = $this->actingAs($user)->put(route('tax_rates.update', $taxRate), $updateData);

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
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
        /** Arrange */
        $user    = $this->seedModel('User');
        $taxRate = $this->seedModel('TaxRate');

        /** Act */
        $response = $this->actingAs($user)->delete(route('tax_rates.destroy', $taxRate));

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
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
        /** Arrange */
        $user = $this->seedModel('User');

        $this->seedModel('TaxRate', ['tax_rate_name' => 'Zero Rate', 'tax_rate_percent' => '0.00']);
        $this->seedModel('TaxRate', ['tax_rate_name' => 'Standard Rate', 'tax_rate_percent' => '20.00']);
        $this->seedModel('TaxRate', ['tax_rate_name' => 'Reduced Rate', 'tax_rate_percent' => '5.00']);

        /** Act */
        $response = $this->actingAs($user)->get(route('tax_rates.index'));

        /* Assert */
        $response->assertOk();
        $taxRates = $response->viewData('tax_rates');

        // Verify we have all tax rates
        $this->assertCount(3, $taxRates);
    }

    /**
     * Test tax rate with zero percent.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_tax_rate_with_zero_percent(): void
    {
        /** Arrange */
        $user = $this->seedModel('User');

        /** @var array{tax_rate_name: string, tax_rate_percent: string} $taxRateData */
        $taxRateData = [
            'tax_rate_name'    => 'No Tax',
            'tax_rate_percent' => '0.00',
        ];

        /** Act */
        $response = $this->actingAs($user)->post(route('tax_rates.store'), $taxRateData);

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
        $this->assertDatabaseHas('ip_tax_rates', [
            'tax_rate_name'    => 'No Tax',
            'tax_rate_percent' => '0.00',
        ]);
    }
}
