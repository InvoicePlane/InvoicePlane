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
class Mdl_Uploads extends Response_Model
{
    public $table = 'ip_uploads';

    public $primary_key = 'ip_uploads.upload_id';

    public $date_modified_field = 'uploaded_date';

    public $content_types =
    [
        'avif' => 'image/avif',
        'gif'  => 'image/gif',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'svg'  => 'image/svg+xml',
        'webp' => 'image/webp',
        'txt'  => 'text/plain',
        'xml'  => 'text/xml',
        'pdf'  => 'application/pdf',
        // file-audio
        'mp3'  => 'audio/mpeg',
        'oga'  => 'audio/ogg',
        'ogg'  => 'audio/ogg',
        'wav'  => 'audio/x-wav',
        'weba' => 'audio/webm',
        // file-document
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'odt'  => 'application/vnd.oasis.opendocument.text',
        // file-spreadsheet
        'xls'  => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ods'  => 'application/vnd.oasis.opendocument.spreadsheet',
        // file-presentation
        'ppt'  => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'odp'  => 'application/vnd.oasis.opendocument.presentation',
    ];

    public function default_order_by()
    {
        $this->db->order_by('ip_uploads.upload_id ASC');
    }

    /**
     * @param null $db_array
     * @return int|null
     */
    public function create($db_array = null)
    {
        return parent::save(null, $db_array);
    }

    /**
     * @param $id
     * @return array
     */
    public function get_quote_uploads($id)
    {
        $this->load->model('quotes/mdl_quotes');
        $quote = $this->mdl_quotes->get_by_id($id);
        $query = $this->db->query("SELECT file_name_new,file_name_original FROM ip_uploads WHERE url_key = '" . $quote->quote_url_key . "'");

        $names = [];

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $names[] =
                [
                    'path'     => UPLOADS_CFILES_FOLDER . $row->file_name_new,
                    'filename' => $row->file_name_original,
                ];
            }
        }

        return $names;
    }

    /**
     * @param $id
     * @return array
     */
    public function get_invoice_uploads($id)
    {
        $this->load->model('invoices/mdl_invoices');
        $invoice = $this->mdl_invoices->get_by_id($id);
        $query = $this->db->query("SELECT file_name_new,file_name_original FROM ip_uploads WHERE url_key = '" . $invoice->invoice_url_key . "'");

        $names = [];

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $names[] =
                [
                    'path'     => UPLOADS_CFILES_FOLDER . $row->file_name_new,
                    'filename' => $row->file_name_original,
                ];
            }
        }

        return $names;
    }

    /**
     * @param $url_key
     * @return array
     */
    public function get_files($url_key)
    {
        $result = [];
        if ($url_key && $rows = $this->where('url_key', $url_key)->get()->result()) {
            foreach ($rows as $row) {
                $size = @filesize(UPLOADS_CFILES_FOLDER . $row->file_name_new);
                if ($size === false) {
                    // Probably Deleted, remove it
                    $this->delete_file($url_key, $row->file_name_original);
                    continue;
                }

                $result[] =
                [
                    'name' => $row->file_name_original,
                    'size' => $size,
                ];
            }
        }

        return $result;
    }

    /**
     * @param $url_key
     * @param $filename
     */
    public function delete_file($url_key, $filename)
    {
        $this->db->where(['url_key' => $url_key, 'file_name_original' => $filename])->delete('ip_uploads');
    }

    /**
     * @param $client_id
     * @return $this
     */
    public function by_client($client_id)
    {
        $this->filter_where('ip_uploads.client_id', $client_id);
        return $this;
    }
}
