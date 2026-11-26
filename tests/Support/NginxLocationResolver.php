<?php

declare(strict_types=1);

namespace Tests\Support;

/**
 * Selects the nginx `location` that would serve a request path, following nginx's documented order:
 * an exact (=) match, else the longest prefix; a longest prefix marked ^~ wins outright; otherwise the
 * first matching regex location in file order; otherwise the longest prefix. Only what the bundled
 * resources/docker/nginx/invoiceplane.conf uses is supported (no nested or named locations).
 */
final class NginxLocationResolver
{
    /** @param list<array{modifier: string, pattern: string, body: string}> $locations */
    public function __construct(private readonly array $locations) {}

    public static function fromConfig(string $config): self
    {
        $withoutComments = (string) preg_replace('/(^|\s)#.*$/m', '$1', $config);

        preg_match_all('/location\s+(?:(=|~\*|~|\^~)\s+)?(\S+)\s*\{([^{}]*)\}/', $withoutComments, $matches, PREG_SET_ORDER);

        $locations = [];
        foreach ($matches as $match) {
            $locations[] = ['modifier' => $match[1], 'pattern' => $match[2], 'body' => trim($match[3])];
        }

        return new self($locations);
    }

    /** A location refuses the request when it answers 404 (or denies) and never hands it to PHP, a proxy or the filesystem. */
    public static function refuses(?array $location): bool
    {
        if ($location === null) {
            return false;
        }

        $body = $location['body'];
        if (preg_match('/\b(fastcgi_pass|proxy_pass|try_files|root|alias)\b/', $body) === 1) {
            return false;
        }

        return preg_match('/\breturn\s+(404|403)\s*;/', $body) === 1 || preg_match('/\bdeny\s+all\s*;/', $body) === 1;
    }

    /** @param list<string> $modifiers modifiers to keep; '' is a plain prefix location */
    public function onlyModifiers(array $modifiers): self
    {
        return new self(array_values(array_filter(
            $this->locations,
            static fn (array $l): bool => in_array($l['modifier'], $modifiers, true) || $l['pattern'] === '/'
        )));
    }

    /** @return list<array{modifier: string, pattern: string, body: string}> */
    public function locations(): array
    {
        return $this->locations;
    }

    /** @return array{modifier: string, pattern: string, body: string}|null */
    public function resolve(string $path): ?array
    {
        $best = null;
        foreach ($this->locations as $location) {
            if ($location['modifier'] === '=' && $location['pattern'] === $path) {
                return $location;
            }
            if (in_array($location['modifier'], ['', '^~'], true) && str_starts_with($path, $location['pattern'])
                && ($best === null || strlen($location['pattern']) > strlen($best['pattern']))) {
                $best = $location;
            }
        }

        if ($best !== null && $best['modifier'] === '^~') {
            return $best;
        }

        foreach ($this->locations as $location) {
            if ( ! in_array($location['modifier'], ['~', '~*'], true)) {
                continue;
            }
            $regex = '#' . str_replace('#', '\#', $location['pattern']) . '#' . ($location['modifier'] === '~*' ? 'i' : '');
            if (preg_match($regex, $path) === 1) {
                return $location;
            }
        }

        return $best;
    }
}
