<?php

declare(strict_types=1);

namespace Tests\Feature\Projects;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\AbstractTestCase;

class ProjectsTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function __ProjectsController_setUp(): void

        {



            $this->actingAsAdmin();

        }



        // -------------------------------------------------------------------------

        // List

        // -------------------------------------------------------------------------
    #[Test]

    public function it_lists_projects(): void

        {

            $this->__ProjectsController_setUp();

            /* Arrange */

            $clientId = $this->seedClient(['client_name' => 'Project List Client']);

            $this->databaseInsert('ip_projects', [

                'client_id'    => $clientId,

                'project_name' => 'Listed Project',

            ]);



            /* Act */

            $response = $this->get('/projects');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'Listed Project');

        }



        // -------------------------------------------------------------------------

        // Create

        // -------------------------------------------------------------------------
    #[Test]

    public function it_renders_the_create_project_form(): void

        {

            $this->__ProjectsController_setUp();

            /* Arrange */



            /* Act */

            $response = $this->get('/projects/form');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

        }
    #[Test]

    public function it_creates_a_project(): void

        {

            $this->__ProjectsController_setUp();

            /**

             * POST /projects/form

             * {

             *     "project_name": "Build a Rocket",

             *     "client_id": "<clientId>",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */

            $clientId = $this->seedClient(['client_name' => 'Project Create Client']);



            /* Act */

            $response = $this->post('/projects/form', [

                'project_name' => 'Build a Rocket',

                'client_id'    => $clientId,

                'btn_submit'   => '1',

            ]);



            /* Assert */

            $this->assertResponseRedirectsToRoute($response, 'projects');

            $this->assertDatabaseHas('ip_projects', ['project_name' => 'Build a Rocket']);

        }



        // -------------------------------------------------------------------------

        // Update

        // -------------------------------------------------------------------------
    #[Test]

    public function it_renders_the_edit_form_showing_existing_project_name(): void

        {

            $this->__ProjectsController_setUp();

            /* Arrange */

            $clientId = $this->seedClient(['client_name' => 'Project Edit Client']);

            $id       = $this->databaseInsert('ip_projects', [

                'client_id'    => $clientId,

                'project_name' => 'Editable Project',

            ]);



            /* Act */

            $response = $this->get('/projects/form/' . $id);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            $this->assertResponseBodyContains($response, 'Editable Project');

        }
    #[Test]

    public function it_updates_a_project(): void

        {

            $this->__ProjectsController_setUp();

            /**

             * POST /projects/form/{id}

             * {

             *     "project_name": "Renamed Project",

             *     "client_id": "<clientId>",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */

            $clientId = $this->seedClient(['client_name' => 'Project Update Client']);

            $id       = $this->databaseInsert('ip_projects', [

                'client_id'    => $clientId,

                'project_name' => 'Original Project',

            ]);



            /* Act */

            $response = $this->post('/projects/form/' . $id, [

                'project_name' => 'Renamed Project',

                'client_id'    => $clientId,

                'btn_submit'   => '1',

            ]);



            /* Assert */

            $this->assertResponseRedirectsToRoute($response, 'projects');

            $this->assertDatabaseHas('ip_projects', ['project_name' => 'Renamed Project']);

            $this->assertDatabaseMissing('ip_projects', ['project_name' => 'Original Project']);

        }



        // -------------------------------------------------------------------------

        // View

        // -------------------------------------------------------------------------
    #[Test]

    public function it_views_a_single_project_and_shows_the_project_name(): void

        {

            $this->__ProjectsController_setUp();

            /* Arrange */

            $clientId = $this->seedClient(['client_name' => 'Project View Client']);

            $id       = $this->databaseInsert('ip_projects', [

                'client_id'    => $clientId,

                'project_name' => 'View Me Project',

            ]);



            /* Act */

            $response = $this->get('/projects/view/' . $id);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'View Me Project');

        }



        // -------------------------------------------------------------------------

        // Delete

        // -------------------------------------------------------------------------
    #[Test]

    public function it_deletes_a_project(): void

        {

            $this->__ProjectsController_setUp();

            /* Arrange */

            $clientId = $this->seedClient(['client_name' => 'Project Delete Client']);

            $id       = $this->databaseInsert('ip_projects', [

                'client_id'    => $clientId,

                'project_name' => 'Deletable Project',

            ]);

            $this->assertDatabaseHas('ip_projects', ['project_name' => 'Deletable Project']);



            /* Act */

            $response = $this->post('/projects/delete/' . $id, []);



            /* Assert */

            $this->assertResponseRedirectsToRoute($response, 'projects');

            $this->assertDatabaseMissing('ip_projects', ['project_name' => 'Deletable Project']);

        }



        // -------------------------------------------------------------------------

        // Validation failures — missing required fields

        // -------------------------------------------------------------------------
    #[Test]

    public function it_fails_to_create_without_project_name(): void

        {

            $this->__ProjectsController_setUp();

            /**

             * POST /projects/form

             * {

             *     "project_name": "",

             *     "client_id": "<clientId>",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */

            $clientId = $this->seedClient(['client_name' => 'Project Fail Client']);



            /* Act */

            $response = $this->post('/projects/form', [

                'project_name' => '',

                'client_id'    => $clientId,

                'btn_submit'   => '1',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            $this->assertDatabaseCount('ip_projects', 0);

        }
    #[Test]

    public function it_fails_to_update_without_project_name(): void

        {

            $this->__ProjectsController_setUp();

            /**

             * POST /projects/form/{id}

             * {

             *     "project_name": "",

             *     "client_id": "<clientId>",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */

            $clientId = $this->seedClient(['client_name' => 'Project No Change Client']);

            $id       = $this->databaseInsert('ip_projects', [

                'client_id'    => $clientId,

                'project_name' => 'Will Not Change',

            ]);



            /* Act */

            $response = $this->post('/projects/form/' . $id, [

                'project_name' => '',

                'client_id'    => $clientId,

                'btn_submit'   => '1',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            $this->assertDatabaseHas('ip_projects', ['project_name' => 'Will Not Change']);

        }



        // -------------------------------------------------------------------------

        // Guest redirect — always last

        // -------------------------------------------------------------------------
    #[Test]

    public function it_redirects_a_guest_to_login(): void

        {

            $this->__ProjectsController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/projects');



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');

        }
    protected function __ProjectsSmoke_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]
    #[Group('projects')]

    public function it_returns_a_successful_response_or_redirect(): void

        {

            $this->__ProjectsSmoke_setUp();

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

    public function it_redirects_a_guest_to_login_from_projectssmoke(): void

        {

            $this->__ProjectsSmoke_setUp();

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
    private int $projectId;
    protected function __TaskDeletionValidationFeature_setUp(): void

        {



            $this->actingAsAdmin();

            $clientId        = $this->seedClient(['client_name' => 'Task Deletion Client']);

            $this->projectId = $this->databaseInsert('ip_projects', [

                'client_id'    => $clientId,

                'project_name' => 'Deletion Test Project',

            ]);

        }
    #[Test]

    public function it_orphans_the_projects_tasks_instead_of_deleting_them(): void

        {

            $this->__TaskDeletionValidationFeature_setUp();

            /* Arrange */

            $taskId = $this->databaseInsert('ip_tasks', [

                'project_id'       => $this->projectId,

                'task_name'        => 'Task Surviving Project Delete',

                'task_description' => '',

                'task_price'       => '0.00',

                'task_finish_date' => date('Y-m-d'),

                'task_status'      => 1,

                'tax_rate_id'      => 0,

            ]);



            /* Act */

            $response = $this->post('/projects/delete/' . $this->projectId, []);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Delete must redirect.');

            $this->assertDatabaseMissing('ip_projects', ['project_id' => $this->projectId]);

            // ip_tasks.project_id is a non-nullable column, so the null the

            // model assigns is coerced to 0 on write, not a real SQL NULL.

            $this->assertDatabaseHas('ip_tasks', ['task_id' => $taskId, 'project_id' => 0]);

        }
    #[Test]

    public function it_redirects_a_guest_to_login_from_taskdeletionvalidationfeature(): void

        {

            $this->__TaskDeletionValidationFeature_setUp();

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
    protected function __TasksAjaxLookups_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]

    public function it_renders_the_task_lookup_modal_with_no_invoice(): void

        {

            $this->__TasksAjaxLookups_setUp();

            /* Arrange */

            /* Act */

            $response = $this->ajax('POST', '/tasks/ajax/modal_task_lookups', []);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_processes_a_task_selection(): void

        {

            $this->__TasksAjaxLookups_setUp();

            /* Arrange */

            $seeded = $this->__TasksAjaxLookups_seedProjectAndTask();



            /* Act */

            $response = $this->ajax('POST', '/tasks/ajax/process_task_selections', ['task_ids' => [(string) $seeded['taskId']]]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'Lookup Task Marker');

        }
    #[Test]

    public function it_returns_an_empty_result_when_no_task_ids_are_selected(): void

        {

            $this->__TasksAjaxLookups_setUp();

            /* Arrange */

            /* Act */

            $response = $this->ajax('POST', '/tasks/ajax/process_task_selections', []);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            self::assertSame([], json_decode($response->body(), true));

        }
    #[Test]

    public function it_requires_an_ajax_request(): void

        {

            $this->__TasksAjaxLookups_setUp();

            /* Arrange */

            /* Act */

            $response = $this->post('/tasks/ajax/process_task_selections', []);



            /* Assert */

            self::assertSame('', $response->body());

        }
    private function __TasksAjaxLookups_seedProjectAndTask(array $overrides = []): array

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
    private int $projectId_from_taskscontroller;
    protected function __TasksController_setUp(): void

        {



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

            $this->__TasksController_setUp();

            /* Arrange */

            $this->databaseInsert('ip_tasks', $this->__TasksController_taskRow(['task_name' => 'Listed Task']));



            /* Act */

            $response = $this->get('/tasks');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'Listed Task');

        }



        // -------------------------------------------------------------------------

        // Create

        // -------------------------------------------------------------------------
    #[Test]

    public function it_renders_the_create_task_form(): void

        {

            $this->__TasksController_setUp();

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

            $this->__TasksController_setUp();

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

            $this->assertResponseRedirectsToRoute($response, 'tasks');

            $this->assertDatabaseHas('ip_tasks', ['task_name' => 'Build API']);

        }



        // -------------------------------------------------------------------------

        // Update

        // -------------------------------------------------------------------------
    #[Test]

    public function it_renders_the_edit_form_showing_existing_task_name(): void

        {

            $this->__TasksController_setUp();

            /* Arrange */

            $id = $this->databaseInsert('ip_tasks', $this->__TasksController_taskRow(['task_name' => 'Editable Task']));



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

            $this->__TasksController_setUp();

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

            $id = $this->databaseInsert('ip_tasks', $this->__TasksController_taskRow(['task_name' => 'Original Task']));



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

            $this->assertResponseRedirectsToRoute($response, 'tasks');

            $this->assertDatabaseHas('ip_tasks', ['task_id' => $id, 'task_name' => 'Renamed Task']);

            $this->assertDatabaseMissing('ip_tasks', ['task_id' => $id, 'task_name' => 'Original Task']);

        }



        // -------------------------------------------------------------------------

        // Delete

        // -------------------------------------------------------------------------
    #[Test]

    public function it_deletes_a_task(): void

        {

            $this->__TasksController_setUp();

            /* Arrange */

            $id = $this->databaseInsert('ip_tasks', $this->__TasksController_taskRow(['task_name' => 'Deletable Task']));

            $this->assertDatabaseHas('ip_tasks', ['task_id' => $id]);



            /* Act */

            $response = $this->post('/tasks/delete/' . $id, []);



            /* Assert */

            $this->assertResponseRedirectsToRoute($response, 'tasks');

            $this->assertDatabaseMissing('ip_tasks', ['task_id' => $id]);

        }



        // -------------------------------------------------------------------------

        // Validation failures — missing required fields

        // -------------------------------------------------------------------------
    #[Test]

    public function it_fails_to_create_without_task_name(): void

        {

            $this->__TasksController_setUp();

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

            $this->assertDatabaseCount('ip_tasks', 0);

        }
    #[Test]

    public function it_fails_to_create_without_task_price(): void

        {

            $this->__TasksController_setUp();

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

            $this->__TasksController_setUp();

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

            $this->__TasksController_setUp();

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

            $id = $this->databaseInsert('ip_tasks', $this->__TasksController_taskRow(['task_name' => 'Will Not Change']));



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

    public function it_redirects_a_guest_to_login_from_taskscontroller(): void

        {

            $this->__TasksController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/tasks');



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');

        }
    private function __TasksController_taskRow(array $overrides_from_taskscontroller = []): array

        {

            return array_merge([

                'project_id'       => $this->projectId,

                'task_name'        => 'Default Task',

                'task_description' => '',

                'task_price'       => '0.00',

                'task_finish_date' => date('Y-m-d'),

                'task_status'      => 1,

                'tax_rate_id'      => 0,

            ], $overrides_from_taskscontroller);

        }
}
