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
 * Class Mdl_Import
 */
class Mdl_Import extends Response_Model
{
    public $table = 'ip_imports';
    public $primary_key = 'ip_imports.import_id';
    public $expected_headers = array(
        'clients.csv' => array(
            'client_name',
            'client_address_1',
            'client_address_2',
            'client_city',
            'client_state',
            'client_zip',
            'client_country',
            'client_phone',
            'client_fax',
            'client_mobile',
            'client_email',
            'client_web',
            'client_vat_id',
            'client_tax_code',
            'client_active'
        ),
        'invoices.csv' => array(
            'user_email',
            'client_name',
            'invoice_date_created',
            'invoice_date_due',
            'invoice_number',
            'invoice_terms'
        ),
        'invoice_items.csv' => array(
            'invoice_number',
            'item_tax_rate',
            'item_date_added',
            'item_name',
            'item_description',
            'item_quantity',
            'item_price'
        ),
        'payments.csv' => array(
            'invoice_number',
            'payment_method',
            'payment_date',
            'payment_amount',
            'payment_note'
        )
    );
    public $primary_keys = array(
        'ip_clients' => 'client_id',
        'ip_invoices' => 'invoice_id',
        'ip_invoice_items' => 'item_id',
        'ip_payments' => 'payment_id'
    );

    /**
     * Mdl_Import constructor.
     */
    public function __construct()
    {
        // Provides better line ending detection
        ini_set("auto_detect_line_endings", true);
    }

