<?php

namespace Tests\Feature\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Modules\Invoices\Controllers\RecurringController;
use Modules\Invoices\Models\InvoicesRecurring;

/**
 * RecurringController Feature Tests
 * 
 * Comprehensive test coverage for recurring invoice management
 */
#[CoversClass(RecurringController::class)]
class RecurringControllerTest extends TestCase
{
    /**
     * Test recurring invoices index displays all recurring configurations
     */
    #[Test]
    public function it_displays_list_of_recurring_invoices(): void
    {
        /** Arrange */
        $controller = new RecurringController();
        $recurring1 = InvoicesRecurring::factory()->create();
        $recurring2 = InvoicesRecurring::factory()->create();
        
        /** Act */
        $response = $controller->index();
        
        /** Assert */
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $viewData = $response->getData();
        $this->assertArrayHasKey('recurring_invoices', $viewData);
        $this->assertGreaterThanOrEqual(2, $viewData['recurring_invoices']->count());
    }
    
    /**
     * Test recurring invoices index includes frequency options
     */
    #[Test]
    public function it_includes_recur_frequencies_in_view_data(): void
    {
        /** Arrange */
        $controller = new RecurringController();
        
        /** Act */
        $response = $controller->index();
        
        /** Assert */
        $viewData = $response->getData();
        $this->assertArrayHasKey('recur_frequencies', $viewData);
        $this->assertIsArray($viewData['recur_frequencies']);
    }
    
    /**
     * Test pagination works correctly for recurring invoices
     */
    #[Test]
    public function it_paginates_recurring_invoices_correctly(): void
    {
        /** Arrange */
        $controller = new RecurringController();
        InvoicesRecurring::factory()->count(20)->create();
        
        /** Act */
        $response = $controller->index(0);
        
        /** Assert */
        $viewData = $response->getData();
        $this->assertLessThanOrEqual(15, $viewData['recurring_invoices']->count());
    }
    
    /**
     * Test stopping a recurring invoice sets status to 0
     */
    #[Test]
    public function it_stops_recurring_invoice_and_sets_status_to_zero(): void
    {
        /** Arrange */
        $controller = new RecurringController();
        $recurring = InvoicesRecurring::factory()->create(['recur_status' => 1]);
        
        /** Act */
        $response = $controller->stop($recurring->invoice_recurring_id);
        
        /** Assert */
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $recurring->refresh();
        $this->assertEquals(0, $recurring->recur_status);
    }
    
    /**
     * Test stopping recurring invoice redirects to index
     */
    #[Test]
    public function it_redirects_to_index_after_stopping_recurring_invoice(): void
    {
        /** Arrange */
        $controller = new RecurringController();
        $recurring = InvoicesRecurring::factory()->create();
        
        /** Act */
        $response = $controller->stop($recurring->invoice_recurring_id);
        
        /** Assert */
        $this->assertEquals('invoices.recurring.index', $response->getTargetUrl());
    }
    
    /**
     * Test stopping non-existent recurring invoice throws 404
     */
    #[Test]
    public function it_throws_404_when_stopping_non_existent_recurring_invoice(): void
    {
        /** Arrange */
        $controller = new RecurringController();
        
        /** Assert */
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        
        /** Act */
        $controller->stop(99999);
    }
    
    /**
     * Test deleting a recurring invoice removes it from database
     */
    #[Test]
    public function it_deletes_recurring_invoice_from_database(): void
    {
        /** Arrange */
        $controller = new RecurringController();
        $recurring = InvoicesRecurring::factory()->create();
        $recurringId = $recurring->invoice_recurring_id;
        
        /** Act */
        $response = $controller->delete($recurringId);
        
        /** Assert */
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertNull(InvoicesRecurring::find($recurringId));
    }
    
    /**
     * Test deleting recurring invoice redirects to index
     */
    #[Test]
    public function it_redirects_to_index_after_deleting_recurring_invoice(): void
    {
        /** Arrange */
        $controller = new RecurringController();
        $recurring = InvoicesRecurring::factory()->create();
        
        /** Act */
        $response = $controller->delete($recurring->invoice_recurring_id);
        
        /** Assert */
        $this->assertEquals('invoices.recurring.index', $response->getTargetUrl());
    }
    
    /**
     * Test deleting non-existent recurring invoice throws 404
     */
    #[Test]
    public function it_throws_404_when_deleting_non_existent_recurring_invoice(): void
    {
        /** Arrange */
        $controller = new RecurringController();
        
        /** Assert */
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        
        /** Act */
        $controller->delete(99999);
    }
    
    /**
     * Test index includes filter display configuration
     */
    #[Test]
    public function it_includes_filter_configuration_in_view_data(): void
    {
        /** Arrange */
        $controller = new RecurringController();
        
        /** Act */
        $response = $controller->index();
        
        /** Assert */
        $viewData = $response->getData();
        $this->assertTrue($viewData['filter_display']);
        $this->assertNotEmpty($viewData['filter_placeholder']);
        $this->assertEquals('filter_invoices_recuring', $viewData['filter_method']);
    }
}
