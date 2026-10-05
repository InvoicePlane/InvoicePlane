<?php

namespace Tests\Feature\Core\Setup;

use mysqli;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Concerns\UsesCodeIgniterModels;

/**
 * Mdl_setup against a throwaway schema: the real 000_1.0.0.sql install, every
 * later migration in order, the default data and default settings. A scratch
 * database keeps the shared invoiceplane_test schema untouched.
 */
#[Group('setup')]
final class SetupModelInstallTest extends TestCase
{
    use UsesCodeIgniterModels;

    private const SCRATCH = 'invoiceplane_setup_scratch';

    private ?object $db = null;

    private ?object $session = null;

    protected function setUp(): void
    {
        require_once BASEPATH . 'core/Common.php';
        require_once BASEPATH . 'database/DB.php';
        require_once BASEPATH . 'core/Model.php';
        require_once BASEPATH . 'helpers/directory_helper.php';
        require_once APPPATH . 'helpers/sql_helper.php';
        require_once APPPATH . 'helpers/ip_security_helper.php';
        require_once APPPATH . 'modules/setup/models/Mdl_setup.php';

        $admin = new mysqli(env('DB_HOSTNAME'), env('DB_USERNAME'), env('DB_PASSWORD'), '', (int) env('DB_PORT', 3306));
        $admin->query('DROP DATABASE IF EXISTS `' . self::SCRATCH . '`');
        self::assertTrue($admin->query('CREATE DATABASE `' . self::SCRATCH . '` CHARACTER SET utf8mb4'), 'The DB user must be allowed to CREATE DATABASE: ' . $admin->error);
        $admin->close();

        $this->session = new class {
            /** @var array<string, mixed> */
            public array $data = ['ip_lang' => 'english'];

            public function userdata(string $key): mixed
            {
                return $this->data[$key] ?? null;
            }

            public function set_userdata(string $key, mixed $value): void
            {
                $this->data[$key] = $value;
            }
        };

        $ci          = $this->bootCodeIgniter();
        $this->db    = $ci->db;
        $ci->session = $this->session;
    }

    protected function ciDatabaseName(): string
    {
        return self::SCRATCH;
    }

    protected function tearDown(): void
    {
        $this->tearDownCodeIgniter();
        $admin = new mysqli(env('DB_HOSTNAME'), env('DB_USERNAME'), env('DB_PASSWORD'), '', (int) env('DB_PORT', 3306));
        $admin->query('DROP DATABASE IF EXISTS `' . self::SCRATCH . '`');
        $admin->close();
    }

    #[Test]
    public function it_installs_the_base_schema_with_default_groups_and_payment_methods(): void
    {
        $model = new \Mdl_Setup();

        self::assertTrue($model->install_tables(), implode('; ', $model->errors));

        self::assertSame([], $model->errors);
        self::assertSame(
            ['Invoice Default', 'Quote Default'],
            array_column($this->db->order_by('invoice_group_id')->get('ip_invoice_groups')->result_array(), 'invoice_group_name')
        );
        self::assertSame(
            ['Cash', 'Credit Card'],
            array_column($this->db->order_by('payment_method_id')->get('ip_payment_methods')->result_array(), 'payment_method_name')
        );
        self::assertSame(1, $this->db->where('version_file', '000_1.0.0.sql')->count_all_results('ip_versions'));
    }

    #[Test]
    public function it_seeds_default_settings_without_overwriting_existing_ones(): void
    {
        $model = new \Mdl_Setup();
        $model->install_tables();
        $this->db->where('setting_key', 'currency_code')->update('ip_settings', ['setting_value' => 'EUR']);

        $model->upgrade_tables();

        self::assertSame(1, $this->db->where('setting_key', 'currency_code')->count_all_results('ip_settings'), 'A re-run must not duplicate a setting.');
        self::assertSame('EUR', $this->setting('currency_code'), 'An operator-chosen value must survive a re-run.');
        self::assertSame('english', $this->setting('default_language'));
        self::assertSame('0', $this->setting('invoice_reminders_enabled'));
        self::assertMatchesRegularExpression("/^[0-9a-f]{16}$/", $this->setting("cron_key"));
    }

    #[Test]
    public function it_applies_every_migration_file_exactly_once_in_order(): void
    {
        $model = new \Mdl_Setup();
        $model->install_tables();

        self::assertTrue($model->upgrade_tables(), implode('; ', $model->errors));

        $files = array_values(array_filter(scandir(APPPATH . 'modules/setup/sql'), static fn ($f) => str_ends_with($f, '.sql')));
        sort($files);
        $applied = array_column($this->db->order_by('version_id')->get('ip_versions')->result_array(), 'version_file');
        self::assertSame($files, $applied);

        $before = $this->db->count_all('ip_versions');
        self::assertTrue($model->upgrade_tables());
        self::assertSame($before, $this->db->count_all('ip_versions'), 'A second run must not re-apply anything.');
    }

    #[Test]
    public function it_converts_every_table_to_innodb(): void
    {
        $model = new \Mdl_Setup();
        $model->install_tables();
        $model->upgrade_tables();

        $myisam = $this->db->query(
            "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND ENGINE = 'MyISAM'"
        )->result_array();

        self::assertSame([], $myisam);
    }

    #[Test]
    public function it_converts_a_leftover_myisam_table_when_the_hook_runs_again(): void
    {
        $model = new \Mdl_Setup();
        $model->install_tables();
        $model->upgrade_tables();
        $this->db->query('CREATE TABLE ip_legacy_probe (id INT) ENGINE=MyISAM');
        self::assertTrue($model->upgrade_046_innodb_conversion());

        $engine = $this->db->query("SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ip_legacy_probe'")->row()->ENGINE;
        self::assertSame('InnoDB', $engine);
    }

    #[Test]
    public function it_records_an_error_when_a_migration_file_cannot_be_read(): void
    {
        $model = new \Mdl_Setup();

        $this->invoke($model, 'execute_contents', [false, '999_missing.sql']);

        self::assertCount(1, $model->errors);
        self::assertStringContainsString("'999_missing.sql'", $model->errors[0]);
        self::assertStringContainsString('boolean', $model->errors[0]);
    }

    #[Test]
    public function it_records_the_database_error_of_a_failing_statement_and_carries_on(): void
    {
        $model = new \Mdl_Setup();

        $this->invoke($model, 'execute_contents', ["SELECT * FROM ip_does_not_exist;\nCREATE TABLE ip_after (id INT);", 'bad.sql']);

        self::assertCount(1, $model->errors);
        self::assertStringContainsString('ip_does_not_exist', $model->errors[0]);
        self::assertSame(1, $this->db->query("SELECT COUNT(*) c FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ip_after'")->row()->c + 0);
    }

    #[Test]
    public function it_stores_the_error_count_with_the_version_row(): void
    {
        $model = new \Mdl_Setup();
        $model->install_tables();
        $model->errors = ['x', 'y'];

        $this->invoke($model, 'save_version', ['zzz_test.sql']);

        self::assertSame(2, (int) $this->db->where('version_file', 'zzz_test.sql')->get('ip_versions')->row()->version_sql_errors);
    }

    /** @param array<int, mixed> $args */
    private function invoke(object $obj, string $method, array $args): mixed
    {
        $m = new \ReflectionMethod($obj, $method);

        return $m->invoke($obj, ...$args);
    }

    private function setting(string $key): string
    {
        $row = $this->db->where('setting_key', $key)->get('ip_settings')->row();
        self::assertNotNull($row, "Setting {$key} missing.");

        return (string) $row->setting_value;
    }
}
