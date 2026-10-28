<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * GHSA-hmqm-59ff-gmcw: Stored XSS on the Unauthenticated Login Page via the system_theme Setting.
 *
 * ThemeAssetStoredXssTest covers the attribute-breakout shape; this one covers the tag-breakout
 * shape (a value that closes the <link> element and opens a <script>) on the same sink.
 */
#[Group('security')]
final class ThemeXssLoginPageTest extends AbstractTestCase
{
    #[Test]
    public function it_escapes_a_script_tag_in_the_system_theme_value_on_the_login_page(): void
    {
        /* Arrange */
        $payload = '"><script>alert(1)</script><"';
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'system_theme', 'setting_value' => '']);
        $this->databaseUpdate('ip_settings', ['setting_value' => $payload], ['setting_key' => 'system_theme']);

        /* Act */
        $response = $this->get('/sessions/login');

        /* Assert */
        $this->assertResponseOk($response);
        $this->assertResponseBodyNotContains($response, '<script>alert(1)</script>');
        $this->assertResponseBodyContains($response, '&lt;script&gt;alert(1)&lt;/script&gt;');
    }
}
