<?php

namespace Tests\Integration\Support;

class HttpResponse
{
    public function __construct(
        private readonly string $body,
        private readonly int $statusCode,
        private readonly array $headers,
        private readonly string $stderr = '',
    ) {}

    public function body(): string
    {
        return $this->body;
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function stderr(): string
    {
        return $this->stderr;
    }

    public function isRedirect(): bool
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    public function redirectUrl(): string
    {
        foreach ($this->headers as $header) {
            if (stripos($header, 'Location:') === 0) {
                return trim(substr($header, 9));
            }
        }

        return '';
    }
}
