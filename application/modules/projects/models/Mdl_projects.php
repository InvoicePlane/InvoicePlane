<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

#[AllowDynamicProperties]
class Mdl_Projects extends Response_Model
{
    public $table = 'ip_projects';

    public $primary_key = 'ip_projects.project_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_projects.project_id');
    }

    public function default_join()
    {
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_projects.client_id', 'left');
    }

    public function get_latest()
    {
        $this->db->order_by('ip_projects.project_id', 'DESC');

        return $this;
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return [
            'project_name' => [
                'field' => 'project_name',
                'label' => trans('project_name'),
                'rules' => 'required',
            ],
            'client_id' => [
                'field' => 'client_id',
                'label' => trans('client'),
            ],
        ];
    }

    public function get_tasks($project_id)
    {
        $result = [];

        if ( ! $project_id) {
            return $result;
        }

        $this->load->model('tasks/mdl_tasks');
        $query = $this->mdl_tasks->where('ip_tasks.project_id', $project_id)->get();

        foreach ($query->result() as $row) {
            $result[] = $row;
        }

        return $result;
    }

    /**
     * Check if the current user has access to this project.
     *
     * Security: Prevents IDOR vulnerabilities for project access by verifying
     * the user can access the project's associated client.
     *
     * @param int $project_id The project ID to check
     *
     * @return bool True if user has access, false otherwise
     */
    public function can_user_access($project_id)
    {
        $CI = & get_instance();

        // Normalize to integer to prevent type juggling
        $project_id = (int) $project_id;

        // Admin users (type 1) have access to all projects
        if ((int) $CI->session->userdata('user_type') === 1) {
            return true;
        }

        // For other user types, check if they have access to the project's client
        $project = $this->get_by_id($project_id);

        if ( ! $project) {
            return false;
        }

        // Check if user has access to the project's client
        $this->load->model('clients/mdl_clients');

        return $this->mdl_clients->can_user_access((int) $project->client_id);
    }
}
