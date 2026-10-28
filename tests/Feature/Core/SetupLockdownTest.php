<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Setup;
use Tests\AbstractTestCase;

/**
 * GHSA-pp5w-98m2-gvc8: the setup wizard (which reaches Mdl_setup::upgrade_tables()) must be
 * unreachable once either lock is on: SETUP_COMPLETED=true or DISABLE_SETUP=true.
 *
 * SetupControllerTest only walks the GET routes with no wizard state. These tests drive the
 * routes an attacker actually uses: POSTs that advance the wizard, and requests that already
 * carry the wizard's session state. Each negative case has an unlocked positive control that
 * proves the same request does change the wizard state when the lock is off.
 */
#[CoversClass(Setup::class)]
#[Group('feature')]
#[Group('security')]
final class SetupLockdownTest extends AbstractTestCase
{
    #[Test]
    public function it_does_not_advance_the_wizard_past_upgrade_tables_when_setup_is_completed(): void
    {
        /* Arrange: the session state an attacker reaches after detecting an existing install */
        $this->withEnvironment(['SETUP_COMPLETED' => 'true', 'DISABLE_SETUP' => 'false']);
        $this->sessionData = ['install_step' => 'upgrade_tables', 'is_upgrade' => true];

        /* Act */
        $response = $this->post('/setup/upgrade_tables', ['btn_continue' => '1']);

        /* Assert */
        $this->assertResponseStatusCode($response, 403);
        self::assertSame(
            'upgrade_tables',
            $response->sessionValue('install_step'),
            'A locked setup must not move the wizard to its next step.'
        );
    }

    #[Test]
    public function it_refuses_every_wizard_route_when_setup_is_disabled_even_if_it_is_not_completed(): void
    {
        /* Arrange: the documented "security purposes" flag alone, SETUP_COMPLETED left at its default */
        $this->withEnvironment(['SETUP_COMPLETED' => 'false', 'DISABLE_SETUP' => 'true']);
        $this->sessionData = ['install_step' => 'upgrade_tables', 'is_upgrade' => true];
        $versionsBefore    = $this->databaseCount('ip_versions');
        $settingsBefore    = $this->databaseCount('ip_settings');

        /* Act */
        $upgrade   = $this->get('/setup/upgrade_tables');
        $language  = $this->get('/setup/language');
        $configure = $this->post('/setup/configure_database', ['btn_continue' => '1']);

        /* Assert */
        $this->assertResponseStatusCode($upgrade, 403);
        $this->assertResponseStatusCode($language, 403);
        $this->assertResponseStatusCode($configure, 403);
        self::assertSame($versionsBefore, $this->databaseCount('ip_versions'), 'No migration may be recorded.');
        self::assertSame($settingsBefore, $this->databaseCount('ip_settings'), 'No default setting may be (re)installed.');
    }

    #[Test]
    public function it_advances_the_wizard_past_upgrade_tables_when_setup_is_unlocked(): void
    {
        /* Arrange: identical request to the locked case, lock off (positive control) */
        $this->withEnvironment(['SETUP_COMPLETED' => 'false', 'DISABLE_SETUP' => 'false']);
        $this->sessionData = ['install_step' => 'upgrade_tables', 'is_upgrade' => true];

        /* Act */
        $response = $this->post('/setup/upgrade_tables', ['btn_continue' => '1']);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'setup/calculation_info');
        self::assertSame('calculation_info', $response->sessionValue('install_step'));
    }

    #[Test]
    public function it_does_not_run_the_upgrade_when_setup_is_completed_and_the_wizard_state_is_primed(): void
    {
        /* Arrange */
        $this->withEnvironment(['SETUP_COMPLETED' => 'true', 'DISABLE_SETUP' => 'false']);
        $this->sessionData = ['install_step' => 'upgrade_tables', 'is_upgrade' => true];
        $versionsBefore    = $this->databaseCount('ip_versions');
        $settingsBefore    = $this->databaseCount('ip_settings');

        /* Act: GET /setup/upgrade_tables is the request that executes Mdl_setup::upgrade_tables() */
        $response = $this->get('/setup/upgrade_tables');

        /* Assert */
        $this->assertResponseStatusCode($response, 403);
        self::assertSame($versionsBefore, $this->databaseCount('ip_versions'), 'No migration may be recorded.');
        self::assertSame($settingsBefore, $this->databaseCount('ip_settings'), 'No default setting may be (re)installed.');
    }

    #[Test]
    public function it_does_not_start_the_wizard_when_setup_is_completed(): void
    {
        /* Arrange */
        $this->withEnvironment(['SETUP_COMPLETED' => 'true', 'DISABLE_SETUP' => 'false']);

        /* Act: the first wizard POST stores the language and sets install_step */
        $locked = $this->post('/setup/language', ['btn_continue' => '1', 'ip_lang' => 'english']);

        /* Assert */
        $this->assertResponseStatusCode($locked, 403);
        self::assertNull($locked->sessionValue('install_step'), 'A locked setup must not initialise wizard state.');
    }

    #[Test]
    public function it_starts_the_wizard_when_setup_is_unlocked(): void
    {
        /* Arrange: positive control for the locked case above */
        $this->withEnvironment(['SETUP_COMPLETED' => 'false', 'DISABLE_SETUP' => 'false']);

        /* Act */
        $response = $this->post('/setup/language', ['btn_continue' => '1', 'ip_lang' => 'english']);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'setup/prerequisites');
        self::assertSame('prerequisites', $response->sessionValue('install_step'));
    }

    #[Test]
    public function it_tells_the_visitor_why_the_setup_is_blocked(): void
    {
        /* Arrange */
        $this->withEnvironment(['SETUP_COMPLETED' => 'true', 'DISABLE_SETUP' => 'false']);

        /* Act */
        $response = $this->get('/setup/language');

        /* Assert */
        $this->assertResponseStatusCode($response, 403);
        $this->assertResponseBodyContains($response, 'installation has already been completed');
        $this->assertResponseBodyNotContains($response, 'btn_continue');
    }

    #[Test]
    public function it_locks_the_setup_for_every_truthy_spelling_of_setup_completed(): void
    {
        foreach (['1', 'TRUE', 'yes', 'on'] as $value) {
            /* Arrange */
            $this->withEnvironment(['SETUP_COMPLETED' => $value, 'DISABLE_SETUP' => 'false']);

            /* Act */
            $response = $this->get('/setup/language');

            /* Assert */
            self::assertSame(
                403,
                $response->statusCode(),
                "SETUP_COMPLETED={$value} is a truthy env_bool() value and must lock the setup."
            );
        }
    }

    #[Test]
    public function it_leaves_the_setup_open_for_every_falsy_spelling_of_setup_completed(): void
    {
        foreach (['0', 'no', 'off', 'false', ''] as $value) {
            /* Arrange */
            $this->withEnvironment(['SETUP_COMPLETED' => $value, 'DISABLE_SETUP' => 'false']);

            /* Act */
            $response = $this->get('/setup/language');

            /* Assert: a fresh install must stay reachable */
            self::assertSame(
                200,
                $response->statusCode(),
                "SETUP_COMPLETED='{$value}' is falsy and must not lock a fresh install."
            );
            $this->assertResponseBodyContains($response, 'btn_continue');
        }
    }
}
