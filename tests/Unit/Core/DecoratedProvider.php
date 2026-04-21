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

class DecoratedProvider implements StubIntegrationProviderInterface
{
    public function __construct(private readonly StubIntegrationProviderInterface $inner) {}

    public function validateParticipant(string $participantId): bool
    {
        try {
            return $this->inner->validateParticipant($participantId);
        } catch (Throwable) {
            return false;
        }
    }

    public function sendInvoice(array $payload): bool
    {
        try {
            return $this->inner->sendInvoice($payload);
        } catch (Throwable) {
            return false;
        }
    }
}
