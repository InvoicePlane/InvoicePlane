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
 * Class Cron
 */
class Cron extends Base_Controller
{
    /**
     * @param string|null $cron_key
     */
    public function recur($cron_key = null)
    {
        // Check the provided cron key
        if ($cron_key != get_setting('cron_key')) {
            if (IP_DEBUG) log_message('error', 'Wrong cron key provided!');
            exit('Wrong cron key!');
        }

        $this->load->model('invoices/mdl_invoices_recurring');
        $this->load->model('invoices/mdl_invoices');
        $this->load->helper('mailer');

        // Gather a list of recurring invoices to generate
        $invoices_recurring = $this->mdl_invoices_recurring->active()->get()->result();

        foreach ($invoices_recurring as $invoice_recurring) {
            // This is the original invoice id
            $source_id = $invoice_recurring->invoice_id;

            // This is the original invoice
            // $invoice = $this->db->where('ip_invoices.invoice_id', $source_id)->get('ip_invoices')->row();
            $invoice = $this->mdl_invoices->get_by_id($source_id);

            // Create the new invoice
            $db_array = array(
                'client_id' => $invoice->client_id,
                'invoice_date_created' => $invoice_recurring->recur_next_date,
                'invoice_date_due' => $this->mdl_invoices->get_date_due($invoice_recurring->recur_next_date),
                'invoice_group_id' => $invoice->invoice_group_id,
                'user_id' => $invoice->user_id,
                'invoice_number' => $this->mdl_invoices->get_invoice_number($invoice->invoice_group_id),
                'invoice_url_key' => $this->mdl_invoices->get_url_key(),
                'invoice_terms' => $invoice->invoice_terms
            );

            // This is the new invoice id
            $target_id = $this->mdl_invoices->create($db_array, false);

            // Copy the original invoice to the new invoice
            $this->mdl_invoices->copy_invoice($source_id, $target_id);

            // Update the next recur date for the recurring invoice
            $this->mdl_invoices_recurring->set_next_recur_date($invoice_recurring->invoice_recurring_id);

            // Email the new invoice if applicable
            if (get_setting('automatic_email_on_recur') && mailer_configured()) {
                $new_invoice = $this->mdl_invoices->get_by_id($target_id);

                // Set the email body, use default email template if available
                $this->load->model('email_templates/mdl_email_templates');

                $email_template_id = get_setting('email_invoice_template');
                if (!$email_template_id) {
                    return;
                }

                $email_template = $this->mdl_email_templates->where('email_template_id', $email_template_id)->get();
                if ($email_template->num_rows() == 0) {
                    return;
                }

                $tpl = $email_template->row();

                // Prepare the attachments
                $this->load->model('upload/mdl_uploads');
                $attachment_files = $this->mdl_uploads->get_invoice_uploads($target_id);

                // Prepare the body
                $body = $tpl->email_template_body;
                if (strlen($body) != strlen(strip_tags($body))) {
                    $body = htmlspecialchars_decode($body);
                } else {
                    $body = htmlspecialchars_decode(nl2br($body));
                }

                $from = !empty($tpl->email_template_from_email) ?
                    array($tpl->email_template_from_email, $tpl->email_template_from_name) :
                    array($invoice->user_email, "");

                $subject = !empty($tpl->email_template_subject) ?
                    $tpl->email_template_subject :
                    trans('invoice') . ' #' . $new_invoice->invoice_number;

                $pdf_template = $tpl->email_template_pdf_template;
                $to = $invoice->client_email;
                $cc = $tpl->email_template_cc;
                $bcc = $tpl->email_template_bcc;

                $email_invoice = email_invoice($target_id, $pdf_template, $from, $to, $subject, $body, $cc, $bcc, $attachment_files);

                if ($email_invoice) {
                    $this->mdl_invoices->mark_sent($target_id);
                    $this->mdl_invoice_amounts->calculate($target_id);
                } else {
                    log_message('error', 'Invoice ' . $target_id . 'could not be sent. Please review your Email settings.');
                }
            }
        }

        log_message('debug', '[Recurring Invoices] ' . count($invoices_recurring) . ' recurring invoices processed');
    }

}
