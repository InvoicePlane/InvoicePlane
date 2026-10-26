<?php

namespace Tests\Unit\Security;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * GHSA-6pr3-c7rf-gc7x / GHSA-257j-c733-6h5f: verify_get_csrf_token() gates the state change on generate_pdf.
 * Each rejection branch (missing, empty, non-string, mismatching) and the configured names are pinned here.
 */
#[Group('security')]
final class VerifyGetCsrfTokenBoundaryTest extends TestCase
{
    private const TOKEN = 'boundary-csrf-token-0123456789';

    protected function setUp(): void
    {
        require_once ROOT_PATH . '/application/helpers/security_helper.php';
    }

    /** @return array<string, array{array<string, mixed>, array<string, mixed>}> */
    public static function rejectedRequests(): array
    {
        return [
            'token without cookie'        => [['_ip_csrf' => self::TOKEN], []],
            'cookie without token'        => [[], ['ip_csrf_cookie' => self::TOKEN]],
            'both empty strings'          => [['_ip_csrf' => ''], ['ip_csrf_cookie' => '']],
            'empty token, real cookie'    => [['_ip_csrf' => ''], ['ip_csrf_cookie' => self::TOKEN]],
            'real token, empty cookie'    => [['_ip_csrf' => self::TOKEN], ['ip_csrf_cookie' => '']],
            'different token'             => [['_ip_csrf' => 'another-token-0123456789-abcdef'], ['ip_csrf_cookie' => self::TOKEN]],
            'token is a prefix of cookie' => [['_ip_csrf' => substr(self::TOKEN, 0, -1)], ['ip_csrf_cookie' => self::TOKEN]],
            'token is an array'           => [['_ip_csrf' => [self::TOKEN]], ['ip_csrf_cookie' => self::TOKEN]],
            'cookie is an array'          => [['_ip_csrf' => self::TOKEN], ['ip_csrf_cookie' => [self::TOKEN]]],
        ];
    }

    #[Test]
    #[DataProvider('rejectedRequests')]
    public function it_rejects_every_malformed_or_mismatching_token_pair(array $get, array $cookies): void
    {
        /* Arrange */
        $this->setRequest(true, $get, $cookies);

        /* Act */
        $valid = verify_get_csrf_token();

        /* Assert */
        self::assertFalse($valid);
    }

    #[Test]
    public function it_accepts_the_identical_pair_as_a_positive_control(): void
    {
        /* Arrange */
        $this->setRequest(true, ['_ip_csrf' => self::TOKEN], ['ip_csrf_cookie' => self::TOKEN]);

        /* Act */
        $valid = verify_get_csrf_token();

        /* Assert */
        self::assertTrue($valid);
    }

    #[Test]
    public function it_trusts_every_request_when_csrf_protection_is_disabled(): void
    {
        /* Arrange */
        $this->setRequest(false, [], []);

        /* Act */
        $valid = verify_get_csrf_token();

        /* Assert */
        self::assertTrue($valid);
    }

    #[Test]
    public function it_reads_the_token_from_the_query_and_the_cookie_under_the_configured_names(): void
    {
        /* Arrange: decoys under the other name must not be picked up */
        $this->setRequest(
            true,
            ['x_tok'    => self::TOKEN, 'x_cookie' => 'decoy-from-query'],
            ['x_cookie' => self::TOKEN, 'x_tok' => 'decoy-from-cookie'],
            'x_tok',
            'x_cookie'
        );

        /* Act */
        $valid = verify_get_csrf_token();

        /* Assert */
        self::assertTrue($valid);
    }

    #[Test]
    public function it_does_not_look_for_the_token_in_the_cookie_jar_or_the_cookie_in_the_query(): void
    {
        /* Arrange: the matching value sits in the wrong place only */
        $this->setRequest(true, ['ip_csrf_cookie' => self::TOKEN], ['_ip_csrf' => self::TOKEN]);

        /* Act */
        $valid = verify_get_csrf_token();

        /* Assert */
        self::assertFalse($valid);
    }

    /**
     * CodeIgniter's own Security library prepends cookie_prefix to
     * csrf_cookie_name when it actually sets/reads the CSRF cookie
     * (CI_Security::__construct()). A genuine logout link must still
     * validate when a deployment sets a non-default cookie_prefix.
     */
    #[Test]
    public function it_accepts_a_valid_token_when_cookie_prefix_is_configured(): void
    {
        /* Arrange */
        $this->setRequest(true, ['_ip_csrf' => self::TOKEN], ['myapp_ip_csrf_cookie' => self::TOKEN], cookiePrefix: 'myapp_');

        /* Act */
        $valid = verify_get_csrf_token();

        /* Assert */
        self::assertTrue($valid, 'A token matching the prefixed cookie name must be accepted.');
    }

    #[Test]
    public function it_rejects_a_token_matching_only_the_unprefixed_cookie_name_when_cookie_prefix_is_configured(): void
    {
        /* Arrange: the cookie the real framework would never set under this name */
        $this->setRequest(true, ['_ip_csrf' => self::TOKEN], ['ip_csrf_cookie' => self::TOKEN], cookiePrefix: 'myapp_');

        /* Act */
        $valid = verify_get_csrf_token();

        /* Assert */
        self::assertFalse($valid, 'The unprefixed cookie name must not be treated as a match once cookie_prefix is set.');
    }

    private function setRequest(
        bool $enabled,
        array $get,
        array $cookies,
        string $tokenName = '_ip_csrf',
        string $cookieName = 'ip_csrf_cookie',
        string $cookiePrefix = ''
    ): void {
        $GLOBALS['unitCiConfig'] = array_merge($GLOBALS['unitCiConfig'] ?? [], [
            'csrf_protection'  => $enabled,
            'csrf_token_name'  => $tokenName,
            'csrf_cookie_name' => $cookieName,
            'cookie_prefix'    => $cookiePrefix,
        ]);
        $GLOBALS['unitCiInstance'] = new class ($get, $cookies) {
            public object $input;

            public function __construct(array $get, array $cookies)
            {
                $this->input = new class ($get, $cookies) {
                    public function __construct(private array $get, private array $cookies) {}

                    public function get(string $key): mixed
                    {
                        return $this->get[$key] ?? null;
                    }

                    public function cookie(string $key): mixed
                    {
                        return $this->cookies[$key] ?? null;
                    }
                };
            }
        };
    }
}
