<?php
use Mpdf\Tag\B;

if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class Statements
 */
class Statements extends Admin_Controller
{

    const TRANSACTION_TYPE_INVOICE      = 1;
    const TRANSACTION_TYPE_CREDIT_NOTE  = 2;
    const TRANSACTION_TYPE_PAYMENT      = 3;

    /**
     * Quotes constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // $this->load->model('mdl_quotes');
        $this->load->model('clients/mdl_clients');

    }

    public function index($client_id)
    {
        $this->view($client_id);
    }

    /**
     * @param $client_id
     *
     */
    public function view($client_id)
    {

        $this->load->model('custom_fields/mdl_client_custom');

        /*
         * Load the client
         */
        $client = $this->mdl_clients
            ->where('ip_clients.client_id', $client_id)
            ->get()->row();

        if (!$client) {
            show_404();
        }

        $custom_fields = $this->mdl_client_custom->get_by_client($client_id)->result();

        $this->mdl_client_custom->prep_form($client_id);

        if($this->input->method() === 'post')
        {

            // We should use the form value if supplied, otherwise the hidden field sdate
            $statement_start_date   = strtotime($this->input->post('sdate'));
            $statement_end_date     = strtotime($this->input->post('edate'));

            $statement_number       = $this->input->post('statement_number');
            $statement_date         = $this->input->post('statement_date_created');
            $notes                  = $this->input->post('notes');

        } else {

            $statement_start_date   = null;
            $statement_end_date     = null;

            $statement_number       = null;
            $statement_date         = null;
            $notes                  = null;

        }

        $statement = $this->build_statement($client_id, $statement_start_date, $statement_end_date, $statement_date, $statement_number);


        // TODO: Send statement number

        $this->layout->set(
            array(

                'client'                    => $client,
                'statement_start_date'      => $statement->getStatement_start_date(),
                'statement_end_date'        => $statement->getStatement_end_date(),
                'statement_date'            => $statement->getStatement_date(),
                'custom_fields'             => $custom_fields,
                'statement_transactions'    => $statement->getStatement_transactions(),
                'opening_balance'           => $statement->getOpening_balance(),
                'client_total_balance'      => $statement->getStatement_balance(),
                'statement_number'          => $statement->getStatement_number(),

            )
        );

        $this->layout->buffer(
            array(
                array('content', 'statements/view')
            )
        );

        $this->layout->render();

    }

    /**
     * @param client_total_balance
     * @param transaction
     */
    private function build_statement($client_id, $statement_start_date = null, $statement_end_date = null, $statement_date = null, $statement_number = null)
    {

        $this->load->model('mdl_statement');

        $this->load->model('invoices/mdl_invoices');
        $this->load->model('payments/mdl_payments');

        if (!empty($statement_number)){

            $this->mdl_statement->setStatement_number($statement_number);

        } else {
            $this->mdl_statement->setStatement_number( 'STM-' . $client_id . '-' . date('ymd'));
        }

        /*
         * Load the opening balance
         */
        if (empty($statement_start_date)) {
            $statement_start_date = null;
        }
        if (empty($statement_end_date)) {
            $statement_end_date = strtotime("-1 month");
        }


        $this->mdl_statement->setStatement_date( (!empty($statement_date)) ? $statement_date : date('Y-m-d'));


//         $this->mdl_statement->setStatement_start_date($statement_start_date);
//         $this->mdl_statement->setStatement_end_date($statement_end_date);

        $client_invoices = $this->mdl_invoices->by_client($client_id)->by_date_range($statement_start_date, $statement_end_date)->get()->result();
        $client_payments = $this->mdl_payments->by_client($client_id)->by_date_range($statement_start_date, $statement_end_date)->get()->result();

        $client_invoice_total = 0;
        foreach ($client_invoices as $invoice_entry) {
            $client_invoice_total += $invoice_entry->invoice_total;
        }

        $client_payment_total = 0;
        foreach ($client_payments as $payment_entry) {
            $client_payment_total += $payment_entry->payment_amount;
        }

        $client_opening_balance = $client_invoice_total - $client_payment_total;

        $this->mdl_statement->setOpening_balance($client_opening_balance);

        $statement_start_date = strtotime("-1 month");
        $statement_end_date   = time();

        $this->mdl_statement->setStatement_start_date(date('Y-m-d', $statement_start_date));
        $this->mdl_statement->setStatement_end_date(date('Y-m-d', $statement_end_date));

        /*
         * NOTE: These two calls brings back all invoices and payments over the
         * ...date range, and we manually sum up the totals
         */

        $client_invoices = $this->mdl_invoices->by_client($client_id)->by_date_range($statement_start_date, $statement_end_date)->get()->result();
        $client_payments = $this->mdl_payments->by_client($client_id)->by_date_range($statement_start_date, $statement_end_date)->get()->result();


        $statement_transactions = array();
        $client_total_balance = $client_opening_balance;
        foreach ($client_invoices as $invoice_entry) {

            $transaction = [
                'transaction_type'          => self::TRANSACTION_TYPE_INVOICE,
                'transaction_date'          => $invoice_entry->invoice_date_created,
                'transaction_amount'        => $invoice_entry->invoice_total,

                'invoice_id'                => $invoice_entry->invoice_id,
                'client_id'                 => $invoice_entry->client_id,
                'user_company'              => $invoice_entry->user_company,
                'invoice_amount_id'         => $invoice_entry->invoice_amount_id,
                'invoice_item_subtotal'     => $invoice_entry->invoice_item_subtotal,
                'invoice_item_tax_total'    => $invoice_entry->invoice_item_tax_total,
                'invoice_total'             => $invoice_entry->invoice_total,
                'invoice_sign'              => $invoice_entry->invoice_sign,
                'invoice_status_id'         => $invoice_entry->invoice_status_id,
                'invoice_date_created'      => $invoice_entry->invoice_date_created,
                'invoice_time_created'      => $invoice_entry->invoice_time_created,
                'invoice_number'            => $invoice_entry->invoice_number,
            ];

            $client_total_balance += $invoice_entry->invoice_total;

            $statement_transactions[] = $transaction;

        }

        foreach ($client_payments as $payment_entry) {

            $transaction = [
                'transaction_type'          => self::TRANSACTION_TYPE_PAYMENT,
                'transaction_date'          => $payment_entry->payment_date,
                'transaction_amount'        => $payment_entry->payment_amount,


                'invoice_id'                => $payment_entry->invoice_id,
                'client_id'                 => $payment_entry->client_id,
                'invoice_date_created'      => $payment_entry->invoice_date_created,
                'invoice_item_subtotal'     => $payment_entry->invoice_item_subtotal,
                'invoice_item_tax_total'    => $payment_entry->invoice_item_tax_total,
                'invoice_total'             => $payment_entry->invoice_total,
                'invoice_sign'              => $payment_entry->invoice_sign,
                'invoice_number'            => $payment_entry->invoice_number,
                'payment_id'                => $payment_entry->payment_id,
                'payment_method_id'         => $payment_entry->payment_method_id,
                'payment_method_name'       => $payment_entry->payment_method_name,
                'payment_date'              => $payment_entry->payment_date,
                'payment_amount'            => $payment_entry->payment_amount,

            ];

            $statement_transactions[] = $transaction;

            $client_total_balance -= $payment_entry->payment_amount;

        }

        usort($statement_transactions, array($this, "compare_statement_dates"));

        $this->mdl_statement->setStatement_transactions($statement_transactions);

        $this->mdl_statement->setStatement_balance($client_total_balance);


        return $this->mdl_statement;

    }




