<?php

namespace Tests\Feature\Projects;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Tasks controller — application/modules/tasks/controllers/Tasks.php.
 *
 * Required fields (Mdl_Tasks::validation_rules): task_name, task_price, task_finish_date.
 * Absorbs Issue1694TasksDeleteCsrfTest. AJAX lookups live in
 * TasksAjaxControllerTest. TaskDeletionValidationFeatureTest's real subject —
 * Projects::delete() orphaning (not cascade-deleting) a project's tasks — is
 * Projects::delete()'s behaviour, not Tasks::delete()'s; that assertion lives
 * in ProjectsControllerTest::it_orphans_rather_than_deletes_the_tasks_of_a_deleted_project.
 */
#[Group('tasks')]
#[CoversClass(\Tasks::class)]
class TasksControllerTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    private const REQUIRED = [
        'task_name'        => 'Payload Task',
        'task_price'       => '100.00',
        'task_finish_date' => '2026-12-31',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    #[Test]
    public function it_lists_every_task(): void
    {
        /* Arrange */
        $this->seedTask(['task_name' => 'Draft Homepage Copy']);
        $this->seedTask(['task_name' => 'Wire Up Analytics']);

        /* Act */
        $response = $this->get('/tasks');

        /* Assert */
        $this->assertResponseBodyContains($response, 'Draft Homepage Copy');
        $this->assertResponseBodyContains($response, 'Wire Up Analytics');
    }

    // -------------------------------------------------------------------------
    // Create — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_a_task(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/tasks/form', array_merge(self::REQUIRED, [
            'task_name'   => 'Review Pull Request',
            'task_status' => '1',
            'btn_submit'  => '1',
        ]));

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'tasks');
        $this->assertDatabaseHas('ip_tasks', ['task_name' => 'Review Pull Request', 'task_price' => '100.00']);
    }

    // -------------------------------------------------------------------------
    // Create — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_task_name(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/tasks/form', array_merge(self::REQUIRED, [
            'task_name'  => '',
            'task_price' => '77.00',
            'btn_submit' => '1',
        ]));

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseMissing('ip_tasks', ['task_price' => '77.00']);
    }

    #[Test]
    public function it_fails_to_create_without_task_price(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/tasks/form', array_merge(self::REQUIRED, [
            'task_name'  => 'Priceless Task',
            'task_price' => '',
            'btn_submit' => '1',
        ]));

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseMissing('ip_tasks', ['task_name' => 'Priceless Task']);
    }

    #[Test]
    public function it_fails_to_create_without_task_finish_date(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/tasks/form', array_merge(self::REQUIRED, [
            'task_name'        => 'Dateless Task',
            'task_finish_date' => '',
            'btn_submit'       => '1',
        ]));

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseMissing('ip_tasks', ['task_name' => 'Dateless Task']);
    }

    // -------------------------------------------------------------------------
    // Update — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_edit_form_for_the_requested_task_only(): void
    {
        /* Arrange */
        $target = $this->seedTask(['task_name' => 'Editable Task']);
        $this->seedTask(['task_name' => 'Other Task']);

        /* Act */
        $response = $this->get('/tasks/form/' . $target);

        /* Assert */
        $this->assertResponseBodyContains($response, 'Editable Task');
        $this->assertResponseBodyNotContains($response, 'Other Task');
    }

    #[Test]
    public function it_updates_a_task(): void
    {
        /* Arrange */
        $id = $this->seedTask(['task_name' => 'Original Task', 'task_price' => '50.00']);

        /* Act */
        $response = $this->post('/tasks/form/' . $id, array_merge(self::REQUIRED, [
            'task_name'   => 'Renamed Task',
            'task_price'  => '150.00',
            'task_status' => '1',
            'btn_submit'  => '1',
        ]));

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'tasks');
        $this->assertDatabaseHas('ip_tasks', ['task_id' => $id, 'task_name' => 'Renamed Task', 'task_price' => '150.00']);
        $this->assertDatabaseMissing('ip_tasks', ['task_name' => 'Original Task']);
    }

    // -------------------------------------------------------------------------
    // Update — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_update_without_task_name(): void
    {
        /* Arrange */
        $id = $this->seedTask(['task_name' => 'Keep This Task']);

        /* Act */
        $response = $this->post('/tasks/form/' . $id, array_merge(self::REQUIRED, [
            'task_name'  => '',
            'btn_submit' => '1',
        ]));

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_tasks', ['task_id' => $id, 'task_name' => 'Keep This Task']);
    }

    #[Test]
    public function it_fails_to_update_without_task_price(): void
    {
        /* Arrange */
        $id = $this->seedTask(['task_name' => 'Price Kept Task', 'task_price' => '50.00']);

        /* Act */
        $response = $this->post('/tasks/form/' . $id, array_merge(self::REQUIRED, [
            'task_name'  => 'Price Kept Task',
            'task_price' => '',
            'btn_submit' => '1',
        ]));

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_tasks', ['task_id' => $id, 'task_price' => '50.00']);
    }

    #[Test]
    public function it_fails_to_update_without_task_finish_date(): void
    {
        /* Arrange */
        $id = $this->seedTask(['task_name' => 'Date Kept Task', 'task_finish_date' => '2026-06-30']);

        /* Act */
        $response = $this->post('/tasks/form/' . $id, array_merge(self::REQUIRED, [
            'task_name'        => 'Date Kept Task',
            'task_finish_date' => '',
            'btn_submit'       => '1',
        ]));

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_tasks', ['task_id' => $id, 'task_finish_date' => '2026-06-30']);
    }

    // -------------------------------------------------------------------------
    // Delete — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_task(): void
    {
        /* Arrange */
        $id   = $this->seedTask(['task_name' => 'Deletable Task']);
        $keep = $this->seedTask(['task_name' => 'Kept Task']);

        /* Act */
        $response = $this->post('/tasks/delete/' . $id, []);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'tasks');
        $this->assertDatabaseMissing('ip_tasks', ['task_id' => $id]);
        $this->assertDatabaseHas('ip_tasks', ['task_id' => $keep]);
    }

    // -------------------------------------------------------------------------
    // Delete — CSRF regression (#1694)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_still_deletes_a_task_when_csrf_protection_is_on_and_the_token_is_valid(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->seedTask(['task_name' => 'CSRF Task']);

        /* Act */
        $response = $this->postWithValidCsrfToken('/tasks/delete/' . $id);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'tasks');
        $this->assertDatabaseMissing('ip_tasks', ['task_id' => $id]);
    }

    #[Test]
    public function it_does_not_delete_a_task_when_the_csrf_token_is_missing(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->seedTask(['task_name' => 'CSRF Task Kept']);

        /* Act */
        $response = $this->postWithoutCsrfToken('/tasks/delete/' . $id);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less delete must not reach the controller.');
        $this->assertDatabaseHas('ip_tasks', ['task_id' => $id, 'task_name' => 'CSRF Task Kept']);
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login_and_leaks_no_task(): void
    {
        /* Arrange */
        $this->seedTask(['task_name' => 'Secret Task']);
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/tasks');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
        $this->assertResponseBodyNotContains($response, 'Secret Task');
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
