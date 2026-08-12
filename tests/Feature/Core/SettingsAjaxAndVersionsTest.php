<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class SettingsAjaxAndVersionsTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_generates_a_16_character_hex_cron_key(): void
    {
        /* Arrange */
        /* Act */
        $response = $this->ajax('GET', '/settings/ajax/get_cron_key', []);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        self::assertMatchesRegularExpression('/^[0-9a-f]{16}$/', trim($response->body()));
    }

    #[Test]
    public function it_generates_a_different_cron_key_on_each_call(): void
    {
        /* Arrange */
        /* Act */
        $first  = trim($this->ajax('GET', '/settings/ajax/get_cron_key', [])->body());
        $second = trim($this->ajax('GET', '/settings/ajax/get_cron_key', [])->body());

        /* Assert */
        self::assertNotSame($first, $second);
    }

    #[Test]
    public function it_requires_an_ajax_request_for_get_cron_key(): void
    {
        /* Arrange */
        /* Act */
        $response = $this->get('/settings/ajax/get_cron_key');

        /* Assert */
        self::assertSame('', $response->body());
    }

    #[Test]
    public function it_lists_applied_versions(): void
    {
        /* Arrange */
        /* Act */
        $response = $this->get('/settings/versions');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_denies_versions_access_to_a_guest(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/settings/versions');

        /* Assert */
        self::assertTrue($response->isRedirect());
    }
}
