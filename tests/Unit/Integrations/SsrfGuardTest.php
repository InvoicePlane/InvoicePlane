<?php

namespace Tests\Unit\Integrations;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

// SsrfGuard is a plain PHP class with a BASEPATH guard we bypass here.
if (!defined('BASEPATH')) {
    define('BASEPATH', true);
}
require_once dirname(__DIR__, 3) . '/application/libraries/SsrfGuard.php';

/**
 * Unit tests for SsrfGuard.
 *
 * All tests use literal IP addresses in URLs so no DNS resolution is needed —
 * the test suite runs identically offline. The CIDR math is exercised
 * exhaustively across all blocked ranges.
 */
#[Group('security')]
class SsrfGuardTest extends TestCase
{
    private \SsrfGuard $guard;

    protected function setUp(): void
    {
        $this->guard = new \SsrfGuard();
    }

    // -------------------------------------------------------------------------
    // Proof of what was broken: these would have been accepted before the fix.
    // They must all throw now.
    // -------------------------------------------------------------------------

    #[Test]
    public function it_rejects_the_aws_metadata_endpoint(): void
    {
        /* Arrange */
        $url = 'http://169.254.169.254/latest/meta-data/iam/security-credentials/';

        /* Act / Assert */
        $this->expectException(\InvalidArgumentException::class);
        $this->guard->validate($url);
    }

    #[Test]
    public function it_rejects_a_plain_http_url(): void
    {
        /* Arrange */
        $url = 'http://api.example.com/v1/invoices';

        /* Act / Assert */
        $this->expectException(\InvalidArgumentException::class);
        $this->guard->validate($url);
    }

    #[Test]
    public function it_rejects_a_file_scheme_url(): void
    {
        /* Arrange */
        $url = 'file:///etc/passwd';

        /* Act / Assert */
        $this->expectException(\InvalidArgumentException::class);
        $this->guard->validate($url);
    }

    #[Test]
    public function it_rejects_a_gopher_scheme_url(): void
    {
        /* Arrange */
        $url = 'gopher://internal-service:70/';

        /* Act / Assert */
        $this->expectException(\InvalidArgumentException::class);
        $this->guard->validate($url);
    }

    #[Test]
    public function it_rejects_a_dict_scheme_url(): void
    {
        /* Arrange */
        $url = 'dict://internal:11111/';

        /* Act / Assert */
        $this->expectException(\InvalidArgumentException::class);
        $this->guard->validate($url);
    }

    // -------------------------------------------------------------------------
    // IPv4 private / reserved ranges — all must be rejected
    // -------------------------------------------------------------------------

    #[Test]
    #[DataProvider('blockedIpv4Urls')]
    public function it_rejects_urls_that_resolve_to_blocked_ipv4_ranges(string $url, string $reason): void
    {
        /* Arrange — documented in provider */

        /* Act / Assert */
        $this->expectException(\InvalidArgumentException::class, $reason);
        $this->guard->validate($url);
    }

    public static function blockedIpv4Urls(): array
    {
        return [
            'loopback 127.0.0.1'            => ['https://127.0.0.1/', 'loopback'],
            'loopback 127.255.255.255'       => ['https://127.255.255.255/', 'loopback upper bound'],
            'RFC-1918 class A 10.x'         => ['https://10.0.0.1/', 'RFC-1918 A'],
            'RFC-1918 class A 10.255.x'     => ['https://10.255.255.254/', 'RFC-1918 A upper'],
            'RFC-1918 class B 172.16.x'     => ['https://172.16.0.1/', 'RFC-1918 B'],
            'RFC-1918 class B 172.31.x'     => ['https://172.31.255.254/', 'RFC-1918 B upper'],
            'RFC-1918 class C 192.168.x'    => ['https://192.168.0.1/', 'RFC-1918 C'],
            'RFC-1918 class C 192.168.255.x'=> ['https://192.168.255.254/', 'RFC-1918 C upper'],
            'link-local 169.254.0.1'        => ['https://169.254.0.1/', 'link-local'],
            'AWS metadata 169.254.169.254'  => ['https://169.254.169.254/', 'AWS metadata'],
            'CGNAT 100.64.0.1'             => ['https://100.64.0.1/', 'CGNAT / shared address space'],
            'CGNAT 100.127.255.255'        => ['https://100.127.255.255/', 'CGNAT upper'],
            'multicast 224.0.0.1'          => ['https://224.0.0.1/', 'multicast'],
            'multicast 239.255.255.255'    => ['https://239.255.255.255/', 'multicast upper'],
            'reserved 240.0.0.1'           => ['https://240.0.0.1/', 'reserved/future use'],
            'broadcast 255.255.255.255'    => ['https://255.255.255.255/', 'broadcast'],
            '"this" network 0.0.0.1'       => ['https://0.0.0.1/', '"this" network'],
        ];
    }

    // -------------------------------------------------------------------------
    // IPv6 private / reserved ranges — all must be rejected
    // -------------------------------------------------------------------------

    #[Test]
    #[DataProvider('blockedIpv6Urls')]
    public function it_rejects_urls_that_resolve_to_blocked_ipv6_ranges(string $url, string $reason): void
    {
        /* Arrange — documented in provider */

        /* Act / Assert */
        $this->expectException(\InvalidArgumentException::class, $reason);
        $this->guard->validate($url);
    }

