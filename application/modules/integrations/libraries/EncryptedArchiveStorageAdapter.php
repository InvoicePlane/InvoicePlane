<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once APPPATH . 'modules/integrations/libraries/ArchiveEncryptionKeyring.php';

/**
 * Authenticated encrypted storage for archive documents.
 *
 * Files use AES-256-GCM with a versioned binary envelope. The plaintext
 * digest remains the document identity; the encrypted file has its own
 * storage digest for tamper detection at rest.
 */
final class EncryptedArchiveStorageAdapter
{
    private const CIPHER = 'aes-256-gcm';

    private const NONCE_BYTES = 12;

    private const TAG_BYTES = 16;

    private const LEGACY_PREFIX = "IPARCHIVE\0v1\0";

    private const PREFIX = "IPARCHIVE\0v2\0";

    private const AAD = 'invoiceplane:archive-document:v1';

    private ArchiveEncryptionKeyring $keyring;

    public function __construct(
        ?string $configuredKey = null,
        ?string $activeVersion = null,
        ?ArchiveEncryptionKeyring $keyring = null
    ) {
        $this->keyring = $keyring ?? ($configuredKey === null
            ? new ArchiveEncryptionKeyring([], $activeVersion)
            : new ArchiveEncryptionKeyring(['v1' => $configuredKey], 'v1'));
    }

    /**
     * @return array{path: string, plaintext_sha256: string, storage_sha256: string, file_size: int, storage_file_size: int, encryption_status: string, encryption_key_version: string}
     */
    public function store(string $sourcePath, string $destinationRelativePath): array
    {
        if ( ! is_file($sourcePath) || ! is_readable($sourcePath)) {
            throw new RuntimeException('The source archive document is unavailable.');
        }

        $destinationPath = $this->destinationPath($destinationRelativePath);
        if (file_exists($destinationPath)) {
            throw new RuntimeException('The encrypted archive destination already exists.');
        }

        $plaintext = file_get_contents($sourcePath);
        if ($plaintext === false) {
            throw new RuntimeException('The source archive document could not be read.');
        }

        return $this->storePlaintext($plaintext, $destinationRelativePath);
    }

    public function rotate(string $relativePath, string $currentKeyVersion, string $destinationRelativePath): array
    {
        return $this->storePlaintext($this->read($relativePath, $currentKeyVersion), $destinationRelativePath);
    }

    public function activeKeyVersion(): string
    {
        return $this->keyring->activeVersion();
    }

    public function verify(string $relativePath, string $expectedPlaintextSha256, ?string $keyVersion = null): bool
    {
        return hash_equals(strtolower($expectedPlaintextSha256), $this->plaintextSha256($relativePath, $keyVersion));
    }

    public function plaintextSha256(string $relativePath, ?string $keyVersion = null): string
    {
        return hash('sha256', $this->read($relativePath, $keyVersion));
    }

    public function read(string $relativePath, ?string $keyVersion = null): string
    {
        $path = $this->archivePath($relativePath);
        $payload = file_get_contents($path);
        if ($payload === false) {
            throw new RuntimeException('The encrypted archive document is malformed.');
        }

        $prefix = null;
        $embeddedKeyVersion = null;
        if (str_starts_with($payload, self::PREFIX)) {
            $prefix = self::PREFIX;
            $offset = strlen($prefix);
            $versionLength = ord($payload[$offset] ?? "\0");
            $offset++;
            if ($versionLength < 1 || $versionLength > 20) {
                throw new RuntimeException('The encrypted archive key version is malformed.');
            }
            $embeddedKeyVersion = substr($payload, $offset, $versionLength);
            $offset += $versionLength;
        } elseif (str_starts_with($payload, self::LEGACY_PREFIX)) {
            $prefix = self::LEGACY_PREFIX;
            $offset = strlen($prefix);
            $embeddedKeyVersion = $keyVersion ?? 'v1';
        } else {
            throw new RuntimeException('The encrypted archive document is malformed.');
        }

        $keyVersion = $embeddedKeyVersion;
        if (strlen($payload) <= $offset + self::NONCE_BYTES + self::TAG_BYTES) {
            throw new RuntimeException('The encrypted archive document is truncated.');
        }

        $nonce = substr($payload, $offset, self::NONCE_BYTES);
        $offset += self::NONCE_BYTES;
        $tag = substr($payload, $offset, self::TAG_BYTES);
        $ciphertext = substr($payload, $offset + self::TAG_BYTES);
        $plaintext = openssl_decrypt(
            $ciphertext,
            self::CIPHER,
            $this->keyring->keyFor($keyVersion),
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            $this->aad($prefix === self::PREFIX ? $keyVersion : null)
        );
        if ($plaintext === false) {
            throw new RuntimeException('The encrypted archive document failed authentication.');
        }

        return $plaintext;
    }

