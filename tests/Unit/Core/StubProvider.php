<?php

namespace Tests\Unit\Providers;

use Closure;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;

/**
 * Unit tests for IntegrationProviderFactory's registration, resolution,
 * and ExceptionHandlingDecorator wrapping.
 *
 * The factory is tested in isolation using inline stubs so no CI3
 * bootstrap or database is required.
 *
 * @group unit
 * @group providers
 */

class StubProvider implements StubIntegrationProviderInterface
{
    public function __construct(
        private readonly string $key,
        private readonly bool $validateResult = true,
        private readonly bool $sendResult = true,
    ) {}

    public function validateParticipant(string $participantId): bool
    {
        return $this->validateResult;
    }

    public function sendInvoice(array $payload): bool
    {
        return $this->sendResult;
    }
}
