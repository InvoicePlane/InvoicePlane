<?php

defined('BASEPATH') or exit('No direct script access allowed');

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
        return json_decode($client['settings_json'] ?? '{}', true) ?: [];
    }

    public function disable_all_except(int $id): void
    {
        $this->db
            ->where('id !=', $id)
            ->update('ip_merchant_clients', [
                'enabled'    => 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
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

