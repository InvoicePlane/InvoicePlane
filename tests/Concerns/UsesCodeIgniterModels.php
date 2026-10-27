<?php

namespace Tests\Concerns;

use RuntimeException;
use stdClass;

/**
 * Run real CodeIgniter models in the PHPUnit process against the test database.
 *
 * CI_Model::__get() resolves everything through get_instance(), and the unit harness already
 * exposes that as $GLOBALS['unitCiInstance']. This trait fills it with a genuine CI database
 * driver (the same Query Builder the app uses) plus a minimal loader, so a model can be
 * constructed and exercised directly - no HTTP round trip, and its lines show up in in-process
 * coverage. Use it for model logic; keep controllers on the request-subprocess path.
 */
trait UsesCodeIgniterModels
{
    private mixed $ciBackup = null;

    private ?object $ci = null;

    protected function ciDatabaseName(): string
    {
        return (string) env('DB_DATABASE');
    }

    protected function bootCodeIgniter(): object
    {
        if ($this->ci !== null) {
            return $this->ci;
        }

        require_once BASEPATH . 'core/Common.php';
        require_once BASEPATH . 'database/DB.php';
        require_once BASEPATH . 'core/Model.php';
        foreach (['MY_Model', 'Form_Validation_Model', 'Response_Model'] as $base) {
            if ( ! class_exists($base, false)) {
                require_once APPPATH . 'core/' . $base . '.php';
            }
        }

        $this->ciBackup = $GLOBALS['unitCiInstance'] ?? null;

        $ci     = new stdClass();
        $ci->db = DB([
            'dsn'      => '', 'hostname' => env('DB_HOSTNAME'), 'username' => env('DB_USERNAME'), 'password' => env('DB_PASSWORD'),
            'database' => $this->ciDatabaseName(), 'dbdriver' => 'mysqli', 'dbprefix' => '', 'pconnect' => false, 'db_debug' => true,
            'cache_on' => false, 'cachedir' => '', 'char_set' => 'utf8mb4', 'dbcollat' => 'utf8mb4_general_ci', 'swap_pre' => '',
            'encrypt'  => false, 'compress' => false, 'stricton' => false, 'failover' => [], 'save_queries' => false,
        ]);
        $ci->load = new class ($ci) {
            public function __construct(private object $ci) {}

            /** @param string|array<int,string> $models */
            public function model(string|array $models, ?string $alias = null): void
            {
                foreach ((array) $models as $path) {
                    $class                                             = $this->resolveModel($path);
                    $this->ci->{$alias ?? strtolower(basename($path))} = new $class();
                }
            }

            public function library(string $name): void
            {
                $this->ci->{$name} ??= new stdClass();
            }

            /** @return list<string> */
            public function get_package_paths(bool $include_base = false): array
            {
                return $include_base ? [APPPATH, BASEPATH] : [APPPATH];
            }

            /** @param string|array<int,string> $helpers */
            public function helper(string|array $helpers): void
            {
                foreach ((array) $helpers as $helper) {
                    $file = APPPATH . 'helpers/' . $helper . '_helper.php';
                    if (is_file($file)) {
                        require_once $file;
                    }
                }
            }

            private function resolveModel(string $path): string
            {
                $name = basename($path);
                foreach (glob(APPPATH . 'modules/*/models/' . ucfirst($name) . '.php') ?: [] as $file) {
                    require_once $file;

                    return $name;
                }

                throw new RuntimeException('Model not found: ' . $path);
            }
        };

        $GLOBALS['unitCiInstance'] = $this->ci = $ci;

        return $ci;
    }

    /** Instantiate a model by its module-relative file, e.g. "integrations/models/Merchant_responses_model". */
    protected function ciModel(string $relative): object
    {
        $this->bootCodeIgniter();

        $file = APPPATH . 'modules/' . $relative . '.php';
        if ( ! is_file($file)) {
            throw new RuntimeException('Model file not found: ' . $relative);
        }
        require_once $file;

        $class = basename($relative);

        return new $class();
    }

    protected function tearDownCodeIgniter(): void
    {
        if ($this->ci !== null) {
            $this->ci->db->close();
            $GLOBALS['unitCiInstance'] = $this->ciBackup;
            $this->ci                  = null;
        }
    }
}
