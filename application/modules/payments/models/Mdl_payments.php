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
class Mdl_Payments extends Response_Model
{
    public $table = 'ip_payments';

    public $primary_key = 'ip_payments.payment_id';

    public $validation_rules = 'validation_rules';

    public function default_select()
    {
        $this->db->select('
            SQL_CALC_FOUND_ROWS
            ip_payment_methods.*,
            ip_invoice_amounts.*,
            ip_clients.client_name,
            ip_clients.client_surname,
            ip_clients.client_title,
            ip_clients.client_id,
            ip_invoices.invoice_number,
            ip_invoices.invoice_date_created,
            ip_payments.*', false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_payments.payment_date DESC, ip_payments.payment_id DESC');
    }

    public function default_join()
    {
        $this->db->join('ip_invoices', 'ip_invoices.invoice_id = ip_payments.invoice_id');
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_invoices.client_id');
        $this->db->join('ip_invoice_amounts', 'ip_invoice_amounts.invoice_id = ip_invoices.invoice_id');
        $this->db->join('ip_payment_methods', 'ip_payment_methods.payment_method_id = ip_payments.payment_method_id', 'left');
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return [
            'invoice_id' => [
                'field' => 'invoice_id',
                'label' => trans('invoice'),
                'rules' => 'required',
            ],
            'payment_date' => [
                'field' => 'payment_date',
                'label' => trans('date'),
                'rules' => 'required',
            ],
            'payment_amount' => [
                'field' => 'payment_amount',
                'label' => trans('payment'),
                'rules' => 'required|callback_validate_payment_amount',
            ],
            'payment_method_id' => [
                'field' => 'payment_method_id',
                'label' => trans('payment_method'),
            ],
            'payment_note' => [
                'field' => 'payment_note',
                'label' => trans('note'),
            ],
        ];
    }

    /**
     * @param $amount
     *
     * @return bool
     */
    public function validate_payment_amount($amount)
    {
        $amount     = (float) standardize_amount($amount);
        $invoice_id = $this->input->post('invoice_id');
        $payment_id = $this->input->post('payment_id');

        $invoice = $this->db->where('invoice_id', $invoice_id)->get('ip_invoice_amounts')->row();

        if ($invoice == null) {
            return false;
        }

        $invoice_balance = (float) $invoice->invoice_balance;

        if ($payment_id) {
            $payment = $this->db->where('payment_id', $payment_id)->get('ip_payments')->row();

            $invoice_balance += (float) $payment->payment_amount;
        }

        if ($amount > $invoice_balance) {
            $this->form_validation->set_message('validate_payment_amount', trans('payment_cannot_exceed_balance'));

            return false;
        }

        return true;
    }

    /**
     * Atomically record a payment received from an online gateway callback.
     *
     * The "is there still a balance owed" check and the payment insert must be
     * a single atomic operation. Two concurrent gateway callbacks for the same
     * invoice carrying distinct external references (so idx_payment_external_id
     * does not catch them) would otherwise both pass a separate balance check
     * and both insert, over-crediting the invoice and driving the balance
     * negative (CWE-362 / CWE-367).
     *
     * A single conditional UPDATE on ip_invoice_amounts is the gate: InnoDB
     * serialises it, so the second caller sees a balance that is no longer
     * outstanding and is refused before it can insert. calculate() then
     * reconciles the stored figures from the real payment rows.
     *
     * @param array $db_array invoice_id, payment_date, payment_amount,
     *                        payment_method_id, payment_note, payment_external_id
     *
     * @return bool true when the payment was recorded; false when the invoice
     *              balance no longer covered it (already paid or a concurrent
     *              callback won) or the external id was a replay
     */
    public function record_external_payment(array $db_array): bool
    {
        $this->load->model('invoices/mdl_invoice_amounts');

        $invoice_id  = (int) $db_array['invoice_id'];
        $amount      = (float) standardize_amount($db_array['payment_amount']);
        $external_id = isset($db_array['payment_external_id']) && $db_array['payment_external_id'] !== ''
            ? (string) $db_array['payment_external_id']
            : null;

        // One transaction for the whole sequence: the conditional balance claim,
        // the payment insert and the calculate() recalculation either all land
        // or none do. The UPDATE also holds a row lock on ip_invoice_amounts
        // until commit, so a concurrent caller's identical claim blocks here
        // instead of racing a stale balance.
        $this->db->trans_begin();

        // Atomic gate: claim the outstanding balance in one statement. The
        // "balance + epsilon >= amount" test (matching the controller's own
        // tolerance) also refuses an oversized payment, so a single capture
        // cannot drive the balance negative.
        $this->db->set('invoice_paid', sprintf('invoice_paid + %F', $amount), false);
        $this->db->set('invoice_balance', sprintf('invoice_balance - %F', $amount), false);
        $this->db->where('invoice_id', $invoice_id);
        $this->db->where(sprintf('invoice_balance + 0.0001 >= %F', $amount), null, false);
        $this->db->update('ip_invoice_amounts');

        if ($this->db->affected_rows() < 1) {
            $this->db->trans_rollback();
            log_message('warning', __CLASS__ . '::' . __FUNCTION__ . ' - Refused gateway payment for invoice ' . sanitize_for_logging($invoice_id) . ': balance no longer covers this amount (concurrent callback, already paid, or oversized).');

            return false;
        }

        // Claim held. Insert the payment; INSERT IGNORE so an external id that
        // raced past a wider balance is dropped by idx_payment_external_id
        // rather than raising a duplicate-key error. INSERT IGNORE does not mark
        // the transaction failed on the ignored row, so roll back explicitly.
        $this->db->set([
            'invoice_id'          => $invoice_id,
            'payment_date'        => date_to_mysql($db_array['payment_date']),
            'payment_amount'      => $amount,
            'payment_method_id'   => $db_array['payment_method_id'] ?: 0,
            'payment_note'        => (string) ($db_array['payment_note'] ?? ''),
            'payment_external_id' => $external_id,
        ]);
        $insert_sql = preg_replace('/^INSERT INTO/i', 'INSERT IGNORE INTO', $this->db->get_compiled_insert('ip_payments'), 1);
        $this->db->query($insert_sql);

        if ($this->db->affected_rows() < 1) {
            $this->db->trans_rollback();
            log_message('warning', __CLASS__ . '::' . __FUNCTION__ . ' - Duplicate external payment id for invoice ' . sanitize_for_logging($invoice_id) . '; balance claim rolled back.');

            return false;
        }

        // Reconcile invoice_paid = SUM(payment_amount) and the balance from the
        // real rows, and mark the invoice paid when it is fully covered.
        $global_discount['item'] = $this->mdl_invoice_amounts->get_global_discount($invoice_id);
        $this->mdl_invoice_amounts->calculate($invoice_id, $global_discount);

        $amounts = $this->db->where('invoice_id', $invoice_id)->get('ip_invoice_amounts')->row();

        if ($amounts !== null && (float) $amounts->invoice_paid >= (float) $amounts->invoice_total) {
            $this->db->where('invoice_id', $invoice_id);
            $this->db->set('invoice_status_id', 4);
            $this->db->update('ip_invoices');

            $this->mdl_invoice_amounts->calculate($invoice_id, $global_discount);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Transaction failed recording gateway payment for invoice ' . sanitize_for_logging($invoice_id) . '; rolled back.');

            return false;
        }

        $this->db->trans_commit();

        return true;
    }

