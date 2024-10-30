<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__, 2) . '/Enums/ClientTitleEnum.php';

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

<<<<<<< HEAD
#[AllowDynamicProperties]
=======
/**
 * Class Clients
 */
>>>>>>> 4c04c8ce (939: Prep for incoming merge-request)
class Clients extends Admin_Controller
{
    private const CLIENT_TITLE = 'client_title';

    /**
     * Clients constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_clients');
    }

    public function index()
    {
        // Display active clients by default
        redirect('clients/status/active');
    }

    /**
     * @param string $status
     * @param int $page
     */
    public function status($status = 'active', $page = 0)
    {
        if (is_numeric(array_search($status, ['active', 'inactive']))) {
            $function = 'is_' . $status;
            $this->mdl_clients->$function();
        }

        $this->mdl_clients->with_total_balance()->paginate(site_url('clients/status/' . $status), $page);
        $clients = $this->mdl_clients->result();

<<<<<<< HEAD
        $this->layout->set([
            'records'            => $clients,
            'filter_display'     => true,
            'filter_placeholder' => trans('filter_clients'),
            'filter_method'      => 'filter_clients',
        ]);
=======
        $this->layout->set(
            [
                'records' => $clients,
                'filter_display' => true,
                'filter_placeholder' => trans('filter_clients'),
                'filter_method' => 'filter_clients'
            ]
        );
>>>>>>> d2738fb2 (939: Prep for incoming merge-request)

        $this->layout->buffer('content', 'clients/index');
        $this->layout->render();
    }

