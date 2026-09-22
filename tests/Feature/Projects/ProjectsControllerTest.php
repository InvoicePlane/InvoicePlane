<?php

namespace Tests\Feature\Projects;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Projects controller — application/modules/projects/controllers/Projects.php.
 *
 * Required fields (Mdl_Projects::validation_rules): project_name.
 * Absorbs ProjectsSmokeTest, Issue1694ProjectsDeleteCsrfTest and
 * TaskDeletionValidationFeatureTest's orphan-on-delete assertion.
 */
#[Group('projects')]
#[CoversClass(\Projects::class)]
class ProjectsControllerTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    #[Test]
    public function it_lists_every_project(): void
    {
        /* Arrange */
        $this->seedProject(['project_name' => 'Website Redesign']);
        $this->seedProject(['project_name' => 'Mobile App Build']);

        /* Act */
        $response = $this->get('/projects');

        /* Assert */
        $this->assertResponseBodyContains($response, 'Website Redesign');
        $this->assertResponseBodyContains($response, 'Mobile App Build');
    }

    // -------------------------------------------------------------------------
    // Create — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_a_project(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Project Client']);

        /* Act */
        $response = $this->post('/projects/form', [
            'project_name' => 'Q1 Marketing Campaign',
            'client_id'    => (string) $clientId,
            'btn_submit'   => '1',
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'projects');
        $this->assertDatabaseHas('ip_projects', ['project_name' => 'Q1 Marketing Campaign', 'client_id' => $clientId]);
    }

    // -------------------------------------------------------------------------
    // Create — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_project_name(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();

        /* Act */
        $response = $this->post('/projects/form', [
            'project_name' => '',
            'client_id'    => (string) $clientId,
            'btn_submit'   => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseCount('ip_projects', 0);
    }

    // -------------------------------------------------------------------------
    // Update — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_edit_form_for_the_requested_project_only(): void
    {
        /* Arrange */
        $target = $this->seedProject(['project_name' => 'Editable Project']);
        $this->seedProject(['project_name' => 'Other Project']);

        /* Act */
        $response = $this->get('/projects/form/' . $target);

        /* Assert */
        $this->assertResponseBodyContains($response, 'Editable Project');
        $this->assertResponseBodyNotContains($response, 'Other Project');
    }

    #[Test]
    public function it_updates_a_project(): void
    {
        /* Arrange */
        $id = $this->seedProject(['project_name' => 'Original Project']);

        /* Act */
        $response = $this->post('/projects/form/' . $id, [
            'project_name' => 'Renamed Project',
            'btn_submit'   => '1',
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'projects');
        $this->assertDatabaseHas('ip_projects', ['project_id' => $id, 'project_name' => 'Renamed Project']);
        $this->assertDatabaseMissing('ip_projects', ['project_name' => 'Original Project']);
    }

    // -------------------------------------------------------------------------
    // Update — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_update_without_project_name(): void
    {
        /* Arrange */
        $id = $this->seedProject(['project_name' => 'Keep This Project']);

        /* Act */
        $response = $this->post('/projects/form/' . $id, [
            'project_name' => '',
            'btn_submit'   => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_projects', ['project_id' => $id, 'project_name' => 'Keep This Project']);
    }

    // -------------------------------------------------------------------------
    // Delete — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_project(): void
    {
        /* Arrange */
        $id   = $this->seedProject(['project_name' => 'Deletable Project']);
        $keep = $this->seedProject(['project_name' => 'Kept Project']);

        /* Act */
        $response = $this->post('/projects/delete/' . $id, []);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'projects');
        $this->assertDatabaseMissing('ip_projects', ['project_id' => $id]);
        $this->assertDatabaseHas('ip_projects', ['project_id' => $keep]);
    }

    #[Test]
    public function it_orphans_rather_than_deletes_the_tasks_of_a_deleted_project(): void
    {
        /* Arrange */
        $id     = $this->seedProject(['project_name' => 'Project With Tasks']);
        $taskId = $this->seedTask(['project_id' => $id, 'task_name' => 'Task To Orphan']);

        /* Act */
        $response = $this->post('/projects/delete/' . $id, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Delete redirects back to the project list.');
        // Mdl_tasks::update_on_project_delete() saves project_id = null, but
        // ip_tasks.project_id is a non-nullable column, so the DB coerces it
        // to 0 — that coercion is the actual "orphaned" state, not NULL.
        $this->assertDatabaseHas('ip_tasks', ['task_id' => $taskId, 'project_id' => 0]);
    }

    // -------------------------------------------------------------------------
    // Delete — CSRF regression (#1694)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_still_deletes_a_project_when_csrf_protection_is_on_and_the_token_is_valid(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->seedProject(['project_name' => 'CSRF Project']);

        /* Act */
        $response = $this->postWithValidCsrfToken('/projects/delete/' . $id);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'projects');
        $this->assertDatabaseMissing('ip_projects', ['project_id' => $id]);
    }

    #[Test]
    public function it_does_not_delete_a_project_when_the_csrf_token_is_missing(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->seedProject(['project_name' => 'CSRF Project Kept']);

        /* Act */
        $response = $this->postWithoutCsrfToken('/projects/delete/' . $id);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less delete must not reach the controller.');
        $this->assertDatabaseHas('ip_projects', ['project_id' => $id, 'project_name' => 'CSRF Project Kept']);
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login_and_leaks_no_project(): void
    {
        /* Arrange */
        $this->seedProject(['project_name' => 'Secret Project']);
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/projects');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
        $this->assertResponseBodyNotContains($response, 'Secret Project');
    }

    /** @param array<string,mixed> $overrides */
    private function seedProject(array $overrides = []): int
    {
        return $this->databaseInsert('ip_projects', array_merge([
            'project_name' => 'Seeded Project',
            'client_id'    => $this->seedClient(),
        ], $overrides));
    }

    /** @param array<string,mixed> $overrides */
    private function seedTask(array $overrides = []): int
    {
        return $this->databaseInsert('ip_tasks', array_merge([
            'task_name'        => 'Seeded Task',
            'task_description' => '',
            'task_price'       => '50.00',
            'task_finish_date' => '2026-06-30',
            'task_status'      => 1,
            'project_id'       => 0,
        ], $overrides));
    }
}
