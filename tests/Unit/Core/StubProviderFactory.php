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

class StubProviderFactory
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
