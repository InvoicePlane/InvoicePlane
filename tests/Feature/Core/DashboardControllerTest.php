<?php

namespace Tests\Feature\Core;

use Dashboard;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Dashboard::class)]
#[CoversClass(Tests\Feature\Core\DashboardController::class)]
class DashboardControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->seedModel('\Modules\Dashboard\Tests\Feature\User', ['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    #[Group('crud')]
    public function it_displays_dashboard_with_overview_data(): void
    {
        /* Arrange */
        $client  = $this->seedModel('\Modules\Clients\Models\tmpClient');
        $invoice = $this->seedModel('\Modules\Invoices\Models\Invoice', [
            'client_id' => $client->id,
            'total'     => 1000,
        ]);

        /* Act */
        $response = $this->get('/dashboard');

        /* Assert */
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.index');
        $response->assertViewHas('invoice_status_totals');
        $response->assertViewHas('quote_status_totals');
    }

    #[Test]
    public function it_displays_dashboard_with_invoice_status_totals(): void
    {
        $this->seedModelMany('\Modules\Dashboard\Tests\Feature\Invoice', 5, ['invoice_status_id' => 1]); // Draft
        $this->seedModelMany('\Modules\Dashboard\Tests\Feature\Invoice', 3, ['invoice_status_id' => 2]); // Sent
        $this->seedModelMany('\Modules\Dashboard\Tests\Feature\Invoice', 7, ['invoice_status_id' => 4]); // Paid

        $response = $this->get('/dashboard/index');

        $response->assertSuccessful();
        $response->assertViewHas('invoice_status_totals');
        $response->assertViewHas('invoice_statuses');
    }

    #[Test]
    public function it_displays_dashboard_with_quote_status_totals(): void
    {
        $this->seedModelMany('\Modules\Dashboard\Tests\Feature\Quote', 4, ['quote_status_id' => 1]); // Draft
        $this->seedModelMany('\Modules\Dashboard\Tests\Feature\Quote', 2, ['quote_status_id' => 2]); // Sent
        $this->seedModelMany('\Modules\Dashboard\Tests\Feature\Quote', 3, ['quote_status_id' => 3]); // Approved

        $response = $this->get('/dashboard/index');

        $response->assertSuccessful();
        $response->assertViewHas('quote_status_totals');
        $response->assertViewHas('quote_statuses');
    }

    #[Test]
    public function it_displays_recent_invoices_on_dashboard(): void
    {
        $this->seedModelMany('\Modules\Dashboard\Tests\Feature\Invoice', 15);

        $response = $this->get('/dashboard/index');

        $response->assertSuccessful();
        $response->assertViewHas('invoices', function ($invoices): bool {
            return $invoices->count() === 10; // Limited to 10
        });
    }

    #[Test]
    public function it_displays_recent_quotes_on_dashboard(): void
    {
        $this->seedModelMany('\Modules\Dashboard\Tests\Feature\Quote', 15);

        $response = $this->get('/dashboard/index');

        $response->assertSuccessful();
        $response->assertViewHas('quotes', function ($quotes): bool {
            return $quotes->count() === 10; // Limited to 10
        });
    }

    #[Test]
    public function it_displays_overdue_invoices_on_dashboard(): void
    {
        $this->seedModelMany('\Modules\Dashboard\Tests\Feature\Invoice', 3, [
            'invoice_status_id' => 2,
            'invoice_date_due'  => now()->subDays(10),
        ]);

        $response = $this->get('/dashboard/index');

        $response->assertSuccessful();
        $response->assertViewHas('overdue_invoices', function ($invoices): bool {
            return $invoices->count() === 3;
        });
    }

    #[Test]
    public function it_displays_latest_projects_on_dashboard(): void
    {
        $this->seedModelMany('\Modules\Dashboard\Tests\Feature\Project', 5);

        $response = $this->get('/dashboard/index');

        $response->assertSuccessful();
        $response->assertViewHas('projects');
    }

    #[Test]
    public function it_displays_latest_tasks_on_dashboard(): void
    {
        $this->seedModelMany('\Modules\Dashboard\Tests\Feature\Task', 5);

        $response = $this->get('/dashboard/index');

        $response->assertSuccessful();
        $response->assertViewHas('tasks');
        $response->assertViewHas('task_statuses');
    }

    #[Test]
    public function it_uses_custom_invoice_overview_period_setting(): void
    {
        $this->seedModel('\Modules\Dashboard\Tests\Feature\Setting', [
            'setting_key'   => 'invoice_overview_period',
            'setting_value' => 'this-month',
        ]);

        $response = $this->get('/dashboard/index');

        $response->assertSuccessful();
        $response->assertViewHas('invoice_status_period', 'this_month');
    }

    #[Test]
    public function it_uses_custom_quote_overview_period_setting(): void
    {
        $this->seedModel('\Modules\Dashboard\Tests\Feature\Setting', [
            'setting_key'   => 'quote_overview_period',
            'setting_value' => 'this-quarter',
        ]);

        $response = $this->get('/dashboard/index');

        $response->assertSuccessful();
        $response->assertViewHas('quote_status_period', 'this_quarter');
    }
}