    /**
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('clients');
        }

        $new_client = false;
        $this->filter_input();  // <<<--- filters _POST array for nastiness

        // Set validation rule based on is_update
        if ($this->input->post('is_update') == 0 && $this->input->post('client_name') != '') {
            $check = $this->db->get_where('ip_clients', [
                'client_name' => $this->input->post('client_name'),
                'client_surname' => $this->input->post('client_surname')
            ])->result();

            if (!empty($check)) {
                $this->session->set_flashdata('alert_error', trans('client_already_exists'));
                redirect('clients/form');
            } else {
                $new_client = true;
            }
        }

        if ($this->mdl_clients->run_validation()) {
            $client_title_custom = $this->input->post('client_title_custom');
            if ($client_title_custom !== '') {
                $_POST[self::CLIENT_TITLE] = $client_title_custom;
                $this->mdl_clients->set_form_value(self::CLIENT_TITLE, $client_title_custom);
            }
            $id = $this->mdl_clients->save($id);

            if ($new_client) {
                $this->load->model('user_clients/mdl_user_clients');
                $this->mdl_user_clients->get_users_all_clients();
            }

            $this->load->model('custom_fields/mdl_client_custom');
            $result = $this->mdl_client_custom->save_custom($id, $this->input->post('custom'));

            if ($result !== true) {
                $this->session->set_flashdata('alert_error', $result);
                $this->session->set_flashdata('alert_success', null);
                redirect('clients/form/' . $id);
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD


=======
>>>>>>> d2738fb2 (939: Prep for incoming merge-request)
=======

>>>>>>> cf5d220c (939: Just style fixes for now)
=======
>>>>>>> 6c379438 (939: Prep for incoming merge-request)
                return;
            }
            redirect('clients/view/' . $id);
        }

        if ($id && ! $this->input->post('btn_submit')) {
            if ( ! $this->mdl_clients->prep_form($id)) {
                show_404();
            }

            $this->load->model('custom_fields/mdl_client_custom');
            $this->mdl_clients->set_form_value('is_update', true);

            $client_custom = $this->mdl_client_custom->where('client_id', $id)->get();

            if ($client_custom->num_rows()) {
                $client_custom = $client_custom->row();

                unset($client_custom->client_id, $client_custom->client_custom_id);

                foreach ($client_custom as $key => $val) {
                    $this->mdl_clients->set_form_value('custom[' . $key . ']', $val);
                }
            }
        } elseif ($this->input->post('btn_submit')) {
            if ($this->input->post('custom')) {
                foreach ($this->input->post('custom') as $key => $val) {
                    $this->mdl_clients->set_form_value('custom[' . $key . ']', $val);
                }
            }
        }

        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->model('custom_values/mdl_custom_values');
        $this->load->model('custom_fields/mdl_client_custom');

        $custom_fields = $this->mdl_custom_fields->by_table('ip_client_custom')->get()->result();
        $custom_values = [];
        foreach ($custom_fields as $custom_field) {
            if (in_array($custom_field->custom_field_type, $this->mdl_custom_values->custom_value_fields())) {
                $values = $this->mdl_custom_values->get_by_fid($custom_field->custom_field_id)->result();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }

        $fields = $this->mdl_client_custom->get_by_clid($id);

        foreach ($custom_fields as $cfield) {
            foreach ($fields as $fvalue) {
                if ($fvalue->client_custom_fieldid == $cfield->custom_field_id) {
                    // TODO: Hackish, may need a better optimization
                    $this->mdl_clients->set_form_value(
                        'custom[' . $cfield->custom_field_id . ']',
                        $fvalue->client_custom_fieldvalue
                    );
                    break;
                }
            }
        }

        $this->load->helper('country');
        $this->load->helper('custom_values');
        $this->load->helper('e-invoice'); //eInvoicing++

<<<<<<< HEAD
        $this->layout->set([
            'custom_fields'        => $custom_fields,
            'custom_values'        => $custom_values,
            'countries'            => get_country_list(trans('cldr')),
            'selected_country'     => $this->mdl_clients->form_value('client_country') ?: get_setting('default_country'),
            'languages'            => get_available_languages(),
            'client_title_choices' => $this->get_client_title_choices(),
            'xml_templates'        => get_xml_template_files(), //eInvoicing++
        ]);
=======
        $this->layout->set(
            [
                'custom_fields' => $custom_fields,
                'custom_values' => $custom_values,
                'countries' => get_country_list(trans('cldr')),
                'selected_country' => $this->mdl_clients->form_value('client_country') ?: get_setting('default_country'),
                'languages'        => get_available_languages(),
                'xml_templates'    => get_xml_template_files(), //eInvoicing++
            ]
        );
>>>>>>> d2738fb2 (939: Prep for incoming merge-request)

        $this->layout->buffer('content', 'clients/form');
        $this->layout->render();
    }

    /**
     * @param int $client_id
     */
    public function view($client_id)
    {
        $this->load->model('clients/mdl_client_notes');
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('quotes/mdl_quotes');
        $this->load->model('payments/mdl_payments');
        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->model('custom_fields/mdl_client_custom');

        $this->load->helper('e-invoice'); //eInvoicing++

        $client = $this->mdl_clients
            ->with_total()
            ->with_total_balance()
            ->with_total_paid()
            ->where('ip_clients.client_id', $client_id)
            ->get()->row();

        $custom_fields = $this->mdl_client_custom->get_by_client($client_id)->result();

        $this->mdl_client_custom->prep_form($client_id);

        if ( ! $client) {
            show_404();
        }

<<<<<<< HEAD
        $this->mdl_invoices->by_client($client_id)->paginate(site_url('clients/view/' . $client_id . '/invoices'), $page, 5);
        $this->mdl_quotes->by_client($client_id)->paginate(site_url('clients/view/' . $client_id . '/quotes'), $page, 5);
        $this->mdl_payments->by_client($client_id)->paginate(site_url('clients/view/' . $client_id . '/payments'), $page, 5);

        $this->layout->set([
            'client'           => $client,
            'client_notes'     => $this->mdl_client_notes->where('client_id', $client_id)->get()->result(),
            'invoices'         => $this->mdl_invoices->result(),
            'quotes'           => $this->mdl_quotes->result(),
            'payments'         => $this->mdl_payments->result(),
            'custom_fields'    => $custom_fields,
            'quote_statuses'   => $this->mdl_quotes->statuses(),
            'invoice_statuses' => $this->mdl_invoices->statuses(),
            'activeTab'        => $activeTab,
        ]);

        $this->layout->buffer([
            [
                'invoice_table',
                'invoices/partial_invoice_table',
            ],
            [
                'quote_table',
                'quotes/partial_quote_table',
            ],
            [
                'payment_table',
                'payments/partial_payment_table',
            ],
            [
                'partial_notes',
                'clients/partial_notes',
            ],
            [
                'content',
                'clients/view',
            ],
        ]);
=======
        $this->layout->set(
            [
                'client' => $client,
                'client_notes' => $this->mdl_client_notes->where('client_id', $client_id)->get()->result(),
                'invoices' => $this->mdl_invoices->by_client($client_id)->limit(20)->get()->result(),
                'quotes' => $this->mdl_quotes->by_client($client_id)->limit(20)->get()->result(),
                'payments' => $this->mdl_payments->by_client($client_id)->limit(20)->get()->result(),
                'custom_fields' => $custom_fields,
                'quote_statuses' => $this->mdl_quotes->statuses(),
                'invoice_statuses' => $this->mdl_invoices->statuses(),
            ]
        );

        $this->layout->buffer(
            [
                [
                    'invoice_table',
                    'invoices/partial_invoice_table',
                ],
                [
                    'quote_table',
                    'quotes/partial_quote_table',
                ],
                [
                    'payment_table',
                    'payments/partial_payment_table',
                ],
                [
                    'partial_notes',
                    'clients/partial_notes',
                ],
                [
                    'content',
                    'clients/view',
                ],
            ]
        );
>>>>>>> d2738fb2 (939: Prep for incoming merge-request)

        $this->layout->render();
    }

    /**
     * @param int $client_id
     */
    public function delete($client_id)
    {
        $this->mdl_clients->delete($client_id);
        redirect('clients');
    }
<<<<<<< HEAD

<<<<<<< HEAD
    private function get_client_title_choices(): array
    {
        return array_map(
            fn ($clientTitleEnum) => $clientTitleEnum->value,
            ClientTitleEnum::cases()
        );
    }
=======
>>>>>>> d2738fb2 (939: Prep for incoming merge-request)
=======
>>>>>>> cf5d220c (939: Just style fixes for now)
}
