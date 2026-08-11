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
        require_once dirname(__DIR__, 3) . '/application/helpers/security_helper.php';
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
    public function it_escapes_urls_for_html_output(): void
    {
        /* Arrange */
        $url = 'https://invoiceplane.example/search?q="x"&next=ok';

        /* Act */
        $escaped = escape_url_for_output($url);

        /* Assert */
        self::assertStringContainsString('&quot;', $escaped);
    }

    private function setRequest(array $get, array $post, array $cookies): void
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
}
