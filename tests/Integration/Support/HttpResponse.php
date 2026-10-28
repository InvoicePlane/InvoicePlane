<?php

namespace Tests\Integration\Support;

class HttpResponse
{
    public function __construct(
        private readonly string $body,
        private readonly int $statusCode,
        private readonly array $headers,
        private readonly string $stderr = '',
        private readonly array $session = [],
        private readonly bool $sessionActive = false,
        private readonly string $sessionId = '',
        private readonly string $initialSessionId = '',
    ) {}

    /** Session id the request started with (the harness derives it from the seeded session). */
    public function initialSessionId(): string
    {
        return $this->initialSessionId;
    }

    /** Session id at the end of the request; differs from the initial one after sess_regenerate(). */
    public function sessionId(): string
    {
        return $this->sessionId;
    }

    /** Session contents at the end of the request (what the app wrote back). */
    public function session(): array
    {
        return $this->session;
    }

    /** False once the app destroyed the session (e.g. logout). */
    public function sessionActive(): bool
    {
        return $this->sessionActive;
    }

    public function sessionValue(string $key): mixed
    {
        return $this->session[$key] ?? null;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function bodyLength(): int
    {
        return strlen($this->body);
    }

    public function contains(string $needle): bool
    {
        return $needle !== '' && str_contains($this->body, $needle);
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function header(string $name): ?string
    {
        foreach ($this->headers as $header) {
            if (mb_stripos($header, $name . ':') === 0) {
                return trim(mb_substr($header, mb_strlen($name) + 1));
            }
        }

        return null;
    }

    public function hasHeader(string $name): bool
    {
        return $this->header($name) !== null;
    }

    public function stderr(): string
    {
        return $this->stderr;
    }

    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function isRedirect(): bool
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    public function redirectUrl(): string
    {
        foreach ($this->headers as $header) {
            if (mb_stripos($header, 'Location:') === 0) {
                return trim(mb_substr($header, 9));
            }
        }

        return '';
    }

    public function cookie(string $name): ?string
    {
        foreach ($this->headers as $header) {
            if (mb_stripos($header, 'Set-Cookie:') !== 0) {
                continue;
            }

            $parts     = explode(';', trim(mb_substr($header, 11)));
            $nameValue = explode('=', trim($parts[0]), 2);

            if (trim($nameValue[0]) === $name) {
                return $nameValue[1] ?? '';
            }
        }

        return null;
    }
}