    /**
     * @return array{path: string, plaintext_sha256: string, storage_sha256: string, file_size: int, storage_file_size: int, encryption_status: string, encryption_key_version: string}
     */
    private function storePlaintext(string $plaintext, string $destinationRelativePath): array
    {
        $destinationPath = $this->destinationPath($destinationRelativePath);
        if (file_exists($destinationPath)) {
            throw new RuntimeException('The encrypted archive destination already exists.');
        }

        $keyVersion = $this->keyring->activeVersion();
        $nonce = random_bytes(self::NONCE_BYTES);
        $tag = '';
        $ciphertext = openssl_encrypt(
            $plaintext,
            self::CIPHER,
            $this->keyring->keyFor($keyVersion),
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            $this->aad($keyVersion),
            self::TAG_BYTES
        );
        if ($ciphertext === false || strlen($tag) !== self::TAG_BYTES) {
            throw new RuntimeException('The archive document could not be encrypted.');
        }

        $payload = self::PREFIX . chr(strlen($keyVersion)) . $keyVersion . $nonce . $tag . $ciphertext;
        if (file_put_contents($destinationPath, $payload, LOCK_EX) !== strlen($payload)) {
            if (is_file($destinationPath)) {
                unlink($destinationPath);
            }

            throw new RuntimeException('The encrypted archive document could not be stored.');
        }

        return [
            'path' => $destinationRelativePath,
            'plaintext_sha256' => hash('sha256', $plaintext),
            'storage_sha256' => hash('sha256', $payload),
            'file_size' => strlen($plaintext),
            'storage_file_size' => strlen($payload),
            'encryption_status' => 'encrypted',
            'encryption_key_version' => $keyVersion,
        ];
    }

    private function archivePath(string $relativePath): string
    {
        $this->loadFileSecurityHelper();
        if ( ! validate_safe_filename($relativePath)['valid']) {
            throw new RuntimeException('The encrypted archive path is invalid.');
        }

        $path = rtrim(UPLOADS_ARCHIVE_FOLDER, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        if ( ! is_file($path) || ! validate_file_in_directory($path, UPLOADS_ARCHIVE_FOLDER)) {
            throw new RuntimeException('The encrypted archive document is unavailable.');
        }

        return $path;
    }

    private function destinationPath(string $relativePath): string
    {
        $this->loadFileSecurityHelper();
        if ( ! validate_safe_filename($relativePath)['valid']) {
            throw new InvalidArgumentException('The encrypted archive path is invalid.');
        }

        $path = rtrim(UPLOADS_ARCHIVE_FOLDER, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        $directory = dirname($path);
        if ( ! is_dir($directory) && ! mkdir($directory, 0750, true) && ! is_dir($directory)) {
            throw new RuntimeException('The encrypted archive directory could not be created.');
        }
        if ( ! validate_file_in_directory($directory, UPLOADS_ARCHIVE_FOLDER)) {
            throw new RuntimeException('The encrypted archive destination is invalid.');
        }

        return $path;
    }

    private function loadFileSecurityHelper(): void
    {
        require_once APPPATH . 'helpers/file_security_helper.php';
    }

    private function aad(?string $keyVersion): string
    {
        return self::AAD . ($keyVersion === null ? '' : ':' . $keyVersion);
    }
}
