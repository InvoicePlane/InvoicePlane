<?php

namespace Tests\Unit\Core;

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

class ThrowingProvider implements StubIntegrationProviderInterface
{
    public function validateParticipant(string $participantId): bool
    {
        throw new RuntimeException('Provider network failure during validateParticipant.');
    }

    public function sendInvoice(array $payload): bool
    {
        throw new RuntimeException('Provider network failure during sendInvoice.');
    }
}
