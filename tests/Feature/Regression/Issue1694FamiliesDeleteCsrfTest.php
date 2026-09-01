<?php

namespace Tests\Feature\Regression;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * #1694 regression — Controller: Families::delete() (application/modules/families).
 */
#[Group('security')]
class Issue1694FamiliesDeleteCsrfTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function it_deletes_a_family_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $familyId = (int) $this->seedModel('Family', ['family_name' => 'Issue 1694 Family'])->family_id;

        /* Act */
        $response = $this->postWithValidCsrfToken('/families/delete/' . $familyId);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('families/delete must redirect. Got [%d].', $response->statusCode())
        );
        $this->assertDatabaseMissing('ip_families', ['family_id' => $familyId]);
    }

    #[Test]
    public function it_rejects_the_delete_without_a_csrf_token(): void
    {
        /* Arrange */
        $familyId = (int) $this->seedModel('Family', ['family_name' => 'Issue 1694 Family No Token'])->family_id;

        /* Act */
        $response = $this->postWithoutCsrfToken('/families/delete/' . $familyId);

        /* Assert */
        self::assertGreaterThanOrEqual(400, $response->statusCode());
        $this->assertDatabaseHas('ip_families', ['family_id' => $familyId]);
    }
}
