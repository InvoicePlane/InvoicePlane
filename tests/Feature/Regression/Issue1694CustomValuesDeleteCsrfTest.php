<?php

namespace Tests\Feature\Regression;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * #1694 regression — Controller: Custom_values::delete() (application/modules/custom_values).
 *
 * Mdl_custom_values::delete() only bails when the value is "used"; pointing it
 * at a non-existent field id takes the early not-used path so the row deletes.
 */
#[Group('security')]
class Issue1694CustomValuesDeleteCsrfTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function it_deletes_a_custom_value_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $valueId = $this->seedCustomValue();

        /* Act */
        $response = $this->postWithValidCsrfToken('/custom_values/delete/' . $valueId);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('custom_values/delete must redirect. Got [%d].', $response->statusCode())
        );
        $this->assertDatabaseMissing('ip_custom_values', ['custom_values_id' => $valueId]);
    }

    #[Test]
    public function it_rejects_the_delete_without_a_csrf_token(): void
    {
        /* Arrange */
        $valueId = $this->seedCustomValue();

        /* Act */
        $response = $this->postWithoutCsrfToken('/custom_values/delete/' . $valueId);

        /* Assert */
        self::assertGreaterThanOrEqual(400, $response->statusCode());
        $this->assertDatabaseHas('ip_custom_values', ['custom_values_id' => $valueId]);
    }

    private function seedCustomValue(): int
    {
        return $this->databaseInsert('ip_custom_values', [
            'custom_values_field' => 999999,
            'custom_values_value' => 'Issue 1694 Value',
        ]);
    }
}
