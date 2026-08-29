<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * A generated document together with the profile it claims to implement.
 */
final class EInvoiceArtifact
{
    /**
     * @param string[] $validationErrors
     */
    public function __construct(
        private string $path,
        private EInvoiceProfile $profile,
        private array $validationErrors = []
    ) {}

    public function path(): string
    {
        return $this->path;
    }

    public function profile(): EInvoiceProfile
    {
        return $this->profile;
    }

    /**
     * @return string[]
     */
    public function validationErrors(): array
    {
        return $this->validationErrors;
    }

    public function isValid(): bool
    {
        return $this->validationErrors === [];
    }

    public function assertValid(): void
    {
        if ( ! $this->isValid()) {
            throw new RuntimeException('The generated e-invoice failed validation.');
        }
    }

    public function metadata(): array
    {
        return array_filter([
            'format'        => $this->profile->format(),
            'profile'       => $this->profile->code(),
            'syntax'        => $this->profile->syntax(),
            'mime_type'     => $this->profile->mimeType(),
            'document_type' => $this->profile->documentType(),
        ], static fn ($value): bool => $value !== null && $value !== '');
    }
}
