<?php

namespace Tests\Feature\Projects;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Smoke coverage for the projects module via the CI3 HTTP harness.
 */
#[Group('projects')]
class ProjectsSmokeTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        $this->setUpDatabase();
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('smoke')]
    #[Group('projects')]
    public function it_returns_a_successful_response_or_redirect(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Projects Service Client']);
        $this->databaseInsert('ip_projects', [
            'client_id'    => $clientId,
            'project_name' => 'Service Project Epsilon',
        ]);

        /* Act */
        $response = $this->get('/projects');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'Service Project Epsilon');
    }

    #[Test]
    #[Group('projects')]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/projects');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/projects] must redirect. Got [%d].', $response->statusCode())
        );
    }
}
