<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Resolves and pins provider-issued download URLs to a public HTTPS address.
 */
class RemoteUrlGuard
{
    /**
     * @return array{host: string, port: int, ip: string}
     */
    public function validateAndResolve(string $url): array
    {
        if ($url === '' || mb_strlen($url) > 2048) {
            throw new InvalidArgumentException('Provider document URL is missing or too long.');
        }

        $parts = parse_url($url);
        if ($parts === false
            || mb_strtolower((string) ($parts['scheme'] ?? '')) !== 'https'
            || empty($parts['host'])
            || isset($parts['user'])
            || isset($parts['pass'])) {
            throw new InvalidArgumentException('Provider document URL must be an HTTPS URL without credentials.');
        }

        $port = (int) ($parts['port'] ?? 443);
        if ($port !== 443) {
            throw new InvalidArgumentException('Provider document URL must use the standard HTTPS port.');
        }

        $host = mb_strtolower(trim((string) $parts['host'], '[]'));
        if (filter_var($host, FILTER_VALIDATE_IP) === false
            && preg_match('/^(?=.{1,253}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)*[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/', $host) !== 1) {
            throw new InvalidArgumentException('Provider document URL contains an invalid host name.');
        }

        $ips = $this->resolve($host);
        if ($ips === []) {
            throw new InvalidArgumentException('Provider document host cannot be resolved.');
        }

        foreach ($ips as $ip) {
            if (filter_var(
                $ip,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
            ) === false) {
                throw new InvalidArgumentException('Provider document URL resolves to a non-public address.');
            }
        }

        usort($ips, static fn (string $left, string $right): int => mb_substr_count($left, ':') <=> mb_substr_count($right, ':'));

        return ['host' => $host, 'port' => $port, 'ip' => $ips[0]];
    }

    /**
     * @return string[]
     */
    protected function resolve(string $host): array
    {
        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            return [$host];
        }

        $records = @dns_get_record($host, DNS_A | DNS_AAAA);
        if ( ! is_array($records)) {
            return [];
        }

        $ips = [];
        foreach ($records as $record) {
            $ip = $record['ip'] ?? $record['ipv6'] ?? null;
            if (is_string($ip) && filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                $ips[] = $ip;
            }
        }

        return array_values(array_unique($ips));
    }
}
