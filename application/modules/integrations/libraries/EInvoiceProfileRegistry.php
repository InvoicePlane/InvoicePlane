<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Explicit allow-list of e-invoice profiles supported by provider integrations.
 *
 * Profiles are intentionally registered in code. Merely placing a PHP generator
 * on disk must never make it eligible for automatic provider transmission.
 */
final class EInvoiceProfileRegistry
{
    /**
     * @var array<string, EInvoiceProfile>
     */
    private array $profiles = [];

    /**
     * @param EInvoiceProfile[] $profiles
     */
    public function __construct(array $profiles = [])
    {
        foreach ($profiles as $profile) {
            $this->register($profile);
        }
    }

    public function register(EInvoiceProfile $profile): void
    {
        if (isset($this->profiles[$profile->code()])) {
            throw new LogicException('Duplicate e-invoice profile code.');
        }

        $this->profiles[$profile->code()] = $profile;
    }

    public function get(string $code): EInvoiceProfile
    {
        if ( ! isset($this->profiles[$code])) {
            throw new OutOfBoundsException('Unsupported e-invoice profile.');
        }

        return $this->profiles[$code];
    }

    public function has(string $code): bool
    {
        return isset($this->profiles[$code]);
    }

    /**
     * @return array<string, EInvoiceProfile>
     */
    public function all(): array
    {
        return $this->profiles;
    }
}
