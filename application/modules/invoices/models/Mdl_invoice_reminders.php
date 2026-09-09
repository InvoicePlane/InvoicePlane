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
class Mdl_Invoice_Reminders extends MY_Model
{
    public $table = 'ip_invoice_reminders';

    public $primary_key = 'ip_invoice_reminders.reminder_id';

    public function default_select(): void
    {
        $this->db->select('ip_invoice_reminders.*', false);
    }

    public function default_order_by(): void
    {
        $this->db->order_by('ip_invoice_reminders.reminder_date_sent DESC, ip_invoice_reminders.reminder_id DESC');
    }

    /**
     * Invoices that a reminder may be sent for today.
     *
     * Built standalone rather than through Mdl_Invoices scopes on purpose:
     * Mdl_Invoices::default_select() carries SQL_CALC_FOUND_ROWS and five joins this
     * pass has no use for, and its is_overdue is a computed column needing HAVING.
     *
     * days_relative is DATEDIFF(CURDATE(), invoice_date_due) — negative before due,
     * positive once overdue. It is computed in SQL, matching the basis the existing
     * is_overdue / days_overdue columns already use, so reminders stay consistent with
     * what the invoice list shows.
     *
     * @return array
     */
    public function eligible_invoices()
    {
        return $this->db
            ->select(
                'ip_invoices.invoice_id,
                 ip_invoices.invoice_date_due,
                 ip_invoices.client_id,
                 ip_clients.client_email,
                 DATEDIFF(CURDATE(), ip_invoices.invoice_date_due) AS days_relative',
                false
            )
            ->from('ip_invoices')
            ->join('ip_clients', 'ip_clients.client_id = ip_invoices.client_id')
            ->join('ip_invoice_amounts', 'ip_invoice_amounts.invoice_id = ip_invoices.invoice_id', 'left')
            ->where_in('ip_invoices.invoice_status_id', [2, 3])
            ->where('IFNULL(ip_invoice_amounts.invoice_balance, 0) > 0', null, false)
            ->where('ip_invoices.invoice_disable_reminders', 0)
            ->where('ip_clients.client_disable_reminders', 0)
            ->where('ip_clients.client_active', 1)
            ->where("IFNULL(ip_clients.client_email, '') <> ''", null, false)
            ->order_by('ip_invoices.invoice_id')
            ->get()
            ->result();
    }

    /**
     * Slot keys already recorded, grouped by invoice.
     *
     * Fetched for the whole candidate set at once rather than per invoice, so a cron
     * run over a few thousand open invoices stays at one query instead of thousands.
     *
     * @param int[] $invoice_ids
     *
     * @return array<int, string[]> [invoice_id => ['before_due:3', 'overdue:1', ...]]
     */
    public function logged_slots_for(array $invoice_ids): array
    {
        if ($invoice_ids === []) {
            return [];
        }

        $this->load->helper('invoice_reminder');

        $rows = $this->db
            ->select('invoice_id, reminder_type, reminder_offset')
            ->from($this->table)
            ->where_in('invoice_id', $invoice_ids)
            ->get()
            ->result();

        $slots = [];
        foreach ($rows as $row) {
            $slots[(int) $row->invoice_id][] = reminder_slot_key(
                (string) $row->reminder_type,
                (int) $row->reminder_offset
            );
        }

        return $slots;
    }

    /**
     * Number of reminders actually delivered per invoice, for the max-total cap.
     * Skipped and failed slots do not count against it — no email left the building.
     *
     * @param int[] $invoice_ids
     *
     * @return array<int, int>
     */
    public function sent_counts_for(array $invoice_ids): array
    {
        if ($invoice_ids === []) {
            return [];
        }

        $rows = $this->db
            ->select('invoice_id, COUNT(*) AS total', false)
            ->from($this->table)
            ->where_in('invoice_id', $invoice_ids)
            ->where('reminder_status', 'sent')
            ->group_by('invoice_id')
            ->get()
            ->result();

        $counts = [];
        foreach ($rows as $row) {
            $counts[(int) $row->invoice_id] = (int) $row->total;
        }

        return $counts;
    }

    /**
     * Reserve a reminder slot before sending it.
     *
     * INSERT IGNORE leans on the idx_reminder_slot unique key: if a concurrent cron run
     * already holds this slot the insert is a no-op, affected_rows() is 0 and the caller
     * skips the send. These tables are MyISAM, so there is no transaction to fall back
     * on and the unique index is the only real guard — this mirrors the atomic upsert
     * the cron rate limiter already uses in Cron.php.
     */
    public function claim(int $invoice_id, string $type, int $offset, string $status = 'pending'): bool
    {
        $this->db->query(
            'INSERT IGNORE INTO ip_invoice_reminders
             (invoice_id, reminder_type, reminder_offset, reminder_status, reminder_date_sent)
             VALUES (?, ?, ?, ?, ?)',
            [$invoice_id, $type, $offset, $status, date('Y-m-d H:i:s')]
        );

        return $this->db->affected_rows() > 0;
    }

    /**
     * Record the outcome of a claimed slot.
     */
    public function mark_result(int $invoice_id, string $type, int $offset, string $status, ?string $to = null): void
    {
        $this->db
            ->where('invoice_id', $invoice_id)
            ->where('reminder_type', $type)
            ->where('reminder_offset', $offset)
            ->update($this->table, [
                'reminder_status'    => $status,
                'reminder_date_sent' => date('Y-m-d H:i:s'),
                'reminder_email_to'  => $to,
            ]);
    }

    /**
     * Release a claimed slot so a later run can retry it. Used when the send never got
     * as far as an actual delivery attempt.
     */
    public function release(int $invoice_id, string $type, int $offset): void
    {
        $this->db
            ->where('invoice_id', $invoice_id)
            ->where('reminder_type', $type)
            ->where('reminder_offset', $offset)
            ->delete($this->table);
    }

    /**
     * Reminder history for one invoice, newest first.
     */
    public function by_invoice(int $invoice_id)
    {
        $this->filter_where('ip_invoice_reminders.invoice_id', $invoice_id);

        return $this;
    }
}
