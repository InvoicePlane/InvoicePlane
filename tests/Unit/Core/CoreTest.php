<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use Crypt;
use Cryptor;
use Exception;
use InvalidArgumentException;
use Mdl_Templates;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TaxRateDecimalPlacesProcessor;
use Tests\AbstractTestCase;

class CoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        if (isset($this->this)) {
            $this->__Cryptor_tearDown();
        }
        parent::tearDown();
    }

    protected function __CountryHelper_setUp(): void

        {

            require_once dirname(__DIR__, 3) . '/application/helpers/country_helper.php';

        }
    #[Test]

    public function it_loads_a_valid_country_locale(): void

        {

            $this->__CountryHelper_setUp();

            /* Arrange */

            $locale = 'nl';



            /* Act */

            $countries = get_country_list($locale);



            /* Assert */

            self::assertSame('Nederland', $countries['NL']);

        }
    #[Test]

    public function it_falls_back_to_english_for_a_path_traversal_locale(): void

        {

            $this->__CountryHelper_setUp();

            /* Arrange */

            $locale = '../etc/passwd';



            /* Act */

            $countries = get_country_list($locale);



            /* Assert */

            self::assertSame('The Netherlands', $countries['NL']);

        }
    private string $key;
    private bool $hadEncryptionKey = false;
    private mixed $previousEncryptionKey = null;
    protected function __Cryptor_setUp(): void

        {





            require_once dirname(__DIR__, 3) . '/application/libraries/Cryptor.php';

            require_once dirname(__DIR__, 3) . '/application/libraries/Crypt.php';



            $this->key                   = random_bytes(32);

            $this->hadEncryptionKey      = array_key_exists('ENCRYPTION_KEY', $_ENV);

            $this->previousEncryptionKey = $_ENV['ENCRYPTION_KEY'] ?? null;

        }
    protected function __Cryptor_tearDown(): void

        {

            if ($this->hadEncryptionKey) {

                $_ENV['ENCRYPTION_KEY'] = $this->previousEncryptionKey;

            } else {

                unset($_ENV['ENCRYPTION_KEY']);

            }





        }
    #[Test]

    public function it_round_trips_text_using_base64_output(): void

        {

            $this->__Cryptor_setUp();

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

            $this->__Cryptor_setUp();

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

            $this->__Cryptor_setUp();

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

            $this->__Cryptor_setUp();

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

            $this->__Cryptor_setUp();

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

            $this->__Cryptor_setUp();

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

            $this->__Cryptor_setUp();

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

            $this->__Cryptor_setUp();

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
    protected function __FileSecurityHelper_setUp(): void

        {

            require_once dirname(__DIR__, 3) . '/application/helpers/file_security_helper.php';

        }
    public static function __FileSecurityHelper_unsafeFilenameProvider(): array

        {

            return [

                'empty'          => ['', 'empty_filename'],

                'unix escape'    => ['../../etc/passwd', 'path_traversal'],

                'windows escape' => ['..\\..\\boot.ini', 'path_traversal'],

                'null byte'      => ["invoice.pdf\0.php", 'null_byte'],

                'absolute'       => ['/etc/passwd', 'absolute_path'],

                'drive path'     => ['C:/Windows/win.ini', 'drive_letter'],

            ];

        }
    #[Test]
    #[DataProvider('__FileSecurityHelper_unsafeFilenameProvider')]

    public function it_rejects_unsafe_filenames(string $filename, string $error): void

        {

            $this->__FileSecurityHelper_setUp();

            /* Arrange */



            /* Act */

            $result = validate_safe_filename($filename);



            /* Assert */

            self::assertFalse($result['valid']);

            self::assertSame($error, $result['error']);

        }
    #[Test]

    public function it_sanitizes_header_injection_and_uses_a_fallback_for_empty_output(): void

        {

            $this->__FileSecurityHelper_setUp();

            /* Arrange */

            $injected = "report\r\n\".pdf";

            $empty    = "\r\n\\\"";



            /* Act */

            $sanitized = sanitize_filename_for_header($injected);

            $fallback  = sanitize_filename_for_header($empty);



            /* Assert */

            self::assertSame('report.pdf', $sanitized);

            self::assertSame('attachment.bin', $fallback);

        }
    #[Test]

    public function it_sanitizes_document_numbers_to_filename_safe_characters(): void

        {

            $this->__FileSecurityHelper_setUp();

            /* Arrange */

            $documentNumbers = ['INV/2026:001', '../etc/passwd'];



            /* Act */

            $sanitized = array_map('sanitize_document_number_for_filename', $documentNumbers);



            /* Assert */

            self::assertSame(['INV_2026_001', '___etc_passwd'], $sanitized);

        }
    #[Test]

    public function it_validates_database_ports_in_the_valid_range(): void

        {

            $this->__FileSecurityHelper_setUp();

            /* Arrange */

            $ports = [null, '443', '0', '65536', '3306;DROP TABLE users'];



            /* Act */

            $validated = array_map('sanitize_database_port', $ports);



            /* Assert */

            self::assertSame([3306, 443, null, null, null], $validated);

        }
    #[Test]

    public function it_confines_resolved_files_to_the_allowed_directory(): void

        {

            $this->__FileSecurityHelper_setUp();

            /* Arrange */

            $directory = sys_get_temp_dir() . '/invoiceplane-file-security-' . bin2hex(random_bytes(4));

            mkdir($directory, 0700, true);

            $inside  = $directory . '/inside.txt';

            $outside = $directory . '-outside.txt';

            file_put_contents($inside, 'inside');

            file_put_contents($outside, 'outside');



            try {

                /* Act */

                $insideResult  = validate_file_in_directory($inside, $directory);

                $outsideResult = validate_file_in_directory($outside, $directory);



                /* Assert */

                self::assertTrue($insideResult);

                self::assertFalse($outsideResult);

            } finally {

                unlink($inside);

                unlink($outside);

                rmdir($directory);

            }

        }
    protected function __IpSecurityHelper_setUp(): void

        {

            require_once dirname(__DIR__, 3) . '/application/helpers/ip_security_helper.php';

        }
    #[Test]

    public function it_generates_a_hexadecimal_token_with_the_requested_entropy(): void

        {

            $this->__IpSecurityHelper_setUp();

            /* Arrange */

            $length = 32;



            /* Act */

            $token = generate_secure_token($length);



            /* Assert */

            self::assertSame(64, strlen($token));

            self::assertMatchesRegularExpression('/\A[0-9a-f]{64}\z/', $token);

        }
    #[Test]

    public function it_rejects_a_non_positive_token_length(): void

        {

            $this->__IpSecurityHelper_setUp();

            /* Arrange */

            $length = 0;



            /* Act */

            try {

                generate_secure_token($length);

                $exception = null;

            } catch (InvalidArgumentException $error) {

                $exception = $error;

            }



            /* Assert */

            self::assertInstanceOf(InvalidArgumentException::class, $exception);

        }
    #[Test]

    public function it_generates_a_password_reset_token_with_256_bits_of_entropy(): void

        {

            $this->__IpSecurityHelper_setUp();

            /* Arrange */



            /* Act */

            $token = generate_password_reset_token();



            /* Assert */

            self::assertSame(64, strlen($token));

            self::assertMatchesRegularExpression('/\A[0-9a-f]{64}\z/', $token);

        }
    #[Test]

    public function it_generates_a_bcrypt_compatible_salt(): void

        {

            $this->__IpSecurityHelper_setUp();

            /* Arrange */



            /* Act */

            $salt = generate_secure_salt();



            /* Assert */

            self::assertSame(22, strlen($salt));

            self::assertMatchesRegularExpression('/\A[.\/[0-9A-Za-z]{22}\z/', $salt);

        }
    protected function __MarkupSanitizer_setUp(): void

        {

            require_once dirname(__DIR__, 3) . '/application/helpers/html_sanitizer_helper.php';

            require_once dirname(__DIR__, 3) . '/application/helpers/mpdf_helper.php';

        }
    #[Test]

    public function it_keeps_safe_email_markup_and_removes_script_content(): void

        {

            $this->__MarkupSanitizer_setUp();

            /* Arrange */

            $html = '<p>Hello <strong>world</strong></p><script>alert(1)</script>';



            /* Act */

            $sanitized = sanitize_email_template_html($html);



            /* Assert */

            self::assertStringContainsString('<p>Hello <strong>world</strong></p>', $sanitized);

            self::assertStringNotContainsString('<script', $sanitized);

            self::assertStringNotContainsString('alert(1)', $sanitized);

        }
    #[Test]

    public function it_removes_external_images_from_email_markup(): void

        {

            $this->__MarkupSanitizer_setUp();

            /* Arrange */

            $html = '<p><img src="https://attacker.example/track.gif"></p>';



            /* Act */

            $sanitized = sanitize_email_template_html($html);



            /* Assert */

            self::assertStringNotContainsString('attacker.example', $sanitized);

        }
    #[Test]

    public function it_keeps_only_safe_pdf_footer_tags_and_strips_attributes(): void

        {

            $this->__MarkupSanitizer_setUp();

            /* Arrange */

            $footer = '<strong class="unsafe">Footer</strong><script>alert(1)</script>';



            /* Act */

            $sanitized = sanitize_pdf_footer_content($footer);



            /* Assert */

            self::assertSame('<strong>Footer</strong>', $sanitized);

            self::assertStringNotContainsString('class=', $sanitized);

            self::assertStringNotContainsString('<script', $sanitized);

        }
    #[Test]

    public function it_converts_pdf_footer_break_tags_and_handles_null(): void

        {

            $this->__MarkupSanitizer_setUp();

            /* Arrange */

            $footer = 'Line one<br>Line two';



            /* Act */

            $sanitized = sanitize_pdf_footer_content($footer);

            $empty     = sanitize_pdf_footer_content(null);



            /* Assert */

            self::assertSame("Line one\nLine two", $sanitized);

            self::assertSame('', $empty);

        }
    protected function __SecurityHelper_setUp(): void

        {

            $this->__SecurityHelper_setRequest([], [], []);

            require_once dirname(__DIR__, 3) . '/application/helpers/security_helper.php';

        }
    #[Test]

    public function it_accepts_a_same_origin_referer(): void

        {

            $this->__SecurityHelper_setUp();

            /* Arrange */

            $referer = 'https://invoiceplane.example/invoices/index';



            /* Act */

            $result = get_safe_referer($referer, 'invoices/index');



            /* Assert */

            self::assertSame($referer, $result);

        }
    #[Test]

    public function it_replaces_an_external_referer_with_the_safe_default(): void

        {

            $this->__SecurityHelper_setUp();

            /* Arrange */

            $referer = 'https://evil.example/steal';



            /* Act */

            $result = get_safe_referer($referer, 'sessions/login');



            /* Assert */

            self::assertSame('sessions/login', $result);

        }
    #[Test]

    public function it_rejects_a_missing_get_csrf_token(): void

        {

            $this->__SecurityHelper_setUp();

            /* Arrange */



            /* Act */

            $valid = verify_get_csrf_token();



            /* Assert */

            self::assertFalse($valid);

        }
    #[Test]

    public function it_accepts_a_matching_get_csrf_token(): void

        {

            $this->__SecurityHelper_setUp();

            /* Arrange */

            $token = 'unit-csrf-token';

            $this->__SecurityHelper_setRequest(['_ip_csrf' => $token], [], ['ip_csrf_cookie' => $token]);



            /* Act */

            $valid = verify_get_csrf_token();



            /* Assert */

            self::assertTrue($valid);

        }
    #[Test]

    public function it_rejects_a_missing_post_csrf_token_instead_of_treating_null_as_equal(): void

        {

            $this->__SecurityHelper_setUp();

            /* Arrange */



            /* Act */

            $valid = verify_csrf_token();



            /* Assert */

            self::assertFalse($valid);

        }
    #[Test]

    public function it_escapes_urls_for_html_output(): void

        {

            $this->__SecurityHelper_setUp();

            /* Arrange */

            $url = 'https://invoiceplane.example/search?q="x"&next=ok';



            /* Act */

            $escaped = escape_url_for_output($url);



            /* Assert */

            self::assertStringContainsString('&quot;', $escaped);

        }
    private function __SecurityHelper_setRequest(array $get, array $post, array $cookies): void

        {

            $GLOBALS['unitCiConfig'] = [

                'csrf_protection'  => true,

                'csrf_token_name'  => '_ip_csrf',

                'csrf_cookie_name' => 'ip_csrf_cookie',

            ];

            $GLOBALS['unitBaseUrl']    = 'https://invoiceplane.example/';

            $GLOBALS['unitCiInstance'] = new class ($get, $post, $cookies) {

                public object $input;



                public object $load;



                public function __construct(array $get, array $post, array $cookies)

                {

                    $this->input = new class ($get, $post, $cookies) {

                        public function __construct(

                            private array $get,

                            private array $post,

                            private array $cookies,

                        ) {}



                        public function get(string $key): mixed

                        {

                            return $this->get[$key] ?? null;

                        }



                        public function post(string $key): mixed

                        {

                            return $this->post[$key] ?? null;

                        }



                        public function cookie(string $key): mixed

                        {

                            return $this->cookies[$key] ?? null;

                        }



                        public function ip_address(): string

                        {

                            return '127.0.0.1';

                        }

                    };

                    $this->load = new class () {

                        public function helper(string $helper): void {}

                    };

                }

            };

        }
    #[Test]

    public function it_restricts_sumex_remote_requests_to_https(): void

        {

            /* Arrange */

            $source = (string) file_get_contents(dirname(__DIR__, 3) . '/application/libraries/Sumex.php');



            /* Act */

            $hasHttpsGuard      = str_contains($source, 'mb_strtolower((string) $scheme) !== \'https\'');

            $restrictsScheme    = str_contains($source, 'CURLOPT_PROTOCOLS, CURLPROTO_HTTPS');

            $restrictsRedirects = str_contains($source, 'CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTPS');



            /* Assert */

            self::assertTrue($hasHttpsGuard, 'SUMEX_URL must reject non-HTTPS schemes.');

            self::assertTrue($restrictsScheme, 'SUMEX cURL requests must allow HTTPS only.');

            self::assertTrue($restrictsRedirects, 'SUMEX redirects must remain HTTPS-only.');

        }
    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]

    public function it_lists_a_custom_invoice_pdf_template_configured_in_ipconfig(): void

        {

            /* Arrange */

            $model = $this->__CustomTemplateAllowlist_bootModelWith(['CUSTOM_INVOICE_TEMPLATES_PDF' => 'My Custom Template']);



            /* Act */

            $templates = $model->get_invoice_templates('pdf');



            /* Assert */

            self::assertContains(

                'My Custom Template',

                $templates,

                'A custom invoice PDF template configured in ipconfig.php must appear in the settings dropdown.'

            );

        }
    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]

    public function it_lists_a_custom_invoice_public_template_configured_in_ipconfig(): void

        {

            /* Arrange */



            /* Act */

            $model = $this->__CustomTemplateAllowlist_bootModelWith(['CUSTOM_INVOICE_TEMPLATES_PUBLIC' => 'My Web Template']);



            /* Assert */

            self::assertContains(

                'My Web Template',

                $model->get_invoice_templates('public'),

                'A custom invoice public template configured in ipconfig.php must appear in the settings dropdown.'

            );

        }
    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]

    public function it_lists_a_custom_quote_pdf_template_configured_in_ipconfig(): void

        {

            /* Arrange */

            $model = $this->__CustomTemplateAllowlist_bootModelWith(['CUSTOM_QUOTE_TEMPLATES_PDF' => 'My Quote Template']);



            /* Act */

            $templates = $model->get_quote_templates('pdf');



            /* Assert */

            self::assertContains(

                'My Quote Template',

                $templates,

                'A custom quote PDF template configured in ipconfig.php must appear in the settings dropdown.'

            );

        }
    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]

    public function it_lists_a_custom_quote_public_template_configured_in_ipconfig(): void

        {

            /* Arrange */



            /* Act */

            $model = $this->__CustomTemplateAllowlist_bootModelWith(['CUSTOM_QUOTE_TEMPLATES_PUBLIC' => 'My Quote Web Template']);



            /* Assert */

            self::assertContains(

                'My Quote Web Template',

                $model->get_quote_templates('public'),

                'A custom quote public template configured in ipconfig.php must appear in the settings dropdown.'

            );

        }
    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]

    public function it_keeps_built_in_templates_alongside_a_custom_one(): void

        {

            /* Arrange */

            $model = $this->__CustomTemplateAllowlist_bootModelWith(['CUSTOM_INVOICE_TEMPLATES_PDF' => 'My Custom Template']);



            /* Act */

            $templates = $model->get_invoice_templates('pdf');



            /* Assert */

            self::assertContains('My Custom Template', $templates, 'The custom template must be listed.');

            self::assertContains('InvoicePlane', $templates, 'Built-in templates must still be listed after merging in a custom one.');

        }
    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]

    public function it_lists_multiple_comma_separated_custom_templates(): void

        {

            /* Arrange */



            /* Act */

            $model = $this->__CustomTemplateAllowlist_bootModelWith(['CUSTOM_INVOICE_TEMPLATES_PDF' => 'Corporate - Modern,Corporate - Classic']);



            $templates = $model->get_invoice_templates('pdf');



            /* Assert */

            self::assertContains('Corporate - Modern', $templates);

            self::assertContains('Corporate - Classic', $templates);

        }
    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]

    public function it_returns_only_built_ins_when_no_custom_templates_are_configured(): void

        {

            /* Arrange */

            $model = $this->__CustomTemplateAllowlist_bootModelWith([]);



            /* Act */

            $templates = $model->get_invoice_templates('pdf');



            /* Assert */

            self::assertSame(

                ['InvoicePlane', 'InvoicePlane - paid', 'InvoicePlane - overdue'],

                $templates,

                'With nothing configured the list must be exactly the built-in whitelist, unchanged.'

            );

        }



        // -- Deny path: the allowlist regex in _merge_custom() must reject unsafe names --
    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]

    public function it_rejects_a_path_traversal_custom_template_name(): void

        {

            /* Arrange */



            /* Act */

            $model = $this->__CustomTemplateAllowlist_bootModelWith(['CUSTOM_INVOICE_TEMPLATES_PDF' => '../../evil']);



            $templates = $model->get_invoice_templates('pdf');



            /* Assert */

            self::assertNotContains('../../evil', $templates, 'A path-traversal name must never enter the allowlist.');

            self::assertSame(

                ['InvoicePlane', 'InvoicePlane - paid', 'InvoicePlane - overdue'],

                $templates,

                'A rejected name must leave the built-in list untouched.'

            );

        }
    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]

    public function it_rejects_a_php_extension_custom_template_name(): void

        {

            /* Arrange */

            $model = $this->__CustomTemplateAllowlist_bootModelWith(['CUSTOM_INVOICE_TEMPLATES_PDF' => 'evil.php']);



            /* Act */

            $templates = $model->get_invoice_templates('pdf');



            /* Assert */

            self::assertNotContains(

                'evil.php',

                $templates,

                'A name containing a file extension (the "." is disallowed) must be rejected.'

            );

        }
    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]

    public function it_keeps_the_valid_name_and_drops_the_invalid_one_from_a_mixed_list(): void

        {

            /* Arrange */



            /* Act */

            $model = $this->__CustomTemplateAllowlist_bootModelWith(['CUSTOM_INVOICE_TEMPLATES_PDF' => 'Good Template,../evil']);



            $templates = $model->get_invoice_templates('pdf');



            /* Assert */

            self::assertContains('Good Template', $templates, 'The valid name must pass the allowlist.');

            self::assertNotContains('../evil', $templates, 'The invalid name must be dropped, not the whole list.');

        }



        // -- Bootstrap wiring guard: the single boot path must define all four --
    #[Test]

    public function it_wires_all_four_allowlist_constants_from_ipconfig_through_the_single_bootstrap(): void

        {

            /* Arrange */

            $repoRoot = dirname(__DIR__, 3);



            // The legacy root index.php was removed; public/index.php -> bootstrap/constants.php

            // is now the only boot path, so this is the one file that must wire the constants.

            $source = (string) file_get_contents($repoRoot . '/bootstrap/constants.php');



            $constants = [

                'CUSTOM_INVOICE_TEMPLATES_PDF',

                'CUSTOM_INVOICE_TEMPLATES_PUBLIC',

                'CUSTOM_QUOTE_TEMPLATES_PDF',

                'CUSTOM_QUOTE_TEMPLATES_PUBLIC',

            ];



            /* Act */

            $definedFromEnvironment = [];

            foreach ($constants as $constant) {

                $definedFromEnvironment[$constant] = (bool) preg_match(

                    '/define\(\s*[\'"]' . preg_quote($constant, '/') . '[\'"]\s*,\s*env\(/',

                    $source

                );

            }



            /* Assert */

            self::assertFileDoesNotExist(

                $repoRoot . '/index.php',

                'The legacy root index.php must stay removed so a second bootstrap cannot drift out of sync.'

            );



            foreach ($definedFromEnvironment as $constant => $wasDefinedFromEnvironment) {

                self::assertTrue(

                    $wasDefinedFromEnvironment,

                    sprintf('bootstrap/constants.php must define %s from its ipconfig env key, or the fix is absent.', $constant)

                );

            }

        }



        /**

         * Boot the real bootstrap constants with the given ipconfig values in $_ENV,

         * then return a fresh Mdl_Templates. Mirrors the runtime order: env is

         * populated (Dotenv) -> bootstrap defines the constants -> the model reads them.

         *

         * @param array<string, string> $ipconfig

         */
    private function __CustomTemplateAllowlist_bootModelWith(array $ipconfig): Mdl_Templates

        {

            // Stubs: CI_Model, log_message(), and an env() that reads $_ENV exactly

            // like the real helper kernel.php defines before requiring constants.php.

            require_once dirname(__DIR__, 2) . '/Support/template_model_stubs.php';



            foreach ($ipconfig as $key => $value) {

                $_ENV[$key] = $value;

            }



            require_once dirname(__DIR__, 3) . '/bootstrap/constants.php';

            require_once dirname(__DIR__, 3) . '/application/modules/invoices/models/Mdl_templates.php';



            return new Mdl_Templates();

        }
    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]

    public function it_populates_the_invoice_pdf_allowlist_constant_through_the_real_kernel(): void

        {

            /* Arrange */

            $env = ['CUSTOM_INVOICE_TEMPLATES_PDF' => 'My Kernel Template'];



            /* Act */

            $value = $this->__CustomTemplateKernelBoot_runKernelProbe($env, 'CUSTOM_INVOICE_TEMPLATES_PDF');



            /* Assert */

            self::assertSame(

                'My Kernel Template',

                $value,

                'The ipconfig value in $_ENV must reach the constant through the real kernel boot path.'

            );

        }
    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]

    public function it_defines_all_four_allowlist_constants_after_boot(): void

        {

            /* Arrange */



            /* Act */

            $env = [

                'CUSTOM_INVOICE_TEMPLATES_PDF'    => 'Inv PDF',

                'CUSTOM_INVOICE_TEMPLATES_PUBLIC' => 'Inv Web',

                'CUSTOM_QUOTE_TEMPLATES_PDF'      => 'Quote PDF',

                'CUSTOM_QUOTE_TEMPLATES_PUBLIC'   => 'Quote Web',

            ];



            /* Assert */

            self::assertSame('Inv PDF', $this->__CustomTemplateKernelBoot_runKernelProbe($env, 'CUSTOM_INVOICE_TEMPLATES_PDF'));

            self::assertSame('Inv Web', $this->__CustomTemplateKernelBoot_runKernelProbe($env, 'CUSTOM_INVOICE_TEMPLATES_PUBLIC'));

            self::assertSame('Quote PDF', $this->__CustomTemplateKernelBoot_runKernelProbe($env, 'CUSTOM_QUOTE_TEMPLATES_PDF'));

            self::assertSame('Quote Web', $this->__CustomTemplateKernelBoot_runKernelProbe($env, 'CUSTOM_QUOTE_TEMPLATES_PUBLIC'));

        }
    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]

    public function it_defines_the_env_helper_before_wiring_the_constants(): void

        {

            /* Arrange */

            $env = ['CUSTOM_INVOICE_TEMPLATES_PDF' => ''];



            /* Act */

            $value = $this->__CustomTemplateKernelBoot_runKernelProbe($env, 'CUSTOM_INVOICE_TEMPLATES_PDF');



            /* Assert */

            self::assertEmpty(

                $value,

                'An unset/empty ipconfig key must yield an empty constant so Mdl_Templates treats it as "no custom templates".'

            );

        }



        /**

         * @param array<string, string> $env

         */
    private function __CustomTemplateKernelBoot_runKernelProbe(array $env, string $constant): string

        {

            $repoRoot = dirname(__DIR__, 3);

            $envCode  = '';



            foreach ($env as $key => $value) {

                $envCode .= sprintf('$_ENV[%s] = %s;', var_export($key, true), var_export($value, true));

            }



            $code = $envCode

                . 'require ' . var_export($repoRoot . '/bootstrap/kernel.php', true) . ';'

                . 'echo defined(' . var_export($constant, true) . ') ? constant(' . var_export($constant, true) . ') : "__missing__";';



            $output = [];

            $status = 0;

            exec(PHP_BINARY . ' -r ' . escapeshellarg($code), $output, $status);



            self::assertSame(0, $status, 'Kernel probe subprocess must exit cleanly.');



            return implode("\n", $output);

        }
    private TaxRateDecimalPlacesProcessor $processor;
    protected function __TaxRateDecimalPlacesProcessor_setUp(): void

        {

            require_once dirname(__DIR__, 3) . '/application/modules/settings/libraries/TaxRateDecimalPlacesProcessor.php';

            $this->processor = new TaxRateDecimalPlacesProcessor();

        }
    #[Test]

    public function it_accepts_an_integer_within_range(): void

        {

            $this->__TaxRateDecimalPlacesProcessor_setUp();

            /* Arrange */

            $value = '4';



            /* Act */

            $normalized = $this->processor->validateAndNormalize($value, 0, 10);



            /* Assert */

            self::assertSame(4, $normalized);

        }
    #[Test]

    public function it_rejects_a_sql_injection_payload(): void

        {

            $this->__TaxRateDecimalPlacesProcessor_setUp();

            /* Arrange */



            /* Act */

            $this->expectException(InvalidArgumentException::class);



            /* Assert */

            $this->processor->validateAndNormalize('4); DROP TABLE ip_tax_rates; --', 0, 10);

        }
    #[Test]

    public function it_rejects_a_non_numeric_string(): void

        {

            $this->__TaxRateDecimalPlacesProcessor_setUp();

            /* Arrange */

            $value = 'abc';



            /* Act */

            $this->expectException(InvalidArgumentException::class);



            /* Assert */

            $this->processor->validateAndNormalize($value, 0, 10);

        }
    #[Test]

    public function it_rejects_a_value_above_the_max_range(): void

        {

            $this->__TaxRateDecimalPlacesProcessor_setUp();

            /* Arrange */



            /* Act */

            $this->expectException(InvalidArgumentException::class);



            /* Assert */

            $this->processor->validateAndNormalize(999, 0, 10);

        }
    #[Test]

    public function it_rejects_a_negative_value_below_the_min_range(): void

        {

            $this->__TaxRateDecimalPlacesProcessor_setUp();

            /* Arrange */

            $value = -1;



            /* Act */

            $this->expectException(InvalidArgumentException::class);



            /* Assert */

            $this->processor->validateAndNormalize($value, 0, 10);

        }
    #[Test]

    public function it_rejects_a_float_masquerading_as_an_integer(): void

        {

            $this->__TaxRateDecimalPlacesProcessor_setUp();

            /* Arrange */



            /* Act */

            $this->expectException(InvalidArgumentException::class);



            /* Assert */

            $this->processor->validateAndNormalize('2.5', 0, 10);

        }
    #[Test]

    public function it_reports_a_schema_change_only_when_the_value_actually_differs(): void

        {

            $this->__TaxRateDecimalPlacesProcessor_setUp();

            /* Arrange */

            $currentPrecision = 4;



            /* Act */

            $unchangedPrecisionRequiresAlter = $this->processor->shouldAlterSchema($currentPrecision, 4);

            $changedPrecisionRequiresAlter   = $this->processor->shouldAlterSchema($currentPrecision, 6);



            /* Assert */

            self::assertFalse($unchangedPrecisionRequiresAlter);

            self::assertTrue($changedPrecisionRequiresAlter);

        }
}
