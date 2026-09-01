<?php

namespace Tests\Feature\Regression;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * #1694 regression — Controller: Projects::delete() (application/modules/projects).
 */
#[Group('security')]
class Issue1694ProjectsDeleteCsrfTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function it_deletes_a_project_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $projectId = (int) $this->seedModel('Project', ['client_id' => $this->seedClient()])->project_id;

        /* Act */
        $response = $this->postWithValidCsrfToken('/projects/delete/' . $projectId);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('projects/delete must redirect. Got [%d].', $response->statusCode())
        );
        $this->assertDatabaseMissing('ip_projects', ['project_id' => $projectId]);
    }

    #[Test]
    public function it_rejects_the_delete_without_a_csrf_token(): void
    {
        /* Arrange */
        $projectId = (int) $this->seedModel('Project', ['client_id' => $this->seedClient()])->project_id;

        /* Act */
        $response = $this->postWithoutCsrfToken('/projects/delete/' . $projectId);

        /* Assert */
        self::assertGreaterThanOrEqual(400, $response->statusCode());
        $this->assertDatabaseHas('ip_projects', ['project_id' => $projectId]);
    }
}
