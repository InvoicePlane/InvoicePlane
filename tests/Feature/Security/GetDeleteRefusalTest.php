<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Delete actions are state-changing and must refuse anything but a POST, so a link, an image tag or a prefetch in
 * another site cannot delete a record. Base_Controller answers any non-POST request whose URL contains "delete" with
 * a 404 before the controller runs, which makes the method check in Admin_Controller::ensure_valid_post_request()
 * defence in depth for these URLs (mutating it alone is an equivalent mutant here). This pins the layer that is
 * actually in front, for three controllers that use the shared guard.
 */
#[Group('security')]
final class GetDeleteRefusalTest extends AbstractTestCase
{
    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: string}>
     */
    public static function deleteActions(): array
    {
        return [
            'import'         => ['/import/delete/', 'ip_imports', 'import_id'],
            'invoice group'  => ['/invoice_groups/delete/', 'ip_invoice_groups', 'invoice_group_id'],
            'custom value'   => ['/custom_values/delete/', 'ip_custom_values', 'custom_values_id'],
        ];
    }

    #[Test]
    #[DataProvider('deleteActions')]
    public function it_answers_a_get_request_with_404_and_keeps_the_row(string $path, string $table, string $key): void
    {
        /* Arrange */
        $this->actingAsAdmin();
        $id = $this->seedRow($table);

        /* Act */
        $response = $this->get($path . $id);

        /* Assert */
        self::assertSame(404, $response->statusCode());
        $this->assertDatabaseHas($table, [$key => $id]);
    }

    #[Test]
    #[DataProvider('deleteActions')]
    public function it_deletes_the_row_with_a_post_request(string $path, string $table, string $key): void
    {
        /* Arrange: positive control */
        $this->actingAsAdmin();
        $id = $this->seedRow($table);

        /* Act */
        $this->post($path . $id);

        /* Assert */
        $this->assertDatabaseMissing($table, [$key => $id]);
    }

    private function seedRow(string $table): int
    {
        return match ($table) {
            'ip_imports'        => $this->databaseInsert($table, ['import_date' => date('Y-m-d H:i:s')]),
            'ip_invoice_groups' => $this->databaseInsert($table, ['invoice_group_name' => 'Group ' . bin2hex(random_bytes(3)), 'invoice_group_identifier_format' => '{{{id}}}', 'invoice_group_next_id' => 1, 'invoice_group_left_pad' => 0]),
            'ip_custom_values'  => $this->databaseInsert($table, ['custom_values_field' => 'f' . bin2hex(random_bytes(3)), 'custom_values_value' => 'v']),
        };
    }
}
