<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Business logic and persistence for service records and service/client assignments.
 */
class ServicesService
{
    /**
     * @var CI_Controller
     */
    private $ci;

    public function __construct()
    {
        $this->ci = &get_instance();
    }

    public function normalizeId($id): int
    {
        if ( ! is_scalar($id)) {
            return 0;
        }

        $normalized_id = filter_var($id, FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => 1,
            ],
        ]);

        if ($normalized_id === false) {
            return 0;
        }

        return $normalized_id;
    }

    public function normalizePage($page): int
    {
        if ( ! is_scalar($page)) {
            return 0;
        }

        $normalized_page = filter_var($page, FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => 0,
            ],
        ]);

        if ($normalized_page === false) {
            return 0;
        }

        return $normalized_page;
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

    public function getPaginatedServices(Mdl_Services $services_model, string $base_url, int $page): array
    {
        $services_model->paginate($base_url, $page);

        return $services_model->result();
    }

    public function saveService($id, Mdl_Services $services_model): int
    {
        return (int) $services_model->save($id, $services_model->db_array());
    }

    public function prepareForm($id, Mdl_Services $services_model, bool $is_submit): bool
    {
        if ( ! $id || $is_submit) {
            return true;
        }

        return (bool) $services_model->prep_form($id);
    }

    public function assignServiceToClient(int $client_id, int $service_id): void
    {
        if ($client_id <= 0 || $service_id <= 0) {
            return;
        }

        if ($this->clientServiceLinkExists($client_id, $service_id)) {
            return;
        }

        $this->ci->db->insert('ip_client_services', [
            'client_id'  => $client_id,
            'service_id' => $service_id,
        ]);
    }

    public function deleteService(int $service_id, Mdl_Services $services_model): void
    {
        if ($service_id <= 0) {
            return;
        }

        $this->deleteClientLinksForService($service_id);
        $services_model->delete($service_id);
    }

    public function getServiceNamesByIds(array $service_ids): array
    {
        $service_ids = $this->normalizeIdList($service_ids);

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

    private function clientServiceLinkExists(int $client_id, int $service_id): bool
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

    private function deleteClientLinksForService(int $service_id): void
    {
        if ($service_id <= 0) {
            return;
        }

        $this->ci->db->where('service_id', $service_id);
        $this->ci->db->delete('ip_client_services');
    }

    private function normalizeIdList(array $ids): array
    {
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids, static function (int $id): bool {
            return $id > 0;
        });

        return array_values(array_unique($ids));
    }
}
