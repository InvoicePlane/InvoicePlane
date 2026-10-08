<?php

namespace Tests\Unit\Security;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('security')]
class SecurityHelperTest extends TestCase
{
    protected function setUp(): void
    {
        $this->setRequest([], [], []);
        require_once ROOT_PATH . '/application/helpers/security_helper.php';
    }

    #[Test]
    public function it_accepts_a_same_origin_referer(): void
    {
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
        /* Arrange */

        /* Act */
        $valid = verify_get_csrf_token();

        /* Assert */
        self::assertFalse($valid);
    }

    #[Test]
    public function it_accepts_a_matching_get_csrf_token(): void
    {
        /* Arrange */
        $token = 'unit-csrf-token';
        $this->setRequest(['_ip_csrf' => $token], [], ['ip_csrf_cookie' => $token]);

        /* Act */
        $valid = verify_get_csrf_token();

        /* Assert */
        self::assertTrue($valid);
    }

    #[Test]
    public function it_rejects_a_missing_post_csrf_token_instead_of_treating_null_as_equal(): void
    {
        /* Arrange */

        /* Act */
        $valid = verify_csrf_token();

        /* Assert */
        self::assertFalse($valid);
    }

    #[Test]
    public function it_trusts_the_framework_check_on_a_post_whose_token_was_already_consumed(): void
    {
        /*
         * Regression for #1694: CodeIgniter's Security::csrf_verify() validates
         * every POST during bootstrap and then unsets $_POST[csrf_token_name].
         * verify_csrf_token() must not re-reject that already-verified request.
         */

        /* Arrange */
        $this->setRequest([], [], [], withSecurity: true);
        $originalMethod            = $_SERVER['REQUEST_METHOD'] ?? null;
        $_SERVER['REQUEST_METHOD'] = 'POST';

        try {
            /* Act */
            $valid = verify_csrf_token();
        } finally {
            if ($originalMethod === null) {
                unset($_SERVER['REQUEST_METHOD']);
            } else {
                $_SERVER['REQUEST_METHOD'] = $originalMethod;
            }
        }

        /* Assert */
        self::assertTrue($valid);
    }

    #[Test]
    public function it_escapes_urls_for_html_output(): void
    {
        /* Arrange */
        $url = 'https://invoiceplane.example/search?q="x"&next=ok';

        /* Act */
        $escaped = escape_url_for_output($url);

        /* Assert */
        self::assertStringContainsString('&quot;', $escaped);
    }

    #[Test]
    public function it_breaks_the_javascript_string_breakout_payload_for_first_day_of_week(): void
    {
        /*
         * Regression for GHSA-x3r7-qm3m-hc48: first_day_of_week was echoed raw into a
         * single-quoted JS string literal. A value like 1' + alert(1) + ' must no longer
         * be able to close the literal.
         */

        /* Arrange */
        $payload = "1' + window['ale'+'rt']('XSS ' + document.domain) + '";

        /* Act */
        $encoded = encode_for_javascript_string($payload);

        /* Assert: every single quote must be escaped — remove the escaped sequences and
         * confirm no bare quote remains that could close the surrounding JS string literal. */
        self::assertStringContainsString("\\'", $encoded);
        self::assertStringNotContainsString("'", str_replace("\\'", '', $encoded));
    }

    #[Test]
    public function it_escapes_backslashes_before_quotes_to_avoid_reintroducing_a_live_escape(): void
    {
        /* Arrange: a trailing backslash must not combine with the following escaped quote
         * to produce an unescaped quote (e.g. "\\" . "'" must not read as \' being a single
         * escaped backslash followed by a live quote). */
        $payload = 'end\\';

        /* Act */
        $encoded = encode_for_javascript_string($payload);

        /* Assert */
        self::assertSame('end\\\\', $encoded);
    }

    #[Test]
    public function it_escapes_line_terminators_that_are_invalid_in_a_js_string_literal(): void
    {
        /* Arrange */
        $payload = "line1\r\nline2\u{2028}line3\u{2029}";

        /* Act */
        $encoded = encode_for_javascript_string($payload);

        /* Assert */
        self::assertStringNotContainsString("\r", $encoded);
        self::assertStringNotContainsString("\n", $encoded);
        self::assertStringNotContainsString("\u{2028}", $encoded);
        self::assertStringNotContainsString("\u{2029}", $encoded);
    }

    #[Test]
    public function it_escapes_a_closing_script_tag_sequence(): void
    {
        /* Arrange */
        $payload = '</script><script>alert(1)</script>';

        /* Act */
        $encoded = encode_for_javascript_string($payload);

        /* Assert */
        self::assertStringNotContainsString('</script>', $encoded);
    }

    #[Test]
    public function it_casts_a_non_string_value_before_encoding(): void
    {
        /* Arrange */
        $payload = 3;

        /* Act */
        $encoded = encode_for_javascript_string($payload);

        /* Assert */
        self::assertSame('3', $encoded);
    }

    private function setRequest(array $get, array $post, array $cookies, bool $withSecurity = false): void
    {
        $GLOBALS['unitCiConfig'] = [
            'csrf_protection'  => true,
            'csrf_token_name'  => '_ip_csrf',
            'csrf_cookie_name' => 'ip_csrf_cookie',
        ];
        $GLOBALS['unitBaseUrl']    = 'https://invoiceplane.example/';
        $GLOBALS['unitCiInstance'] = new class ($get, $post, $cookies, $withSecurity) {
            public object $input;

            public object $load;

            public ?object $security = null;

            public function __construct(array $get, array $post, array $cookies, bool $withSecurity)
            {
                if ($withSecurity) {
                    $this->security = new class () {
                        public function get_csrf_hash(): string
                        {
                            return 'regenerated-hash';
                        }
                    };
                }

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
}
