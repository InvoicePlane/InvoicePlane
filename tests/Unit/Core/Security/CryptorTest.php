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
        $cryptor   = new Cryptor(fmt: Cryptor::FORMAT_B64);
        $plaintext = 'smtp_password_1234';

        $ciphertext = $cryptor->encryptString($plaintext, $this->key);
        $decrypted  = $cryptor->decryptString($ciphertext, $this->key);

        self::assertSame($plaintext, $decrypted);
    }

    #[Test]
    public function it_never_misextracts_the_iv_across_many_random_round_trips(): void
    {
        /**
         * Regression guard for upstream issue #1680: "openssl_decrypt(): IV
         * passed is 18 bytes long which is longer than the 16 expected by
         * selected cipher".
         *
         * Root cause (verified empirically, not database corruption): the IV
         * extraction in decryptString() must stay byte-wise (strlen()/substr()).
         * If it is ever rewritten to mb_strlen()/mb_substr() — e.g. by an
         * automated Pint `mb_str_functions` fixer run, which is exactly what
         * happened upstream — those functions operate on *characters* under
         * the internal encoding (UTF-8), not bytes. Random binary ciphertext
         * occasionally contains byte sequences that happen to form valid
         * multi-byte UTF-8 characters, so mb_substr($raw, 0, 16) can silently
         * consume MORE than 16 bytes to satisfy "16 characters" — producing
         * an oversized IV that openssl_decrypt() then truncates, decrypting
         * to garbage and breaking SMTP authentication.
         *
         * A single round trip won't reliably catch a mb_* regression, since
         * the failure is probabilistic (it only triggers when the random IV/
         * ciphertext bytes happen to look like multi-byte UTF-8). Looping
         * many iterations makes this test very likely to fail fast if mb_*
         * ever creeps back into this method.
         */
        $cryptor = new Cryptor(fmt: Cryptor::FORMAT_RAW);

        for ($i = 0; $i < 500; $i++) {
            $plaintext  = random_bytes(random_int(1, 64));
            $ciphertext = $cryptor->encryptString($plaintext, $this->key);
            $decrypted  = $cryptor->decryptString($ciphertext, $this->key);

            self::assertSame($plaintext, $decrypted, "Round trip failed on iteration {$i}");
        }
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

    #[Test]
    public function it_never_calls_mb_string_functions_on_binary_ciphertext(): void
    {
        /**
         * Deterministic regression guard for #1680, complementing the
         * probabilistic fuzz test above. That test proves current behavior is
         * correct but relies on randomness to catch a regression; this test
         * fails instantly and unconditionally if mb_strlen()/mb_substr() (or
         * any other mb_* function) is ever reintroduced into Cryptor.php's
         * source — e.g. by re-enabling Pint's mb_str_functions fixer, which
         * is exactly what caused #1680 originally (see commit 98c60c77).
         */
        $source = file_get_contents(dirname(__DIR__, 3) . '/application/libraries/Cryptor.php');
        self::assertIsString($source, 'Could not read Cryptor.php source');

        // Tokenize and strip comments before scanning: the file legitimately contains the
        // strings "mb_strlen()"/"mb_substr()" inside an explanatory comment (warning future
        // maintainers away from them), which a naive string/regex search over raw source
        // would misreport as a violation.
        $codeOnly = '';
        foreach (token_get_all($source) as $token) {
            if (is_array($token) && in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true)) {
                continue;
            }
            $codeOnly .= is_array($token) ? $token[1] : $token;
        }

        self::assertDoesNotMatchRegularExpression(
            '/\bmb_(strlen|substr|str_split)\s*\(/',
            $codeOnly,
            'Cryptor.php must never use mb_* string functions on binary ciphertext (see #1680) - '
            . 'mb_* counts characters under the internal encoding, not bytes, and can silently '
            . 'consume more bytes than requested when random data resembles multi-byte UTF-8.'
        );
    }

    #[Test]
    public function it_keeps_pint_mb_str_functions_fixer_disabled(): void
    {
        /**
         * Guards the actual root cause of #1680: Laravel Pint's
         * mb_str_functions fixer auto-rewrites str*() calls to mb_str*()
         * repo-wide, with no awareness that Cryptor::decryptString() operates
         * on raw binary data rather than text. That auto-fix is what silently
         * reintroduced the bug after it had already been fixed once (see
         * commit 98c60c77). If this setting is ever flipped back to true,
         * the next `pint` run will corrupt Cryptor.php again before this
         * test's sibling ever gets a chance to catch it - so guard the
         * setting directly, not just its effect.
         */
        $pintConfigPath = dirname(__DIR__, 3) . '/pint.json';
        $config         = json_decode(file_get_contents($pintConfigPath), true, flags: JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('rules', $config, 'pint.json is missing the "rules" section');
        self::assertArrayHasKey(
            'mb_str_functions',
            $config['rules'],
            'pint.json no longer configures mb_str_functions explicitly'
        );
        self::assertFalse(
            $config['rules']['mb_str_functions'],
            'pint.json must keep mb_str_functions disabled (see #1680) - enabling it lets Pint '
            . 'silently rewrite Cryptor.php\'s byte-safe strlen()/substr() calls to mb_strlen()/'
            . 'mb_substr(), corrupting IV extraction on binary ciphertext.'
        );
    }
}
