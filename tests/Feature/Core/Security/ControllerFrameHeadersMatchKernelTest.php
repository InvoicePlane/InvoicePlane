<?php

namespace Tests\Feature\Core\Security;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\CapturesControllerOutputHeaders;

/**
 * bootstrap/kernel.php validates X_FRAME_OPTIONS (SAMEORIGIN|DENY, anything else falls back to
 * SAMEORIGIN) and derives a matching CSP. Base_Controller and Guest_Controller queue the same
 * headers again with replace=TRUE, so on a real web request whatever they queue wins. They must
 * queue the kernel's validated values, not rebuild them from the raw environment value:
 *   - X_FRAME_OPTIONS=ALLOWALL  would send "X-Frame-Options: ALLOWALL" (invalid, ignored by browsers)
 *   - X_FRAME_OPTIONS=DENY      would send CSP frame-ancestors 'self' although the operator asked for 'none'.
 */
#[Group('security')]
final class ControllerFrameHeadersMatchKernelTest extends AbstractTestCase
{
    use CapturesControllerOutputHeaders;

    /** @return array<string, array{0: string, 1: string, 2: string}> */
    public static function configuredValues(): array
    {
        return [
            'DENY'           => ['DENY', 'DENY', "'none'"],
            'lowercase deny' => ['deny', 'DENY', "'none'"],
            'ALLOWALL'       => ['ALLOWALL', 'SAMEORIGIN', "'self'"],
            'ALLOW-FROM'     => ['ALLOW-FROM https://evil.example', 'SAMEORIGIN', "'self'"],
        ];
    }

    #[Test]
    #[DataProvider('configuredValues')]
    public function it_never_lets_the_guest_controller_weaken_what_the_kernel_derived(string $configured, string $expectedOption, string $expectedAncestors): void
    {
        /* Arrange */
        $this->seedLoggedInGuest();
        $this->withEnvironment(['X_FRAME_OPTIONS' => $configured]);

        /* Act */
        $result = $this->requestCapturingControllerHeaders('GET', '/guest/invoices/status/open');

        /* Assert */
        self::assertSame(200, $result['status']);
        self::assertSame($expectedOption, $result['headers']['X-Frame-Options']);
        self::assertStringContainsString("frame-ancestors {$expectedAncestors};", (string) $result['headers']['Content-Security-Policy']);
    }

    #[Test]
    #[DataProvider('configuredValues')]
    public function it_never_lets_the_base_controller_weaken_what_the_kernel_derived(string $configured, string $expectedOption, string $expectedAncestors): void
    {
        /* Arrange */
        $this->actingAsGuest();
        $this->withEnvironment(['X_FRAME_OPTIONS' => $configured]);

        /* Act */
        $result = $this->requestCapturingControllerHeaders('GET', '/sessions/login');

        /* Assert */
        self::assertSame($expectedOption, $result['headers']['X-Frame-Options']);
        self::assertStringContainsString("frame-ancestors {$expectedAncestors};", (string) $result['headers']['Content-Security-Policy']);
    }
}
