<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Versioned keyring for encrypted archive documents.
 *
 * Keys are configuration secrets only. The database stores the key version,
 * never key material. ARCHIVE_ENCRYPTION_KEYS is a JSON object such as
 * {"v1":"base64:...","v2":"base64:..."}.
 */
final class ArchiveEncryptionKeyring
{
    /** @var array<string, string> */
    private array $keys;

    private string $activeVersion;

    /**
     * @param array<string, string> $keys
     */
    public function __construct(array $keys = [], ?string $activeVersion = null)
    {
        if ($keys === []) {
            $keys = $this->configuredKeys();
        }
        $this->keys = [];
        foreach ($keys as $version => $key) {
            $version = trim((string) $version);
            if (preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]{0,19}$/', $version) !== 1) {
                throw new InvalidArgumentException('Archive encryption key version is invalid.');
            }
            if ( ! is_string($key) || trim($key) === '') {
                throw new InvalidArgumentException('Archive encryption key material is missing.');
            }
            $this->keys[$version] = $this->decodeKey($key);
        }
        if ($this->keys === []) {
            throw new RuntimeException('At least one archive encryption key is required.');
        }

        $this->activeVersion = trim((string) ($activeVersion ?? $this->configuredActiveVersion()));
        if ( ! isset($this->keys[$this->activeVersion])) {
            throw new RuntimeException('The active archive encryption key is not available.');
        }
    }

    public function activeVersion(): string
    {
        return $this->activeVersion;
    }

    public function keyFor(string $version): string
    {
        if ( ! isset($this->keys[$version])) {
            throw new RuntimeException('The requested archive encryption key version is unavailable.');
        }

        return $this->keys[$version];
    }

    /** @return array<int, string> */
    public function versions(): array
    {
        return array_keys($this->keys);
    }

    /** @return array<string, string> */
    private function configuredKeys(): array
    {
        $configured = env('ARCHIVE_ENCRYPTION_KEYS') ?: ($_ENV['ARCHIVE_ENCRYPTION_KEYS'] ?? null);
        if (is_string($configured) && trim($configured) !== '') {
            try {
                $keys = json_decode($configured, true, 8, JSON_THROW_ON_ERROR);
            } catch (JsonException $exception) {
                throw new RuntimeException('ARCHIVE_ENCRYPTION_KEYS must contain valid JSON.', 0, $exception);
            }
            if ( ! is_array($keys)) {
                throw new RuntimeException('ARCHIVE_ENCRYPTION_KEYS must be a JSON object.');
            }

            return $keys;
        }

        $legacy = env('ENCRYPTION_KEY') ?: ($_ENV['ENCRYPTION_KEY'] ?? null);

        return is_string($legacy) && $legacy !== '' ? ['v1' => $legacy] : [];
    }

    private function configuredActiveVersion(): string
    {
        $configured = env('ARCHIVE_ENCRYPTION_ACTIVE_KEY_VERSION')
            ?: ($_ENV['ARCHIVE_ENCRYPTION_ACTIVE_KEY_VERSION'] ?? null);

        return is_string($configured) && trim($configured) !== '' ? trim($configured) : 'v1';
    }

    private function decodeKey(string $key): string
    {
        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7), true);
            if ($key === false || $key === '') {
                throw new RuntimeException('Archive encryption key contains invalid base64 data.');
            }
        }

        return hash('sha256', $key, true);
    }
}
