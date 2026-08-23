<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Authenticated encryption for provider settings stored in the database.
 */
final class IntegrationSettingsCipher
{
    private const AAD_PREFIX = 'invoiceplane:integration-settings:v1:';

    private const CIPHER = 'aes-256-gcm';

    private const NONCE_BYTES = 12;

    private const PREFIX = 'ipenc:v1:';

    private const TAG_BYTES = 16;

    public function __construct(private ?string $configuredKey = null) {}

    public function encrypt(array $settings, string $providerCode): string
    {
        $plaintext = json_encode($settings, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
        $nonce     = random_bytes(self::NONCE_BYTES);
        $tag       = '';
        $encrypted = openssl_encrypt(
            $plaintext,
            self::CIPHER,
            $this->key(),
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            self::AAD_PREFIX . $providerCode,
            self::TAG_BYTES
        );

        if ($encrypted === false || mb_strlen($tag, '8bit') !== self::TAG_BYTES) {
            throw new RuntimeException('Unable to encrypt provider settings.');
        }

        return self::PREFIX . base64_encode($nonce . $tag . $encrypted);
    }

    public function decrypt(?string $stored, string $providerCode): array
    {
        if ($stored === null || $stored === '') {
            return [];
        }

        if ( ! str_starts_with($stored, self::PREFIX)) {
            return $this->decodeJson($stored);
        }

        $encoded = mb_substr($stored, mb_strlen(self::PREFIX));
        $payload = base64_decode($encoded, true);
        if ($payload === false || mb_strlen($payload, '8bit') <= self::NONCE_BYTES + self::TAG_BYTES) {
            throw new RuntimeException('Encrypted provider settings are malformed.');
        }

        $nonce      = mb_substr($payload, 0, self::NONCE_BYTES, '8bit');
        $tag        = mb_substr($payload, self::NONCE_BYTES, self::TAG_BYTES, '8bit');
        $ciphertext = mb_substr($payload, self::NONCE_BYTES + self::TAG_BYTES, null, '8bit');
        $plaintext  = openssl_decrypt(
            $ciphertext,
            self::CIPHER,
            $this->key(),
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            self::AAD_PREFIX . $providerCode
        );

        if ($plaintext === false) {
            throw new RuntimeException('Unable to decrypt provider settings. Check ENCRYPTION_KEY.');
        }

        return $this->decodeJson($plaintext);
    }

    public function isEncrypted(?string $stored): bool
    {
        return is_string($stored) && str_starts_with($stored, self::PREFIX);
    }

    private function decodeJson(string $json): array
    {
        try {
            $object   = json_decode($json, false, 32, JSON_THROW_ON_ERROR);
            $settings = json_decode($json, true, 32, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException('Stored provider settings are invalid.', 0, $e);
        }

        if ( ! is_object($object) || ! is_array($settings)) {
            throw new RuntimeException('Stored provider settings must be a JSON object.');
        }

        return $settings;
    }

    private function key(): string
    {
        $configured = $this->configuredKey;
        if ($configured === null && function_exists('env')) {
            $configured = env('ENCRYPTION_KEY');
        }
        if ($configured === null || $configured === '') {
            $environmentKey = $_ENV['ENCRYPTION_KEY'] ?? getenv('ENCRYPTION_KEY');
            $configured     = is_string($environmentKey) ? $environmentKey : null;
        }
        if ( ! is_string($configured) || $configured === '') {
            throw new RuntimeException('ENCRYPTION_KEY is required to protect provider settings.');
        }

        if (str_starts_with($configured, 'base64:')) {
            $decoded = base64_decode(mb_substr($configured, 7), true);
            if ($decoded === false || $decoded === '') {
                throw new RuntimeException('ENCRYPTION_KEY contains invalid base64 data.');
            }
            $configured = $decoded;
        }

        return hash('sha256', $configured, true);
    }
}
