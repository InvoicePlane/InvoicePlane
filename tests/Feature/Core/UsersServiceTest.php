<?php

namespace Tests\Feature\Core;

use Mdl_Users;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(Mdl_Users::class)]
class UsersServiceTest extends AbstractTestCase
{
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(UsersService::class);
    }

    #[Test]
    #[Group('crud')]
    public function it_retrieves_all_users(): void
    {
        /* Arrange */
        User::create([
            'user_name'     => 'John Doe',
            'user_email'    => 'john@example.com',
            'user_password' => bcrypt('password'),
        ]);
        User::create([
            'user_name'     => 'Jane Doe',
            'user_email'    => 'jane@example.com',
            'user_password' => bcrypt('password'),
        ]);

        /* Act */
        $result = $this->service->defaultSelect()->get();

        /* Assert */
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $rules = $this->service->validationRules();

        /* Assert */
        $this->assertIsArray($rules);
    }
}
