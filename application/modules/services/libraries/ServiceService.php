<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Business logic for service records and service/client assignments.
 */
class ServiceService
{
    /**
     * @var CI_Controller
     */
    private $ci;

    /**
     * @var ServiceRepository
     */
    private $repository;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->library('services/ServiceRepository', [], 'service_repository');
        $this->repository = $this->ci->service_repository;
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
        return $this->repository->clientExists($client_id);
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

        if ($this->repository->clientServiceLinkExists($client_id, $service_id)) {
            return;
        }

        $this->repository->linkServiceToClient($client_id, $service_id);
    }

    public function deleteService(int $service_id, Mdl_Services $services_model): void
    {
        if ($service_id <= 0) {
            return;
        }

        $this->repository->deleteClientLinksForService($service_id);
        $services_model->delete($service_id);
    }

    public function getServiceNamesByIds(array $service_ids): array
    {
        $service_ids = $this->normalizeIdList($service_ids);

        if ($service_ids === []) {
            return [];
        }

        return $this->repository->getServiceNamesByIds($service_ids);
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
