<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2024 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class Services.
 */
class Services extends Admin_Controller
{
    /**
     * Services constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_services');
        $this->load->library('services/ServiceService', [], 'service_service');
    }

    /**
     * @param int $page
     */
    public function index($page = 0)
    {
        $services = $this->service_service->getPaginatedServices(
            $this->mdl_services,
            site_url('services/index'),
            $this->service_service->normalizePage($page)
        );

        $this->layout->set('services', $services);
        $this->layout->buffer('content', 'services/index');
        $this->layout->render();
    }

    /**
     * @param null $id
     */
    public function form($id = null)
    {
        $id = $id === null ? null : $this->service_service->normalizeId($id);

        if ($id === 0) {
            show_404();
        }

        if ($this->input->post('btn_cancel')) {
            redirect('services');
        }

        if ( ! $this->mdl_services->run_validation()) {
            $this->render_form($id);

            return;
        }

        $this->service_service->saveService($id, $this->mdl_services);

        redirect('services');
    }

    /**
     * @param int $client_id
     * @param int|null $id
     */
    public function form_client($client_id, $id = null)
    {
        $client_id = $this->service_service->normalizeId($client_id);
        $id = $id === null ? null : $this->service_service->normalizeId($id);

        if ($id === 0) {
            show_404();
        }

        if ($client_id <= 0 || ! $this->service_service->clientExists($client_id)) {
            show_404();
        }

        if ($this->input->post('btn_cancel')) {
            redirect('clients/form/' . $client_id);
        }

        $this->layout->set([
            'client_id' => $client_id,
        ]);

        if ( ! $this->mdl_services->run_validation()) {
            $this->render_form($id);

            return;
        }

        $service_id = $this->service_service->saveService($id, $this->mdl_services);

        $this->service_service->assignServiceToClient($client_id, $service_id);

        redirect('clients/form/' . $client_id);
    }

    /**
     * @param int $id
     */
    public function delete($id)
    {
        $id = $this->service_service->normalizeId($id);

        if ($id <= 0) {
            show_404();
        }

        $this->service_service->deleteService($id, $this->mdl_services);
        redirect('services');
    }

    /**
     * @param int|null $id
     */
    private function render_form($id = null): void
    {
        if ( ! $this->service_service->prepareForm($id, $this->mdl_services, (bool) $this->input->post('btn_submit'))) {
            show_404();
        }

        $this->layout->buffer('content', 'services/form');
        $this->layout->render();
    }
}
