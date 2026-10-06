<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * The unlocked setup wizard, step by step. Only steps that do not rewrite
 * ipconfig.php or the schema are driven here (table install/upgrade is covered
 * against a scratch database by SetupModelInstallTest); the configuration file
 * is hash-checked around every database-configuration POST.
 */
#[Group('setup')]
final class SetupWizardFlowTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withEnvironment(['SETUP_COMPLETED' => 'false', 'DISABLE_SETUP' => 'false']);
    }

    #[Test]
    public function it_lists_the_available_languages_on_the_first_step(): void
    {
        $response = $this->get('/setup/language');

        $this->assertResponseOk($response);
        $this->assertResponseBodyContains($response, 'english');
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_moves_to_prerequisites_after_choosing_a_language(): void
    {
        $response = $this->post('/setup/language', ['btn_continue' => '1', 'ip_lang' => 'dutch']);

        $this->assertResponseRedirectsToRoute($response, 'setup/prerequisites');
        self::assertSame('prerequisites', $response->sessionValue('install_step'));
        self::assertSame('dutch', $response->sessionValue('ip_lang'), 'The chosen language must be stored for the later steps.');
    }

    #[Test]
    public function it_sends_the_root_of_the_wizard_to_the_language_step(): void
    {
        $response = $this->get('/setup');

        $this->assertResponseRedirectsToRoute($response, 'setup/language');
        self::assertNull($response->sessionValue('install_step'), 'Entering the wizard must not skip a step.');
    }

    #[Test]
    public function it_bounces_prerequisites_back_to_language_without_the_step_in_session(): void
    {
        $response = $this->get('/setup/prerequisites');

        $this->assertResponseRedirectsToRoute($response, 'setup/language');
        self::assertNotSame('configure_database', $response->sessionValue('install_step'));
        self::assertSame('', $response->body(), 'A bounced step must not render the prerequisites page.');
    }

    #[Test]
    public function it_reports_the_php_version_and_writable_checks_on_prerequisites(): void
    {
        $this->sessionData = (['install_step' => 'prerequisites', 'ip_lang' => 'english']);

        $response = $this->get('/setup/prerequisites');

        $this->assertResponseOk($response);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseBodyContains($response, 'btn_continue');
    }

    #[Test]
    public function it_advances_from_prerequisites_to_database_configuration(): void
    {
        $this->sessionData = (['install_step' => 'prerequisites', 'ip_lang' => 'english']);

        $response = $this->post('/setup/prerequisites', ['btn_continue' => '1']);

        $this->assertResponseRedirectsToRoute($response, 'setup/configure_database');
        self::assertSame('configure_database', $response->sessionValue('install_step'));
    }

    #[Test]
    #[DataProvider('guardedSteps')]
    public function it_sends_every_later_step_back_to_prerequisites_when_reached_out_of_order(string $route): void
    {
        $this->sessionData = (['install_step' => 'language', 'ip_lang' => 'english']);

        $response = $this->get($route);

        $this->assertResponseRedirectsToRoute($response, 'setup/prerequisites');
        self::assertSame('language', $response->sessionValue('install_step'), 'An out-of-order request must not advance the wizard.');
        self::assertSame('', $response->body(), 'An out-of-order request must not render the step.');
    }

    /** @return array<string, array{string}> */
    public static function guardedSteps(): array
    {
        return [
            'configure_database' => ['/setup/configure_database'],
            'install_tables'     => ['/setup/install_tables'],
            'upgrade_tables'     => ['/setup/upgrade_tables'],
            'create_user'        => ['/setup/create_user'],
            'calculation_info'   => ['/setup/calculation_info'],
            'complete'           => ['/setup/complete'],
        ];
    }

    #[Test]
    public function it_shows_the_database_check_on_the_configure_step(): void
    {
        $this->sessionData = (['install_step' => 'configure_database', 'ip_lang' => 'english']);

        $response = $this->get('/setup/configure_database');

        $this->assertResponseOk($response);
        $this->assertResponseBodyContains($response, 'The database is successfully configured.');
        $this->assertResponseBodyContains($response, 'name="btn_continue"');
    }

    #[Test]
    public function it_treats_an_existing_versions_table_as_an_upgrade(): void
    {
        $this->sessionData = (['install_step' => 'configure_database', 'ip_lang' => 'english']);

        $response = $this->post('/setup/configure_database', ['btn_continue' => '1']);

        $this->assertResponseRedirectsToRoute($response, 'setup/upgrade_tables');
        self::assertSame('upgrade_tables', $response->sessionValue('install_step'));
        self::assertTrue($response->sessionValue('is_upgrade'), 'An existing ip_versions table must flag the run as an upgrade.');
    }

    #[Test]
    public function it_treats_a_missing_versions_table_as_a_fresh_install(): void
    {
        $this->sessionData = ['install_step' => 'configure_database', 'ip_lang' => 'english'];
        $this->databaseRunScript('RENAME TABLE `ip_versions` TO `ip_versions_hidden`');

        try {
            $response = $this->post('/setup/configure_database', ['btn_continue' => '1']);
        } finally {
            $this->databaseRunScript('RENAME TABLE `ip_versions_hidden` TO `ip_versions`');
        }

        $this->assertResponseRedirectsToRoute($response, 'setup/install_tables');
        self::assertSame('install_tables', $response->sessionValue('install_step'));
        self::assertNull($response->sessionValue('is_upgrade'));
    }

    #[Test]
    #[DataProvider('rejectedDatabaseConfigurations')]
    public function it_rejects_unsafe_database_settings_without_touching_ipconfig(array $post, string $message): void
    {
        $this->sessionData = (['install_step' => 'configure_database', 'ip_lang' => 'english']);
        $before = hash_file('sha256', IPCONFIG_FILE);

        $response = $this->post('/setup/configure_database', $post + [
            'db_hostname' => 'mariadb',
            'db_username' => 'root',
            'db_password' => 'x',
            'db_database' => 'invoiceplane',
            'db_port'     => '3306',
        ]);

        $this->assertResponseOk($response);
        $this->assertResponseBodyContains($response, htmlspecialchars($message, ENT_QUOTES));
        self::assertSame($before, hash_file('sha256', IPCONFIG_FILE), 'ipconfig.php must not be rewritten for a rejected input.');
    }

    /** @return array<string, array{array<string, string>, string}> */
    public static function rejectedDatabaseConfigurations(): array
    {
        return [
            'hostname with shell chars' => [['db_hostname' => 'db;rm -rf /'], 'Invalid hostname format'],
            'username with a newline'   => [['db_username' => "root\nDB_X=1"], 'Invalid'],
            'password with a quote'     => [['db_password' => "pa'ss"], "contains a single quote"],
            'database with a slash'     => [['db_database' => '../etc'], 'Invalid database name format'],
            'port out of range'         => [['db_port' => '70000'], 'Invalid port'],
            'port not numeric'          => [['db_port' => 'abc'], 'Invalid port'],
        ];
    }

    #[Test]
    public function it_continues_to_create_user_after_a_fresh_table_install(): void
    {
        $this->sessionData = (['install_step' => 'upgrade_tables', 'ip_lang' => 'english']);

        $response = $this->post('/setup/upgrade_tables', ['btn_continue' => '1']);

        $this->assertResponseRedirectsToRoute($response, 'setup/create_user');
        self::assertSame('create_user', $response->sessionValue('install_step'));
    }

    #[Test]
    public function it_skips_user_creation_when_upgrading(): void
    {
        $this->sessionData = (['install_step' => 'upgrade_tables', 'ip_lang' => 'english', 'is_upgrade' => true]);

        $response = $this->post('/setup/upgrade_tables', ['btn_continue' => '1']);

        $this->assertResponseRedirectsToRoute($response, 'setup/calculation_info');
        self::assertSame('calculation_info', $response->sessionValue('install_step'));
    }

    #[Test]
    public function it_continues_from_install_tables_to_upgrade_tables(): void
    {
        $this->sessionData = (['install_step' => 'install_tables', 'ip_lang' => 'english']);

        $response = $this->post('/setup/install_tables', ['btn_continue' => '1']);

        $this->assertResponseRedirectsToRoute($response, 'setup/upgrade_tables');
        self::assertSame('upgrade_tables', $response->sessionValue('install_step'));
    }

    #[Test]
    public function it_continues_from_calculation_info_to_complete(): void
    {
        $this->sessionData = (['install_step' => 'calculation_info', 'ip_lang' => 'english']);

        $response = $this->post('/setup/calculation_info', ['btn_continue' => '1']);

        $this->assertResponseRedirectsToRoute($response, 'setup/complete');
        self::assertSame('complete', $response->sessionValue('install_step'));
    }
}
