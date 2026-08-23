<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Immutable description of an e-invoice syntax and its national/business profile.
 */
final class EInvoiceProfile
{
    public function __construct(
        private string $code,
        private string $label,
        private string $countryCode,
        private string $generator,
        private string $format,
        private string $syntax,
        private string $mimeType,
        private string $extension,
        private bool $embedded,
        private string $xmlName,
        private array $options = [],
        private ?string $customizationId = null,
        private ?string $profileId = null,
        private ?string $documentType = null,
        private array $providers = []
    ) {
        if (preg_match('/^[A-Za-z0-9_-]+$/', $code) !== 1) {
            throw new InvalidArgumentException('Invalid e-invoice profile code.');
        }

        if (preg_match('/^[A-Za-z0-9_-]+$/', $generator) !== 1) {
            throw new InvalidArgumentException('Invalid e-invoice generator name.');
        }

        if (preg_match('/^[a-z0-9]+$/', $extension) !== 1) {
            throw new InvalidArgumentException('Invalid e-invoice file extension.');
        }
    }

    public function code(): string
    {
        return $this->code;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function countryCode(): string
    {
        return $this->countryCode;
    }

    public function generator(): string
    {
        return $this->generator;
    }

    public function format(): string
    {
        return $this->format;
    }

    public function syntax(): string
    {
        return $this->syntax;
    }

    public function mimeType(): string
    {
        return $this->mimeType;
    }

    public function extension(): string
    {
        return $this->extension;
    }

    public function embedded(): bool
    {
        return $this->embedded;
    }

    public function xmlName(): string
    {
        return $this->xmlName;
    }

    public function options(): array
    {
        return $this->options;
    }

    public function customizationId(): ?string
    {
        return $this->customizationId;
    }

    public function profileId(): ?string
    {
        return $this->profileId;
    }

    public function documentType(): ?string
    {
        return $this->documentType;
    }

    public function supportsProvider(string $provider): bool
    {
        return in_array($provider, $this->providers, true);
    }
}
