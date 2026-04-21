<?php

namespace Modules\Core\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Tests\Feature\Auth\route;

use Tests\TestCase;

class CustomFieldsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_custom_fields_list()
    {
        $this->markTestIncomplete('Implement meaningful test for index');
    }

    #[Test]
    public function it_displays_custom_fields_table()
    {
        $this->markTestIncomplete('Implement meaningful test for table');
    }

    #[Test]
    public function it_displays_custom_field_form()
    {
        $this->markTestIncomplete('Implement meaningful test for form');
    }

    #[Test]
    public function it_deletes_custom_field()
    {
        $this->markTestIncomplete('Implement meaningful test for delete');
    }
}
