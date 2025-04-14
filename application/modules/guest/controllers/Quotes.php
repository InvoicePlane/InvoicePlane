<?php

if (! defined('BASEPATH'))
{
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
class Quotes extends Guest_Controller
{
    /**
     * Quotes constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('quotes/mdl_quotes');
    }

    public function index()
    {
        // Display open quotes by default
        redirect('guest/quotes/status/open');
    }

    /**
     * @param string $status
     * @param int $page
     */
    public function status($status = 'open', $page = 0)
    {
        redirect_to_set(); // Sets the current URL in the session to force redirect_to()

        // Determine which group of quotes to load
        switch ($status) {
            case 'all':
                $this->mdl_quotes->guest_visible();
                break;
            case 'viewed':
                $this->mdl_quotes->is_viewed();
                break;
            case 'approved':
                $this->mdl_quotes->is_approved();
                break;
            case 'rejected':
                $this->mdl_quotes->is_rejected();
                $this->layout->set('show_invoice_column', true);
                break;
            default:
                $this->mdl_quotes->is_open();
                break;
        }

        $this->mdl_quotes->where_in('ip_quotes.client_id', $this->user_clients);
        $this->mdl_quotes->paginate(site_url('guest/quotes/status/' . $status), $page);

        $quotes = $this->mdl_quotes->result();

        $this->layout->set(
            [
                'quotes' => $quotes,
                'status' => $status,
            ]
        );
        $this->layout->buffer('content', 'guest/quotes_index');
        $this->layout->render('layout_guest');
    }

    /**
     * @param $quote_id
     */
    public function view($quote_id)
    {
        redirect_to_set(); // Sets the current URL in the session to force redirect_to()

        $quote = $this->mdl_quotes->guest_visible()->where('ip_quotes.quote_id', $quote_id)->where_in('ip_quotes.client_id', $this->user_clients)->get()->row();

        if (! $quote)
        {
            show_404();
        }

        $this->mdl_quotes->mark_viewed($quote->quote_id);

        $this->load->model(
            [
                'quotes/mdl_quote_items',
                'quotes/mdl_quote_tax_rates',
            ]
        );

        $this->load->helper('dropzone');

        $this->layout->set(
            [
                'quote_id'           => $quote_id,
                'quote'              => $quote,
                'items'              => $this->mdl_quote_items->where('quote_id', $quote_id)->get()->result(),
                'quote_tax_rates'    => $this->mdl_quote_tax_rates->where('quote_id', $quote_id)->get()->result(),
                'legacy_calculation' => config_item('legacy_calculation'),
            ]
        );

        $this->layout->buffer('content', 'guest/quotes_view');
        $this->layout->render('layout_guest');
    }

    /**
     * @param $quote_id
     * @param bool $stream
     * @param null $quote_template
     */
    public function generate_pdf($quote_id, $stream = true, $quote_template = null)
    {
        $this->load->helper('pdf');

        $this->mdl_quotes->mark_viewed($quote_id);

        $quote = $this->mdl_quotes->guest_visible()->where('ip_quotes.quote_id', $quote_id)->where_in('ip_quotes.client_id', $this->user_clients)->get()->row();

        if (! $quote)
        {
            show_404();
        }

        generate_quote_pdf($quote_id, $stream, $quote_template);
    }

    /**
     * @param $quote_id
     */
    public function approve($quote_id)
    {
        $this->load->model('quotes/mdl_quotes');
        $this->load->helper('mailer');

        $this->mdl_quotes->approve_quote_by_id($quote_id);
        email_quote_status($quote_id, 'approved');

        redirect_to('guest/quotes');
    }

    /**
     * @param $quote_id
     */
    public function reject($quote_id)
    {
        $this->load->model('quotes/mdl_quotes');
        $this->load->helper('mailer');

        $this->mdl_quotes->reject_quote_by_id($quote_id);
        email_quote_status($quote_id, 'rejected');

        redirect_to('guest/quotes');
    }

}
