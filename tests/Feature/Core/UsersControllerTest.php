<?php

namespace Tests\Feature\Core;

use Modules\Core\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;
use Users;

/**
 * Core AjaxController Feature Tests.
 *
 * Tests AJAX requests for settings operations.
 */
#[CoversClass(Users::class)]
#[CoversClass(Tests\Feature\Core\UsersController::class)]

class UsersControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    /**
     * Test index displays paginated list of users.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_paginated_list_of_users(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        $this->seedModelMany('User', 5);

        /* Act */
        $response = $this->actingAs($user)->get(route('users.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('users::index');
        $response->assertViewHas('users');
        $response->assertViewHas('filter_display', true);
        $response->assertViewHas('filter_placeholder');
        $response->assertViewHas('filter_method', 'filter_users');
        $response->assertViewHas('user_types');
    }

    /**
     * Test users are ordered alphabetically by name.
     */
    #[Test]
    public function it_orders_users_alphabetically_by_name(): void
    {
        /* Arrange */
        $adminUser = $this->seedModel('User', ['user_name' => 'Admin']);

        $this->seedModel('User', ['user_name' => 'Zack']);
        $this->seedModel('User', ['user_name' => 'Alice']);
        $this->seedModel('User', ['user_name' => 'Bob']);

        /* Act */
        $response = $this->actingAs($adminUser)->get(route('users.index'));

        /* Assert */
        $response->assertOk();
        $users = $response->viewData('users');
        $names = $users->pluck('user_name')->toArray();

        $this->assertEquals('Admin', $names[0]);
        $this->assertEquals('Alice', $names[1]);
        $this->assertEquals('Bob', $names[2]);
        $this->assertEquals('Zack', $names[3]);
    }

    /**
     * Test form displays create form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_create_form(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $response = $this->actingAs($user)->get(route('users.form'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('users::form');
        $response->assertViewHas('user');

        $formUser = $response->viewData('user');
        $this->assertInstanceOf(User::class, $formUser);
        $this->assertFalse($formUser->exists);
    }

    /**
     * Test form displays edit form with existing user.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_edit_form_with_existing_user(): void
    {
        /* Arrange */
        $adminUser = $this->seedModel('User');
        $editUser  = $this->seedModel('User');

        /* Act */
        $response = $this->actingAs($adminUser)->get(route('users.form', ['id' => $editUser->user_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('users::form');
        $response->assertViewHas('user');

        $formUser = $response->viewData('user');
        $this->assertEquals($editUser->user_id, $formUser->user_id);
    }

    /**
     * Test form creates new user with valid data.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_new_user_with_valid_data(): void
    {
        /* Arrange */
        $adminUser = $this->seedModel('User');

        /**
         * {
         *     "user_name": "New User",
         *     "user_email": "newuser@example.com",
         *     "user_password": "password123",
         *     "user_type": 1,
         *     "btn_submit": "1"
         * }.
         */
        $userData = [
            'user_name'     => 'New User',
            'user_email'    => 'newuser@example.com',
            'user_password' => 'password123',
            'user_type'     => User::USER_TYPE_ADMINISTRATOR,
            'btn_submit'    => '1',
        ];

        /* Act */
        $response = $this->actingAs($adminUser)->post(route('users.form'), $userData);

        /* Assert */
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_users', [
            'user_name'  => 'New User',
            'user_email' => 'newuser@example.com',
        ]);
    }

    /**
     * Test form updates existing user.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_existing_user_with_valid_data(): void
    {
        /* Arrange */
        $adminUser = $this->seedModel('User');
        $editUser  = $this->seedModel('User', [
            'user_name'  => 'Old Name',
            'user_email' => 'old@example.com',
        ]);

        /**
         * {
         *     "user_name": "Updated Name",
         *     "user_email": "old@example.com",
         *     "user_type": 1,
         *     "btn_submit": "1"
         * }.
         */
        $updateData = [
            'user_name'  => 'Updated Name',
            'user_email' => $editUser->user_email,
            'user_type'  => User::USER_TYPE_ADMINISTRATOR,
            'btn_submit' => '1',
        ];

        /* Act */
        $response = $this->actingAs($adminUser)->post(route('users.form', ['id' => $editUser->user_id]), $updateData);

        /* Assert */
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_users', [
            'user_id'   => $editUser->user_id,
            'user_name' => 'Updated Name',
        ]);
    }

    /**
     * Test form redirects on cancel.
     */
    #[Group('smoke')]
    #[Test]
    public function it_redirects_to_index_on_cancel(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /**
         * {
         *     "btn_cancel": "1"
         * }.
         */
        $cancelData = [
            'btn_cancel' => '1',
        ];

        /* Act */
        $response = $this->actingAs($user)->post(route('users.form'), $cancelData);

        /* Assert */
        $response->assertRedirect(route('users.index'));
    }

    /**
     * Test delete removes user.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_user(): void
    {
        /* Arrange */
        $adminUser  = $this->seedModel('User');
        $deleteUser = $this->seedModel('User');

        /**
         * {
         *     "user_id": 1
         * }.
         */
        $deletePayload = [
            'user_id' => $deleteUser->user_id,
        ];

        /* Act */
        $response = $this->actingAs($adminUser)->post(
            route('users.delete', ['id' => $deleteUser->user_id]),
            $deletePayload
        );

        /* Assert */
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseMissing('ip_users', [
            'user_id' => $deleteUser->user_id,
        ]);
    }

    /**
     * Test delete returns 404 for non-existent user.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_deleting_non_existent_user(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /**
         * {
         *     "user_id": 99999
         * }.
         */
        $deletePayload = [
            'user_id' => 99999,
        ];

        /* Act */
        $response = $this->actingAs($user)->post(
            route('users.delete', ['id' => 99999]),
            $deletePayload
        );

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test form returns 404 for non-existent user in edit mode.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_editing_non_existent_user(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $response = $this->actingAs($user)->get(route('users.form', ['id' => 99999]));

        /* Assert */
        $response->assertNotFound();
    }
}
