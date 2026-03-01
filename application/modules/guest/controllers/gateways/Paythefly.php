<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane - PayTheFly Pro Gateway Controller
 *
 * Handles the crypto payment flow via PayTheFly Pro:
 * 1. Generates EIP-712 signed payment URLs
 * 2. Redirects users to the PayTheFly payment page
 * 3. Handles return callbacks after payment
 *
 * @author      PayTheFly Integration
 * @link        https://pro.paythefly.com
 */

#[AllowDynamicProperties]
class Paythefly extends Base_Controller
{
    /**
     * @var PaytheflyLib
     */
    protected $lib_paythefly;

    public function __construct()
    {
        parent::__construct();

        $this->load->library('crypt');
        $this->load->model('invoices/mdl_invoices');

        // Initialize the PayTheFly library with settings
        $this->load->library('gateways/PaytheflyLib', [
            'project_id'       => get_setting('gateway_paythefly_projectId'),
            'private_key'      => $this->crypt->decode(get_setting('gateway_paythefly_privateKey')),
            'project_key'      => $this->crypt->decode(get_setting('gateway_paythefly_projectKey')),
            'contract_address' => get_setting('gateway_paythefly_contractAddress'),
            'default_chain'    => get_setting('gateway_paythefly_defaultChain') ?: 'BSC',
            'deadline_offset'  => (int) (get_setting('gateway_paythefly_deadlineMinutes') ?: 30) * 60,
        ], 'lib_paythefly');
    }

    /**
     * Initiate a crypto payment for the given invoice.
     *
     * Generates the EIP-712 signed payment URL and redirects
     * the user to the PayTheFly Pro payment page.
     *
     * @param string $invoice_url_key The invoice URL key
     *
     * @return void
     */
    public function pay($invoice_url_key)
    {
        $invoice = $this->mdl_invoices
            ->where('ip_invoices.invoice_url_key', $invoice_url_key)
            ->get()
            ->row();

        if ( ! $invoice) {
            $this->session->set_flashdata('alert_error', lang('invoice_not_found'));
            redirect('guest');
            return;
        }

        // Check if the invoice is payable
        if ($invoice->invoice_balance <= 0) {
            $this->session->set_flashdata('alert_info', lang('invoice_already_paid'));
            redirect('guest/view/invoice/' . $invoice->invoice_url_key);
            return;
        }

        // Determine chain from request or default
        $chain = $this->input->get('chain') ?: (get_setting('gateway_paythefly_defaultChain') ?: 'BSC');

        // Determine token address (native token by default)
        $tokenAddress = $this->input->get('token') ?: null;

        // Use the invoice number as serial number for tracking
        $serialNo = $invoice->invoice_number;

        try {
            // Generate the signed payment URL
            $payment = $this->lib_paythefly->generatePaymentUrl(
                $serialNo,
                (float) $invoice->invoice_balance,
                $chain,
                $tokenAddress
            );

            // Log the payment initiation
            log_message('info', sprintf(
                'PayTheFly: Payment initiated for invoice #%s (ID: %d), amount: %s, chain: %s, deadline: %d',
                $invoice->invoice_number,
                $invoice->invoice_id,
                $invoice->invoice_balance,
                $chain,
                $payment['deadline']
            ));

            // Record the payment attempt in merchant responses
            $this->db->insert('ip_merchant_responses', [
                'invoice_id'                   => $invoice->invoice_id,
                'merchant_response_successful' => 0,
                'merchant_response_date'       => date('Y-m-d'),
                'merchant_response_driver'     => 'PayTheFly',
                'merchant_response'            => 'Payment initiated. Chain: ' . $chain
                    . ', Amount: ' . $invoice->invoice_balance
                    . ', Deadline: ' . date('Y-m-d H:i:s', $payment['deadline']),
                'merchant_response_reference'  => 'serial_no:' . $serialNo,
            ]);

            // Redirect to PayTheFly payment page
            redirect($payment['url']);

        } catch (Exception $e) {
            log_message('error', 'PayTheFly: Payment URL generation failed: ' . $e->getMessage());

            $this->session->set_flashdata('alert_error', lang('paythefly_payment_error'));
            redirect('guest/view/invoice/' . $invoice->invoice_url_key);
        }
    }

    /**
     * Handle the return callback after payment.
     *
     * This is the page the user lands on after completing/canceling
     * payment on PayTheFly. The actual payment confirmation happens
     * via webhook, so this just shows a status message.
     *
     * @param string $invoice_url_key
     *
     * @return void
     */
    public function callback($invoice_url_key)
    {
        $invoice = $this->mdl_invoices
            ->where('ip_invoices.invoice_url_key', $invoice_url_key)
            ->get()
            ->row();

        if ( ! $invoice) {
            $this->session->set_flashdata('alert_error', lang('invoice_not_found'));
            redirect('guest');
            return;
        }

        $status = $this->input->get('status');

        if ($status === 'success') {
            $this->session->set_flashdata(
                'alert_success',
                sprintf(lang('paythefly_payment_processing'), $invoice->invoice_number)
            );
        } elseif ($status === 'cancelled') {
            $this->session->set_flashdata('alert_info', lang('paythefly_payment_cancelled'));
        } else {
            $this->session->set_flashdata(
                'alert_info',
                lang('paythefly_payment_pending')
            );
        }

        redirect('guest/view/invoice/' . $invoice->invoice_url_key);
    }

    /**
     * API endpoint to get payment information (for AJAX calls from the frontend).
     *
     * Returns JSON with payment URL and chain details for the invoice.
     *
     * @param string $invoice_url_key
     *
     * @return void
     */
    public function get_payment_info($invoice_url_key)
    {
        $invoice = $this->mdl_invoices
            ->where('ip_invoices.invoice_url_key', $invoice_url_key)
            ->get()
            ->row();

        if ( ! $invoice || $invoice->invoice_balance <= 0) {
            $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['error' => 'Invoice not payable']));
            return;
        }

        $chain = $this->input->get('chain') ?: (get_setting('gateway_paythefly_defaultChain') ?: 'BSC');

        try {
            $payment = $this->lib_paythefly->generatePaymentUrl(
                $invoice->invoice_number,
                (float) $invoice->invoice_balance,
                $chain
            );

            $chainConfig = $this->lib_paythefly->getChainConfig($chain);

            $this->output->set_output(json_encode([
                'payment_url' => $payment['url'],
                'chain'       => $chain,
                'chain_id'    => $payment['chainId'],
                'deadline'    => $payment['deadline'],
                'amount'      => $invoice->invoice_balance,
                'serial_no'   => $invoice->invoice_number,
                'token'       => $chainConfig['nativeToken'],
                'symbol'      => $chainConfig['symbol'],
            ]));

        } catch (Exception $e) {
            $this->output
                ->set_status_header(500)
                ->set_output(json_encode(['error' => 'Failed to generate payment']));
        }
    }
}
