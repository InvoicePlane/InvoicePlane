<?php

namespace Tests\Feature\Clients;

use Tests\AbstractTestCase;

#[CoversClass(Tests\Feature\Clients\ClientsFeature::class)]
class ClientsFeatureTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Requires live CI3 environment with database — not available in CI');
        $this->actingAsAdmin();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_the_clients_index_page_with_a_200_status(): void
    {
        $response = $this->get('/clients');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_includes_a_html_document_structure_on_the_clients_index(): void
    {
        $response = $this->get('/clients');

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
        $this->actingAsGuest();

        $response = $this->get('/clients');

        self::assertTrue(
            $response->isRedirect(),
            sprintf(
                'Unauthenticated GET /clients must produce a redirect but got status [%d].',
                $response->statusCode()
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_the_create_client_form(): void
    {
        $response = $this->get('/clients/create');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_a_post_to_create_client_with_missing_required_fields(): void
    {
        $response = $this->post('/clients/create', [
            'client_name' => '',
        ]);

        self::assertTrue(
            $response->isRedirect() || $response->statusCode() === 200,
            sprintf(
                'Submitting a blank client form should either re-render (200) or redirect back (3xx). Got [%d].',
                $response->statusCode()
            )
        );

        $isRedirectBack = $response->isRedirect()
            && str_contains((string) $response->redirectUrl(), 'clients/create');

        $isRerenderWithError = $response->statusCode() === 200
            && (
                $response->contains('required')
                || $response->contains('error')
                || $response->contains('field')
            );

        self::assertTrue(
            $isRedirectBack || $isRerenderWithError,
            'Submitting a blank client_name must either redirect to the form or re-render with an error indicator.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_the_view_page_for_a_seeded_client(): void
    {
        $clientId = $this->seedClient(['client_name' => 'Regression Client']);

        $response = $this->get('/clients/view/' . $clientId);

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
        $response = $this->get('/clients/view/999999999');

        self::assertThat(
            $response->statusCode(),
            self::logicalOr(
                self::equalTo(404),
                self::equalTo(302),
                self::equalTo(301)
            ),
            sprintf(
                'Requesting a nonexistent client must produce 404 or redirect, not a silent 200. Got [%d].',
                $response->statusCode()
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_shows_client_name_in_the_edit_form_for_an_existing_client(): void
    {
        $clientId = $this->seedClient(['client_name' => 'Editable Corp']);

        $response = $this->get('/clients/edit/' . $clientId);

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'Editable Corp');
        $this->assertResponseBodyContains($response, '<form');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_not_render_php_errors_when_listing_multiple_clients(): void
    {
        $this->seedClient(['client_name' => 'Alpha Ltd']);
        $this->seedClient(['client_name' => 'Beta GmbH']);
        $this->seedClient(['client_name' => 'Gamma BV']);

        $response = $this->get('/clients');

        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseStatusCode($response, 200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_produces_identical_bodies_for_two_consecutive_client_list_requests(): void
    {
        $first  = $this->get('/clients');
        $second = $this->get('/clients');

        self::assertSame(
            $first->statusCode(),
            $second->statusCode(),
            'Two consecutive GET /clients must return the same status code.'
        );

        self::assertSame(
            mb_strlen($first->body()),
            mb_strlen($second->body()),
            'Two consecutive GET /clients produced bodies of different lengths — the response is non-deterministic.'
        );
    }
}
