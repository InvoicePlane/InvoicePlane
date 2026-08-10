<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once APPPATH . 'helpers/file_security_helper.php';

class IntegrationClientRegistry
{
    private array $providers = [];

    public function __construct()
    {
        $this->loadProviders();
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

    private function loadProviders(): void
    {
        require_once APPPATH . 'modules/integrations/libraries/IntegrationClientInterface.php';
        require_once APPPATH . 'modules/integrations/libraries/ProviderResponseNormalizer.php';
        require_once APPPATH . 'modules/integrations/libraries/RemoteUrlGuard.php';
        require_once APPPATH . 'modules/integrations/libraries/IntegrationSettingsCipher.php';

        $clientPath = APPPATH . 'modules/integrations/libraries/providers/';

        $patterns = [
            $clientPath . '*Client.php',
            $clientPath . '*/*Client.php',
        ];

        $files = [];
        foreach ($patterns as $pattern) {
            $files = array_merge($files, glob($pattern) ?: []);
        }

        foreach ($files as $file) {
            $dir = dirname($file);
            foreach (glob($dir . '/*.php') as $depFile) {
                if ($depFile !== $file) {
                    require_once $depFile;
                }
            }
            foreach (glob($dir . '/Endpoints/*.php') as $endpointFile) {
                require_once $endpointFile;
            }

            require_once $file;

            $className = basename($file, '.php');

            if ( ! class_exists($className)) {
                continue;
            }

            if ( ! is_subclass_of($className, IntegrationClientInterface::class)) {
                continue;
            }

            if ( ! method_exists($className, 'clientCode')) {
                continue;
            }

            $clientCode = $className::clientCode();
            if (preg_match('/^[a-z][a-z0-9_-]*$/', $clientCode) !== 1) {
                throw new RuntimeException('Invalid e-invoicing provider code.');
            }

            if (isset($this->providers[$clientCode])) {
                throw new RuntimeException('Duplicate e-invoicing provider code: ' . $clientCode);
            }

            $this->providers[$clientCode] = $className;
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
