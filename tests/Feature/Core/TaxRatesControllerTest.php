<?php

namespace Tests\Feature\Core;

use Modules\Core\Models\User;
use Tests\Concerns\InteractsWithDatabase;


use Tests\TestCase;

class TaxRatesControllerTest extends TestCase
{
    use InteractsWithDatabase;

    #[Test]
    public function it_displays_tax_rates_list()
    {
        /* Arrange */
        $user = $this->seedModel('User');
        $this->actingAs($user);

        /* Arrange */
        $taxRate = $this->seedModel('TaxRate');

        /* Act */
        $response = $this->get(route('tax_rates.index'));

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

        $response = $this->post(route('tax_rates.form'), $taxRateData);

        $response->assertRedirect(route('tax_rates.index'));
        $this->assertDatabaseHas('ip_tax_rates', [
            'tax_rate_name'    => 'VAT',
            'tax_rate_percent' => 21.00,
        ]);
    }

    #[Test]
    public function it_stores_tax_rate_via_form_store()
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
        $response = $this->post(route('tax_rates.formStore'), [
            'tax_rate_name'    => 'VAT',
            'tax_rate_percent' => '20.00',
            'btn_submit'       => true,
        ]);

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
    }

    #[Test]
    public function it_standardizes_tax_rate_percent_on_creation(): void
    {
        $taxRateData = [
            'tax_rate_name'    => 'Sales Tax',
            'tax_rate_percent' => '15,50', // European format
        ];

        $response = $this->post(route('tax_rates.form'), $taxRateData);

        $response->assertRedirect(route('tax_rates.index'));
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

        $response = $this->post(route('tax_rates.form', ['id' => $taxRate->tax_rate_id]), $updateData);

        $response->assertRedirect(route('tax_rates.index'));
        $this->assertDatabaseHas('ip_tax_rates', [
            'tax_rate_id'      => $taxRate->tax_rate_id,
            'tax_rate_name'    => 'Updated Tax',
            'tax_rate_percent' => 19.00,
        ]);
    }

    #[Test]
    public function it_redirects_when_cancel_button_is_clicked()
    {
        /* Act */
        $response = $this->post(route('tax_rates.form'), [
            'btn_cancel' => true,
        ]);

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
    }

    #[Test]
    public function it_deletes_tax_rate()
    {
        /* Arrange */
        $taxRate = $this->seedModel('TaxRate');

        /* Act */
        $response = $this->get(route('tax_rates.delete', ['id' => $taxRate->id]));

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
        $this->assertDatabaseMissing('ip_tax_rates', ['tax_rate_id' => $taxRate->id]);
    }

    #[Test]
    public function it_cancels_tax_rate_form_and_redirects(): void
    {
        $response = $this->post(route('tax_rates.form'), ['btn_cancel' => true]);

        $response->assertRedirect(route('tax_rates.index'));
    }

    #[Test]
    public function it_validates_tax_rate_percent_is_numeric(): void
    {
        $taxRateData = [
            'tax_rate_name'    => 'Invalid Tax',
            'tax_rate_percent' => 'not-a-number',
        ];

        $response = $this->post(route('tax_rates.form'), $taxRateData);

        $response->assertSessionHasErrors();
    }

    #[Test]
    public function it_returns_404_when_editing_nonexistent_tax_rate(): void
    {
        $response = $this->get(route('tax_rates.form', ['id' => 99999]));

        $response->assertNotFound();
    }
}
