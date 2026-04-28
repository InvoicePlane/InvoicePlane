<?php

namespace Tests\Feature\Core;

use Mdl_Users;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(Mdl_Users::class)]
#[CoversClass(Tests\Feature\Core\UsersService::class)]
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
        $this->markTestIncomplete('This test uses Laravel Model::create pattern which needs to be refactored to use CodeIgniter insert patterns');
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
