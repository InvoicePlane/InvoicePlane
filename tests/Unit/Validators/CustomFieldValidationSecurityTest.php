<?php

namespace Tests\Unit\Validators;

use PHPUnit\Framework\TestCase;

class CustomFieldValidationSecurityTest extends TestCase
{
    private mixed $previousCi = null;

    private object $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $root = dirname(__DIR__, 3);
        require_once $root . '/application/helpers/echo_helper.php';
        require_once $root . '/vendor/pocketarc/codeigniter/system/helpers/language_helper.php';

        $this->previousCi          = $GLOBALS['unitCiInstance'] ?? null;
        $GLOBALS['unitCiInstance'] = (object) [
            'lang' => new class () {
                public function line(string $line): string
                {
                    return 'Unable to process field %s: %s';
                }
            },
        ];

        if ( ! class_exists('Validator', false)) {
            require_once $root . '/vendor/pocketarc/codeigniter/system/core/Model.php';
            require_once $root . '/application/core/MY_Model.php';
            require_once $root . '/application/core/Validator.php';
        }

        // create_error_text() is pure output formatting; skip MY_Model's DB-bound constructor.
        $this->validator = (new \ReflectionClass('Validator'))->newInstanceWithoutConstructor();
    }

    protected function tearDown(): void
    {
        $GLOBALS['unitCiInstance'] = $this->previousCi;

        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_custom_field_labels_in_validation_errors(): void
    {
        $errors = [
            [
                'label' => '<script src=//evil.com/x.js></script>',
                'error' => 'invalid input',
                'error_msg' => 'must be a valid date'
            ]
        ];

        $result = $this->validator->create_error_text($errors);

        // Should NOT contain unescaped script tag
        $this->assertStringNotContainsString('<script', $result);
        // Should contain escaped version
        $this->assertStringContainsString('&lt;script', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_error_messages_in_validation_errors(): void
    {
        $errors = [
            [
                'label' => 'Custom Field',
                'error' => 'invalid input',
                'error_msg' => '<img src=x onerror=alert(1)>'
            ]
        ];

        $result = $this->validator->create_error_text($errors);

        $this->assertStringNotContainsString('<img src=', $result);
        $this->assertStringContainsString('&lt;img', $result);
    }
}
