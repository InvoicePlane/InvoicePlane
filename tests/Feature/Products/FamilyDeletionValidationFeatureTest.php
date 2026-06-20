<?php

namespace Feature\Products;

use Modules\Products\Controllers\FamiliesController;
use Modules\Products\Models\Family;
use Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;

use function Tests\Feature\Invoices\route;

/**
 * FamiliesController Feature Tests.
 *
 * Tests product family (category) management including list, create, update, and delete.
 */
#[CoversClass(FamiliesController::class)]
#[CoversClass(Feature\Products\FamilyDeletionValidationFeature::class)]

class FamilyDeletionValidationFeatureTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Requires Laravel service layer — not available in CI3');
    }
    use InteractsWithDatabase;

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_deletes_family_without_products(): void
    {
        /* Arrange */
        $family = $this->seedModel('Family', ['family_name' => 'Empty Family']);

        /* Act */
        $response = $this->post(route('families.delete', ['family_id' => $family->family_id]));

        /* Assert */
        $response->assertRedirect(route('families.index'));
        $response->assertSessionHas('alert_success');
        $this->assertDatabaseMissing('ip_families', ['family_id' => $family->family_id]);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_with_products(): void
    {
        /* Arrange */
        $family = $this->seedModel('Family');
        $this->seedModel('Product', ['family_id' => $family->family_id]);

        /* Act */
        $response = $this->post(route('families.delete', ['family_id' => $family->family_id]));

        /* Assert */
        $response->assertRedirect(route('families.index'));
        $response->assertSessionHas('alert_error');
        $this->assertDatabaseHas('ip_families', ['family_id' => $family->family_id]);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_with_multiple_products(): void
    {
        /* Arrange */
        $family = $this->seedModel('Family');
        $this->seedModelMany('Product', 3, ['family_id' => $family->family_id]);

        /* Act */
        $response = $this->post(route('families.delete', ['family_id' => $family->family_id]));

        /* Assert */
        $response->assertRedirect(route('families.index'));
        $response->assertSessionHas('alert_error');
        $this->assertDatabaseHas('ip_families', ['family_id' => $family->family_id]);
    }

    #[Group('validation')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_handles_invalid_family_id(): void
    {
        /* Arrange */
        $invalidId = -1;

        /* Act */
        $response = $this->post(route('families.delete', ['family_id' => $invalidId]));

        /* Assert */
        $response->assertRedirect(route('families.index'));
        $response->assertSessionHas('alert_error');
    }

    #[Group('validation')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_handles_nonexistent_family_id(): void
    {
        /* Arrange */
        $nonexistentId = 99999;

        /* Act */
        $response = $this->post(route('families.delete', ['family_id' => $nonexistentId]));

        /* Assert */
        $response->assertRedirect(route('families.index'));
        $response->assertSessionHas('alert_error');
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_allows_deletion_after_products_removed(): void
    {
        /* Arrange */
        $family  = $this->seedModel('Family');
        $product = $this->seedModel('Product', ['family_id' => $family->family_id]);

        // Initially cannot delete
        $response1 = $this->post(route('families.delete', ['family_id' => $family->family_id]));
        $response1->assertSessionHas('alert_error');

        // Remove product
        $product->delete();

        /* Act */
        $response2 = $this->post(route('families.delete', ['family_id' => $family->family_id]));

        /* Assert */
        $response2->assertRedirect(route('families.index'));
        $response2->assertSessionHas('alert_success');
        $this->assertDatabaseMissing('ip_families', ['family_id' => $family->family_id]);
    }
}
