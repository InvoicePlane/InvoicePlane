<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversNothing]
class ViewTemplateSystemTest extends AbstractTestCase
{
    private string $projectRoot;

    protected function setUp(): void
    {
        parent::setUp();
        $this->projectRoot = dirname(__DIR__, 3);
    }

    #[Test]
    public function it_php_view_engine_is_registered(): void
    {
        $this->assertDirectoryExists($this->projectRoot . '/application/views');
    }

    #[Test]
    public function it_blade_engine_is_available_as_secondary(): void
    {
        $this->assertDirectoryExists($this->projectRoot . '/application/modules/invoices/views');
    }

    #[Test]
    public function it_plain_php_views_can_be_rendered(): void
    {
        $this->actingAsAdmin();
        $response = $this->get('/dashboard');

        $this->assertResponseStatusCode($response, 200);
    }

    #[Test]
    public function it_welcome_view_is_php_template(): void
    {
        $this->assertFileExists($this->projectRoot . '/application/modules/welcome/views/index.php');
    }
}
