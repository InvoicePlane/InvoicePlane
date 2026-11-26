<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Layout (application/modules/layout/controllers/Layout.php) extends
 * MX_Controller rather than Admin_Controller/Guest_Controller, so it
 * inherits no authentication gate of its own. Its public methods
 * (render, load_view, buffer, set) are meant to be called only internally
 * as $this->layout->render() by already-authenticated page controllers,
 * but HMVC's router makes any public method on a controller-path class
 * directly HTTP-routable. Reached directly and unauthenticated, the
 * internal view loader runs with helpers/libraries that are never
 * autoloaded in this context, producing PHP errors that -- with
 * display_errors on -- disclose the server's absolute install path and
 * full internal file layout to an anonymous attacker.
 *
 * This branch already guards against it with a _remap() that show_404()s
 * any direct route to this controller (see the class). This test locks
 * that behavior in: removing _remap(), or changing it to allow any of
 * these methods through, must turn it red.
 *
 * GHSA-8cpm-3w3g-97hr: Unauthenticated internal Layout rendering controller exposes verbose PHP errors (absolute path and backtrace disclosure)
 */
final class LayoutControllerUnauthenticatedAccessTest extends AbstractTestCase
{
    /** @return array<string, array{string}> */
    public static function layoutRoutes(): array
    {
        return [
            'load_view' => ['/layout/load_view/dashboard'],
            'render'    => ['/layout/render'],
            'buffer'    => ['/layout/buffer'],
            'set'       => ['/layout/set'],
        ];
    }

    #[Test]
    #[DataProvider('layoutRoutes')]
    public function it_returns_404_for_an_unauthenticated_request_to_a_layout_controller_method(string $uri): void
    {
        /* Arrange: no actingAs*() call -- this request carries no session at all */

        /* Act */
        $response = $this->get($uri);

        /* Assert: routed-but-rejected (404), not the internal view loader running and leaking a PHP error/backtrace */
        self::assertSame(
            404,
            $response->statusCode(),
            sprintf('GET %s must be rejected with 404 before the internal view loader ever runs.', $uri)
        );
        self::assertStringNotContainsStringIgnoringCase(
            'Backtrace',
            $response->body(),
            'The response must never contain a PHP error backtrace, which would disclose server-absolute paths.'
        );
    }

    #[Test]
    #[DataProvider('layoutRoutes')]
    public function it_returns_404_for_the_same_request_even_with_an_admin_session(string $uri): void
    {
        /* Arrange: an authenticated admin session must not change the outcome -- the controller is not meant to be routed to at all */
        $this->actingAsAdmin();

        /* Act */
        $response = $this->get($uri);

        /* Assert */
        self::assertSame(404, $response->statusCode());
    }
}
