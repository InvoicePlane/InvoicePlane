<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * GHSA-8543-x4j8-jj4q: the online-payment settings form rendered the stored (decrypted or encrypted)
 * gateway API keys and secrets into the page's value="" attributes, exposing them to anyone who could
 * view the page source or a cached copy. Password-type gateway fields must render an empty value; plain
 * fields (such as the publishable key) keep theirs. No test rendered the settings page with a stored secret, so reverting
 * the `value=""` branch kept the suite green.
 */
final class GatewaySecretsNotRenderedTest extends AbstractTestCase
{
    #[Test]
    public function it_never_renders_a_stored_gateway_secret_into_the_settings_page(): void
    {
        /* Arrange */
        $this->actingAsAdmin();
        $this->setSetting('gateway_stripe_apiKey', 'sk_live_SECRETGATEWAYVALUE123');
        $this->setSetting('gateway_stripe_apiKeyPublic', 'pk_test_PUBLISHABLE');

        /* Act */
        $response = $this->get('/settings');

        /* Assert: the field is on the page (positive control), but empty, and the secret is nowhere in the HTML */
        $this->assertResponseStatusCode($response, 200);
        self::assertStringContainsString('settings[gateway_stripe_apiKey]', $response->body(), 'The gateway key field must be rendered.');
        self::assertStringNotContainsString('SECRETGATEWAYVALUE123', $response->body(), 'A stored gateway secret must never be printed into the settings page.');
        self::assertMatchesRegularExpression('/name="settings\[gateway_stripe_apiKey\]"[^>]*value=""/s', $response->body());
    }

    #[Test]
    public function it_still_renders_a_non_secret_gateway_field_with_its_stored_value(): void
    {
        /* Arrange */
        $this->actingAsAdmin();
        $this->setSetting('gateway_stripe_apiKeyPublic', 'pk_test_PUBLISHABLE');

        /* Act */
        $response = $this->get('/settings');

        /* Assert: only password-type fields are blanked */
        self::assertMatchesRegularExpression('/name="settings\[gateway_stripe_apiKeyPublic\]"[^>]*value="pk_test_PUBLISHABLE"/s', $response->body());
    }

    private function setSetting(string $key, string $value): void
    {
        $this->databaseDelete('ip_settings', ['setting_key' => $key]);
        $this->databaseInsert('ip_settings', ['setting_key' => $key, 'setting_value' => $value]);
    }
}
