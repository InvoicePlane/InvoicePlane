<?php

defined('BASEPATH') || exit('No direct script access allowed');

class IntegrationClientRegistry
{
    private static array $maps = [];

    private static int $scans = 0;

    private array $providers;

    public function __construct(?string $providersDirectory = null)
    {
        get_instance()->load->helper('file_security');

        $directory = rtrim($providersDirectory ?? APPPATH . 'modules/integrations/libraries/providers', '/');

        self::$maps[$directory] ??= $this->scan($directory);
        $this->providers = self::$maps[$directory];
    }

    public static function scanCount(): int
    {
        return self::$scans;
    }

    public static function flushCache(): void
    {
        self::$maps  = [];
        self::$scans = 0;
    }

    public function getClient(string $clientCode): IntegrationClientInterface
    {
        $clientClass = $this->getClientClass($clientCode);

        return new $clientClass();
    }

    public function getSettingsDefinition(string $clientCode): array
    {
        $clientClass = $this->getClientClass($clientCode);
        $defaults    = $clientClass::defaultSettings();
        $authType    = $clientClass::authType();

        if ( ! in_array($authType, ['none', 'oauth2', 'bearer', 'api_key'], true)) {
            throw new RuntimeException('Unsupported provider authentication type: ' . $clientCode);
        }

        require_once APPPATH . 'modules/integrations/libraries/IntegrationSettingsForm.php';

        return [
            'auth_type' => $authType,
            'defaults'  => $defaults,
            'schema'    => IntegrationSettingsForm::normalizeSchema(
                $clientClass::settingsSchema(),
                $defaults
            ),
        ];
    }

    public function all(): array
    {
        return $this->providers;
    }

    public function syncDatabaseProviders(): void
    {
        $CI = &get_instance();

        foreach ($this->providers as $clientCode => $clientClass) {
            $existing = $CI->db
                ->where('merchant_type', $clientCode)
                ->get('ip_merchant_clients')
                ->row_array();

            if ($existing) {
                log_message(
                    'debug',
                    'eInvoice provider already registered: ' . sanitize_for_logging($clientCode)
                );
                continue;
            }

            $definition = $this->getSettingsDefinition($clientCode);
            $settings   = (new IntegrationSettingsCipher())->encrypt($definition['defaults'], $clientCode);

            $CI->db->insert('ip_merchant_clients', [
                'merchant_type' => $clientCode,
                'label'         => $clientClass::clientName(),
                'enabled'       => 0,
                'auth_type'     => $definition['auth_type'],
                'settings_json' => $settings,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Index every IntegrationClientInterface implementation found in the providers directory by its
     * client code: providers/FooClient.php, or providers/Foo/FooClient.php with any helper classes
     * that live beside it (including Foo/Endpoints/).
     *
     * @return array<string, class-string<IntegrationClientInterface>>
     */
    private function scan(string $directory): array
    {
        self::$scans++;

        require_once APPPATH . 'modules/integrations/libraries/IntegrationClientInterface.php';
        require_once APPPATH . 'modules/integrations/libraries/ProviderResponseNormalizer.php';
        require_once APPPATH . 'modules/integrations/libraries/ProviderPing.php';
        require_once APPPATH . 'modules/integrations/libraries/RemoteUrlGuard.php';
        require_once APPPATH . 'modules/integrations/libraries/IntegrationSettingsCipher.php';
        require_once APPPATH . 'modules/integrations/libraries/IntegrationTransport.php';

        $files = array_merge(
            glob($directory . '/*Client.php') ?: [],
            glob($directory . '/*/*Client.php') ?: []
        );

        $providers = [];
        foreach ($files as $file) {
            $this->requireCompanions($directory, $file);
            require_once $file;

            $className = basename($file, '.php');
            if ( ! class_exists($className) || ! is_subclass_of($className, IntegrationClientInterface::class)) {
                continue;
            }

            $clientCode = $className::clientCode();
            if (preg_match('/^[a-z][a-z0-9_-]*$/', $clientCode) !== 1) {
                throw new RuntimeException('Invalid e-invoicing provider code.');
            }

            if (isset($providers[$clientCode])) {
                throw new RuntimeException('Duplicate e-invoicing provider code: ' . $clientCode);
            }

            $providers[$clientCode] = $className;
        }

        return $providers;
    }

    private function requireCompanions(string $root, string $file): void
    {
        $dir = dirname($file);
        if ($dir === $root) {
            return;
        }

        foreach (array_merge(glob($dir . '/*.php') ?: [], glob($dir . '/Endpoints/*.php') ?: []) as $companion) {
            if ($companion !== $file) {
                require_once $companion;
            }
        }
    }

    private function getClientClass(string $clientCode): string
    {
        if (empty($this->providers[$clientCode])) {
            throw new RuntimeException('Unknown e-invoicing provider: ' . $clientCode);
        }

        return $this->providers[$clientCode];
    }
}
