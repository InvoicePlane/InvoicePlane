<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(Tests\Feature\Core\CustomFieldsController::class)]
class CustomFieldsControllerTest extends AbstractTestCase
{
    #[Test]
    public function it_displays_custom_fields_list(): void
    {
        $this->markTestIncomplete('Implement meaningful test for index');
    }

    #[Test]
    public function it_displays_custom_fields_table(): void
    {
        $this->markTestIncomplete('Implement meaningful test for table');
    }

    #[Test]
    public function it_displays_custom_field_form(): void
    {
        $this->markTestIncomplete('Implement meaningful test for form');
    }

    #[Test]
    public function it_deletes_custom_field(): void
    {
        $this->markTestIncomplete('Implement meaningful test for delete');
    }
}
