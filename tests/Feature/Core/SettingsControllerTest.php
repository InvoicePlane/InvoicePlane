<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Settings;
use Tests\AbstractTestCase;

#[CoversClass(Settings::class)]
class SettingsControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_displays_settings_page(): void
    {
        $response = $this->get('/settings/index');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }
}