    public function default_select()
    {
        $this->db->select("SQL_CALC_FOUND_ROWS ip_imports.*,
            (SELECT COUNT(*) FROM ip_import_details WHERE import_table_name = 'ip_clients' AND ip_import_details.import_id = ip_imports.import_id) AS num_clients,
            (SELECT COUNT(*) FROM ip_import_details WHERE import_table_name = 'ip_invoices' AND ip_import_details.import_id = ip_imports.import_id) AS num_invoices,
            (SELECT COUNT(*) FROM ip_import_details WHERE import_table_name = 'ip_invoice_items' AND ip_import_details.import_id = ip_imports.import_id) AS num_invoice_items,
            (SELECT COUNT(*) FROM ip_import_details WHERE import_table_name = 'ip_payments' AND ip_import_details.import_id = ip_imports.import_id) AS num_payments", false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_imports.import_date DESC');
    }

    public function start_import()
    {
        $db_array = array(
            'import_date' => date('Y-m-d H:i:s')
        );

        $this->db->insert('ip_imports', $db_array);

        return $this->db->insert_id();
    }

    /**
     * @param $file
     * @param $table
     * @return array|bool
     */
    public function import_data($file, $table)
    {
        // Open the file
        $handle = fopen('./uploads/import/' . $file, 'r');

        $row = 1;

        // Get the expected file headers
        $headers = $this->expected_headers[$file];

        // Init an array to store the inserted ids
        $ids = array();

        while (($data = fgetcsv($handle, 1000, ",")) <> false) {
            // Check to make sure the file headers match the expected headers
            $fileheaders = null;
            if ($row == 1) {
                foreach ($headers as $header) {
                    if (!in_array($header, $data))
                        return false;
                }
                $fileheaders = $data;
            } elseif ($row > 1) {
                // Init the array
                $db_array = array();
                // Loop through each of the values in the row
                foreach ($headers as $key => $header) {
                    $db_array[$header] = ($data[array_keys($fileheaders, $header)[0]] <> 'null') ? $data[array_keys($fileheaders, $header)[0]] : '';
                }

                // Create a couple of default values if file is clients.csv
                if ($file == 'clients.csv') {
                    $db_array['client_date_created'] = date('Y-m-d');
                    $db_array['client_date_modified'] = date('Y-m-d');
                }

                // Insert the record
                $this->db->insert($table, $db_array);

                // Record the inserted id
                $ids[] = $this->db->insert_id();
            }

            $row++;
        }

        // Return the array of recorded ids
        return $ids;
    }

    /**
     * @return array|bool
     */
    public function import_invoices()
    {
        // Open the file
        $handle = fopen('./uploads/import/invoices.csv', 'r');

        $row = 1;

        // Get the list of expected headers
        $headers = $this->expected_headers['invoices.csv'];

        // Init an array to store the inserted ids
        $ids = array();

        while (($data = fgetcsv($handle, 1000, ",")) <> false) {
            // Init $record_error as false
            $record_error = false;

            // Check to make sure the file headers match expected headers
            if ($row == 1 and $data <> $headers) {
                return false;
            } elseif ($row > 1) {
                // Init the array
                $db_array = array();

                // Loop through each of the values in the row
                foreach ($headers as $key => $header) {
                    if ($header == 'user_email') {
                        // Attempt to replace email address with user id
                        $this->db->where('user_email', $data[$key]);
                        $user = $this->db->get('ip_users');
                        if ($user->num_rows()) {
                            $header = 'user_id';
                            $data[$key] = $user->row()->user_id;
                        } else {
                            // Email address not found
                            $record_error = true;
                        }
                    } elseif ($header == 'client_name') {
                        // Replace client name with client id
                        $header = 'client_id';
                        $this->db->where('client_name', $data[$key]);
                        $client = $this->db->get('ip_clients');
                        if ($client->num_rows()) {
                            // Existing client found
                            $data[$key] = $client->row()->client_id;
                        } else {
                            // Existing client not found - create new client
                            $client_db_array = array(
                                'client_name' => $data[$key],
                                'client_date_created' => date('Y-m-d'),
                                'client_date_modified' => date('Y-m-d')
                            );

                            $this->db->insert('ip_clients', $client_db_array);
                            $data[$key] = $this->db->insert_id();
                        }
                    }
                    // Each invoice needs a url key
                    $db_array['invoice_url_key'] = $this->mdl_invoices->get_url_key();

                    // Assign the final value to the array
                    $db_array[$header] = ($data[$key] <> 'null') ? $data[$key] : '';
                }

                // Check for any record errors
                if (!$record_error) {
                    // No record errors exist - go ahead and create the invoice
                    $db_array['invoice_group_id'] = 0;
                    $ids[] = $this->mdl_invoices->create($db_array);
                }
            }

            $row++;
        }

        // Return the array of recorded ids
        return $ids;
    }

    /**
     * @return array|bool
     */
    public function import_invoice_items()
    {
        // Open the file
        $handle = fopen('./uploads/import/invoice_items.csv', 'r');

        $row = 1;

        // Get the list of expected headers
        $headers = $this->expected_headers['invoice_items.csv'];

        // Init an array to store the inserted ids
        $ids = array();

        while (($data = fgetcsv($handle, 1000, ",")) <> false) {
            // Init record_error as false
            $record_error = false;

            // Check to make sure the file headers match expected headers
            if ($row == 1 and $data <> $headers) {
                return false;
            } elseif ($row > 1) {
                // Init the array
                $db_array = array();

                foreach ($headers as $key => $header) {
                    if ($header == 'invoice_number') {
                        // Replace invoice_number with invoice_id
                        $this->db->where('invoice_number', $data[$key]);
                        $user = $this->db->get('ip_invoices');
                        if ($user->num_rows()) {
                            $header = 'invoice_id';
                            $data[$key] = $user->row()->invoice_id;
                        } else {
                            $record_error = true;
                        }
                    } elseif ($header == 'item_tax_rate') {
                        // Replace item_tax_rate with item_tax_rate_id
                        $header = 'item_tax_rate_id';
                        if ($data[$key] > 0) {
                            $this->db->where('tax_rate_percent', $data[$key]);
                            $tax_rate = $this->db->get('ip_tax_rates');
                            if ($tax_rate->num_rows()) {
                                $data[$key] = $tax_rate->row()->tax_rate_id;
                            } else {
                                $this->db->insert('ip_tax_rates', array('tax_rate_name' => $data[$key], 'tax_rate_percent' => $data[$key]));
                                $data[$key] = $this->db->insert_id();
                            }
                        } else {
                            $data[$key] = 0;
                        }
                    }

                    // Assign the final value to the array
                    $db_array[$header] = ($data[$key] <> 'null') ? $data[$key] : '';
                }

                if (!$record_error) {
                    // No errors, go ahead and create the record
                    $ids[] = $this->mdl_items->save(null, $db_array);
                }
            }

            $row++;
        }

        return $ids;
    }

    /**
     * @return array|bool
     */
    public function import_payments()
    {
        $handle = fopen('./uploads/import/payments.csv', 'r');

        $row = 1;

        $headers = $this->expected_headers['payments.csv'];

        $ids = array();

        while (($data = fgetcsv($handle, 1000, ",")) <> false) {
            $record_error = false;

            if ($row == 1 and $data <> $headers) {
                return false;
            } elseif ($row > 1) {
                $db_array = array();

                foreach ($headers as $key => $header) {
                    if ($header == 'invoice_number') {
                        $this->db->where('invoice_number', $data[$key]);
                        $user = $this->db->get('ip_invoices');
                        if ($user->num_rows()) {
                            $header = 'invoice_id';
                            $data[$key] = $user->row()->invoice_id;
                        } else {
                            $record_error = true;
                        }
                    } elseif ($header == 'payment_method') {
                        $header = 'payment_method_id';

                        if ($data[$key]) {
                            $this->db->where('payment_method_name', $data[$key]);
                            $payment_method = $this->db->get('ip_payment_methods');
                            if ($payment_method->num_rows()) {
                                $data[$key] = $payment_method->row()->payment_method_id;
                            } else {
                                $this->db->insert('ip_payment_methods', array('payment_method_name' => $data[$key]));
                                $data[$key] = $this->db->insert_id();
                            }
                        } else {
                            // No payment method provided
                            $data[$key] = 0;
                        }
                    }

                    $db_array[$header] = ($data[$key] <> 'null') ? $data[$key] : '';
                }

                if (!$record_error) {
                    $ids[] = $this->mdl_payments->save(null, $db_array);
                }
            }

            $row++;
        }

        return $ids;
    }

    /**
     * @param $import_id
     * @param $table_name
     * @param $import_lang_key
     * @param $ids
     */
    public function record_import_details($import_id, $table_name, $import_lang_key, $ids)
    {
        foreach ($ids as $id) {
            $db_array = array(
                'import_id' => $import_id,
                'import_table_name' => $table_name,
                'import_lang_key' => $import_lang_key,
                'import_record_id' => $id
            );

            $this->db->insert('ip_import_details', $db_array);
        }
    }

    /**
     * @param int $import_id
     */
    public function delete($import_id)
    {
        // Gather the import details
        $import_details = $this->db->where('import_id', $import_id)->get('ip_import_details')->result();

        // Loop through details and delete each of the imported records
        foreach ($import_details as $import_detail) {
            $this->db->query("DELETE FROM " . $import_detail->import_table_name . " WHERE " . $this->primary_keys[$import_detail->import_table_name] . ' = ' . $import_detail->import_record_id);
        }

        // Delete the master import record
        parent::delete($import_id);

        // Delete the detail records
        $this->db->where('import_id', $import_id);
        $this->db->delete('ip_import_details');

        // Delete any orphaned records
        $this->load->helper('orphan');
        delete_orphans();
    }

}
