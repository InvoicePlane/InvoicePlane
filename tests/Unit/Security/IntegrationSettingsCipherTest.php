<?php

namespace Tests\Unit\Security;

use IntegrationSettingsCipher;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[Group('security')]
class IntegrationSettingsCipherTest extends TestCase
{
    private string $key;

    private bool $hadEncryptionKey = false;

    private mixed $previousEncryptionKey = null;

    protected function setUp(): void
    {
        parent::setUp();

        require_once dirname(__DIR__, 3) . '/application/modules/integrations/libraries/IntegrationSettingsCipher.php';
        require_once dirname(__DIR__, 3) . '/bootstrap/kernel.php';

        $this->key                   = random_bytes(32);
        $this->hadEncryptionKey      = array_key_exists('ENCRYPTION_KEY', $_ENV);
        $this->previousEncryptionKey = $_ENV['ENCRYPTION_KEY'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->hadEncryptionKey) {
            $_ENV['ENCRYPTION_KEY'] = $this->previousEncryptionKey;
        } else {
            unset($_ENV['ENCRYPTION_KEY']);
        }

        parent::tearDown();
    }

    #[Test]
    public function it_loads_encryption_key_from_env_not_getenv(): void
    {
        /**
         * REGRESSION TEST: IntegrationSettingsCipher must load the encryption
         * key from env() / $_ENV, never from getenv() (which returns NULL when
         * Dotenv populates only $_ENV, not the process environment).
         */
        $_ENV['ENCRYPTION_KEY'] = 'base64:' . base64_encode($this->key);

        $cipher = new IntegrationSettingsCipher();

        $settings  = ['api_key' => 'secret_123', 'endpoint' => 'https://api.example.com'];
        $encrypted = $cipher->encrypt($settings, 'test_provider');

        $decrypted = $cipher->decrypt($encrypted, 'test_provider');

        self::assertSame($settings, $decrypted);
    }

    #[Test]
    public function it_rejects_missing_encryption_key(): void
    {
        /**
         * SECURITY: Missing ENCRYPTION_KEY should fail loudly, not silently
         * encrypt with empty key.
         */
        unset($_ENV['ENCRYPTION_KEY']);

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('ENCRYPTION_KEY is required');

        $cipher = new IntegrationSettingsCipher();
        $cipher->encrypt(['test' => 'value'], 'provider');
    }

    #[Test]
    public function it_accepts_injected_encryption_key(): void
    {
        /**
         * TESTABILITY: Constructor injection allows tests to provide their own key
         * without relying on $_ENV configuration.
         */
        unset($_ENV['ENCRYPTION_KEY']);

        $injectedKey = 'base64:' . base64_encode($this->key);
        $cipher      = new IntegrationSettingsCipher($injectedKey);

        $settings  = ['config' => 'value'];
        $encrypted = $cipher->encrypt($settings, 'injected_provider');

        $decrypted = $cipher->decrypt($encrypted, 'injected_provider');

        self::assertSame($settings, $decrypted);
    }

    #[Test]
    public function it_detects_encryption_format(): void
    {
        /**
         * BACKWARDS COMPATIBILITY: Cipher should recognize its own format
         * (prefixed with 'ipenc:v1:') and handle legacy formats.
         */
        $_ENV['ENCRYPTION_KEY'] = 'base64:' . base64_encode($this->key);

        $cipher = new IntegrationSettingsCipher();

        $settings  = ['key' => 'value'];
        $encrypted = $cipher->encrypt($settings, 'format_test');

        self::assertTrue($cipher->isEncrypted($encrypted));
        self::assertFalse($cipher->isEncrypted('not encrypted'));
        self::assertFalse($cipher->isEncrypted(''));
        self::assertFalse($cipher->isEncrypted(null));
    }

    #[Test]
    public function it_preserves_complex_provider_settings_structures(): void
    {
        /**
         * REAL-WORLD SCENARIO: Integration settings can have complex nested
         * structures. Encryption/decryption must preserve them exactly.
         */
        $_ENV['ENCRYPTION_KEY'] = 'base64:' . base64_encode($this->key);

        $cipher = new IntegrationSettingsCipher();

        $settings = [
            'api_key'         => 'sk_live_123456',
            'webhook_secret'  => 'whsec_abc123',
            'nested'          => [
                'deep'   => [
                    'config' => 'value',
                ],
                'list'   => ['item1', 'item2'],
            ],
            'numeric'         => 123,
            'float'           => 45.67,
            'boolean'         => true,
            'null_value'      => null,
        ];

        $encrypted = $cipher->encrypt($settings, 'complex_provider');
        $decrypted = $cipher->decrypt($encrypted, 'complex_provider');

        self::assertSame($settings, $decrypted);
    }

    #[Test]
    public function it_rejects_malformed_encrypted_data(): void
    {
        /**
         * SECURITY: Corrupted or tampered encrypted data should throw,
         * not return partial results.
         */
        $_ENV['ENCRYPTION_KEY'] = 'base64:' . base64_encode($this->key);

        $cipher = new IntegrationSettingsCipher();

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('malformed');

        // Malformed ciphertext (incomplete)
        $cipher->decrypt('ipenc:v1:aGVsbG8=', 'test');
    }

    #[Test]
    public function it_rejects_empty_configured_key(): void
    {
        /**
         * SECURITY: Empty string passed as key should fail, not silently
         * encrypt with empty secret.
         */
        unset($_ENV['ENCRYPTION_KEY']);

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('ENCRYPTION_KEY is required');

        new IntegrationSettingsCipher('');
    }

    #[Test]
    public function it_validates_base64_key_format(): void
    {
        /**
         * SECURITY: Invalid base64 keys should fail with clear error.
         */
        unset($_ENV['ENCRYPTION_KEY']);

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('invalid base64');

        $cipher = new IntegrationSettingsCipher('base64:not-valid-base64!!!');
    }

    #[Test]
    public function it_never_silently_uses_empty_key_from_fallback(): void
    {
        /**
         * REGRESSION TEST: The original bug was that if $_ENV['ENCRYPTION_KEY']
         * was not set (but ipconfig.php had it), getenv() would return NULL,
         * and the code would silently use empty key.
         *
         * Now: we only check $_ENV directly and env(), never getenv().
         * If key is missing, we throw immediately.
         */
        unset($_ENV['ENCRYPTION_KEY']);

        self::expectException(RuntimeException::class);

        $cipher = new IntegrationSettingsCipher();
        $cipher->encrypt(['test' => 'value'], 'provider');
    }
}
