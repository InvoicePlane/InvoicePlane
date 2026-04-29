<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class CustomFieldsControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_displays_custom_fields_list(): void
    {
        $response = $this->get('/custom_fields');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_displays_custom_fields_table(): void
    {
        $response = $this->get('/custom_fields/table');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_displays_custom_field_form(): void
    {
        $response = $this->get('/custom_fields/form');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_deletes_custom_field(): void
    {
        $response = $this->post('/custom_fields/delete/999999');

        self::assertTrue(
            $response->isRedirect() || $response->statusCode() === 200,
            sprintf(
                'DELETE /custom_fields/delete/999999 must redirect or return 200. Got [%d].',
                $response->statusCode()
            )
        );
        $this->assertResponseHasNoPhpErrors($response);
    }
}
