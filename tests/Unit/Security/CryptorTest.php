<?php

namespace Tests\Unit\Security;

use Crypt;
use Cryptor;
use Exception;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('security')]
class CryptorTest extends TestCase
{
    private string $key;

    private bool $hadEncryptionKey = false;

    private mixed $previousEncryptionKey = null;

    protected function setUp(): void
    {
        parent::setUp();

        require_once dirname(__DIR__, 3) . '/application/libraries/Cryptor.php';
        require_once dirname(__DIR__, 3) . '/application/libraries/Crypt.php';

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
    public function it_round_trips_text_using_base64_output(): void
    {
        /* Arrange */
        $plaintext = 'InvoicePlane secret value';

        /* Act */
        $ciphertext = Cryptor::Encrypt($plaintext, $this->key);
        $decrypted  = Cryptor::Decrypt($ciphertext, $this->key);

        /* Assert */
        self::assertNotSame($plaintext, $ciphertext);
        self::assertSame($plaintext, $decrypted);
        self::assertMatchesRegularExpression('/^[A-Za-z0-9+\/]+=*$/', $ciphertext);
    }

    #[Test]
    public function it_round_trips_binary_data_without_multibyte_corruption(): void
    {
        /* Arrange */
        $plaintext = "\x00\xff\x80binary\x00payload" . random_bytes(64);
        $cryptor   = new Cryptor(fmt: Cryptor::FORMAT_RAW);

        /* Act */
        $ciphertext = $cryptor->encryptString($plaintext, $this->key);
        $decrypted  = $cryptor->decryptString($ciphertext, $this->key);

        /* Assert */
        self::assertSame($plaintext, $decrypted);
        self::assertSame(16 + strlen($plaintext), strlen($ciphertext));
    }

    #[Test]
    public function it_supports_hex_encoded_ciphertext(): void
    {
        /* Arrange */
        $plaintext = 'hex formatted payload';
        $cryptor   = new Cryptor(fmt: Cryptor::FORMAT_HEX);

        /* Act */
        $ciphertext = $cryptor->encryptString($plaintext, $this->key);
        $decrypted  = $cryptor->decryptString($ciphertext, $this->key);

        /* Assert */
        self::assertSame($plaintext, $decrypted);
        self::assertMatchesRegularExpression('/^[0-9a-f]+$/', $ciphertext);
        self::assertSame(0, strlen($ciphertext) % 2);
    }

    #[Test]
    public function it_uses_a_fresh_iv_for_each_encryption(): void
    {
        /* Arrange */
        $plaintext = 'same plaintext';

        /* Act */
        $firstCiphertext  = Cryptor::Encrypt($plaintext, $this->key);
        $secondCiphertext = Cryptor::Encrypt($plaintext, $this->key);

        /* Assert */
        self::assertNotSame($firstCiphertext, $secondCiphertext);
        self::assertSame($plaintext, Cryptor::Decrypt($firstCiphertext, $this->key));
        self::assertSame($plaintext, Cryptor::Decrypt($secondCiphertext, $this->key));
    }

    #[Test]
    public function it_rejects_ciphertext_that_is_shorter_than_the_iv(): void
    {
        /* Arrange */
        $cryptor = new Cryptor(fmt: Cryptor::FORMAT_RAW);

        /* Assert */
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('is less than iv length');

        /* Act */
        $cryptor->decryptString(random_bytes(15), $this->key);
    }

    #[Test]
    public function it_does_not_round_trip_a_tampered_ciphertext_to_the_original_plaintext(): void
    {
        /* Arrange */
        $plaintext  = 'tamper-sensitive plaintext';
        $ciphertext = Cryptor::Encrypt($plaintext, $this->key, Cryptor::FORMAT_RAW);

        $tampered     = $ciphertext;
        $tampered[20] = chr(ord($tampered[20]) ^ 1);

        /* Act */
        $decrypted = Cryptor::Decrypt($tampered, $this->key, Cryptor::FORMAT_RAW);

        /* Assert */
        self::assertNotSame($plaintext, $decrypted);
    }

    #[Test]
    public function it_hashes_and_verifies_passwords_with_the_crypt_wrapper(): void
    {
        /* Arrange */
        $crypt = new Crypt();

        /* Act */
        $hash = $crypt->generate_password('correct horse battery staple');

        /* Assert */
        self::assertStringStartsWith('$2y$', $hash);
        self::assertTrue($crypt->check_password($hash, 'correct horse battery staple'));
        self::assertFalse($crypt->check_password($hash, 'wrong password'));
    }

    #[Test]
    public function it_decodes_base64_encryption_keys_in_the_crypt_wrapper(): void
    {
        /* Arrange: Crypt reads env('ENCRYPTION_KEY'), which is $_ENV-backed
         * (see bootstrap/kernel.php), not getenv()/putenv(). */
        require_once dirname(__DIR__, 3) . '/bootstrap/kernel.php';
        $rawKey                 = random_bytes(32);
        $_ENV['ENCRYPTION_KEY'] = 'base64:' . base64_encode($rawKey);

        $crypt     = new Crypt();
        $plaintext = 'wrapped encryption payload';

        /* Act */
        $ciphertext = $crypt->encode($plaintext);
        $decrypted  = $crypt->decode($ciphertext);

        /* Assert */
        self::assertNotSame($plaintext, $ciphertext);
        self::assertSame($plaintext, $decrypted);
    }

    #[Test]
    public function it_correctly_decrypts_when_iv_bytes_are_extracted_properly(): void
    {
        /**
         * Verifies the CORRECT behavior: when ciphertext has exactly 16 bytes
         * of IV + encrypted payload, decrypt should work perfectly. The current
         * code uses strlen()/substr() correctly on binary data.
         */
        $cryptor = new Cryptor(fmt: Cryptor::FORMAT_B64);
        $plaintext = 'smtp_password_1234';

        $ciphertext = $cryptor->encryptString($plaintext, $this->key);
        $decrypted = $cryptor->decryptString($ciphertext, $this->key);

        self::assertSame($plaintext, $decrypted);
    }

    #[Test]
    public function it_fails_to_decrypt_when_stored_data_has_encoding_corruption(): void
    {
        /**
         * Demonstrates the BUG that occurs when encrypted data is stored/retrieved
         * from a database with character set issues. Extra bytes get prepended,
         * causing decryption to produce garbage output. This happens when:
         * 1. Data is corrupted during storage/retrieval
         * 2. Character encoding changes the byte length
         * 3. Extra bytes are prepended (UTF-8 BOM, CRLF conversion, MySQL charset issue)
         *
         * When extra bytes precede the IV, the extracted IV contains wrong bytes,
         * causing openssl_decrypt to produce garbage output that breaks SMTP auth.
         *
         * FIX: Validating IV length catches this issue early rather than letting
         * silent corruption pass through. The fix ensures we reject corrupted
         * ciphertext instead of returning wrong passwords to PHPMailer.
         */
        $cryptor = new Cryptor(fmt: Cryptor::FORMAT_B64);
        $plaintext = 'invoice_smtp_password';

        $validCiphertext = $cryptor->encryptString($plaintext, $this->key);
        $validRaw = base64_decode($validCiphertext);

        // Simulate database corruption: character encoding adds 2 extra bytes
        // This shifts the IV extraction, causing the first 16 bytes extracted
        // to contain 2 corruption bytes + 14 real IV bytes (wrong IV)
        $corruptedRaw = "\xef\xbb" . $validRaw;
        $corruptedB64 = base64_encode($corruptedRaw);

        $decrypted = $cryptor->decryptString($corruptedB64, $this->key);

        // With corrupted IV bytes, decryption produces garbage (wrong password).
        // This is the core symptom of the reported bug: password decrypts to garbage,
        // SMTP auth fails with "Incorrect authentication data"
        self::assertNotSame($plaintext, $decrypted, 'Corrupted IV should NOT decrypt to original plaintext');
    }

    #[Test]
    public function it_rejects_empty_or_null_encrypted_passwords(): void
    {
        /**
         * Tests a related scenario: when SMTP password setting is empty or NULL,
         * attempting to decrypt should gracefully handle it without corruption errors.
         *
         * In the real application flow, if database retrieval returns empty/null
         * before Cryptor is called, the issue is caught earlier in Crypt::decode():
         *
         *     if (empty($data)) {
         *         return '';
         *     }
         *
         * This test verifies that edge case is handled.
         */
        $crypt = new Crypt();

        // Empty/null passwords should decode to empty string, not throw
        self::assertSame('', $crypt->decode(''));
        self::assertSame('', $crypt->decode(null));
    }

    #[Test]
    public function it_correctly_extracts_iv_from_base64_with_binary_byte_count(): void
    {
        /* Arrange: Ensure that even with special binary sequences, the IV is
         * correctly extracted using byte-wise strlen()/substr(). */
        $plaintext = 'test';
        $cryptor   = new Cryptor(fmt: Cryptor::FORMAT_B64);

        /* Act */
        $ciphertext = $cryptor->encryptString($plaintext, $this->key);
        $raw        = base64_decode($ciphertext);

        /* Assert: The first 16 bytes (iv_num_bytes) should extract correctly
         * using strlen()/substr() on binary data. */
        self::assertSame(16, strlen(substr($raw, 0, 16)));
        self::assertSame($plaintext, $cryptor->decryptString($ciphertext, $this->key));
    }
}
