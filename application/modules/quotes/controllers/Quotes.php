<?php

if (! defined('BASEPATH')) {
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
class Quotes extends Admin_Controller
{
    /**
     * Quotes constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_quotes');
    }

    public function index()
    {
        // Display all quotes by default
        redirect('quotes/status/all');
    }

    /**
     * @param string $status
     * @param int $page
     */
    public function status($status = 'all', $page = 0)
    {
        // Determine which group of quotes to load
        switch ($status) {
            case 'draft':
                $this->mdl_quotes->is_draft();
                break;
            case 'sent':
                $this->mdl_quotes->is_sent();
                break;
            case 'viewed':
                $this->mdl_quotes->is_viewed();
                break;
            case 'approved':
                $this->mdl_quotes->is_approved();
                break;
            case 'rejected':
                $this->mdl_quotes->is_rejected();
                break;
            case 'canceled':
                $this->mdl_quotes->is_canceled();
                break;
        }

        $this->mdl_quotes->paginate(site_url('quotes/status/' . $status), $page);
        $quotes = $this->mdl_quotes->result();

        $this->layout->set(
            [
                'quotes'             => $quotes,
                'status'             => $status,
                'filter_display'     => true,
                'filter_placeholder' => trans('filter_quotes'),
                'filter_method'      => 'filter_quotes',
                'quote_statuses'     => $this->mdl_quotes->statuses()
            ]
        );

        $this->layout->buffer('content', 'quotes/index');
        $this->layout->render();
    }

    /**
     * @param $quote_id
     */
    public function view($quote_id)
    {
        $this->load->model(
            [
                'quotes/mdl_quote_items',
                'tax_rates/mdl_tax_rates',
                'units/mdl_units',
                'mdl_quote_tax_rates',
                'custom_fields/mdl_custom_fields',
                'custom_values/mdl_custom_values',
                'custom_fields/mdl_quote_custom',
                'upload/mdl_uploads',
            ]
        );

        $this->load->helper(['custom_values', 'dropzone']);

        $fields = $this->mdl_quote_custom->by_id($quote_id)->get()->result();
        $this->db->reset_query();

        $quote_custom = $this->mdl_quote_custom->where('quote_id', $quote_id)->get();

        if ($quote_custom->num_rows()) {
            $quote_custom = $quote_custom->row();

            unset($quote_custom->quote_id, $quote_custom->quote_custom_id);

            foreach ($quote_custom as $key => $val) {
                $this->mdl_quotes->set_form_value('custom[' . $key . ']', $val);
            }
        }

        $quote = $this->mdl_quotes->get_by_id($quote_id);

        if (!$quote) {
            show_404();
        }

        $custom_fields = $this->mdl_custom_fields->by_table('ip_quote_custom')->get()->result();
        $custom_values = [];
        foreach ($custom_fields as $custom_field)
        {
            if (in_array($custom_field->custom_field_type, $this->mdl_custom_values->custom_value_fields()))
            {
                $values = $this->mdl_custom_values->get_by_fid($custom_field->custom_field_id)->result();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }

        foreach ($custom_fields as $cfield)
        {
            foreach ($fields as $fvalue)
            {
                if ($fvalue->quote_custom_fieldid == $cfield->custom_field_id)
                {
                    // TODO: Hackish, may need a better optimization
                    $this->mdl_quotes->set_form_value(
                        'custom[' . $cfield->custom_field_id . ']',
                        $fvalue->quote_custom_fieldvalue
                    );
                    break;
                }
            }
        }

        $items = $this->mdl_quote_items->where('quote_id', $quote_id)->get()->result();

        // Legacy calculation false: helper to Alert id not standard taxes (number_helper) - since 1.6.3
        $bads = items_tax_usages_bad($items); // bads is false or array ids[0] no taxes, ids[1] taxes

        $this->layout->set(
            [
                'quote'           => $quote,
                'items'           => $items,
                'quote_id'        => $quote_id,
                'units'           => $this->mdl_units->get()->result(),
                'tax_rates'       => $this->mdl_tax_rates->get()->result(),
                'quote_tax_rates' => $this->mdl_quote_tax_rates->where('quote_id', $quote_id)->get()->result(),
                'quote_statuses'  => $this->mdl_quotes->statuses(),
                'custom_fields'   => $custom_fields,
                'custom_values'   => $custom_values,
                'custom_js_vars'  => [
                    'currency_symbol'           => get_setting('currency_symbol'),
                    'currency_symbol_placement' => get_setting('currency_symbol_placement'),
                    'decimal_point'             => get_setting('decimal_point')
                ],
                'legacy_calculation' => config_item('legacy_calculation'),
            ]
        );

        $this->layout->buffer(
            [
                ['modal_delete_quote', 'quotes/modal_delete_quote'],
                ['modal_add_quote_tax', 'quotes/modal_add_quote_tax'],
                ['content', 'quotes/view'],
            ]
        );

        $this->layout->render();
    }

    /**
     * @param $quote_id
     */
    public function delete($quote_id)
    {
        // Delete the quote
        $this->mdl_quotes->delete($quote_id);

        // Redirect to quote index
        redirect('quotes/index');
    }

    /**
     * @param $quote_id
     * @param bool $stream
     * @param null $quote_template
     */
    public function generate_pdf($quote_id, $stream = true, $quote_template = null)
    {
        $this->load->helper('pdf');

        if (get_setting('mark_quotes_sent_pdf') == 1) {
            $this->mdl_quotes->generate_quote_number_if_applicable($quote_id);
            $this->mdl_quotes->mark_sent($quote_id);
        }

        generate_quote_pdf($quote_id, $stream, $quote_template);
    }

    /**
     * @param $quote_id
     * @param $quote_tax_rate_id
     */
    public function delete_quote_tax($quote_id, $quote_tax_rate_id)
    {
        $this->load->model('quotes/mdl_quote_tax_rates');
        $this->mdl_quote_tax_rates->delete($quote_tax_rate_id);

        $this->load->model('quotes/mdl_quote_amounts');
        $global_discount['item'] = $this->mdl_quote_amounts->get_global_discount($quote_id);
        // Recalculate quote amounts
        $this->mdl_quote_amounts->calculate($quote_id, $global_discount);

        redirect('quotes/view/' . $quote_id);
    }

    public function recalculate_all_quotes()
    {
        $this->db->select('quote_id');
        $quote_ids = $this->db->get('ip_quotes')->result();

        $this->load->model('mdl_quote_amounts');

        foreach ($quote_ids as $quote_id) {
            $global_discount['item'] = $this->mdl_quote_amounts->get_global_discount($quote_id->quote_id);
            // Recalculate quote amounts
            $this->mdl_quote_amounts->calculate($quote_id->quote_id, $global_discount);
        }
    }

}
