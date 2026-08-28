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
class Users extends Admin_Controller
{
    /**
     * Users constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_users');
    }

    /**
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_users->paginate(site_url('users/index'), $page);
        $users = $this->mdl_users->result();

        $this->layout->set(
            [
                'filter_display'     => true,
                'filter_placeholder' => trans('filter_users'),
                'filter_method'      => 'filter_users',
                'users'              => $users,
                'user_types'         => $this->mdl_users->user_types(),
            ]
        );
        $this->layout->buffer('content', 'users/index');
        $this->layout->render();
    }

    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('users');
        }

        if ($this->mdl_users->run_validation(($id) ? 'validation_rules_existing' : 'validation_rules')) {
            $db_array      = $this->mdl_users->db_array();
            $requested_type = (int) $this->input->post('user_type');
            $current_user_id = (string) $this->session->userdata('user_id');
            $is_self_edit = $id && (string) $id === $current_user_id;

            // Only allow user_type changes through explicit authorization:
            // - New user creation: set the requested type
            // - Admin editing another user: set the requested type and invalidate sessions
            // - User editing themselves: do not allow type changes (prevents self-escalation)
            $old_user = $id ? $this->mdl_users->get_by_id($id) : null;
            $role_changed = false;

            if ( ! $is_self_edit) {
                if ( ! $old_user || (int) $old_user->user_type !== $requested_type) {
                    $db_array['user_type'] = $requested_type;
                    $role_changed = true;
                }
            }

            // Also detect if user is being deactivated; invalidate their sessions
            // so the change takes effect immediately.
            $requested_active = isset($db_array['user_active']) ? (int) $db_array['user_active'] : ($old_user ? (int) $old_user->user_active : 1);
            if ($old_user && (int) $old_user->user_active !== $requested_active && $requested_active === 0) {
                $role_changed = true;
            }

            $id = $this->mdl_users->save($id, $db_array);

            if ($old_user && $role_changed) {
                $this->invalidate_user_sessions($id);
            }

            $this->load->model('custom_fields/mdl_user_custom');
            $this->mdl_user_custom->save_custom($id, $this->input->post('custom'));

            // Update the session details if the logged in user edited his account
            if ($this->session->userdata('user_id') == $id) {
                $new_details = $this->mdl_users->get_by_id($id);

                $session_data = [
                    'user_type'     => $new_details->user_type,
                    'user_id'       => $new_details->user_id,
                    'user_name'     => $new_details->user_name,
                    'user_email'    => $new_details->user_email,
                    'user_company'  => $new_details->user_company,
                    'user_language' => $new_details->user_language ?? 'system',
                ];

                $this->session->set_userdata($session_data);
            }

            $this->session->unset_userdata('user_clients');

            redirect('users');
        }

        if ($id && ! $this->input->post('btn_submit')) {
            if ( ! $this->mdl_users->prep_form($id)) {
                show_404();
            }

            $this->load->model('custom_fields/mdl_user_custom');

            $user_custom = $this->mdl_user_custom->where('user_id', $id)->get();

            if ($user_custom->num_rows()) {
                $user_custom = $user_custom->row();

                unset($user_custom->user_id, $user_custom->user_custom_id);

                foreach ($user_custom as $key => $val) {
                    $this->mdl_users->set_form_value('custom[' . $key . ']', $val);
                }
            }
        } elseif ($this->input->post('btn_submit')) {
            if ($this->input->post('custom')) {
                foreach ($this->input->post('custom') as $key => $val) {
                    $this->mdl_users->set_form_value('custom[' . $key . ']', $val);
                }
            }
        }

        $this->load->helper(['custom_values', 'e-invoice']);
        $this->load->model(
            [
                'user_clients/mdl_user_clients',
                'clients/mdl_clients',
                'custom_fields/mdl_custom_fields',
                'custom_fields/mdl_user_custom',
                'custom_values/mdl_custom_values',
            ]
        );

        $custom_fields['ip_user_custom'] = $this->mdl_custom_fields->by_table('ip_user_custom')->get()->result();
        $custom_values                   = [];
        foreach ($custom_fields['ip_user_custom'] as $custom_field) {
            if (in_array($custom_field->custom_field_type, $this->mdl_custom_values->custom_value_fields())) {
                $values                                        = $this->mdl_custom_values->get_by_fid($custom_field->custom_field_id)->result();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }

        $fields = $this->mdl_user_custom->get_by_useid($id);

        foreach ($custom_fields['ip_user_custom'] as $cfield) {
            foreach ($fields as $fvalue) {
                if ($fvalue->user_custom_fieldid == $cfield->custom_field_id) {
                    // TODO: Hackish, may need a better optimization
                    $this->mdl_users->set_form_value(
                        'custom[' . $cfield->custom_field_id . ']',
                        $fvalue->user_custom_fieldvalue
                    );
                    break;
                }
            }
        }

        // Need in remittance text tags selector (template-tags-invoices)
        $custom_fields['ip_invoice_custom'] = $this->mdl_custom_fields->by_table('ip_invoice_custom')->get()->result();

        $this->layout->set(
            [
                'id'               => $id,
                'user_types'       => $this->mdl_users->user_types(),
                'user_clients'     => $this->mdl_user_clients->where('ip_user_clients.user_id', $id)->get()->result(),
                'custom_fields'    => $custom_fields,
                'custom_values'    => $custom_values,
                'countries'        => get_country_list(trans('cldr')),
                'selected_country' => $this->mdl_users->form_value('user_country') ?: get_setting('default_country'),
                'clients'          => $this->mdl_clients->where('client_active', 1)->get()->result(),
                'languages'        => get_available_languages(),
                'einvoicing'       => get_setting('einvoicing'),
            ]
        );

        $this->layout->buffer('content', 'users/form');
        $this->layout->render();
    }

    /**
     * @param $user_id
     */
    public function change_password(string $user_id)
    {
        $acting_user_id = (string) $this->session->userdata('user_id');

        if ((string) $user_id !== $acting_user_id && $acting_user_id !== '1') {
            show_error(trans('access_denied'), 403);

            return;
        }

        if ($this->input->post('btn_cancel')) {
            redirect('users');
        }

        if ($this->mdl_users->run_validation('validation_rules_change_password')) {
            $this->mdl_users->save_change_password($user_id, $this->input->post('user_password'));
            redirect('users/form/' . $user_id);
        }

        $this->layout->buffer('content', 'users/form_change_password');
        $this->layout->render();
    }

    /**
     * Invalidate all sessions for a user when their role or active status changes,
     * forcing immediate revocation of any stale privileged sessions.
     *
     * @param string|int $user_id
     */
    private function invalidate_user_sessions($user_id): void
    {
        $this->load->model('sessions/mdl_sessions');
        $this->mdl_sessions->invalidate_user_sessions($user_id);
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        if ( ! $this->ensure_valid_post_request('users/index')) {
            return;
        }

        if ($id != 1) {
            $this->mdl_users->delete($id);
        }

        redirect('users');
    }

    /**
     * @param $user_id
     * @param $user_client_id
     */
    public function delete_user_client(string $user_id, $user_client_id)
    {
        if ( ! $this->ensure_valid_post_request('users/form/' . $user_id)) {
            return;
        }

        $this->load->model('mdl_user_clients');

        if ( ! $this->mdl_user_clients->can_user_manage($user_client_id)) {
            show_error(trans('access_denied'), 403);
        }

        $user_client = $this->mdl_user_clients->get_by_id($user_client_id);
        if ( ! $user_client) {
            show_404();
        }

        $this->mdl_user_clients->delete($user_client_id);

        redirect('users/form/' . $user_id);
    }
}
