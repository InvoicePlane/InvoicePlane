<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Welcome;

#[CoversClass(Welcome::class)]
class WelcomeControllerTest extends AbstractTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function it_displays_welcome_page(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/welcome');

        /* Assert */
        $this->assertResponseOk($response);
        $this->assertResponseBodyContains($response, '<html');
    }
}
