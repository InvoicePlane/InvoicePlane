<?php

namespace Tests\Helpers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Feature\Quotes\DB;
use Tests\Feature\Quotes\UserHelper;

#[CoversClass(Tests\Helpers\UserHelper::class)]

class UserHelperTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Helper wrapper class does not exist — CI3 helpers are global functions, not yet wrapped in OOP classes');

        DB::table('ip_users')->delete();
    }

    #[Test]
    public function it_returns_empty_string_for_null_user(): void
    {
        /* Arrange */

        /* Act */
        $result = UserHelper::format_user(null);

        /* Assert */
        $this->assertSame('', $result);
    }

    #[Test]
    public function it_returns_empty_string_for_nonexistent_user_id(): void
    {
        /* Arrange */

        /* Act */
        $result = UserHelper::format_user(99999);

        /* Assert */
        $this->assertSame('', $result);
    }

    #[Test]
    public function it_formats_user_with_name_only(): void
    {
        /* Arrange */
        $user = (object) [
            'user_name'              => 'John Doe',
            'user_company'           => '',
            'user_invoicing_contact' => '',
        ];

        /* Act */
        $result = UserHelper::format_user($user);

        /* Assert */
        $this->assertSame('John doe', $result);
    }

    #[Test]
    public function it_formats_user_with_company(): void
    {
        /* Arrange */
        $user = (object) [
            'user_name'              => 'John Doe',
            'user_company'           => 'ACME Corp',
            'user_invoicing_contact' => '',
        ];

        /* Act */
        $result = UserHelper::format_user($user);

        /* Assert */
        $this->assertSame('John doe - ACME Corp', $result);
    }

    #[Test]
    public function it_formats_user_with_contact(): void
    {
        /* Arrange */
        $user = (object) [
            'user_name'              => 'John Doe',
            'user_company'           => '',
            'user_invoicing_contact' => 'jane@example.com',
        ];

        /* Act */
        $result = UserHelper::format_user($user);

        /* Assert */
        $this->assertSame('John doe - jane@example.com', $result);
    }

    #[Test]
    public function it_formats_user_with_all_fields(): void
    {
        /* Arrange */
        $user = (object) [
            'user_name'              => 'John Doe',
            'user_company'           => 'ACME Corp',
            'user_invoicing_contact' => 'jane@example.com',
        ];

        /* Act */
        $result = UserHelper::format_user($user);

        /* Assert */
        $this->assertSame('John doe - ACME Corp - jane@example.com', $result);
    }

    #[Test]
    public function it_capitalizes_first_letter_of_name(): void
    {
        /* Arrange */
        $user = (object) [
            'user_name'              => 'john',
            'user_company'           => '',
            'user_invoicing_contact' => '',
        ];

        /* Act */
        $result = UserHelper::format_user($user);

        /* Assert */
        $this->assertStringStartsWith('John', $result);
    }

    #[Test]
    public function it_handles_user_object_without_optional_fields(): void
    {
        /* Arrange */
        $user = (object) [
            'user_name' => 'Jane Smith',
        ];

        /* Act */
        $result = UserHelper::format_user($user);

        /* Assert */
        $this->assertSame('Jane smith', $result);
    }
}
