<?php

defined('BASEPATH') || exit('No direct script access allowed');

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

    private const PREFIX = "IPARCHIVE\0v1\0";

    private const AAD = 'invoiceplane:archive-document:v1';

    public function __construct(private ?string $configuredKey = null) {}

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

        $nonce = random_bytes(self::NONCE_BYTES);
        $tag = '';
        $ciphertext = openssl_encrypt(
            $plaintext,
            self::CIPHER,
            $this->key(),
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            self::AAD,
            self::TAG_BYTES
        );
        if ($ciphertext === false || strlen($tag) !== self::TAG_BYTES) {
            throw new RuntimeException('The archive document could not be encrypted.');
        }

        $payload = self::PREFIX . $nonce . $tag . $ciphertext;
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
            'encryption_key_version' => 'v1',
        ];
    }

    public function verify(string $relativePath, string $expectedPlaintextSha256): bool
    {
        return hash_equals(strtolower($expectedPlaintextSha256), $this->plaintextSha256($relativePath));
    }

    public function plaintextSha256(string $relativePath): string
    {
        return hash('sha256', $this->read($relativePath));
    }

    public function read(string $relativePath): string
    {
        $path = $this->archivePath($relativePath);
        $payload = file_get_contents($path);
        if ($payload === false || ! str_starts_with($payload, self::PREFIX)) {
            throw new RuntimeException('The encrypted archive document is malformed.');
        }

        $offset = strlen(self::PREFIX);
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
            $this->key(),
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            self::AAD
        );
        if ($plaintext === false) {
            throw new RuntimeException('The encrypted archive document failed authentication.');
        }

        return $plaintext;
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

    private function key(): string
    {
        $configured = $this->configuredKey;
        if ($configured === null) {
            $configured = env('ENCRYPTION_KEY') ?: ($_ENV['ENCRYPTION_KEY'] ?? null);
        }
        if ( ! is_string($configured) || $configured === '') {
            throw new RuntimeException('ENCRYPTION_KEY is required for encrypted archive storage.');
        }
        if (str_starts_with($configured, 'base64:')) {
            $configured = base64_decode(substr($configured, 7), true);
            if ($configured === false || $configured === '') {
                throw new RuntimeException('ENCRYPTION_KEY contains invalid base64 data.');
            }
        }

        return hash('sha256', $configured, true);
    }
}
