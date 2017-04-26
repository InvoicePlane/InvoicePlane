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
        $this->load->model('quotes/mdl_quote_items');
        $this->load->model('quotes/mdl_quotes');
        $this->load->model('units/mdl_units');

        $quote_id = $this->input->post('quote_id');

        $this->mdl_quotes->set_id($quote_id);

        if ($this->mdl_quotes->run_validation('validation_rules_save_quote')) {
            $items = json_decode($this->input->post('items'));

            foreach ($items as $item) {
                if ($item->item_name) {
                    $item->item_quantity = ($item->item_quantity ? standardize_amount($item->item_quantity) : floatval(0));
                    $item->item_price = ($item->item_quantity ? standardize_amount($item->item_price) : floatval(0));
                    $item->item_discount_amount = ($item->item_discount_amount) ? standardize_amount($item->item_discount_amount) : null;
                    $item->item_product_id = ($item->item_product_id ? $item->item_product_id : null);
                    $item->item_product_unit_id = ($item->item_product_unit_id ? $item->item_product_unit_id : null);
                    $item->item_product_unit = $this->mdl_units->get_name($item->item_product_unit_id, $item->item_quantity);

                    $item_id = ($item->item_id) ?: null;
                    unset($item->item_id);

                    $this->mdl_quote_items->save($item_id, $item);
                }
            }

            if ($this->input->post('quote_discount_amount') === '') {
                $quote_discount_amount = floatval(0);
            } else {
                $quote_discount_amount = $this->input->post('quote_discount_amount');
            }

            if ($this->input->post('quote_discount_percent') === '') {
                $quote_discount_percent = floatval(0);
            } else {
                $quote_discount_percent = $this->input->post('quote_discount_percent');
            }

            // Generate new quote number if needed
            $quote_number = $this->input->post('quote_number');
            $quote_status_id = $this->input->post('quote_status_id');

            if (empty($quote_number) && $quote_status_id != 1) {
                $quote_group_id = $this->mdl_quotes->get_invoice_group_id($quote_id);
                $quote_number = $this->mdl_quotes->get_quote_number($quote_group_id);
            }

            $db_array = array(
                'quote_number' => $quote_number,
                'quote_date_created' => date_to_mysql($this->input->post('quote_date_created')),
                'quote_date_expires' => date_to_mysql($this->input->post('quote_date_expires')),
                'quote_status_id' => $quote_status_id,
                'quote_password' => $this->input->post('quote_password'),
                'notes' => $this->input->post('notes'),
                'quote_discount_amount' => standardize_amount($quote_discount_amount),
                'quote_discount_percent' => standardize_amount($quote_discount_percent),
            );

            $this->mdl_quotes->save($quote_id, $db_array);

            // Recalculate for discounts
            $this->load->model('quotes/mdl_quote_amounts');
            $this->mdl_quote_amounts->calculate($quote_id);

            $response = array(
                'success' => 1
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
            $this->load->model('custom_fields/mdl_quote_custom');
            $result = $this->mdl_quote_custom->save_custom($quote_id, $db_array);
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

    public function save_quote_tax_rate()
    {
        $this->load->model('quotes/mdl_quote_tax_rates');

        if ($this->mdl_quote_tax_rates->run_validation()) {
            $this->mdl_quote_tax_rates->save();

            $response = array(
                'success' => 1
            );
        } else {
            $response = array(
                'success' => 0,
                'validation_errors' => $this->mdl_quote_tax_rates->validation_errors
            );
        }

        echo json_encode($response);
    }

    public function create()
    {
        $this->load->model('quotes/mdl_quotes');

        if ($this->mdl_quotes->run_validation()) {
            $quote_id = $this->mdl_quotes->create();

            $response = array(
                'success' => 1,
                'quote_id' => $quote_id
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

    public function modal_change_client()
    {
        $this->load->module('layout');
        $this->load->model('clients/mdl_clients');

        $data = array(
            'client_id' => $this->input->post('client_id'),
            'quote_id' => $this->input->post('quote_id'),
            'clients' => $this->mdl_clients->get_latest(),
        );

        $this->layout->load_view('quotes/modal_change_client', $data);
    }

    public function change_client()
    {
        $this->load->model('quotes/mdl_quotes');
        $this->load->model('clients/mdl_clients');

        // Get the client ID
        $client_id = $this->input->post('client_id');
        $client = $this->mdl_clients->where('ip_clients.client_id', $client_id)
            ->get()->row();

        if (!empty($client)) {
            $quote_id = $this->input->post('quote_id');

            $db_array = array(
                'client_id' => $client_id,
            );
            $this->db->where('quote_id', $quote_id);
            $this->db->update('ip_quotes', $db_array);

            $response = array(
                'success' => 1,
                'quote_id' => $quote_id
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
        $this->load->model('quotes/mdl_quote_items');

        $item = $this->mdl_quote_items->get_by_id($this->input->post('item_id'));

        echo json_encode($item);
    }

    public function modal_create_quote()
    {
        $this->load->module('layout');
        $this->load->model('invoice_groups/mdl_invoice_groups');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('clients/mdl_clients');

        $data = array(
            'invoice_groups' => $this->mdl_invoice_groups->get()->result(),
            'tax_rates' => $this->mdl_tax_rates->get()->result(),
            'client' => $this->mdl_clients->get_by_id($this->input->post('client_id')),
            'clients' => $this->mdl_clients->get_latest(),
        );

        $this->layout->load_view('quotes/modal_create_quote', $data);
    }

    public function modal_copy_quote()
    {
        $this->load->module('layout');

        $this->load->model('quotes/mdl_quotes');
        $this->load->model('invoice_groups/mdl_invoice_groups');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('clients/mdl_clients');

        $data = array(
            'invoice_groups' => $this->mdl_invoice_groups->get()->result(),
            'tax_rates' => $this->mdl_tax_rates->get()->result(),
            'quote_id' => $this->input->post('quote_id'),
            'quote' => $this->mdl_quotes->where('ip_quotes.quote_id', $this->input->post('quote_id'))->get()->row(),
            'client' => $this->mdl_clients->get_by_id($this->input->post('client_id')),
        );

        $this->layout->load_view('quotes/modal_copy_quote', $data);
    }

    public function copy_quote()
    {
        $this->load->model('quotes/mdl_quotes');
        $this->load->model('quotes/mdl_quote_items');
        $this->load->model('quotes/mdl_quote_tax_rates');

        if ($this->mdl_quotes->run_validation()) {
            $target_id = $this->mdl_quotes->save();
            $source_id = $this->input->post('quote_id');

            $this->mdl_quotes->copy_quote($source_id, $target_id);

            $response = array(
                'success' => 1,
                'quote_id' => $target_id
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

    public function modal_quote_to_invoice($quote_id)
    {
        $this->load->model('invoice_groups/mdl_invoice_groups');
        $this->load->model('quotes/mdl_quotes');

        $data = array(
            'invoice_groups' => $this->mdl_invoice_groups->get()->result(),
            'quote_id' => $quote_id,
            'quote' => $this->mdl_quotes->where('ip_quotes.quote_id', $quote_id)->get()->row()
        );

        $this->load->view('quotes/modal_quote_to_invoice', $data);
    }

    public function quote_to_invoice()
    {
        $this->load->model(
            array(
                'invoices/mdl_invoices',
                'invoices/mdl_items',
                'quotes/mdl_quotes',
                'quotes/mdl_quote_items',
                'invoices/mdl_invoice_tax_rates',
                'quotes/mdl_quote_tax_rates'
            )
        );

        if ($this->mdl_invoices->run_validation()) {
            // Get the quote
            $quote = $this->mdl_quotes->get_by_id($this->input->post('quote_id'));

            $invoice_id = $this->mdl_invoices->create(null, false);

            // Update the discounts
            $this->db->where('invoice_id', $invoice_id);
            $this->db->set('invoice_discount_amount', $quote->quote_discount_amount);
            $this->db->set('invoice_discount_percent', $quote->quote_discount_percent);
            $this->db->update('ip_invoices');

            // Save the invoice id to the quote
            $this->db->where('quote_id', $this->input->post('quote_id'));
            $this->db->set('invoice_id', $invoice_id);
            $this->db->update('ip_quotes');

            $quote_items = $this->mdl_quote_items->where('quote_id', $this->input->post('quote_id'))->get()->result();

            foreach ($quote_items as $quote_item) {
                $db_array = array(
                    'invoice_id' => $invoice_id,
                    'item_tax_rate_id' => $quote_item->item_tax_rate_id,
                    'item_product_id' => $quote_item->item_product_id,
                    'item_name' => $quote_item->item_name,
                    'item_description' => $quote_item->item_description,
                    'item_quantity' => $quote_item->item_quantity,
                    'item_price' => $quote_item->item_price,
                    'item_product_unit_id' => $quote_item->item_product_unit_id,
                    'item_product_unit' => $quote_item->item_product_unit,
                    'item_discount_amount' => $quote_item->item_discount_amount,
                    'item_order' => $quote_item->item_order
                );

                $this->mdl_items->save(null, $db_array);
            }

            $quote_tax_rates = $this->mdl_quote_tax_rates->where('quote_id', $this->input->post('quote_id'))->get()->result();

            foreach ($quote_tax_rates as $quote_tax_rate) {
                $db_array = array(
                    'invoice_id' => $invoice_id,
                    'tax_rate_id' => $quote_tax_rate->tax_rate_id,
                    'include_item_tax' => $quote_tax_rate->include_item_tax,
                    'invoice_tax_rate_amount' => $quote_tax_rate->quote_tax_rate_amount
                );

                $this->mdl_invoice_tax_rates->save(null, $db_array);
            }

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

}
