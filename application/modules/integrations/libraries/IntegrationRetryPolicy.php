<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Bounded retries for idempotent provider read operations only.
 */
final class IntegrationRetryPolicy
{
    private int $attempts = 0;

    private Closure $sleeper;

    public function __construct(
        private int $maximumAttempts = 3,
        ?callable $sleeper = null
    ) {
        if ($maximumAttempts < 1 || $maximumAttempts > 5) {
            throw new InvalidArgumentException('Retry attempts must be between 1 and 5.');
        }

        $this->sleeper = $sleeper !== null
            ? Closure::fromCallable($sleeper)
            : static fn (int $microseconds) => usleep($microseconds);
    }

    /**
     * @return array{response: array, attempts: int}
     */
    public function execute(callable $operation): array
    {
        $this->attempts = 0;
        $lastError      = null;

        for ($attempt = 1; $attempt <= $this->maximumAttempts; $attempt++) {
            $this->attempts = $attempt;
            try {
                $response = $operation();
                if ( ! is_array($response)) {
                    throw new RuntimeException('Provider returned an invalid response type.');
                }

                if (($response['success'] ?? false) === true) {
                    return ['response' => $response, 'attempts' => $attempt];
                }

                $lastError = new RuntimeException(
                    IntegrationPayloadSanitizer::text($response['message'] ?? null)
                        ?? 'Provider read operation failed.'
                );

                if ( ! $this->isRetryableHttpCode((int) ($response['http_code'] ?? 0))) {
                    throw $lastError;
                }
            } catch (Throwable $e) {
                $lastError = $e;
                if ( ! $this->isRetryableException($e)) {
                    throw $e;
                }
            }

            if ($attempt < $this->maximumAttempts) {
                ($this->sleeper)($this->delayMicroseconds($attempt));
            }
        }

        throw $lastError ?? new RuntimeException('Provider read operation failed after retries.');
    }

    public function lastAttempts(): int
    {
        return $this->attempts;
    }

    private function isRetryableHttpCode(int $httpCode): bool
    {
        return $httpCode === 0 || $httpCode === 429 || $httpCode >= 500 && $httpCode <= 599;
    }

    private function isRetryableException(Throwable $error): bool
    {
        $message = mb_strtolower($error->getMessage());

        return str_contains($message, 'connection')
            || str_contains($message, 'timeout')
            || str_contains($message, 'temporar')
            || str_contains($message, 'rate limit')
            || str_contains($message, 'after retries');
    }

    private function delayMicroseconds(int $failedAttempt): int
    {
        return min(4_000_000, 250_000 * (2 ** ($failedAttempt - 1)));
    }
}
