<?php

namespace Tests\Feature\Projects;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class ProjectsControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    #[Test]
    public function it_lists_projects(): void
    {
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
        $this->assertDatabaseHas('ip_projects', ['project_name' => 'Listed Project']);
        $this->assertResponseBodyContains($response, '<html');
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_create_project_form(): void
    {
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
        /**
         * POST /projects/form
         * {
         *     "project_name": "Build a Rocket",
         *     "client_id": "<clientId>",
         *     "btn_submit": "1"
         * }
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
        self::assertTrue($response->isRedirect(), 'Successful create must redirect.');
        $this->assertDatabaseHas('ip_projects', ['project_name' => 'Build a Rocket']);
    }

    // -------------------------------------------------------------------------
    // Update
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_edit_form_showing_existing_project_name(): void
    {
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
        /**
         * POST /projects/form/{id}
         * {
         *     "project_name": "Renamed Project",
         *     "client_id": "<clientId>",
         *     "btn_submit": "1"
         * }
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
        self::assertTrue($response->isRedirect(), 'Successful update must redirect.');
        $this->assertDatabaseHas('ip_projects', ['project_name' => 'Renamed Project']);
        $this->assertDatabaseMissing('ip_projects', ['project_name' => 'Original Project']);
    }

    // -------------------------------------------------------------------------
    // View
    // -------------------------------------------------------------------------

    #[Test]
    public function it_views_a_single_project_and_shows_the_project_name(): void
    {
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
        self::assertTrue($response->isRedirect(), 'Delete must redirect.');
        $this->assertDatabaseMissing('ip_projects', ['project_name' => 'Deletable Project']);
    }

    // -------------------------------------------------------------------------
    // Validation failures — missing required fields
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_project_name(): void
    {
        /**
         * POST /projects/form
         * {
         *     "project_name": "",
         *     "client_id": "<clientId>",
         *     "btn_submit": "1"
         * }
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
    }

    #[Test]
    public function it_fails_to_update_without_project_name(): void
    {
        /**
         * POST /projects/form/{id}
         * {
         *     "project_name": "",
         *     "client_id": "<clientId>",
         *     "btn_submit": "1"
         * }
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
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/projects');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
    }
}
