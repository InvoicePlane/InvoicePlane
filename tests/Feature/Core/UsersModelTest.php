<?php

namespace Tests\Feature\Core;

use Mdl_Users;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(Mdl_Users::class)]
class UsersModelTest extends AbstractTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        get_instance()->load->model('users/mdl_users');
        $this->model = get_instance()->mdl_users;
    }

    #[Test]
    #[Group('crud')]
    public function it_retrieves_all_users(): void
    {
        /* Arrange */
        $this->model->save(null, [
            'user_name'     => 'John Doe',
            'user_email'    => 'john@example.com',
            'user_password' => password_hash('password', PASSWORD_DEFAULT),
            'user_type'     => 1,
        ]);
        $this->model->save(null, [
            'user_name'     => 'Jane Doe',
            'user_email'    => 'jane@example.com',
            'user_password' => password_hash('password', PASSWORD_DEFAULT),
            'user_type'     => 1,
        ]);

        /* Act */
        $this->model->default_select();
        $result = $this->model->get()->result();

        /* Assert */
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $rules = $this->model->validation_rules();

        /* Assert */
        $this->assertIsArray($rules);
    }
}
