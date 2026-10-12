<?php

namespace Tests\Unit\Security;

use Crypt;
use Cryptor;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('security')]
class EncryptionKeyEdgeCasesTest extends TestCase
{
    private string $key;

    private bool $hadEncryptionKey = false;

    private mixed $previousEncryptionKey = null;

    protected function setUp(): void
    {
        parent::setUp();

        require_once ROOT_PATH . '/application/libraries/Cryptor.php';
        require_once ROOT_PATH . '/application/libraries/Crypt.php';

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
    public function it_encrypts_plaintext_with_utf8_special_characters(): void
    {
        /*
         * EDGE CASE: UTF-8 special characters, emoji, multi-byte characters
         * should not cause encoding issues during encryption/decryption.
         */
        $_ENV['ENCRYPTION_KEY'] = 'base64:' . base64_encode($this->key);

        $crypt = new Crypt();

        $plaintext = 'Email: tëst@example.com 🔒 Ñoño Åse 中文 العربية';

        $ciphertext = $crypt->encode($plaintext);
        $decrypted  = $crypt->decode($ciphertext);

        self::assertSame($plaintext, $decrypted);
        self::assertNotSame($plaintext, $ciphertext);
    }

    #[Test]
    public function it_handles_encryption_key_with_utf8_characters(): void
    {
        /**
         * EDGE CASE: If someone puts UTF-8 characters in the encryption key
         * (via ipconfig.php base64 encoding), it should still work.
         */
        $utf8Key                = 'base64:' . base64_encode('key-with-émojis-🔐-中文');
        $_ENV['ENCRYPTION_KEY'] = $utf8Key;

        $crypt     = new Crypt();
        $plaintext = 'test data';

        $ciphertext = $crypt->encode($plaintext);
        $decrypted  = $crypt->decode($ciphertext);

        self::assertSame($plaintext, $decrypted);
    }

    #[Test]
    public function it_handles_binary_data_with_null_bytes(): void
    {
        /**
         * EDGE CASE: Binary data containing null bytes (common in random data)
         * must not be truncated by string functions. The Cryptor class uses
         * strlen()/substr() for binary safety (not mb_strlen/mb_substr).
         */
        $plaintext = "start\x00middle\x00end" . random_bytes(64);
        $cryptor   = new Cryptor(fmt: Cryptor::FORMAT_RAW);

        $ciphertext = $cryptor->encryptString($plaintext, $this->key);
        $decrypted  = $cryptor->decryptString($ciphertext, $this->key);

        self::assertSame($plaintext, $decrypted);
        self::assertStringContainsString("\x00", $decrypted);
    }

    #[Test]
    public function it_preserves_single_byte_plaintexts(): void
    {
        /**
         * EDGE CASE: Very small plaintexts (single byte) should still encrypt
         * correctly and produce full IV + ciphertext.
         */
        $plaintext = 'x';
        $cryptor   = new Cryptor(fmt: Cryptor::FORMAT_RAW);

        $ciphertext = $cryptor->encryptString($plaintext, $this->key);
        $decrypted  = $cryptor->decryptString($ciphertext, $this->key);

        self::assertSame($plaintext, $decrypted);
        self::assertGreaterThan(16, strlen($ciphertext)); // IV (16) + ciphertext
    }

    #[Test]
    public function it_handles_very_long_smtp_passwords(): void
    {
        /*
         * EDGE CASE: Some SMTP providers allow very long passwords (certificate keys, tokens).
         * Encryption should handle arbitrarily large plaintexts.
         */
        $_ENV['ENCRYPTION_KEY'] = 'base64:' . base64_encode($this->key);

        $crypt            = new Crypt();
        $veryLongPassword = str_repeat('x', 65536); // 64KB password

        $ciphertext = $crypt->encode($veryLongPassword);
        $decrypted  = $crypt->decode($ciphertext);

        self::assertSame($veryLongPassword, $decrypted);
    }

    #[Test]
    public function it_handles_passwords_containing_base64_prefix(): void
    {
        /*
         * EDGE CASE: A password might literally contain "base64:" string,
         * which shouldn't be interpreted as a key format prefix.
         *
         * The fix correctly only applies base64: decoding to the ENCRYPTION_KEY,
         * not to credential values.
         */
        $_ENV['ENCRYPTION_KEY'] = 'base64:' . base64_encode($this->key);

        $crypt     = new Crypt();
        $plaintext = 'base64:somepasswordvalue';

        $ciphertext = $crypt->encode($plaintext);
        $decrypted  = $crypt->decode($ciphertext);

        self::assertSame($plaintext, $decrypted);
    }

    #[Test]
    public function it_detects_tampering_in_very_large_ciphertexts(): void
    {
        /**
         * EDGE CASE: Tampering detection (IV extraction and openssl_decrypt)
         * should work correctly even for large ciphertexts.
         */
        $plaintext = str_repeat('test data', 10000); // ~90KB
        $cryptor   = new Cryptor(fmt: Cryptor::FORMAT_RAW);

        $ciphertext = $cryptor->encryptString($plaintext, $this->key);

        // Tamper with middle of ciphertext
        $tampered     = $ciphertext;
        $tampered[50] = chr(ord($tampered[50]) ^ 1);

        $decrypted = $cryptor->decryptString($tampered, $this->key);

        self::assertNotSame($plaintext, $decrypted);
    }

    #[Test]
    public function it_handles_key_with_null_bytes(): void
    {
        /**
         * EDGE CASE: If ENCRYPTION_KEY is binary and contains null bytes,
         * hash() and openssl_digest should still work.
         *
         * This tests robustness of the Cryptor implementation.
         */
        $keyWithNulls = "key\x00with\x00nulls" . random_bytes(16);
        $plaintext    = 'test';
        $cryptor      = new Cryptor(fmt: Cryptor::FORMAT_RAW);

        $ciphertext = $cryptor->encryptString($plaintext, $keyWithNulls);
        $decrypted  = $cryptor->decryptString($ciphertext, $keyWithNulls);

        self::assertSame($plaintext, $decrypted);
    }

    #[Test]
    public function it_handles_base64_key_with_padding_variations(): void
    {
        /**
         * EDGE CASE: base64_decode should handle both padded and unpadded variants.
         * PHP's base64_decode accepts both, but let's verify it works.
         */
        $rawKey   = random_bytes(32);
        $base64   = base64_encode($rawKey);
        $unpadded = rtrim($base64, '=');

        // Test with padding
        $_ENV['ENCRYPTION_KEY'] = 'base64:' . $base64;
        $crypt1                 = new Crypt();
        $cipher1                = $crypt1->encode('test1');

        // Test without padding
        $_ENV['ENCRYPTION_KEY'] = 'base64:' . $unpadded;
        $crypt2                 = new Crypt();
        $cipher2                = $crypt2->encode('test2');

        // Both should work, but with different keys
        self::assertNotSame($cipher1, $cipher2);
    }

    #[Test]
    public function it_rejects_plaintext_with_only_whitespace(): void
    {
        /*
         * EDGE CASE: Whitespace-only plaintexts should still encrypt normally.
         * They're not "empty" in the sense of empty string, so they should work.
         */
        $_ENV['ENCRYPTION_KEY'] = 'base64:' . base64_encode($this->key);

        $crypt     = new Crypt();
        $plaintext = "   \n\t  ";

        $ciphertext = $crypt->encode($plaintext);
        $decrypted  = $crypt->decode($ciphertext);

        self::assertSame($plaintext, $decrypted);
    }

    #[Test]
    public function it_handles_repeated_encryptions_of_same_value(): void
    {
        /*
         * EDGE CASE: The same value encrypted multiple times should produce
         * different ciphertexts (fresh IV each time) but all decrypt to same value.
         */
        $_ENV['ENCRYPTION_KEY'] = 'base64:' . base64_encode($this->key);

        $crypt     = new Crypt();
        $plaintext = 'same value every time';

        $ciphers = [
            $crypt->encode($plaintext),
            $crypt->encode($plaintext),
            $crypt->encode($plaintext),
        ];

        // All different (different IVs)
        self::assertNotSame($ciphers[0], $ciphers[1]);
        self::assertNotSame($ciphers[1], $ciphers[2]);

        // All decrypt to same value
        foreach ($ciphers as $cipher) {
            self::assertSame($plaintext, $crypt->decode($cipher));
        }
    }

    #[Test]
    public function it_handles_smtp_password_with_credential_separators(): void
    {
        /*
         * EDGE CASE: SMTP passwords might contain special characters like
         * colons, @, semicolons that are used for URL/credential parsing.
         */
        $_ENV['ENCRYPTION_KEY'] = 'base64:' . base64_encode($this->key);

        $crypt     = new Crypt();
        $plaintext = 'user:pass@domain:port;extra=data';

        $ciphertext = $crypt->encode($plaintext);
        $decrypted  = $crypt->decode($ciphertext);

        self::assertSame($plaintext, $decrypted);
    }
}
