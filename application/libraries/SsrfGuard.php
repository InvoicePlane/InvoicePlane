<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * SsrfGuard — validates that a URL is safe for outbound server-side HTTP requests.
 *
 * Enforces:
 *   - HTTPS scheme only (no http://, file://, gopher://, dict://, …)
 *   - Destination IP must not fall in any private, loopback, link-local, or
 *     reserved range (blocks cloud metadata endpoints, internal services, etc.)
 *
 * Call SsrfGuard::validate() before storing or using any admin-supplied URL
 * that the server will later fetch.
 */
class SsrfGuard
{
    private const BLOCKED_V4 = [
        '0.0.0.0/8',           // "This" network
        '10.0.0.0/8',          // RFC 1918 private A
        '100.64.0.0/10',       // Shared address space / CGNAT (RFC 6598)
        '127.0.0.0/8',         // Loopback
        '169.254.0.0/16',      // Link-local — AWS/GCP/Azure metadata (169.254.169.254)
        '172.16.0.0/12',       // RFC 1918 private B
        '192.0.0.0/24',        // IETF Protocol Assignments
        '192.0.2.0/24',        // TEST-NET-1 (documentation only)
        '192.168.0.0/16',      // RFC 1918 private C
        '198.18.0.0/15',       // Benchmarking (RFC 2544)
        '198.51.100.0/24',     // TEST-NET-2 (documentation only)
        '203.0.113.0/24',      // TEST-NET-3 (documentation only)
        '224.0.0.0/4',         // Multicast
        '240.0.0.0/4',         // Reserved / future use
        '255.255.255.255/32',  // Broadcast
    ];

    private const BLOCKED_V6 = [
        '::/128',              // Unspecified
        '::1/128',             // Loopback
        '::ffff:0:0/96',       // IPv4-mapped (can carry RFC-1918 addresses)
        '64:ff9b::/96',        // IPv4/IPv6 translation (RFC 6052)
        '100::/64',            // Discard (RFC 6666)
        'fc00::/7',            // Unique-local (fc00:: and fd00::, RFC 4193)
        'fe80::/10',           // Link-local
        'ff00::/8',            // Multicast
    ];

    /**
     * Assert that $url is a safe HTTPS destination for an outbound server request.
     *
     * Empty string is allowed (means "not configured").
     *
     * @throws \InvalidArgumentException with a human-readable message on rejection.
     */
    public function validate(string $url): void
    {
        if ($url === '') {
            return;
        }

        $parsed = parse_url($url);

        if ($parsed === false || empty($parsed['host'])) {
            throw new \InvalidArgumentException(
                "Invalid URL — cannot parse '{$url}'."
            );
        }

        $scheme = strtolower($parsed['scheme'] ?? '');

        if ($scheme !== 'https') {
            throw new \InvalidArgumentException(
                "Only HTTPS URLs are permitted for integration endpoints (got scheme '{$scheme}')."
            );
        }

        // Strip IPv6 brackets e.g. [::1] → ::1
        $host = strtolower(trim($parsed['host'], '[]'));

        $ips = $this->resolveHost($host);

        if ($ips === []) {
            throw new \InvalidArgumentException(
                "Cannot resolve host '{$host}'. The URL is rejected to prevent misconfiguration."
            );
        }

        foreach ($ips as $ip) {
            if ($this->isBlockedIp($ip)) {
                throw new \InvalidArgumentException(
                    "The URL resolves to a private or reserved IP address ({$ip}) " .
                    "and cannot be used as an integration endpoint."
                );
            }
        }
    }

    /**
     * Assert that $path is a relative path (no scheme), not a full URL.
     * Endpoint fields like upload_endpoint are relative paths that get
     * appended to api_base_url — a full URL there overrides the base.
     *
     * @throws \InvalidArgumentException
     */
    public function validateRelativePath(string $path): void
    {
        if ($path === '') {
            return;
        }

        if (preg_match('#://#', $path)) {
            throw new \InvalidArgumentException(
                "Endpoint paths must be relative (e.g. /v1/invoices), not absolute URLs (got '{$path}')."
            );
        }
    }

    // -------------------------------------------------------------------------
    // Internal — overridable in tests
    // -------------------------------------------------------------------------

    /**
     * Resolve a hostname or literal IP to a list of IP address strings.
     * Returns an empty array when resolution fails.
     *
     * @return string[]
     */
    protected function resolveHost(string $host): array
    {
        // Literal IPv6
        if (str_contains($host, ':')) {
            return filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false
                ? [$host]
                : [];
        }

        // Literal IPv4
        if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
            return [$host];
        }

        // Hostname: resolve A records
        $v4 = gethostbynamel($host);
        $ips = is_array($v4) ? $v4 : [];

        // Resolve AAAA records
        $v6 = @dns_get_record($host, DNS_AAAA);
        if (is_array($v6)) {
            foreach ($v6 as $record) {
                if (!empty($record['ipv6'])) {
                    $ips[] = $record['ipv6'];
                }
            }
        }

        return $ips;
    }

    private function isBlockedIp(string $ip): bool
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
            foreach (self::BLOCKED_V6 as $cidr) {
                if ($this->ipv6InCidr($ip, $cidr)) {
                    return true;
                }
            }
            return false;
        }

        foreach (self::BLOCKED_V4 as $cidr) {
            if ($this->ipv4InCidr($ip, $cidr)) {
                return true;
            }
        }

        return false;
    }

    private function ipv4InCidr(string $ip, string $cidr): bool
    {
        [$subnet, $bits] = explode('/', $cidr, 2);
        $ipLong     = ip2long($ip);
        $subnetLong = ip2long($subnet);
        if ($ipLong === false || $subnetLong === false) {
            return false;
        }
        $bits = (int) $bits;
        $mask = $bits === 0 ? 0 : (int) (~0 << (32 - $bits));
        return ($ipLong & $mask) === ($subnetLong & $mask);
    }

    private function ipv6InCidr(string $ip, string $cidr): bool
    {
        [$subnet, $bits] = explode('/', $cidr, 2);
        $ipBin     = inet_pton($ip);
        $subnetBin = inet_pton($subnet);
        if ($ipBin === false || $subnetBin === false) {
            return false;
        }
        $bits      = (int) $bits;
        $fullBytes = intdiv($bits, 8);
        for ($i = 0; $i < $fullBytes; $i++) {
            if (ord($ipBin[$i]) !== ord($subnetBin[$i])) {
                return false;
            }
        }
        $rem = $bits % 8;
        if ($rem > 0) {
            $mask = 0xFF & (0xFF << (8 - $rem));
            if ((ord($ipBin[$fullBytes]) & $mask) !== (ord($subnetBin[$fullBytes]) & $mask)) {
                return false;
            }
        }
        return true;
    }
}
