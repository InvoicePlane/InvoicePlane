<?php

namespace Tests\Feature\Regression;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * #1694 regression — Controller: Units::delete() (application/modules/units).
 */
#[Group('security')]
class Issue1694UnitsDeleteCsrfTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function it_deletes_a_unit_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $unitId = (int) $this->seedModel('Unit', ['unit_name' => 'Issue 1694 Unit'])->unit_id;

        /* Act */
        $response = $this->postWithValidCsrfToken('/units/delete/' . $unitId);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('units/delete must redirect. Got [%d].', $response->statusCode())
        );
        $this->assertDatabaseMissing('ip_units', ['unit_id' => $unitId]);
    }

    #[Test]
    public function it_rejects_the_delete_without_a_csrf_token(): void
    {
        /* Arrange */
        $unitId = (int) $this->seedModel('Unit', ['unit_name' => 'Issue 1694 Unit No Token'])->unit_id;

        /* Act */
        $response = $this->postWithoutCsrfToken('/units/delete/' . $unitId);

        /* Assert */
        self::assertGreaterThanOrEqual(400, $response->statusCode());
        $this->assertDatabaseHas('ip_units', ['unit_id' => $unitId]);
    }
}
