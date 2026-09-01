<?php

namespace Tests\Feature\Regression;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * #1694 regression — Controller: Custom_fields::delete() (application/modules/custom_fields).
 *
 * Mdl_custom_fields::delete() refuses unless custom_field_table is one of the
 * static allow-listed *_custom tables and the field is unused, so the field is
 * seeded against 'ip_client_custom' with a unique label.
 */
#[Group('security')]
class Issue1694CustomFieldsDeleteCsrfTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function it_deletes_a_custom_field_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $fieldId = $this->seedCustomField();

        /* Act */
        $response = $this->postWithValidCsrfToken('/custom_fields/delete/' . $fieldId);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('custom_fields/delete must redirect. Got [%d].', $response->statusCode())
        );
        $this->assertDatabaseMissing('ip_custom_fields', ['custom_field_id' => $fieldId]);
    }

    #[Test]
    public function it_rejects_the_delete_without_a_csrf_token(): void
    {
        /* Arrange */
        $fieldId = $this->seedCustomField();

        /* Act */
        $response = $this->postWithoutCsrfToken('/custom_fields/delete/' . $fieldId);

        /* Assert */
        self::assertGreaterThanOrEqual(400, $response->statusCode());
        $this->assertDatabaseHas('ip_custom_fields', ['custom_field_id' => $fieldId]);
    }

    private function seedCustomField(): int
    {
        return $this->databaseInsert('ip_custom_fields', [
            'custom_field_table' => 'ip_client_custom',
            'custom_field_label' => 'Issue 1694 Field ' . bin2hex(random_bytes(4)),
            'custom_field_type'  => 'TEXT',
        ]);
    }
}
