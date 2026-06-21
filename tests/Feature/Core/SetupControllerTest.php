<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * SetupController Feature Tests.
 *
 * Tests that the login page renders a form.
 */
class SetupControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // public route — no auth needed
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_or_redirect(): void
    {
        /* Arrange */
        /* (public route — no auth needed) */

        /* Act */
        $response = $this->get('/sessions/login');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
    }
}
