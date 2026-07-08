<?php

namespace Tests\Feature\Projects;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class TasksControllerTest extends AbstractTestCase
{
    private int $projectId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $clientId        = $this->seedClient(['client_name' => 'Task Test Client']);
        $this->projectId = $this->databaseInsert('ip_projects', [
            'client_id'    => $clientId,
            'project_name' => 'Task Test Project',
        ]);
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    #[Test]
    public function it_lists_tasks(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_tasks', $this->taskRow(['task_name' => 'Listed Task']));

        /* Act */
        $response = $this->get('/tasks');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertDatabaseHas('ip_tasks', ['task_name' => 'Listed Task']);
        $this->assertResponseBodyContains($response, '<html');
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_create_task_form(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->get('/tasks/form');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
    }

    #[Test]
    public function it_creates_a_task(): void
    {
        /**
         * POST /tasks/form
         * {
         *     "task_name": "Build API",
         *     "task_price": "100.00",
         *     "task_finish_date": "2026-12-31",
         *     "project_id": "<projectId>",
         *     "task_description": "",
         *     "task_status": "1",
         *     "tax_rate_id": "0",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */

        /* Act */
        $response = $this->post('/tasks/form', [
            'task_name'        => 'Build API',
            'task_price'       => '100.00',
            'task_finish_date' => '2026-12-31',
            'project_id'       => (string) $this->projectId,
            'task_description' => '',
            'task_status'      => '1',
            'tax_rate_id'      => '0',
            'btn_submit'       => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful create must redirect.');
        $this->assertDatabaseHas('ip_tasks', ['task_name' => 'Build API']);
    }

    // -------------------------------------------------------------------------
    // Update
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_edit_form_showing_existing_task_name(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_tasks', $this->taskRow(['task_name' => 'Editable Task']));

        /* Act */
        $response = $this->get('/tasks/form/' . $id);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertResponseBodyContains($response, 'Editable Task');
    }

    #[Test]
    public function it_updates_a_task(): void
    {
        /**
         * POST /tasks/form/{id}
         * {
         *     "task_name": "Renamed Task",
         *     "task_price": "200.00",
         *     "task_finish_date": "2026-12-31",
         *     "project_id": "<projectId>",
         *     "task_description": "",
         *     "task_status": "1",
         *     "tax_rate_id": "0",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */
        $id = $this->databaseInsert('ip_tasks', $this->taskRow(['task_name' => 'Original Task']));

        /* Act */
        $response = $this->post('/tasks/form/' . $id, [
            'task_name'        => 'Renamed Task',
            'task_price'       => '200.00',
            'task_finish_date' => '2026-12-31',
            'project_id'       => (string) $this->projectId,
            'task_description' => '',
            'task_status'      => '1',
            'tax_rate_id'      => '0',
            'btn_submit'       => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful update must redirect.');
        $this->assertDatabaseHas('ip_tasks', ['task_id' => $id, 'task_name' => 'Renamed Task']);
        $this->assertDatabaseMissing('ip_tasks', ['task_id' => $id, 'task_name' => 'Original Task']);
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_task(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_tasks', $this->taskRow(['task_name' => 'Deletable Task']));
        $this->assertDatabaseHas('ip_tasks', ['task_id' => $id]);

        /* Act */
        $response = $this->post('/tasks/delete/' . $id, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Delete must redirect.');
        $this->assertDatabaseMissing('ip_tasks', ['task_id' => $id]);
    }

    // -------------------------------------------------------------------------
    // Validation failures — missing required fields
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_task_name(): void
    {
        /**
         * POST /tasks/form
         * {
         *     "task_name": "",
         *     "task_price": "100.00",
         *     "task_finish_date": "2026-12-31",
         *     "project_id": "<projectId>",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */

        /* Act */
        $response = $this->post('/tasks/form', [
            'task_name'        => '',
            'task_price'       => '100.00',
            'task_finish_date' => '2026-12-31',
            'project_id'       => (string) $this->projectId,
            'task_description' => '',
            'task_status'      => '1',
            'tax_rate_id'      => '0',
            'btn_submit'       => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
    }

    #[Test]
    public function it_fails_to_create_without_task_price(): void
    {
        /**
         * POST /tasks/form
         * {
         *     "task_name": "No Price Task",
         *     "task_price": "",
         *     "task_finish_date": "2026-12-31",
         *     "project_id": "<projectId>",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */

        /* Act */
        $response = $this->post('/tasks/form', [
            'task_name'        => 'No Price Task',
            'task_price'       => '',
            'task_finish_date' => '2026-12-31',
            'project_id'       => (string) $this->projectId,
            'task_description' => '',
            'task_status'      => '1',
            'tax_rate_id'      => '0',
            'btn_submit'       => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertDatabaseMissing('ip_tasks', ['task_name' => 'No Price Task']);
    }

    #[Test]
    public function it_fails_to_create_without_task_finish_date(): void
    {
        /**
         * POST /tasks/form
         * {
         *     "task_name": "No Date Task",
         *     "task_price": "100.00",
         *     "task_finish_date": "",
         *     "project_id": "<projectId>",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */

        /* Act */
        $response = $this->post('/tasks/form', [
            'task_name'        => 'No Date Task',
            'task_price'       => '100.00',
            'task_finish_date' => '',
            'project_id'       => (string) $this->projectId,
            'task_description' => '',
            'task_status'      => '1',
            'tax_rate_id'      => '0',
            'btn_submit'       => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertDatabaseMissing('ip_tasks', ['task_name' => 'No Date Task']);
    }

    #[Test]
    public function it_fails_to_update_without_task_name(): void
    {
        /**
         * POST /tasks/form/{id}
         * {
         *     "task_name": "",
         *     "task_price": "100.00",
         *     "task_finish_date": "2026-12-31",
         *     "project_id": "<projectId>",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */
        $id = $this->databaseInsert('ip_tasks', $this->taskRow(['task_name' => 'Will Not Change']));

        /* Act */
        $response = $this->post('/tasks/form/' . $id, [
            'task_name'        => '',
            'task_price'       => '100.00',
            'task_finish_date' => '2026-12-31',
            'project_id'       => (string) $this->projectId,
            'task_description' => '',
            'task_status'      => '1',
            'tax_rate_id'      => '0',
            'btn_submit'       => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertDatabaseHas('ip_tasks', ['task_id' => $id, 'task_name' => 'Will Not Change']);
    }

    // -------------------------------------------------------------------------
    // Guest redirect — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/tasks');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
    }

    private function taskRow(array $overrides = []): array
    {
        return array_merge([
            'project_id'       => $this->projectId,
            'task_name'        => 'Default Task',
            'task_description' => '',
            'task_price'       => '0.00',
            'task_finish_date' => date('Y-m-d'),
            'task_status'      => 1,
            'tax_rate_id'      => 0,
        ], $overrides);
    }
}
