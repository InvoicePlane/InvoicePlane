<?php

declare(strict_types=1);

namespace Tests\Integration\Support;

final class HttpResponse
{
    /**
     * @param list<string> $headers
     */
    public function __construct(
        public readonly string $body,
        public readonly int $statusCode,
        public readonly array $headers,
        public readonly string $stderr,
    ) {
    }

    public function header(string $name): ?string
    {
        $needle = mb_strtolower($name) . ':';

        foreach ($this->headers as $header) {
            if (str_starts_with(mb_strtolower($header), $needle)) {
                return mb_trim(substr($header, strlen($name) + 1));
            }
        }

        return null;
    }
}
