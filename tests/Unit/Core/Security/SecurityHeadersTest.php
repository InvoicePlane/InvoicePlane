<?php

namespace Tests\Unit\Security;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Executes the real bootstrap/kernel.php in a child process and inspects the
 * header list it computes, so a regression in the kernel fails these tests.
 */
class SecurityHeadersTest extends TestCase
{
    /**
     * @return array<string, array{0: string|null, 1: string, 2: string}>
     */
    public static function frameOptionProvider(): array
    {
        return [
            'default is SAMEORIGIN'            => [null, 'SAMEORIGIN', "'self'"],
            'explicit SAMEORIGIN'              => ['SAMEORIGIN', 'SAMEORIGIN', "'self'"],
            'explicit DENY'                    => ['DENY', 'DENY', "'none'"],
            'lowercase is normalised'          => ['deny', 'DENY', "'none'"],
            'whitespace is trimmed'            => ['  DENY  ', 'DENY', "'none'"],
            'ALLOWALL falls back'              => ['ALLOWALL', 'SAMEORIGIN', "'self'"],
            'ALLOW-FROM falls back'            => ['ALLOW-FROM https://evil.example', 'SAMEORIGIN', "'self'"],
            'empty string falls back'          => ['', 'SAMEORIGIN', "'self'"],
        ];
    }

    #[Test]
    #[DataProvider('frameOptionProvider')]
    public function it_derives_matching_frame_headers_from_the_configured_value(?string $configured, string $expectedOption, string $expectedAncestors): void
    {
        /* Arrange */
        $env = $configured === null ? [] : ['X_FRAME_OPTIONS' => $configured];

        /* Act */
        $headers = $this->kernelHeaders($env);

        /* Assert */
        self::assertContains('X-Frame-Options: ' . $expectedOption, $headers);
        self::assertContains(
            "Content-Security-Policy: frame-ancestors {$expectedAncestors}; object-src 'none'; base-uri 'self'",
            $headers,
        );
    }

    #[Test]
    public function it_always_sends_a_strict_referrer_policy(): void
    {
        /* Act */
        $headers = $this->kernelHeaders([]);

        /* Assert */
        self::assertContains('Referrer-Policy: strict-origin-when-cross-origin', $headers);
    }

    #[Test]
    public function it_sends_nosniff_by_default_and_omits_it_when_disabled(): void
    {
        /* Act */
        $enabled  = $this->kernelHeaders([]);
        $disabled = $this->kernelHeaders(['ENABLE_X_CONTENT_TYPE_OPTIONS' => 'false']);

        /* Assert */
        self::assertContains('X-Content-Type-Options: nosniff', $enabled);
        self::assertNotContains('X-Content-Type-Options: nosniff', $disabled);
    }

    /**
     * @param array<string, string> $env
     *
     * @return list<string>
     */
    private function kernelHeaders(array $env): array
    {
        $root   = dirname(__DIR__, 4);
        $script = <<<'PHP'
            define('CI_TESTING', true);
            define('ROOT_PATH', $argv[1]);
            foreach (json_decode($argv[2], true) as $k => $v) {
                $_ENV[$k] = $v;
            }
            require $argv[1] . '/bootstrap/kernel.php';
            echo "\n__HEADERS__" . json_encode($GLOBALS['ip_security_response_headers'] ?? null);
            PHP;

        $command = sprintf(
            'php -r %s %s %s 2>&1',
            escapeshellarg($script),
            escapeshellarg($root),
            escapeshellarg((string) json_encode($env, JSON_FORCE_OBJECT)),
        );
        $output = (string) shell_exec($command);

        self::assertMatchesRegularExpression('/__HEADERS__(\[.*\])\s*$/s', $output, 'Kernel did not report headers. Output: ' . $output);
        preg_match('/__HEADERS__(\[.*\])\s*$/s', $output, $m);

        return json_decode($m[1], true, 512, JSON_THROW_ON_ERROR);
    }
}
