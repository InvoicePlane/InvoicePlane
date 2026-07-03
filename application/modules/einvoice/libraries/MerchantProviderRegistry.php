<?php

defined('BASEPATH') || exit('No direct script access allowed');

class MerchantProviderRegistry
{
    private array $providers = [];

    public function __construct()
    {
        $this->loadProviders();
    }

    public function getProvider(string $providerCode): MerchantProviderInterface
    {
        if (empty($this->providers[$providerCode])) {
            throw new RuntimeException('Unknown e-invoicing provider: ' . $providerCode);
        }

        $providerClass = $this->providers[$providerCode];

        return new $providerClass();
    }

    public function all(): array
    {
        return $this->providers;
    }

    public function syncDatabaseProviders(): void
    {
        $CI = &get_instance();

        foreach ($this->providers as $providerCode => $providerClass) {
            $existing = $CI->db
                ->where('merchant_type', $providerCode)
                ->get('ip_merchant_clients')
                ->row_array();

            if ($existing) {
                log_message(
                    'debug',
                    'eInvoice provider already registered: ' . $providerCode
                );
                continue;
            }

            $settings = [];

            if (method_exists($providerClass, 'defaultSettings')) {
                $settings = $providerClass::defaultSettings();
            }

            $CI->db->insert('ip_merchant_clients', [
                'merchant_type' => $providerCode,
                'label'         => $providerClass::providerName(),
                'enabled'       => 0,
                'auth_type'     => $this->guessAuthType($settings),
                'settings_json' => json_encode($settings),
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function loadProviders(): void
    {
        require_once APPPATH . 'modules/einvoice/libraries/MerchantProviderInterface.php';

        $providerPath = APPPATH . 'modules/einvoice/libraries/providers/';

        foreach (glob($providerPath . '*Provider.php') as $file) {
            require_once $file;

            $className = basename($file, '.php');

            if ( ! class_exists($className)) {
                continue;
            }

            if ( ! is_subclass_of($className, MerchantProviderInterface::class)) {
                continue;
            }

            if ( ! method_exists($className, 'providerCode')) {
                continue;
            }

            $this->providers[$className::providerCode()] = $className;
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