    /**
     * @return bool|int|null
     */
    public function save($id = null, $db_array = null)
    {
        $db_array = ($db_array) ? $db_array : $this->db_array();
        $this->load->model('invoices/mdl_invoice_amounts');

        // Save the payment
        $id = parent::save($id, $db_array);

        $global_discount['item'] = $this->mdl_invoice_amounts->get_global_discount($db_array['invoice_id']);
        // Recalculate invoice amounts
        $this->mdl_invoice_amounts->calculate($db_array['invoice_id'], $global_discount);

        // Set proper status for the invoice
        $invoice = $this->db->where('invoice_id', $db_array['invoice_id'])->get('ip_invoice_amounts')->row();

        if ($invoice == null) {
            return false;
        }

        // Calculate sum for payments
        $paid  = (float) $invoice->invoice_paid;
        $total = (float) $invoice->invoice_total;

        if ($paid >= $total) {
            $this->db->where('invoice_id', $db_array['invoice_id']);
            $this->db->set('invoice_status_id', 4);
            $this->db->update('ip_invoices');
        }

        $global_discount['item'] = $this->mdl_invoice_amounts->get_global_discount($db_array['invoice_id']);
        // Recalculate invoice amounts
        $this->mdl_invoice_amounts->calculate($db_array['invoice_id'], $global_discount);

        return $id;
    }

    /**
     * @return array
     */
    public function db_array()
    {
        $db_array = parent::db_array();

        $db_array['payment_date']   = date_to_mysql($db_array['payment_date']);
        $db_array['payment_amount'] = standardize_amount($db_array['payment_amount']);

        return $db_array;
    }

    public function delete($id = null)
    {
        // Get the invoice id before deleting payment
        $this->db->select('invoice_id');
        $this->db->where('payment_id', $id);

        $invoice_id = $this->db->get('ip_payments')->row()->invoice_id;

        // Delete the payment
        parent::delete($id);

        $this->load->model('invoices/mdl_invoice_amounts');
        $global_discount['item'] = $this->mdl_invoice_amounts->get_global_discount($invoice_id);
        // Recalculate invoice amounts
        $this->mdl_invoice_amounts->calculate($invoice_id, $global_discount);

        // Change invoice status back to sent
        $this->db->select('invoice_status_id');
        $this->db->where('invoice_id', $invoice_id);

        $invoice = $this->db->get('ip_invoices')->row();

        if ($invoice->invoice_status_id == 4) {
            $this->db->where('invoice_id', $invoice_id);
            $this->db->set('invoice_status_id', 2);
            $this->db->update('ip_invoices');
        }

        $this->load->helper('orphan');
        delete_orphans();
    }

    public function prep_form($id = null): bool
    {
        if ( ! parent::prep_form($id)) {
            return false;
        }

        if ( ! $id) {
            parent::set_form_value('payment_date', date('Y-m-d'));
        }

        return true;
    }

    /**
     * @param $client_id
     *
     * @return $this
     */
    public function by_client($client_id)
    {
        $this->filter_where('ip_clients.client_id', $client_id);

        return $this;
    }
}
