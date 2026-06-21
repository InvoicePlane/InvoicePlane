<?php

defined('BASEPATH') or exit('No direct script access allowed');

class IntegrationClientRegistry
{
    private array $providers = [];

    public function __construct()
    {
        $this->loadProviders();
    }

    public function getClient(string $clientCode): IntegrationClientInterface
    {
        if (empty($this->providers[$clientCode])) {
            throw new RuntimeException('Unknown e-invoicing provider: ' . $clientCode);
        }

        $clientClass = $this->providers[$clientCode];

        return new $clientClass();
    }

    public function all(): array
    {
        return $this->providers;
    }

    private function loadProviders(): void
    {
        require_once APPPATH . 'modules/integrations/libraries/IntegrationClientInterface.php';

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

            if (!class_exists($className)) {
                continue;
            }

            if (!is_subclass_of($className, IntegrationClientInterface::class)) {
                continue;
            }

            if (!method_exists($className, 'clientCode')) {
                continue;
            }

            $this->providers[$className::clientCode()] = $className;
        }
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
                    'eInvoice provider already registered: ' . $clientCode
                );
                continue;
            }

            $settings = [];

            if (method_exists($clientClass, 'defaultSettings')) {
                $settings = $clientClass::defaultSettings();
            }

            $CI->db->insert('ip_merchant_clients', [
                'merchant_type' => $clientCode,
                'label' => $clientClass::clientName(),
                'enabled' => 0,
                'auth_type' => $this->guessAuthType($settings),
                'settings_json' => json_encode($settings),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function guessAuthType(array $settings): string
    {
        if (isset($settings['client_id']) || isset($settings['client_secret'])) {
            return 'oauth2';
        }

        if (isset($settings['access_token']) || isset($settings['api_key'])) {
            return 'api_key';
        }

        return 'none';
    }
}

