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

        DB::table('ip_users')->delete();
    }

    #[Test]
    public function it_returns_empty_string_for_null_user(): void
    {
        $result = UserHelper::format_user(null);

        $this->assertSame('', $result);
    }

    #[Test]
    public function it_returns_empty_string_for_nonexistent_user_id(): void
    {
        $result = UserHelper::format_user(99999);

        $this->assertSame('', $result);
    }

    #[Test]
    public function it_formats_user_with_name_only(): void
    {
        $user = (object) [
            'user_name'              => 'John Doe',
            'user_company'           => '',
            'user_invoicing_contact' => '',
        ];

        $result = UserHelper::format_user($user);

        $this->assertSame('John doe', $result);
    }

    #[Test]
    public function it_formats_user_with_company(): void
    {
        $user = (object) [
            'user_name'              => 'John Doe',
            'user_company'           => 'ACME Corp',
            'user_invoicing_contact' => '',
        ];

        $result = UserHelper::format_user($user);

        $this->assertSame('John doe - ACME Corp', $result);
    }

    #[Test]
    public function it_formats_user_with_contact(): void
    {
        $user = (object) [
            'user_name'              => 'John Doe',
            'user_company'           => '',
            'user_invoicing_contact' => 'jane@example.com',
        ];

        $result = UserHelper::format_user($user);

        $this->assertSame('John doe - jane@example.com', $result);
    }

    #[Test]
    public function it_formats_user_with_all_fields(): void
    {
        $user = (object) [
            'user_name'              => 'John Doe',
            'user_company'           => 'ACME Corp',
            'user_invoicing_contact' => 'jane@example.com',
        ];

        $result = UserHelper::format_user($user);

        $this->assertSame('John doe - ACME Corp - jane@example.com', $result);
    }

    #[Test]
    public function it_capitalizes_first_letter_of_name(): void
    {
        $user = (object) [
            'user_name'              => 'john',
            'user_company'           => '',
            'user_invoicing_contact' => '',
        ];

        $result = UserHelper::format_user($user);

        $this->assertStringStartsWith('John', $result);
    }

    #[Test]
    public function it_handles_user_object_without_optional_fields(): void
    {
        $user = (object) [
            'user_name' => 'Jane Smith',
        ];

        $result = UserHelper::format_user($user);

        $this->assertSame('Jane smith', $result);
    }
}
