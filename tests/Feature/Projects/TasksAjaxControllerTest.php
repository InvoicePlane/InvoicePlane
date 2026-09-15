<?php

namespace Tests\Feature\Projects;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[\PHPUnit\Framework\Attributes\Group('tasks')]
#[CoversClass(\Ajax::class)]
class TasksAjaxControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_renders_the_task_lookup_modal_with_no_invoice(): void
    {
        /* Arrange */
        /* Act */
        $response = $this->ajax('POST', '/tasks/ajax/modal_task_lookups', []);

        /* Assert */
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_processes_a_task_selection(): void
    {
        /* Arrange */
        $seeded = $this->seedProjectAndTask();

        /* Act */
        $response = $this->ajax('POST', '/tasks/ajax/process_task_selections', ['task_ids' => [(string) $seeded['taskId']]]);

        /* Assert */
        $this->assertResponseBodyContains($response, 'Lookup Task Marker');
    }

    #[Test]
    public function it_returns_an_empty_result_when_no_task_ids_are_selected(): void
    {
        /* Arrange */
        /* Act */
        $response = $this->ajax('POST', '/tasks/ajax/process_task_selections', []);

        /* Assert */
        self::assertSame([], json_decode($response->body(), true));
    }

    #[Test]
    public function it_requires_an_ajax_request(): void
    {
        /* Arrange */
        /* Act */
        $response = $this->post('/tasks/ajax/process_task_selections', []);

        /* Assert */
        self::assertSame('', $response->body());
    }

    private function seedProjectAndTask(array $overrides = []): array
    {
        $clientId  = $this->seedClient();
        $projectId = $this->databaseInsert('ip_projects', ['client_id' => $clientId, 'project_name' => 'Task Lookup Project']);
        $taskId    = $this->databaseInsert('ip_tasks', array_merge([
            'project_id'       => $projectId,
            'task_name'        => 'Lookup Task Marker',
            'task_description' => '',
            'task_price'       => '15.00',
            'task_finish_date' => date('Y-m-d'),
            'task_status'      => 1,
            'tax_rate_id'      => 0,
        ], $overrides));

        return ['projectId' => $projectId, 'taskId' => $taskId];
    }
}
