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
     * Statement constructor.
     */
    public function __construct()
    {
        parent::__construct();

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

            if (!empty($this->input->post('statement_start_date'))) {
                // BUG : strtotime is not recognising the date format d M,Y" and changing the date
                // $statement_start_date   = strtotime($this->input->post('statement_start_date'));
                $date_time   = date_create_from_format("d M,Y", $this->input->post('statement_start_date'));
                $statement_start_date   = $date_time->getTimestamp();

            } else {
                $statement_start_date   = strtotime($this->input->post('sdate'));
            }
            $statement_end_date     = $this->input->post('edate');

            // BUG : strtotime is not recognising the date format d M,Y" and changing the date
            // $statement_date         = strtotime($this->input->post('statement_date_created'));
            $date_time   = date_create_from_format("d M,Y", $this->input->post('statement_date_created'));
            $statement_date   = $date_time->getTimestamp();

            $statement_number       = $this->input->post('statement_number');
            $notes                  = $this->input->post('notes');

        } else {

            $statement_start_date   = null;
            $statement_end_date     = null;

            $statement_number       = null;
            $statement_date         = null;
            $notes                  = null;

        }

        $statement = $this->build_statement($client_id, $statement_start_date, $statement_end_date, $statement_date, $statement_number);

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
     * Populate the Statement model
     *
     *
     * @param Mdl_Clients $client
     * @param $statement_start_date
     * @param $statement_end_date
     * @param $statement_date,
     * @param $statement_number
     *
     */
    private function build_statement($client_id, $statement_start_date = null, $statement_end_date = null, $statement_date = null, $statement_number = null)
    {
        $this->load->model('mdl_statement');

        $this->load->model('invoices/mdl_invoices');
        $this->load->model('payments/mdl_payments');


        /*
         * Use the user supplied start date, or set the start date to a month ago
         */
        if (empty($statement_start_date)) {
            $statement_start_date = strtotime("-1 month");
        }

        /*
         * Use the user supplied end date, or draw the statement up to now
         */
        if (empty($statement_end_date)) {
            $statement_end_date = time();
        }

        /*
         * Use the user supplied statament date, or use the current date
         */
        if (empty($statement_date)) {
            $statement_date = time();
        }

        /*
         * Create the statement number based on the client id and date, or overwrite it with the user value.
         */
        if (!empty($statement_number)) {
            $this->mdl_statement->setStatement_number($statement_number);
        } else {
            $this->mdl_statement->setStatement_number( 'STM-' . $client_id . '-' . date('ymd'));
        }

        /*
         * Set the statement date to now, or overwrite it with the user value.
         */
        $this->mdl_statement->setStatement_date($statement_date);


        /*
         * Calculate the opening statement as from the start of the user account to the start of the statement period
         */
        $opening_balance_start_date = null;
        $opening_balance_end_date   = $statement_start_date;


        $client_invoices = $this->mdl_invoices
            ->by_client($client_id)
            ->by_date_range(date('Y-m-d', $opening_balance_start_date), date('Y-m-d', $opening_balance_end_date))
            ->get()
            ->result();
        $client_payments = $this->mdl_payments
            ->by_client($client_id)
            ->by_date_range(date('Y-m-d', $opening_balance_start_date), date('Y-m-d', $opening_balance_end_date))
            ->get()
            ->result();


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


        $this->mdl_statement->setStatement_start_date($statement_start_date);
        $this->mdl_statement->setStatement_end_date($statement_end_date);

        /*
         * NOTE: These two calls brings back all invoices and payments over the
         * ...date range, and we manually sum up the totals
         */

        $client_invoices = $this->mdl_invoices
            ->by_client($client_id)
            ->by_date_range(date('Y-m-d', $statement_start_date), date('Y-m-d', $statement_end_date))
            ->get()
            ->result();
        $client_payments = $this->mdl_payments
            ->by_client($client_id)
            ->by_date_range(date('Y-m-d', $statement_start_date), date('Y-m-d', $statement_end_date))
            ->get()
            ->result();


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
     * Controller action to print pdf. From POST action.
     *
     */
    public function generate_pdf()
    {
        $this->load->model('clients/mdl_clients');
        $this->load->helper('country');

        $client_id              = $this->input->post('cid');
        $statement_number       = $this->input->post('statement_number');

        if (!empty($this->input->post('statement_start_date'))) {
            //$statement_start_date   = strtotime($this->input->post('statement_start_date'));
            $date_time   = date_create_from_format("d M,Y", $this->input->post('statement_start_date'));
            $statement_start_date   = $date_time->getTimestamp();

        } else {
            $statement_start_date   = strtotime($this->input->post('sdate'));
        }
        $statement_end_date     = strtotime($this->input->post('edate'));

        // BUG : strtotime is not recognising the date format d M,Y" and changing the date
        // $statement_date         = strtotime($this->input->post('statement_date_created'));
        $date_time   = date_create_from_format("d M,Y", $this->input->post('statement_date_created'));
        $statement_date   = $date_time->getTimestamp();


        $notes                  = $this->input->post('notes');

        /*
         * Load the client
         */
        $client = $this->mdl_clients
            ->where('ip_clients.client_id', $client_id)
            ->get()->row();

        if (!$client) {
            show_404();
        }

        $statement = $this->build_statement($client->client_id, $statement_start_date, $statement_end_date);

        $this->load->helper('pdf');

        generate_statement_pdf($client, $statement, $notes);
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
