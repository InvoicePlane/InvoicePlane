<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Integration\Support;

use JsonException;

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
    ) {}

    public function __toString(): string
    {
        return $this->body;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function bodyLength(): int
    {
        return mb_strlen($this->body);
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function contains(string $needle): bool
    {
        return str_contains($this->body, $needle);
    }

    public function isRedirect(): bool
    {
        return in_array($this->statusCode, [301, 302, 303, 307, 308], true);
    }

    public function redirectUrl(): ?string
    {
        return $this->header('Location');
    }

    public function json(): array
    {
        try {
            $decoded = json_decode($this->body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return [];
        }

        return is_array($decoded) ? $decoded : [];
    }

    public function header(string $name): ?string
    {
        $needle = mb_strtolower($name) . ':';

        foreach ($this->headers as $header) {
            if (str_starts_with(mb_strtolower($header), $needle)) {
                return mb_trim(mb_substr($header, mb_strlen($name) + 1));
            }
        }

        return null;
    }
}
