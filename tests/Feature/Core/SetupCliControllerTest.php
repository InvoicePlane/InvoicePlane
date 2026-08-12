<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * setup/controllers/Cli.php — guarded by is_cli(), which the normal Feature
 * request harness (tests/Integration/bin/request.php) hardcodes to false so
 * URI-based routing works. That's enough to prove the guard rejects HTTP
 * access, but create_default_user()'s real behavior needs a genuine CLI
 * subprocess (php public/index.php setup/cli/...), matching how it's
 * actually invoked in Docker entrypoints per this repo's CLAUDE.md.
 */
class SetupCliControllerTest extends AbstractTestCase
{
    #[Test]
    public function it_denies_http_access_to_the_cli_controller(): void
    {
        /* Arrange */
        /* Act */
        $response = $this->get('/setup/cli/create_default_user');

        /* Assert */
        $this->assertResponseStatusCode($response, 403);
    }

    #[Test]
    public function it_creates_a_default_admin_user_when_none_exist(): void
    {
        /* Arrange */
        // The baseline seed always includes an admin user; empty it so this
        // genuinely exercises the "no users exist yet" path.
        $this->databaseTruncate('ip_users');
        $email = 'cli-default-' . bin2hex(random_bytes(4)) . '@test.local';

        /* Act */
        [$exitCode, $stdout, $stderr] = $this->runCli('setup/cli/create_default_user', [
            'DEFAULT_ADMIN_EMAIL'    => $email,
            'DEFAULT_ADMIN_PASSWORD' => 'a-fixed-test-password',
            'DEFAULT_ADMIN_NAME'     => 'CLI Test Admin',
        ]);

        /* Assert */
        self::assertSame(0, $exitCode, "CLI exited non-zero.\nSTDOUT: {$stdout}\nSTDERR: {$stderr}");
        self::assertStringContainsString('Default admin user created', $stdout);
        $this->resetDatabaseConnection();
        $this->assertDatabaseHas('ip_users', ['user_email' => $email, 'user_type' => 1]);
    }

    #[Test]
    public function it_skips_creating_a_default_admin_user_when_one_already_exists(): void
    {
        /* Arrange: the baseline seed already provides an admin user (user_id 1) */
        /* Act */
        [$exitCode, $stdout, $stderr] = $this->runCli('setup/cli/create_default_user');

        /* Assert */
        self::assertSame(0, $exitCode, "CLI exited non-zero.\nSTDOUT: {$stdout}\nSTDERR: {$stderr}");
        self::assertStringContainsString('already exist', $stdout);
        $this->assertDatabaseCount('ip_users', 1);
    }

    private function runCli(string $route, array $env = []): array
    {
        $repoRoot = dirname(__DIR__, 3);
        $command  = sprintf('%s %s %s', escapeshellarg(PHP_BINARY), escapeshellarg($repoRoot . '/public/index.php'), escapeshellarg($route));

        $process = proc_open(
            $command,
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            $repoRoot,
            array_merge(['CI_ENV' => 'testing', 'PATH' => getenv('PATH') ?: '/usr/bin:/bin'], $env),
        );

        self::assertIsResource($process, 'Unable to start CLI subprocess.');

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        return [$exitCode, $stdout, $stderr];
    }
}
