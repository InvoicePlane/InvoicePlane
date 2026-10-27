<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * GHSA-858w-pp5p-wphm follow-up: security_frame_headers() is the single place that turns the
 * X_FRAME_OPTIONS setting into X-Frame-Options and a matching CSP frame-ancestors. It must only ever
 * emit a value browsers honour, and bootstrap/kernel.php (which sends the same headers for every
 * response) must agree with it for every input.
 */
#[Group('security')]
final class SecurityFrameHeadersTest extends TestCase
{
    /** @return array<string, array{0: string, 1: string, 2: string}> */
    public static function configuredValues(): array
    {
        return [
            'sameorigin'       => ['SAMEORIGIN', 'SAMEORIGIN', "'self'"],
            'deny'             => ['DENY', 'DENY', "'none'"],
            'lowercase deny'   => ['deny', 'DENY', "'none'"],
            'padded deny'      => ["  Deny \n", 'DENY', "'none'"],
            'allowall'         => ['ALLOWALL', 'SAMEORIGIN', "'self'"],
            'allow-from'       => ['ALLOW-FROM https://evil.example', 'SAMEORIGIN', "'self'"],
            'header injection' => ["DENY\r\nX-Evil: 1", 'SAMEORIGIN', "'self'"],
            'empty'            => ['', 'SAMEORIGIN', "'self'"],
        ];
    }

    #[Test]
    #[DataProvider('configuredValues')]
    public function it_emits_only_a_valid_frame_option_and_the_matching_frame_ancestors(string $configured, string $expectedOption, string $expectedAncestors): void
    {
        /* Arrange / Act */
        $headers = $this->probe($configured)['helper'];

        /* Assert */
        self::assertSame(
            ['X-Frame-Options: ' . $expectedOption, "Content-Security-Policy: frame-ancestors {$expectedAncestors}; object-src 'none'; base-uri 'self'"],
            $headers
        );
    }

    #[Test]
    #[DataProvider('configuredValues')]
    public function it_agrees_with_the_headers_the_kernel_sends_for_every_response(string $configured): void
    {
        /* Arrange / Act */
        $probe = $this->probe($configured);

        /* Assert */
        self::assertSame(array_slice($probe['kernel'], 0, 2), $probe['helper']);
    }

    /**
     * @return array{helper: string[], kernel: string[]}
     */
    private function probe(string $configured): array
    {
        $code = '$_ENV["X_FRAME_OPTIONS"] = ' . var_export($configured, true) . ';'
            . 'require ' . var_export(ROOT_PATH . '/bootstrap/kernel.php', true) . ';'
            . 'require ' . var_export(ROOT_PATH . '/application/helpers/ip_security_helper.php', true) . ';'
            . 'echo json_encode(["helper" => security_frame_headers(), "kernel" => $GLOBALS["ip_security_response_headers"]]);';

        $output = [];
        $status = 1;
        exec(PHP_BINARY . ' -r ' . escapeshellarg($code), $output, $status);

        self::assertSame(0, $status, implode("\n", $output));

        return json_decode(implode("\n", $output), true, 512, JSON_THROW_ON_ERROR);
    }
}
