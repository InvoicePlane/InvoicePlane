<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class GatewaySecurityTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_requires_json_encoding_for_js_string_contexts(): void
    {
        $malicious_client_id = "'-alert('XSS')-'";
        $safe = json_encode($malicious_client_id, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

        $this->assertStringContainsString('"', $safe);
        $this->assertStringNotContainsString("'-alert('XSS')-'", $safe);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_prevents_js_string_breakout_with_json_encode(): void
    {
        $payload = "'-window.location='http://attacker.com'-'";
        $encoded = json_encode($payload, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

        $this->assertStringStartsWith('"', $encoded);
        $this->assertStringEndsWith('"', $encoded);
    }
}
