<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2017 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class Ajax
 */
class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    public function save()
    {
        $this->load->model('invoices/mdl_items');
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('units/mdl_units');
        $this->load->model('invoices/mdl_invoice_sumex');

        $invoice_id = $this->input->post('invoice_id');

        $this->mdl_invoices->set_id($invoice_id);

        if ($this->mdl_invoices->run_validation('validation_rules_save_invoice')) {
            $items = json_decode($this->input->post('items'));

            foreach ($items as $item) {
                // Check if an item has either a quantity + price or name or description
                if (!empty($item->item_name)) {
                    $item->item_quantity = ($item->item_quantity ? standardize_amount($item->item_quantity) : floatval(0));
                    $item->item_price = ($item->item_quantity ? standardize_amount($item->item_price) : floatval(0));
                    $item->item_discount_amount = ($item->item_discount_amount) ? standardize_amount($item->item_discount_amount) : null;
                    $item->item_product_id = ($item->item_product_id ? $item->item_product_id : null);
                    if (property_exists($item, 'item_date')) {
                        $item->item_date = ($item->item_date ? date_to_mysql($item->item_date) : null);
                    }
                    $item->item_product_unit_id = ($item->item_product_unit_id ? $item->item_product_unit_id : null);
                    $item->item_product_unit = $this->mdl_units->get_name($item->item_product_unit_id, $item->item_quantity);
                    $item_id = ($item->item_id) ?: null;
                    unset($item->item_id);

                    if (!$item->item_task_id) {
                        unset($item->item_task_id);
                    } else {
                        $this->load->model('tasks/mdl_tasks');
                        $this->mdl_tasks->update_status(4, $item->item_task_id);
                    }

                    $this->mdl_items->save($item_id, $item);
                } else {
                    // Throw an error message and use the form validation for that
                    $this->load->library('form_validation');
                    $this->form_validation->set_rules('item_name', trans('item'), 'required');
                    $this->form_validation->run();

                    $response = array(
                        'success' => 0,
                        'validation_errors' => array(
                            'item_name' => form_error('item_name', '', ''),
                        )
                    );

                    echo json_encode($response);
                    exit;
                }
            }

            $invoice_status = $this->input->post('invoice_status_id');

            if ($this->input->post('invoice_discount_amount') === '') {
                $invoice_discount_amount = floatval(0);
            } else {
                $invoice_discount_amount = $this->input->post('invoice_discount_amount');
            }

            if ($this->input->post('invoice_discount_percent') === '') {
                $invoice_discount_percent = floatval(0);
            } else {
                $invoice_discount_percent = $this->input->post('invoice_discount_percent');
            }

            // Generate new invoice number if needed
            $invoice_number = $this->input->post('invoice_number');

            if (empty($invoice_number) && $invoice_status != 1) {
                $invoice_group_id = $this->mdl_invoices->get_invoice_group_id($invoice_id);
                $invoice_number = $this->mdl_invoices->get_invoice_number($invoice_group_id);
            }

            $db_array = array(
                'invoice_number' => $invoice_number,
                'invoice_terms' => $this->input->post('invoice_terms'),
                'invoice_date_created' => date_to_mysql($this->input->post('invoice_date_created')),
                'invoice_date_due' => date_to_mysql($this->input->post('invoice_date_due')),
                'invoice_password' => $this->input->post('invoice_password'),
                'invoice_status_id' => $invoice_status,
                'payment_method' => $this->input->post('payment_method'),
                'invoice_discount_amount' => standardize_amount($invoice_discount_amount),
                'invoice_discount_percent' => standardize_amount($invoice_discount_percent),
            );

            // check if status changed to sent, the feature is enabled and settings is set to sent
            if ($this->config->item('disable_read_only') === false) {
                if ($invoice_status == get_setting('read_only_toggle')) {
                    $db_array['is_read_only'] = 1;
                }
            }

            $this->mdl_invoices->save($invoice_id, $db_array);
            $sumexInvoice = $this->mdl_invoices->where('sumex_invoice', $invoice_id)->get()->num_rows();

            if ($sumexInvoice >= 1) {
                $sumex_array = array(
                    'sumex_invoice' => $invoice_id,
                    'sumex_reason' => $this->input->post('invoice_sumex_reason'),
                    'sumex_diagnosis' => $this->input->post('invoice_sumex_diagnosis'),
                    'sumex_treatmentstart' => date_to_mysql($this->input->post('invoice_sumex_treatmentstart')),
                    'sumex_treatmentend' => date_to_mysql($this->input->post('invoice_sumex_treatmentend')),
                    'sumex_casedate' => date_to_mysql($this->input->post('invoice_sumex_casedate')),
                    'sumex_casenumber' => $this->input->post('invoice_sumex_casenumber'),
                    'sumex_observations' => $this->input->post('invoice_sumex_observations')
                );
                $this->mdl_invoice_sumex->save($invoice_id, $sumex_array);
            }

            // Recalculate for discounts
            $this->load->model('invoices/mdl_invoice_amounts');
            $this->mdl_invoice_amounts->calculate($invoice_id);

            $response = array(
                'success' => 1,
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        // Save all custom fields
        if ($this->input->post('custom')) {
            $db_array = array();

            $values = [];
            foreach ($this->input->post('custom') as $custom) {
                if (preg_match("/^(.*)\[\]$/i", $custom['name'], $matches)) {
                    $values[$matches[1]][] = $custom['value'];
                } else {
                    $values[$custom['name']] = $custom['value'];
                }
            }

            foreach ($values as $key => $value) {
                preg_match("/^custom\[(.*?)\](?:\[\]|)$/", $key, $matches);
                if ($matches) {
                    $db_array[$matches[1]] = $value;
                }
            }


            $this->load->model('custom_fields/mdl_invoice_custom');
            $result = $this->mdl_invoice_custom->save_custom($invoice_id, $db_array);
            if ($result !== true) {
                $response = array(
                    'success' => 0,
                    'validation_errors' => $result
                );

                echo json_encode($response);
                exit;
            }
        }

        echo json_encode($response);
    }

    public function save_invoice_tax_rate()
    {
        $this->load->model('invoices/mdl_invoice_tax_rates');

        if ($this->mdl_invoice_tax_rates->run_validation()) {
            $this->mdl_invoice_tax_rates->save();

            $response = array(
                'success' => 1
            );
        } else {
            $response = array(
                'success' => 0,
                'validation_errors' => $this->mdl_invoice_tax_rates->validation_errors
            );
        }

        echo json_encode($response);
    }

    public function create()
    {
        $this->load->model('invoices/mdl_invoices');

        if ($this->mdl_invoices->run_validation()) {
            $invoice_id = $this->mdl_invoices->create();

            $response = array(
                'success' => 1,
                'invoice_id' => $invoice_id
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        echo json_encode($response);
    }

    public function create_recurring()
    {
        $this->load->model('invoices/mdl_invoices_recurring');

        if ($this->mdl_invoices_recurring->run_validation()) {
            $this->mdl_invoices_recurring->save();

            $response = array(
                'success' => 1,
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        echo json_encode($response);
    }

    public function get_item()
    {
        $this->load->model('invoices/mdl_items');

        $item = $this->mdl_items->get_by_id($this->input->post('item_id'));

        echo json_encode($item);
    }

    public function modal_create_invoice()
    {
        $this->load->module('layout');
        $this->load->helper('client');
        $this->load->model('invoice_groups/mdl_invoice_groups');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('clients/mdl_clients');

        $data = array(
            'invoice_groups' => $this->mdl_invoice_groups->get()->result(),
            'tax_rates' => $this->mdl_tax_rates->get()->result(),
            'client' => $this->mdl_clients->get_by_id($this->input->post('client_id')),
            'clients' => $this->mdl_clients->get_latest(),
        );

        $this->layout->load_view('invoices/modal_create_invoice', $data);
    }

    public function modal_create_recurring()
    {
        $this->load->module('layout');

        $this->load->model('mdl_invoices_recurring');

        $data = array(
            'invoice_id' => $this->input->post('invoice_id'),
            'recur_frequencies' => $this->mdl_invoices_recurring->recur_frequencies
        );

        $this->layout->load_view('invoices/modal_create_recurring', $data);
    }

    public function get_recur_start_date()
    {
        $invoice_date = $this->input->post('invoice_date');
        $recur_frequency = $this->input->post('recur_frequency');

        echo increment_user_date($invoice_date, $recur_frequency);
    }

    public function modal_change_client()
    {
        $this->load->module('layout');
        $this->load->model('clients/mdl_clients');
        $this->load->helper('client');

        $data = array(
            'client_id' => $this->input->post('client_id'),
            'invoice_id' => $this->input->post('invoice_id'),
            'clients' => $this->mdl_clients->get_latest(),
        );

        $this->layout->load_view('invoices/modal_change_client', $data);
    }

    public function change_client()
    {
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('clients/mdl_clients');

        // Get the client ID
        $client_id = $this->input->post('client_id');
        $client = $this->mdl_clients->where('ip_clients.client_id', $client_id)->get()->row();

        if (!empty($client)) {
            $invoice_id = $this->input->post('invoice_id');

            $db_array = array(
                'client_id' => $client_id,
            );
            $this->db->where('invoice_id', $invoice_id);
            $this->db->update('ip_invoices', $db_array);

            $response = array(
                'success' => 1,
                'invoice_id' => $invoice_id,
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        echo json_encode($response);
    }

    public function modal_copy_invoice()
    {
        $this->load->module('layout');

        $this->load->model('invoices/mdl_invoices');
        $this->load->model('invoice_groups/mdl_invoice_groups');
        $this->load->model('tax_rates/mdl_tax_rates');

        $data = array(
            'invoice_groups' => $this->mdl_invoice_groups->get()->result(),
            'tax_rates' => $this->mdl_tax_rates->get()->result(),
            'invoice_id' => $this->input->post('invoice_id'),
            'invoice' => $this->mdl_invoices->where('ip_invoices.invoice_id', $this->input->post('invoice_id'))->get()->row(),
        );

        $this->layout->load_view('invoices/modal_copy_invoice', $data);
    }

    public function copy_invoice()
    {
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('invoices/mdl_items');
        $this->load->model('invoices/mdl_invoice_tax_rates');

        if ($this->mdl_invoices->run_validation()) {
            $target_id = $this->mdl_invoices->save();
            $source_id = $this->input->post('invoice_id');

            $this->mdl_invoices->copy_invoice($source_id, $target_id);

            $response = array(
                'success' => 1,
                'invoice_id' => $target_id
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        echo json_encode($response);
    }

    public function modal_create_credit()
    {
        $this->load->module('layout');

        $this->load->model('invoices/mdl_invoices');
        $this->load->model('invoice_groups/mdl_invoice_groups');
        $this->load->model('tax_rates/mdl_tax_rates');

        $data = array(
            'invoice_groups' => $this->mdl_invoice_groups->get()->result(),
            'tax_rates' => $this->mdl_tax_rates->get()->result(),
            'invoice_id' => $this->input->post('invoice_id'),
            'invoice' => $this->mdl_invoices->where('ip_invoices.invoice_id', $this->input->post('invoice_id'))->get()->row()
        );

        $this->layout->load_view('invoices/modal_create_credit', $data);
    }

    public function create_credit()
    {
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('invoices/mdl_items');
        $this->load->model('invoices/mdl_invoice_tax_rates');

        if ($this->mdl_invoices->run_validation()) {
            $target_id = $this->mdl_invoices->save();
            $source_id = $this->input->post('invoice_id');

            $this->mdl_invoices->copy_credit_invoice($source_id, $target_id);

            // Set source invoice to read-only
            if ($this->config->item('disable_read_only') == false) {
                $this->mdl_invoices->where('invoice_id', $source_id);
                $this->mdl_invoices->update('ip_invoices', array('is_read_only' => '1'));
            }

            // Set target invoice to credit invoice
            $this->mdl_invoices->where('invoice_id', $target_id);
            $this->mdl_invoices->update('ip_invoices', array('creditinvoice_parent_id' => $source_id));

            $this->mdl_invoices->where('invoice_id', $target_id);
            $this->mdl_invoices->update('ip_invoice_amounts', array('invoice_sign' => '-1'));

            $response = array(
                'success' => 1,
                'invoice_id' => $target_id
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        echo json_encode($response);
    }

}
