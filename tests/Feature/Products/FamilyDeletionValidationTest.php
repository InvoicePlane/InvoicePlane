<?php

namespace Feature\Products;

use Modules\Products\Models\Family;
use Modules\Products\Models\Product;
use Modules\Products\Services\FamilyService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

/**
 * FamilyService Deletion Validation Tests.
 *
 * Tests business rules for family deletion:
 * - Families with products cannot be deleted
 */
#[CoversClass(FamilyService::class)]
#[CoversClass(Feature\Products\FamilyDeletionValidation::class)]

class FamilyDeletionValidationTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    private FamilyService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FamilyService();
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_allows_deletion_of_family_without_products(): void
    {
        /* Arrange */
        $family = $this->seedModel('Family', ['family_name' => 'Empty Family']);

        /* Act */
        $canDelete = $this->service->canDelete($family->family_id);
        $blockers  = $this->service->getDeletionBlockers($family->family_id);

        /* Assert */
        $this->assertTrue($canDelete);
        $this->assertEquals(0, $blockers['products']);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_with_products(): void
    {
        /* Arrange */
        $family = $this->seedModel('Family');
        $this->seedModel('Product', ['family_id' => $family->family_id]);

        /* Act */
        $canDelete = $this->service->canDelete($family->family_id);
        $blockers  = $this->service->getDeletionBlockers($family->family_id);

        /* Assert */
        $this->assertFalse($canDelete);
        $this->assertGreaterThan(0, $blockers['products']);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_with_multiple_products(): void
    {
        /* Arrange */
        $family = $this->seedModel('Family');
        $this->seedModelMany('Product', 5, ['family_id' => $family->family_id]);

        /* Act */
        $canDelete = $this->service->canDelete($family->family_id);
        $blockers  = $this->service->getDeletionBlockers($family->family_id);

        /* Assert */
        $this->assertFalse($canDelete);
        $this->assertEquals(5, $blockers['products']);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_allows_deletion_after_products_removed(): void
    {
        /* Arrange */
        $family  = $this->seedModel('Family');
        $product = $this->seedModel('Product', ['family_id' => $family->family_id]);

        // Initially cannot delete
        $this->assertFalse($this->service->canDelete($family->family_id));

        // Remove product
        $product->delete();

        /* Act */
        $canDelete = $this->service->canDelete($family->family_id);

        /* Assert */
        $this->assertTrue($canDelete);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_returns_correct_blocker_structure(): void
    {
        /* Arrange */
        $family = $this->seedModel('Family');

        /* Act */
        $blockers = $this->service->getDeletionBlockers($family->family_id);

        /* Assert */
        $this->assertIsArray($blockers);
        $this->assertArrayHasKey('products', $blockers);
    }
}
