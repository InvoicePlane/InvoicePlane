<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once APPPATH . 'modules/integrations/libraries/IntegrationSettingsCipher.php';

class Merchant_clients_model extends CI_Model
{
    public function get_all()
    {
        return $this->db
            ->order_by('id', 'ASC')
            ->get('ip_merchant_clients')
            ->result_array();
    }

    public function get_enabled_clients()
    {
        return $this->db
            ->where('enabled', 1)
            ->get('ip_merchant_clients')
            ->result_array();
    }

    public function get_by_id($id)
    {
        $row = $this->db
            ->where('id', (int) $id)
            ->get('ip_merchant_clients')
            ->row_array();

        return $row ?: null;
    }

    public function update_client($id, $data)
    {
        return $this->db
            ->where('id', (int) $id)
            ->update('ip_merchant_clients', $data);
    }

    public function get_settings($client)
    {
        $providerCode = $client['merchant_type'] ?? null;
        if ( ! is_string($providerCode) || $providerCode === '') {
            throw new RuntimeException('Provider code is required to decrypt integration settings.');
        }

        $cipher   = new IntegrationSettingsCipher();
        $stored   = $client['settings_json'] ?? null;
        $settings = $cipher->decrypt($stored, $providerCode);

        if ( ! $cipher->isEncrypted($stored) && isset($client['id'])) {
            $migrated = $this->db
                ->where('id', (int) $client['id'])
                ->update('ip_merchant_clients', [
                    'settings_json' => $cipher->encrypt($settings, $providerCode),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);

            if ( ! $migrated) {
                throw new RuntimeException('Unable to migrate provider settings to encrypted storage.');
            }
        }

        return $settings;
    }

    public function protect_settings(string $providerCode, array $settings): string
    {
        return (new IntegrationSettingsCipher())->encrypt($settings, $providerCode);
    }

    public function get_default_enabled()
    {
        return $this->db
            ->where('enabled', 1)
            ->order_by('id', 'ASC')
            ->limit(1)
            ->get('ip_merchant_clients')
            ->row_array();
    }
}
