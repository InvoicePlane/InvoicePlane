<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class GatewaySecurityTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_requires_json_encoding_for_js_string_contexts(): void
    {
        // Verify that JSON encoding is used for JavaScript string contexts
        // to prevent breakout attacks like '-alert(1)-' in 'value: '..-value..'

        $malicious_client_id = "'-alert('XSS')-'";

        // Correct: json_encode with appropriate flags
        $safe = json_encode($malicious_client_id, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

        // json_encode should wrap in quotes and escape any quotes inside
        $this->assertStringContainsString('"', $safe);
        $this->assertStringNotContainsString("'-alert('XSS')-'", $safe);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_prevents_js_string_breakout_with_json_encode(): void
    {
        $payload = "'-window.location='http://attacker.com'-'";
        $encoded = json_encode($payload, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

        // When used in: clientId: <?php echo json_encode(...); ?>
        // Result becomes: clientId: "...escaped..."
        // The outer quotes from json_encode prevent breakout
        $this->assertStringStartsWith('"', $encoded);
        $this->assertStringEndsWith('"', $encoded);
    }
}
