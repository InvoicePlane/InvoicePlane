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
class Paypal extends Base_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file_security');
        $this->_create_client();
    }

    /**
     * Create the order on PayPal that is then processed when
     * the user inserts the payment method.
     *
     * @param string $invoice_url_key
     *
     * @return json the PayPal object to be loaded in the JS SDK script
     */
    public function paypal_create_order($invoice_url_key)
    {
        // Require POST request to prevent CSRF attacks
        if ($this->input->method() !== 'post') {
            show_404();
        }

        // Check if the invoice exists and is billable
        $this->load->model('invoices/mdl_invoices');

        $invoice = $this->mdl_invoices->where('ip_invoices.invoice_url_key', $invoice_url_key)->get()->row();

        // Check if the invoice is payable
        if ($invoice->invoice_balance <= 0) {
            $this->session->set_userdata('alert_error', lang('invoice_already_paid'));
            redirect(site_url('guest/view/invoice/' . $invoice->invoice_url_key));
        }

        //create the order
        $paypal_client = $this->lib_paypal->createOrder([
            'invoice_id'    => $invoice->invoice_id,
            'currency_code' => get_setting('gateway_paypal_currency'),
            'value'         => $invoice->invoice_balance,
            'custom_id'     => $invoice_url_key,
        ]);

        // Decode the PayPal response
        $paypal_response = json_decode($paypal_client, true);

        // Handle JSON decode errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', 'PayPal createOrder JSON decode error for invoice ' . sanitize_for_logging($invoice_url_key) . ': ' . sanitize_for_logging(json_last_error_msg()));
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Invalid response from payment gateway']));

            return;
        }

        // Validate required fields from PayPal response
        if (empty($paypal_response['id'])) {
            log_message('error', 'PayPal createOrder missing order ID for invoice ' . sanitize_for_logging($invoice_url_key));
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Invalid response from payment gateway']));

            return;
        }

        // Add refreshed CSRF token to response (token regenerates on each POST)
        $response = [
            'id'         => $paypal_response['id'],
            'status'     => $paypal_response['status'] ?? null,
            'csrf_token' => $this->security->get_csrf_hash(),
        ];

        // Preserve any additional fields from PayPal response
        foreach ($paypal_response as $key => $value) {
            if ( ! isset($response[$key])) {
                $response[$key] = $value;
            }
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Capture the payment which is put on hold on PayPal
     * after the user has set the card details.
     *
     *
     * @return void
     */
    public function paypal_capture_payment(string $order_id)
    {
        // Require POST request to prevent CSRF attacks
        if ($this->input->method() !== 'post') {
            show_404();
        }

        $paypal_response = $this->lib_paypal->captureOrder($order_id);

        //handle the payment
        if ($paypal_response['status']) {
            $paypal_object = json_decode($paypal_response['response']->getBody());

            // Set the status of the actual transaction (not just the API call result.)
            $capture_status = mb_strtoupper($paypal_object->purchase_units[0]->payments->captures[0]->status) ?? null;

            // If either Completed or Pending, we're treating it as completed from the buyer's perspective.
            if ($capture_status === 'COMPLETED' || $capture_status === 'PENDING') {
                // Extract payment data with defensive null safety checks at each level
                $purchase_units = $paypal_object->purchase_units ?? null;
                $payments       = $purchase_units[0]->payments ?? null;
                $captures       = $payments->captures ?? null;
                $capture_data   = $captures[0] ?? null;

                if ( ! $capture_data) {
                    log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Invalid PayPal response structure: missing capture data');
                    throw new Exception('Invalid PayPal response structure');
                }

                $invoice_id = $capture_data->invoice_id ?? null;
                $amount     = $capture_data->amount->value ?? null;
                $capture_id = $capture_data->id ?? null;

                // Validate required fields
                if (empty($invoice_id) || empty($amount) || empty($capture_id)) {
                    log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Missing required PayPal data fields');
                    throw new Exception('Missing required PayPal data');
                }

                $capture_id = (string) $capture_id; // Ensure string type

                // Validate and sanitize the capture_id
                if (mb_strlen($capture_id) > 255) {
                    log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - PayPal capture ID too long: ' . mb_strlen($capture_id) . ' characters');
                    throw new Exception('Invalid capture ID length');
                }

                //record the payment
                $this->load->model('payments/mdl_payments');

                // Check if this capture_id has already been processed (deduplication check)
                $existing_payment = $this->db
                    ->where('payment_external_id', $capture_id)
                    ->get('ip_payments')
                    ->row();

                if ($existing_payment) {
                    // Duplicate payment attempt detected
                    log_message('warning', __CLASS__ . '::' . __FUNCTION__ . ' - Duplicate payment attempt blocked. PayPal capture ID: ' . sanitize_for_logging($capture_id) . ' already exists as payment_id: ' . sanitize_for_logging($existing_payment->payment_id));

                    $invoice = $this->mdl_invoices->where('ip_invoices.invoice_id', $invoice_id)->get()->row();
                    $this->session->set_flashdata('alert_info', trans('online_payment_already_processed'));
                    $this->session->keep_flashdata('alert_info');
                } else {
                    // Check if invoice is already fully paid
                    $invoice = $this->mdl_invoices->where('ip_invoices.invoice_id', $invoice_id)->get()->row();

                    if ($invoice->invoice_balance <= 0) {
                        log_message('warning', __CLASS__ . '::' . __FUNCTION__ . ' - Payment rejected. Invoice ' . sanitize_for_logging($invoice->invoice_number) . ' already fully paid. Balance: ' . sanitize_for_logging($invoice->invoice_balance));
                        $this->session->set_flashdata('alert_info', trans('invoice_already_paid'));
                        $this->session->keep_flashdata('alert_info');
                    } else {
                        // If the payment status is pending, set a note accordingly.
                        $payment_note = ($capture_status === 'PENDING') ? trans('online_payment_pending') : '';

                        $this->mdl_payments->save(null, [
                            'invoice_id'          => $invoice_id,
                            'payment_date'        => date('Y-m-d'),
                            'payment_amount'      => $amount,
                            'payment_method_id'   => get_setting('gateway_paypal_payment_method'),
                            'payment_note'        => $payment_note,
                            'payment_external_id' => $capture_id,
                        ]);

                        $this->session->set_flashdata('alert_success', sprintf(trans('online_payment_payment_successful'), $invoice->invoice_number));
                        $this->session->keep_flashdata('alert_success');
                    }
                }

                /*
                 * merchant_response_success will be set to true for both completed and pending,
                 * so that it will show up as green in the logs.
                 *
                 * In the future we could include a "pending" status, and maybe show it
                 * as blue..??
                 *
                 * TODO Add Pending Status
                 *
                 * merchant_response is now the actual capture status.
                 */
                $this->db->insert('ip_merchant_responses', [
                    'invoice_id'                   => $invoice_id,
                    'merchant_response_successful' => true,
                    'merchant_response_date'       => date('Y-m-d'),
                    'merchant_response_driver'     => 'paypal',
                    'merchant_response'            => $capture_status,
                    'merchant_response_reference'  => 'Resource ID:' . $paypal_object->id,
                ]);
            } else {
                // Payment failed (DECLINED or any other non-success status)
                $invoice_id = $paypal_object->purchase_units[0]->payments->captures[0]->invoice_id ?? null;

                // If we can't get invoice_id from captures, try to get it from order details
                if ( ! $invoice_id) {
                    $order_details = json_decode($this->lib_paypal->showOrderDetails($order_id));
                    $invoice_id    = $order_details->purchase_units[0]->payments->captures[0]->invoice_id ?? null;
                }

                // Get processor response code if available.
                $processor_response_code = $paypal_object->purchase_units[0]->payments->captures[0]->processor_response->response_code ?? 'Unknown error';

                // Record the failed transaction in the logs along with processor response code.
                $this->db->insert('ip_merchant_responses', [
                    'invoice_id'                   => $invoice_id,
                    'merchant_response_successful' => false,
                    'merchant_response_date'       => date('Y-m-d'),
                    'merchant_response_driver'     => 'paypal',
                    'merchant_response'            => $capture_status . ': ' . $processor_response_code,
                    'merchant_response_reference'  => 'Resource ID:' . $paypal_object->id,
                ]);

                //set error message to be flashed
                $this->session->set_flashdata(
                    'alert_error',
                    trans('online_payment_payment_failed')
                );
                $this->session->keep_flashdata('alert_error');
            }
        } else {
            $response_error = json_decode($paypal_response['error']->getResponse()->getBody());

            //get the order details to have the invoice id from paypal
            $order_details = json_decode($this->lib_paypal->showOrderDetails($order_id));

            //record the failed transaction in the logs
            $this->db->insert('ip_merchant_responses', [
                'invoice_id'                   => $order_details->purchase_units[0]->payments->captures[0]->invoice_id,
                'merchant_response_successful' => false,
                'merchant_response_date'       => date('Y-m-d'),
                'merchant_response_driver'     => 'paypal',
                'merchant_response'            => 'name: ' . $response_error->name . '; details: ' . $response_error->details[0]->description,
                'merchant_response_reference'  => 'Resource ID:' . $order_id,
            ]);

            //set error message to be flashed
            $this->session->set_flashdata(
                'alert_error',
                trans('online_payment_payment_failed')
            );
            $this->session->keep_flashdata('alert_error');
        }
    }

    protected function _create_client(): void
    {
        $this->load->library('crypt');

        //load the REST API consumer library
        $this->load->library('gateways/PaypalLib', [
            'client_id'     => get_setting('gateway_paypal_clientId'),
            'client_secret' => $this->crypt->decode(get_setting('gateway_paypal_clientSecret')),
            'demo'          => get_setting('gateway_paypal_testMode') == 1,
        ], 'lib_paypal');
    }
}
