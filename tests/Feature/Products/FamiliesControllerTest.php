<?php

namespace Tests\Feature\Products;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class FamiliesControllerTest extends AbstractTestCase
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
        $this->databaseInsert('ip_families', [
            'family_name' => 'Test Family Alpha',
        ]);

        /* Act */
        $response = $this->get('/families');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertDatabaseHas('ip_families', ['family_name' => 'Test Family Alpha']);
        $this->assertResponseBodyContains($response, '<html');
    }
}
