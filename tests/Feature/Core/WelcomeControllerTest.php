<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Support\TestUris;
use Welcome;

#[CoversClass(Welcome::class)]
class WelcomeControllerTest extends AbstractTestCase
{
    #[Test]
    public function it_displays_welcome_page(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get(TestUris::HOME);

        /* Assert */
        $this->assertResponseOk($response);
        $this->assertResponseHasNoPhpErrors($response);
    }
}
