<?php

namespace Tests\Unit\Security;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('security')]
class SumexSecurityTest extends TestCase
{
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
}
