<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Welcome;

#[CoversClass(Welcome::class)]
class WelcomeControllerTest extends AbstractTestCase
{
    #[Test]
    public function it_displays_welcome_page_to_guest(): void
    {
        /* Arrange: no authentication needed for welcome page */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/welcome');

        /* Assert: page loads and displays HTML */
        $this->assertResponseOk($response);
        $this->assertResponseBodyContains($response, '<html');
        self::assertGreaterThan(100, $response->bodyLength(), 'Welcome page should have substantive content');
    }

    #[Test]
    public function it_displays_welcome_page_to_authenticated_user(): void
    {
        /* Arrange: authenticated user accessing welcome page */
        $this->actingAsAdmin();

        /* Act */
        $response = $this->get('/welcome');

        /* Assert: page still loads for authenticated users */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_renders_valid_html_structure(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/welcome');

        /* Assert: HTML structure is valid */
        self::assertTrue(
            $response->contains('<html') && $response->contains('</html>'),
            'Welcome page must have proper HTML opening and closing tags'
        );
        self::assertGreaterThan(
            500,
            $response->bodyLength(),
            'Welcome page body should contain meaningful content (> 500 bytes)'
        );
    }

    #[Test]
    public function it_does_not_expose_php_errors(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/welcome');

        /* Assert */
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_loads_deterministically(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act: make two consecutive requests */
        $first  = $this->get('/welcome');
        $second = $this->get('/welcome');

        /* Assert: both requests return same status code */
        self::assertSame(
            $first->statusCode(),
            $second->statusCode(),
            'Welcome page should load consistently across multiple requests'
        );
    }

    #[Test]
    public function it_contains_expected_content_elements(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/welcome');

        /* Assert: page contains some expected elements (welcome, login, etc) */
        self::assertTrue(
            $response->contains('welcome') || $response->contains('login') || $response->contains('invoice'),
            'Welcome page should contain at least one key navigation or welcome element'
        );
    }
}
