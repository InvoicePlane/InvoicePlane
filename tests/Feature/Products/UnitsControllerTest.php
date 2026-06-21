<?php

namespace Tests\Feature\Products;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class UnitsControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_or_redirect(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_units', [
            'unit_name' => 'Test Unit Beta',
        ]);

        /* Act */
        $response = $this->get('/units');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertDatabaseHas('ip_units', ['unit_name' => 'Test Unit Beta']);
        $this->assertResponseBodyContains($response, '<html');
    }
}
