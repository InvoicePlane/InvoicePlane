<?php

namespace Tests\Feature\Projects;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * TaskDeletionValidation Feature Tests.
 *
 * Tests task deletion validation via the projects route.
 */
class TaskDeletionValidationFeatureTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_or_redirect(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Task Deletion Client']);
        $this->databaseInsert('ip_projects', [
            'client_id'            => $clientId,
            'project_name'         => 'Deletion Test Project',
        ]);

        /* Act */
        $response = $this->get('/projects');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'Deletion Test Project');
    }

    #[Test]
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