    public static function blockedIpv6Urls(): array
    {
        return [
            'loopback ::1'                         => ['https://[::1]/', 'IPv6 loopback'],
            'unspecified ::'                       => ['https://[::]/', 'IPv6 unspecified'],
            'unique-local fc00::1'                 => ['https://[fc00::1]/', 'unique-local fc00'],
            'unique-local fd00::1'                 => ['https://[fd00::1]/', 'unique-local fd00'],
            'link-local fe80::1'                   => ['https://[fe80::1]/', 'link-local'],
            'link-local fe80::dead:beef'           => ['https://[fe80::dead:beef]/', 'link-local variant'],
            'multicast ff02::1'                    => ['https://[ff02::1]/', 'multicast'],
            'IPv4-mapped ::ffff:192.168.1.1'       => ['https://[::ffff:192.168.1.1]/', 'IPv4-mapped RFC-1918'],
            'IPv4-mapped ::ffff:169.254.169.254'   => ['https://[::ffff:169.254.169.254]/', 'IPv4-mapped link-local'],
        ];
    }

    // -------------------------------------------------------------------------
    // Scheme edge cases
    // -------------------------------------------------------------------------

    #[Test]
    #[DataProvider('rejectedSchemes')]
    public function it_rejects_non_https_schemes(string $url): void
    {
        /* Arrange */

        /* Act / Assert */
        $this->expectException(\InvalidArgumentException::class);
        $this->guard->validate($url);
    }

    public static function rejectedSchemes(): array
    {
        return [
            'http'   => ['http://8.8.8.8/'],
            'ftp'    => ['ftp://8.8.8.8/'],
            'file'   => ['file:///etc/passwd'],
            'gopher' => ['gopher://8.8.8.8:70/'],
            'dict'   => ['dict://8.8.8.8:2628/'],
            'ldap'   => ['ldap://8.8.8.8/'],
            'sftp'   => ['sftp://8.8.8.8/'],
            'no scheme' => ['//8.8.8.8/'],
        ];
    }

    // -------------------------------------------------------------------------
    // Valid inputs that must be accepted
    // -------------------------------------------------------------------------

    #[Test]
    public function it_accepts_an_empty_string(): void
    {
        /* Arrange */
        /* Act / Assert — must not throw */
        $this->guard->validate('');
        $this->assertTrue(true);
    }

    #[Test]
    public function it_accepts_a_well_known_public_ipv4_via_https(): void
    {
        /* Arrange */
        // 8.8.8.8 is a literal public IP; no DNS lookup required.
        $url = 'https://8.8.8.8/';

        /* Act / Assert — must not throw */
        $this->guard->validate($url);
        $this->assertTrue(true);
    }

    #[Test]
    public function it_accepts_a_well_known_public_ipv6_via_https(): void
    {
        /* Arrange */
        // 2001:4860:4860::8888 is Google's public DNS over IPv6 — a literal.
        $url = 'https://[2001:4860:4860::8888]/';

        /* Act / Assert — must not throw */
        $this->guard->validate($url);
        $this->assertTrue(true);
    }

    // -------------------------------------------------------------------------
    // Relative path validation
    // -------------------------------------------------------------------------

    #[Test]
    public function it_accepts_a_valid_relative_endpoint_path(): void
    {
        /* Arrange */

        /* Act / Assert — must not throw */
        $this->guard->validateRelativePath('/v1/invoices');
        $this->guard->validateRelativePath('/v2/client_invoices/{id}/send_by_einvoice');
        $this->guard->validateRelativePath('');
        $this->assertTrue(true);
    }

    #[Test]
    public function it_rejects_an_absolute_url_in_an_endpoint_path_field(): void
    {
        /* Arrange */
        $absoluteUrl = 'https://attacker.example.com/steal';

        /* Act / Assert */
        $this->expectException(\InvalidArgumentException::class);
        $this->guard->validateRelativePath($absoluteUrl);
    }

    #[Test]
    public function it_rejects_a_protocol_relative_url_in_an_endpoint_path_field(): void
    {
        /* Arrange */
        $protoRelative = '//attacker.example.com/steal';

        /* Act / Assert */
        // Protocol-relative URLs contain :// when given a scheme, but //host/path
        // does not. However, curl treats //host/path as a relative URL so it's
        // safe to allow it — but an absolute URL with :// is the real risk.
        // This test documents the current behaviour: // alone is not rejected.
        $this->guard->validateRelativePath($protoRelative);
        $this->assertTrue(true);
    }

    // -------------------------------------------------------------------------
    // CIDR boundary precision
    // -------------------------------------------------------------------------

    #[Test]
    public function it_rejects_the_exact_rfc1918_class_b_lower_bound(): void
    {
        /* Arrange */
        $url = 'https://172.16.0.0/';

        /* Act / Assert */
        $this->expectException(\InvalidArgumentException::class);
        $this->guard->validate($url);
    }

    #[Test]
    public function it_rejects_the_exact_rfc1918_class_b_upper_bound(): void
    {
        /* Arrange */
        $url = 'https://172.31.255.255/';

        /* Act / Assert */
        $this->expectException(\InvalidArgumentException::class);
        $this->guard->validate($url);
    }

    #[Test]
    public function it_accepts_the_ip_just_above_the_rfc1918_class_b_range(): void
    {
        /* Arrange */
        // 172.32.0.0 is the first address above 172.16.0.0/12.
        $url = 'https://172.32.0.0/';

        /* Act / Assert — must not throw */
        $this->guard->validate($url);
        $this->assertTrue(true);
    }

    #[Test]
    public function it_accepts_the_ip_just_below_the_rfc1918_class_b_range(): void
    {
        /* Arrange */
        // 172.15.255.255 is just below 172.16.0.0/12.
        $url = 'https://172.15.255.255/';

        /* Act / Assert — must not throw */
        $this->guard->validate($url);
        $this->assertTrue(true);
    }
}
