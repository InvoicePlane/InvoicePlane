<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Tests\Feature\Core\CustomValuesController::class)]
class CustomValuesControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    #[Test]
    public function it_displays_custom_values(): void
    {
        /* Arrange */
        $customValue = $this->seedModel('CustomValue', ['name' => 'Test Value']);

        /* Act */
        $response = $this->get('/custom_values/index');

        /* Assert */
        $response->assertStatus(200);
        $response->assertSee('Test Value');
    }

    #[Test]
    public function it_displays_and_saves_custom_field(): void
    {
        /* Arrange */
        $customField     = $this->seedModel('CustomField', ['name' => 'Test Field']);
        $customValueData = [
            'value' => 'New Value',
            // add other required fields
        ];

        /* Act */
        $response = $this->post('/custom_values/field/' . ($customField->id), $customValueData);

        /* Assert */
        $this->assertDatabaseHas('custom_values', ['value' => 'New Value']);
        $response->assertRedirect('/custom_values');
    }
}
