<?php

namespace Modules\Core\Testing;

/**
 * TestResponse - Wrapper for HTTP response in tests
 * 
 * Provides Laravel-style fluent assertions for testing HTTP responses
 */
class TestResponse
{
    public int $statusCode = 200;
    public string $content = '';
    public array $headers = [];
    public ?string $redirectUrl = null;
    public array $sessionErrors = [];

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    public function isOk(): bool
    {
        return $this->statusCode === 200;
    }

    public function isRedirect(): bool
    {
        return in_array($this->statusCode, [301, 302, 303, 307, 308]);
    }

    public function isClientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    public function isServerError(): bool
    {
        return $this->statusCode >= 500 && $this->statusCode < 600;
    }

    public function json(): array
    {
        $decoded = json_decode($this->content, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($decoded)) {
            throw new \UnexpectedValueException('JSON response is not an array or object.');
        }

        return $decoded;
    }

    /**
     * Assert response status is 200
     */
    public function assertOk(): self
    {
        \PHPUnit\Framework\Assert::assertEquals(200, $this->statusCode, 'Expected status 200 OK');
        return $this;
    }

    /**
     * Assert response status is 201
     */
    public function assertCreated(): self
    {
        \PHPUnit\Framework\Assert::assertEquals(201, $this->statusCode, 'Expected status 201 Created');
        return $this;
    }

    /**
     * Assert response status is 204
     */
    public function assertNoContent(): self
    {
        \PHPUnit\Framework\Assert::assertEquals(204, $this->statusCode, 'Expected status 204 No Content');
        return $this;
    }

    /**
     * Assert response is a redirect
     */
    public function assertRedirect(?string $uri = null): self
    {
        \PHPUnit\Framework\Assert::assertTrue(
            $this->isRedirect(),
            'Expected redirect status code (301, 302, 303, 307, 308)'
        );
        
        if ($uri !== null) {
            \PHPUnit\Framework\Assert::assertStringContainsString(
                $uri,
                $this->redirectUrl ?? '',
                "Expected redirect to [{$uri}]"
            );
        }
        
        return $this;
    }

    /**
     * Assert response status is 401
     */
    public function assertUnauthorized(): self
    {
        \PHPUnit\Framework\Assert::assertEquals(401, $this->statusCode, 'Expected status 401 Unauthorized');
        return $this;
    }

    /**
     * Assert response status is 403
     */
    public function assertForbidden(): self
    {
        \PHPUnit\Framework\Assert::assertEquals(403, $this->statusCode, 'Expected status 403 Forbidden');
        return $this;
    }

    /**
     * Assert response status is 404
     */
    public function assertNotFound(): self
    {
        \PHPUnit\Framework\Assert::assertEquals(404, $this->statusCode, 'Expected status 404 Not Found');
        return $this;
    }

    /**
     * Assert response status is 422
     */
    public function assertUnprocessable(): self
    {
        \PHPUnit\Framework\Assert::assertEquals(422, $this->statusCode, 'Expected status 422 Unprocessable Entity');
        return $this;
    }

    /**
     * Assert session has validation errors
     * 
     * @param array|string $keys Field names or ['field' => 'expected message']
     */
    public function assertSessionHasErrors($keys = []): self
    {
        if (empty($keys)) {
            \PHPUnit\Framework\Assert::assertNotEmpty(
                $this->sessionErrors,
                'Expected session to have validation errors'
            );
            return $this;
        }
        
        if (is_string($keys)) {
            $keys = [$keys];
        }
        
        foreach ($keys as $key => $value) {
            if (is_int($key)) {
                // Just check field exists: ['field_name']
                \PHPUnit\Framework\Assert::assertArrayHasKey(
                    $value,
                    $this->sessionErrors,
                    "Expected validation error for field [{$value}]"
                );
            } else {
                // Check field and message: ['field_name' => 'error message']
                \PHPUnit\Framework\Assert::assertArrayHasKey(
                    $key,
                    $this->sessionErrors,
                    "Expected validation error for field [{$key}]"
                );
                \PHPUnit\Framework\Assert::assertStringContainsString(
                    $value,
                    $this->sessionErrors[$key],
                    "Expected error message for field [{$key}] to contain [{$value}]"
                );
            }
        }
        
        return $this;
    }

    /**
     * Assert session does not have errors for given fields
     */
    public function assertSessionDoesntHaveErrors($keys = []): self
    {
        if (is_string($keys)) {
            $keys = [$keys];
        }
        
        foreach ($keys as $key) {
            \PHPUnit\Framework\Assert::assertArrayNotHasKey(
                $key,
                $this->sessionErrors,
                "Expected no validation error for field [{$key}]"
            );
        }
        
        return $this;
    }

    /**
     * Assert response contains text
     */
    public function assertSee(string $value): self
    {
        \PHPUnit\Framework\Assert::assertStringContainsString(
            $value,
            $this->content,
            "Expected response to contain [{$value}]"
        );
        return $this;
    }

    /**
     * Assert response does not contain text
     */
    public function assertDontSee(string $value): self
    {
        \PHPUnit\Framework\Assert::assertStringNotContainsString(
            $value,
            $this->content,
            "Expected response not to contain [{$value}]"
        );
        return $this;
    }

    /**
     * Assert response has specific header value
     */
    public function assertHeader(string $name, string $value = null): self
    {
        \PHPUnit\Framework\Assert::assertArrayHasKey(
            $name,
            $this->headers,
            "Expected header [{$name}] to be present"
        );
        
        if ($value !== null) {
            \PHPUnit\Framework\Assert::assertStringContainsString(
                $value,
                $this->headers[$name],
                "Expected header [{$name}] to contain [{$value}]"
            );
        }
        
        return $this;
    }

    /**
     * Assert response status code matches
     */
    public function assertStatus(int $status): self
    {
        \PHPUnit\Framework\Assert::assertEquals(
            $status,
            $this->statusCode,
            "Expected status {$status} but got {$this->statusCode}"
        );
        return $this;
    }
}

