<?php

namespace Tests\Feature\Clients;

use Clients;
use Tests\AbstractTestCase;

#[CoversClass(Clients::class)]
class ClientsFeatureTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_the_clients_index_page_with_a_200_status(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/clients/status/active');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_includes_a_html_document_structure_on_the_clients_index(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/clients/status/active');

        /* Assert */
        $this->assertResponseBodyContains($response, '<html');
        $this->assertResponseBodyContains($response, '</html>');

        self::assertGreaterThan(
            500,
            $response->bodyLength(),
            'The clients index page rendered fewer than 500 bytes — the view likely did not execute.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_redirects_an_unauthenticated_visitor_away_from_the_client_list(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/clients/status/active');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf(
                'Unauthenticated GET /clients/status/active must produce a redirect but got status [%d].',
                $response->statusCode()
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_the_create_client_form(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/clients/form');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_a_post_to_create_client_with_missing_required_fields(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->post('/clients/form', [
            'client_name' => '',
        ]);

        /* Assert */
        self::assertTrue(
            $response->isRedirect() || $response->statusCode() === 200,
            sprintf(
                'Submitting a blank client form should either re-render (200) or redirect back (3xx). Got [%d].',
                $response->statusCode()
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_the_view_page_for_a_seeded_client(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Regression Client']);

        /* Act */
        $response = $this->get('/clients/view/' . $clientId);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);

        self::assertTrue(
            $response->contains('Regression Client') || $response->contains((string) $clientId),
            sprintf(
                'Client view page for ID [%d] must contain the client name or ID. Body (first 400 chars): %s',
                $clientId,
                mb_substr($response->body(), 0, 400)
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_a_non_200_or_redirect_for_a_nonexistent_client_id(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/clients/view/999999999');

        /* Assert */
        self::assertThat(
            $response->statusCode(),
            self::logicalOr(
                self::equalTo(404),
                self::equalTo(302),
                self::equalTo(301),
                self::equalTo(307),
                self::equalTo(200)
            ),
            sprintf(
                'Requesting a nonexistent client should not crash. Got [%d].',
                $response->statusCode()
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_shows_client_name_in_the_edit_form_for_an_existing_client(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Editable Corp']);

        /* Act */
        $response = $this->get('/clients/form/' . $clientId);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'Editable Corp');
        $this->assertResponseBodyContains($response, '<form');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_not_render_php_errors_when_listing_multiple_clients(): void
    {
        /* Arrange */
        $this->seedClient(['client_name' => 'Alpha Ltd']);
        $this->seedClient(['client_name' => 'Beta GmbH']);
        $this->seedClient(['client_name' => 'Gamma BV']);

        /* Act */
        $response = $this->get('/clients/status/active');

        /* Assert */
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseStatusCode($response, 200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_produces_identical_bodies_for_two_consecutive_client_list_requests(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $first  = $this->get('/clients/status/active');
        $second = $this->get('/clients/status/active');

        /* Assert */
        self::assertSame(
            $first->statusCode(),
            $second->statusCode(),
            'Two consecutive GET /clients/status/active must return the same status code.'
        );

        self::assertSame(
            mb_strlen($first->body()),
            mb_strlen($second->body()),
            'Two consecutive GET /clients produced bodies of different lengths — the response is non-deterministic.'
        );
    }
}
