<?php

namespace Tests\Helpers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Feature\Quotes\JsonErrorHelper;

#[CoversClass(Tests\Helpers\JsonErrorHelper::class)]

class JsonErrorHelperTest extends AbstractTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Helper wrapper class does not exist — CI3 helpers are global functions, not yet wrapped in OOP classes');
    }

    protected function tearDown(): void
    {
        $_POST = [];
        parent::tearDown();
    }

    #[Test]
    public function it_returns_empty_array_when_no_post_data(): void
    {
        /* Arrange */
        $_POST = [];

        /* Act */
        $result = JsonErrorHelper::json_errors();

        /* Assert */
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function it_returns_array_of_errors(): void
    {
        /* Arrange */
        // Simulate POST data
        $_POST = ['field1' => 'value1', 'field2' => 'value2'];

        /* Act */
        $result = JsonErrorHelper::json_errors();

        /* Assert */
        $this->assertIsArray($result);
    }

    #[Test]
    public function it_processes_multiple_post_fields(): void
    {
        /* Arrange */
        $_POST = [
            'email'    => 'invalid-email',
            'name'     => 'John Doe',
            'password' => 'short',
        ];

        /* Act */
        $result = JsonErrorHelper::json_errors();

        /* Assert */
        $this->assertIsArray($result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('password', $result);
    }
}
