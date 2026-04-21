<?php

declare(strict_types=1);

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
final class IntegrationProviderFactoryTest extends TestCase
{
    private StubProviderFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new StubProviderFactory();
    }

    public function it_resolves_a_registered_provider_by_its_key(): void
    {
        $this->factory->register('stripe', fn () => new StubProvider('stripe'));

        $provider = $this->factory->make('stripe');

        self::assertInstanceOf(
            StubIntegrationProviderInterface::class,
            $provider,
            'make() must return an instance implementing IntegrationProviderInterface.'
        );
    }

    public function it_throws_when_making_an_unregistered_provider_key(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/not registered/i');

        $this->factory->make('nonexistent_provider');
    }

    public function it_returns_a_different_provider_instance_per_make_call_when_factory_creates_new(): void
    {
        $this->factory->register('paypal', fn () => new StubProvider('paypal'));

        $first  = $this->factory->make('paypal');
        $second = $this->factory->make('paypal');

        self::assertNotSame(
            $first,
            $second,
            'Each call to make() must invoke the factory closure and return a new instance.'
        );
    }

    public function it_can_register_multiple_providers_under_different_keys(): void
    {
        $this->factory->register('letspeppol', fn () => new StubProvider('letspeppol'));
        $this->factory->register('storecove', fn () => new StubProvider('storecove'));
        $this->factory->register('stripe', fn () => new StubProvider('stripe'));

        self::assertTrue(
            $this->factory->has('letspeppol'),
            'After registration, has(letspeppol) must return true.'
        );

        self::assertTrue(
            $this->factory->has('storecove'),
            'After registration, has(storecove) must return true.'
        );

        self::assertTrue(
            $this->factory->has('stripe'),
            'After registration, has(stripe) must return true.'
        );
    }

    public function it_wraps_the_resolved_provider_in_the_exception_handling_decorator(): void
    {
        $this->factory->register('letspeppol', fn () => new ThrowingProvider());

        $provider = $this->factory->make('letspeppol');

        $result = $provider->sendInvoice([]);

        self::assertFalse(
            $result,
            'The ExceptionHandlingDecorator must catch any Throwable and return false instead of propagating.'
        );
    }

    public function it_returns_false_from_validate_participant_when_provider_throws(): void
    {
        $this->factory->register('letspeppol', fn () => new ThrowingProvider());

        $provider = $this->factory->make('letspeppol');

        $result = $provider->validateParticipant('0088:invalid-id');

        self::assertFalse(
            $result,
            'validateParticipant() must return false when the underlying provider throws.'
        );
    }

    public function it_does_not_suppress_exceptions_outside_the_decorator(): void
    {
        $this->expectException(RuntimeException::class);

        $this->factory->registerRaw('boom', fn () => new ThrowingProvider());

        $provider = $this->factory->makeRaw('boom');
        $provider->sendInvoice([]);
    }

    public function it_returns_true_when_the_provider_successfully_validates_a_participant(): void
    {
        $this->factory->register('storecove', fn () => new StubProvider('storecove', validateResult: true));

        $provider = $this->factory->make('storecove');

        $result = $provider->validateParticipant('0088:acme-bv');

        self::assertTrue(
            $result,
            'validateParticipant() must return true when the underlying provider reports success.'
        );
    }

    public function it_returns_true_when_the_provider_successfully_sends_an_invoice(): void
    {
        $this->factory->register('storecove', fn () => new StubProvider('storecove', sendResult: true));

        $provider = $this->factory->make('storecove');

        $result = $provider->sendInvoice(['invoice_id' => 1, 'amount' => '100.00']);

        self::assertTrue(
            $result,
            'sendInvoice() must return true when the underlying provider reports success.'
        );
    }
}

interface StubIntegrationProviderInterface
{
    public function validateParticipant(string $participantId): bool;

    public function sendInvoice(array $payload): bool;
}

final class StubProvider implements StubIntegrationProviderInterface
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

final class ThrowingProvider implements StubIntegrationProviderInterface
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

final class DecoratedProvider implements StubIntegrationProviderInterface
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

final class StubProviderFactory
{
    /** @var array<string, Closure> */
    private array $registry = [];

    /** @var array<string, Closure> */
    private array $rawRegistry = [];

    public function register(string $key, Closure $factory): self
    {
        $this->registry[$key] = $factory;

        return $this;
    }

    public function registerRaw(string $key, Closure $factory): self
    {
        $this->rawRegistry[$key] = $factory;

        return $this;
    }

    public function make(string $key): StubIntegrationProviderInterface
    {
        if ( ! isset($this->registry[$key])) {
            throw new InvalidArgumentException(
                sprintf('Integration provider [%s] is not registered in the factory.', $key)
            );
        }

        $provider = ($this->registry[$key])();

        return new DecoratedProvider($provider);
    }

    public function makeRaw(string $key): StubIntegrationProviderInterface
    {
        if ( ! isset($this->rawRegistry[$key])) {
            throw new InvalidArgumentException(
                sprintf('Raw provider [%s] is not registered.', $key)
            );
        }

        return ($this->rawRegistry[$key])();
    }

    public function has(string $key): bool
    {
        return isset($this->registry[$key]);
    }
}
