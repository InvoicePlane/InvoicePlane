<?php

namespace Tests\Integration\Support;

use JsonException;
use PHPUnit\Framework\Assert;

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

    // --- Fluent assertion helpers ---

    public function assertOk(): static
    {
        Assert::assertSame(200, $this->statusCode, sprintf('Response status [%d] is not 200 OK.', $this->statusCode));

        return $this;
    }

    public function assertStatus(int $expected): static
    {
        Assert::assertSame($expected, $this->statusCode, sprintf('Expected HTTP status [%d], got [%d].', $expected, $this->statusCode));

        return $this;
    }

    public function assertRedirect(?string $uri = null): static
    {
        Assert::assertTrue($this->isRedirect(), sprintf('Response status [%d] is not a redirect.', $this->statusCode));

        if ($uri !== null) {
            Assert::assertSame($uri, $this->redirectUrl(), sprintf('Expected redirect to [%s], got [%s].', $uri, $this->redirectUrl() ?? '(none)'));
        }

        return $this;
    }

    public function assertSee(string $value): static
    {
        Assert::assertStringContainsString($value, $this->body, sprintf('Response body does not contain [%s].', $value));

        return $this;
    }

    public function assertDontSee(string $value): static
    {
        Assert::assertStringNotContainsString($value, $this->body, sprintf('Response body unexpectedly contains [%s].', $value));

        return $this;
    }

    /**
     * CI3 renders views directly to HTML; individual view names cannot be
     * introspected from the response object. This method is a no-op stub
     * kept for API compatibility with tests written in a Laravel style.
     */
    public function assertViewIs(string $view): static
    {
        return $this;
    }

    /**
     * CI3 renders views directly to HTML; view-variable bindings cannot be
     * introspected from the response object. This method is a no-op stub
     * kept for API compatibility with tests written in a Laravel style.
     */
    public function assertViewHas(string $key, mixed $value = null): static
    {
        return $this;
    }

    /**
     * CI3 renders views directly to HTML; view-variable bindings cannot be
     * introspected from the response object. Always returns null.
     */
    public function viewData(string $key): mixed
    {
        return null;
    }
}
