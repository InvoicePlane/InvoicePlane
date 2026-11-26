<?php

namespace Tests\Unit\Core\Security;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * GHSA-858w-pp5p-wphm (clickjacking): a framed page only rides on the visitor's session when the
 * cookie is sent cross-site. The session cookie must carry an explicit SameSite value of Lax or
 * Strict instead of relying on a php.ini default that a host can change to None.
 */
#[Group('security')]
final class SessionCookieSameSiteTest extends TestCase
{
    #[Test]
    public function it_configures_the_session_cookie_with_an_explicit_same_site_value(): void
    {
        /* Arrange */
        $config = $this->bootConfig([]);

        /* Act */
        $sameSite = $config['sess_samesite'] ?? null;

        /* Assert */
        self::assertContains($sameSite, ['Lax', 'Strict'], 'sess_samesite must be Lax or Strict, never None or unset.');
        self::assertTrue($config['cookie_httponly']);
    }

    #[Test]
    public function it_does_not_let_the_environment_downgrade_same_site_to_none(): void
    {
        /* Arrange */
        $config = $this->bootConfig(['SESS_SAMESITE' => 'None']);

        /* Act */
        $sameSite = $config['sess_samesite'] ?? null;

        /* Assert */
        self::assertContains($sameSite, ['Lax', 'Strict']);
    }

    /**
     * @param array<string, string> $env
     *
     * @return array<string, mixed>
     */
    private function bootConfig(array $env): array
    {
        $code = 'foreach (' . var_export($env, true) . ' as $k => $v) { $_ENV[$k] = $v; }'
            . 'require ' . var_export(ROOT_PATH . '/bootstrap/kernel.php', true) . ';'
            . '$config = [];'
            . 'require ' . var_export(ROOT_PATH . '/application/config/config.php', true) . ';'
            . 'echo json_encode($config);';

        $output = [];
        $status = 1;
        exec(PHP_BINARY . ' -r ' . escapeshellarg($code), $output, $status);

        self::assertSame(0, $status, 'config boot probe must exit cleanly.');

        return json_decode(implode("\n", $output), true, 512, JSON_THROW_ON_ERROR);
    }
}
