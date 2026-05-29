<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Database access for service-related use cases.
 */
class ServiceRepository
{
    /**
     * @var CI_Controller
     */
    private $ci;

    public function __construct()
    {
        $this->ci = &get_instance();
    }

    public function clientExists(int $client_id): bool
    {
        if ($client_id <= 0) {
            return false;
        }

        return $this->ci->db
            ->select('client_id')
            ->where('client_id', $client_id)
            ->limit(1)
            ->get('ip_clients')
            ->row() !== null;
    }

    public function clientServiceLinkExists(int $client_id, int $service_id): bool
    {
        if ($client_id <= 0 || $service_id <= 0) {
            return false;
        }

        return $this->ci->db
            ->select('client_id')
            ->where('client_id', $client_id)
            ->where('service_id', $service_id)
            ->limit(1)
            ->get('ip_client_services')
            ->row() !== null;
    }

    public function linkServiceToClient(int $client_id, int $service_id): void
    {
        if ($client_id <= 0 || $service_id <= 0) {
            return;
        }

        $this->ci->db->insert('ip_client_services', [
            'client_id'  => $client_id,
            'service_id' => $service_id,
        ]);
    }

    public function deleteClientLinksForService(int $service_id): void
    {
        if ($service_id <= 0) {
            return;
        }

        $this->ci->db->where('service_id', $service_id);
        $this->ci->db->delete('ip_client_services');
    }

    public function getServiceNamesByIds(array $service_ids): array
    {
        if ($service_ids === []) {
            return [];
        }

        $services = $this->ci->db
            ->select('service_id, service_name')
            ->where_in('service_id', $service_ids)
            ->order_by('service_name')
            ->get('ip_services')
            ->result_array();

        return array_column($services, 'service_name', 'service_id');
    }
}