    /**
     */
    public function generate_pdf()
    {

        $client_id              = $this->input->post('cid');
        $statement_number       = $this->input->post('statement_number');

        // We should use the form value if supplied, otherwise the hidden field sdate
        $statement_start_date   = strtotime($this->input->post('sdate'));
        $statement_end_date     = strtotime($this->input->post('edate'));

        $statement_date         = $this->input->post('statement_date_created');
        $notes                  = $this->input->post('notes');


        $this->generate_statement_pdf($client_id, $statement_number, $statement_start_date, $statement_end_date, $statement_date, $notes);
    }

    /**
     * Generate the PDF for the statement
     *
     * @param $quote_id
     * @param bool $stream
     * @param null $quote_template
     *
     * @return string
     * @throws \Mpdf\MpdfException
     */
    function generate_statement_pdf($client_id, $statement_number, $statement_start_date, $statement_end_date, $statement_date, $notes)
    {


        $this->load->model('custom_fields/mdl_client_custom');

        /*
         * Load the client
         */
        $client = $this->mdl_clients
            ->where('ip_clients.client_id', $client_id)
            ->get()->row();

        if (!$client) {
            show_404();
        }

        $custom_fields = $this->mdl_client_custom->get_by_client($client_id)->result();

        $this->mdl_client_custom->prep_form($client_id);

        $statement = $this->build_statement($client_id, $statement_start_date, $statement_end_date);

        // Override language with system language
        set_language($client->client_language);

        $statement_template = "InvoicePlane";
        if (!$statement_template) {
            $statement_template = $this->mdl_settings->setting('pdf_statement_template');
        }

        $data = array(
            'client'        => $client,
            'statement'     => $statement,
            'notes'         => $notes,
            // 'custom_fields' => $custom_fields,
        );

        $html = $this->load->view('statement_templates/pdf/' . $statement_template, $data, true);

        $this->load->helper('mpdf');

        $pdf_password = null;
        $stream = true;
        return pdf_create($html, trans('statement') . '_' . str_replace(array('\\', '/'), '_', $statement->GetStatement_number()), $stream, $pdf_password);
    }


    /**
     * Compare 2 dates
     *
     * NOTE: I am not sure if $this is the correct scope for this function.
     *
     * @param string $a The first date in string format
     * @param string $b The second date in string format
     * @return number
     *    0 is the dates are the same
     *    1 if date A > date B
     *   -1 if date A < date B
     */
    private function compare_statement_dates($a, $b)
    {
        $timeA = strtotime($a['transaction_date']);
        $timeB = strtotime($b['transaction_date']);

        if($timeA == $timeB) {
            return 0;
        }

        return $timeA < $timeB ? -1 : 1;
    }

}
