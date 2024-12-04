<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once dirname(__FILE__, 2) . '/Enums/ClientTitleEnum.php';

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

#[AllowDynamicProperties]
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

    // documents 
    // https://www.buildwithphp.com/how-to-upload-image-in-codeigniter-with-database-example
    public function do_upload_document($client_id=1)
    {
                $this->load->model('clients/mdl_documents');
                
                // generate unique name - YMMV
                $sid = sprintf("%1$04d", $client_id);
                $new_name = "D" . $sid . "_" . substr(md5(time()),0,6) . "_" . $_FILES['document']['name'];

                $config = array(
                       'file_name'  => $new_name,
                        'upload_path' => UPLOADS_FOLDER . "documents/",
                        'allowed_types' => "odt|ods|pdf|doc|docx|xls|xlsx|jpeg|jpg|png|gif|tiff",
                        'max_size' => "15728640" 		// your max file size , here it is 15 MB
                );
                $this->load->library('upload', $config);
                if ($this->upload->do_upload('document')) {

                        $document_filename = $this->upload->data('file_name');
                        $document_description = ""; // TODO
                        // instert into database
                        $this->mdl_documents->insert_document( $client_id, $document_filename, $document_description );
                        $this->session->set_flashdata('alert_success','Record has been saved successfully.');
                        redirect('clients/view/' . $client_id . '/documents');
                } else {
                        $this->session->set_flashdata('alert_error', $this->upload->display_errors());
                        redirect('clients/upload_document/' . $client_id);
                }
        }

    public function upload_document($client_id=1)
    {
        $client = $this->mdl_clients
            ->where('ip_clients.client_id', $client_id)
            ->get()->row();

        $this->layout->set(
            array(
                'client' => $client,
                'client_id' => $client_id,
            )
        );

        $this->layout->buffer('content', 'clients/upload_document');
        $this->layout->render();
    }

    public function show_documents($client_id=1)
    {
        $data = get_documents($client_id );
    }

   public function document_del($client_id, $document_id)
   {
        $this->load->model('clients/mdl_documents');
        if ($this->input->post('del')) {
                $this->mdl_documents->delete_document($document_id);
            redirect('clients/view/' . $client_id . '/documents');
        }

        $this->layout->set(
            array(
                'client_id' => $client_id,
                'document_id' => $document_id
            )
        );

        $this->layout->buffer('content', 'clients/delete_document');
        $this->layout->render();
   }


    public function index()
    {
        // Display active clients by default
        redirect('clients/status/active');
    }

    /**
     * @param string $status
     * @param int    $page
     */
    public function status($status = 'active', $page = 0)
    {
        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->model('custom_fields/mdl_client_custom');

        if (is_numeric(array_search($status, ['active', 'inactive']))) {
            $function = 'is_' . $status;
            $this->mdl_clients->{$function}();
        }

	// original query - unmodified
        //$this->mdl_clients->with_total_balance()->paginate(site_url('clients/status/' . $status), $page);

        // sort asc desc by chrissie
        $sort = $this->input->get('sort') ?? 'id'; // Standard-Spalte
        $order = $this->input->get('order') ?? 'asc';  // Standard-Reihenfolge

	if ($sort == 'name' && $order =='asc')
		$this->mdl_clients->with_total_balance()->order_by('client_name','ASC') ->paginate(site_url('clients/status/' . $status), $page);
	if ($sort == 'name' && $order =='desc')
                $this->mdl_clients->with_total_balance()->order_by('client_name','DESC') ->paginate(site_url('clients/status/' . $status), $page);
	if ($sort == 'id' && $order =='asc')
                $this->mdl_clients->with_total_balance()->order_by('client_id','ASC') ->paginate(site_url('clients/status/' . $status), $page);
	if ($sort == 'id' && $order =='desc')
                $this->mdl_clients->with_total_balance()->order_by('client_id','DESC') ->paginate(site_url('clients/status/' . $status), $page);
	if ($sort == 'amount' && $order =='asc')
                $this->mdl_clients->with_total_balance()->order_by('client_invoice_balance','ASC') ->paginate(site_url('clients/status/' . $status), $page);
	if ($sort == 'amount' && $order =='desc')
                $this->mdl_clients->with_total_balance()->order_by('client_invoice_balance','DESC') ->paginate(site_url('clients/status/' . $status), $page);
        // end sort

        $clients = $this->mdl_clients->result();

// xtra, atac, ... by chrissie: with customer number, ... 

$my_customerno = [];
$my_customerav = [];
$my_customerhosting = [];
$my_customerls = [];

if (ip_xtra()) {
        foreach ($clients as $c) {
		$custom_fields = $this->mdl_client_custom->get_by_client($c->client_id)->result();
		foreach ($custom_fields as $cfield) {
		if ($cfield->custom_field_label == "customer_no")
			$my_customerno[$c->client_id] = $cfield->client_custom_fieldvalue;
		}
        }
}

if (ip_atac() ) {
        foreach ($clients as $c) {
		$custom_fields = $this->mdl_client_custom->get_by_client($c->client_id)->result();
		foreach ($custom_fields as $cfield) {
			if ($cfield->custom_field_label == "Kundennummer")
				$my_customerno[$c->client_id] = $cfield->client_custom_fieldvalue;

                        if (str_starts_with($cfield->custom_field_label, "Hostingvertrag"))
                                $my_customerhosting[$c->client_id] = $cfield->client_custom_fieldvalue;

                        if (str_starts_with($cfield->custom_field_label, "Lastschrift"))
                                $my_customerls[$c->client_id] = $cfield->client_custom_fieldvalue;

			if ($cfield->custom_field_label == "AV-Vertrag")
                                $my_customerav[$c->client_id] = $cfield->client_custom_fieldvalue;
                }
        }
}


        $this->layout->set([
                'my_customerno' => $my_customerno,
                'my_customerhosting' => $my_customerhosting,
                'my_customerls' => $my_customerls,
                'my_customerav' => $my_customerav,

            'sort' => $sort,
            'order' => $order,
            'records'            => $clients,
            'filter_display'     => true,
            'filter_placeholder' => trans('filter_clients'),
            'filter_method'      => 'filter_clients',
        ]);

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
                'client_name'    => $this->input->post('client_name'),
                'client_surname' => $this->input->post('client_surname'),
            ])->result();

            if ( ! empty($check)) {
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

        $this->layout->set([
            'custom_fields'        => $custom_fields,
            'custom_values'        => $custom_values,
            'countries'            => get_country_list(trans('cldr')),
            'selected_country'     => $this->mdl_clients->form_value('client_country') ?: get_setting('default_country'),
            'languages'            => get_available_languages(),
            'client_title_choices' => $this->get_client_title_choices(),
        ]);

        $this->layout->buffer('content', 'clients/form');
        $this->layout->render();
    }

    /**
     * @param int $client_id
     */
    public function view($client_id, $activeTab = 'detail', $page = 0)
    {
        $this->load->model('clients/mdl_client_notes');
	$this->load->model('clients/mdl_documents');
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('quotes/mdl_quotes');
        $this->load->model('payments/mdl_payments');
        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->model('custom_fields/mdl_client_custom');

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

        $this->mdl_invoices->by_client($client_id)->paginate(site_url('clients/view/' . $client_id . '/invoices'), $page, 5);
        $this->mdl_quotes->by_client($client_id)->paginate(site_url('clients/view/' . $client_id . '/quotes'), $page, 5);
        $this->mdl_payments->by_client($client_id)->paginate(site_url('clients/view/' . $client_id . '/payments'), $page, 5);

        $this->layout->set([
            'client'           => $client,
            'client_notes'     => $this->mdl_client_notes->where('client_id', $client_id)->get()->result(),
	'documents' => $this->mdl_documents->get_documents($client_id),
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
                    'document_table',
                    'clients/partial_document_table'
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

    private function get_client_title_choices(): array
    {
        return array_map(
            fn ($clientTitleEnum) => $clientTitleEnum->value,
            ClientTitleEnum::cases()
        );
    }
}

/*
vim:et:ts=4:sw=4:
*/
