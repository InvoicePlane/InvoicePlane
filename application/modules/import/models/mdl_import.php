<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * FusionInvoice
 * 
 * A free and open source web based invoicing system
 *
 * @package		FusionInvoice
 * @author		Jesse Terry
 * @copyright	Copyright (c) 2012 - 2013 FusionInvoice, LLC
 * @license		http://www.fusioninvoice.com/license.txt
 * @link		http://www.fusioninvoice.com
 * 
 */

class Mdl_Import extends Response_Model {

    public $table            = 'fi_imports';
    public $primary_key      = 'fi_imports.import_id';
    public $expected_headers = array(
        'clients.csv'       => array(
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
            'client_active'
        ),
        'invoices.csv'      => array(
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
        'payments.csv'      => array(
            'invoice_number',
            'payment_method',
            'payment_date',
            'payment_amount',
            'payment_note'
        )
    );
    public $primary_keys     = array(
        'fi_clients'       => 'client_id',
        'fi_invoices'      => 'invoice_id',
        'fi_invoice_items' => 'item_id',
        'fi_payments'      => 'payment_id'
    );

    public function __construct()
    {
        // Provides better line ending detection
        ini_set("auto_detect_line_endings", true);
    }

    public function default_select()
    {
        $this->db->select("SQL_CALC_FOUND_ROWS fi_imports.*, 
            (SELECT COUNT(*) FROM fi_import_details WHERE import_table_name = 'fi_clients' AND fi_import_details.import_id = fi_imports.import_id) AS num_clients,
            (SELECT COUNT(*) FROM fi_import_details WHERE import_table_name = 'fi_invoices' AND fi_import_details.import_id = fi_imports.import_id) AS num_invoices,
            (SELECT COUNT(*) FROM fi_import_details WHERE import_table_name = 'fi_invoice_items' AND fi_import_details.import_id = fi_imports.import_id) AS num_invoice_items,
            (SELECT COUNT(*) FROM fi_import_details WHERE import_table_name = 'fi_payments' AND fi_import_details.import_id = fi_imports.import_id) AS num_payments", FALSE);
    }

    public function default_order_by()
    {
        $this->db->order_by('fi_imports.import_date DESC');
    }

    public function start_import()
    {
        $db_array = array(
            'import_date' => date('Y-m-d H:i:s')
        );

        $this->db->insert('fi_imports', $db_array);

        return $this->db->insert_id();
    }

    public function import_data($file, $table)
    {
        // Open the file
        $handle = fopen('./uploads/import/' . $file, 'r');

        $row = 1;

        // Get the expected file headers
        $headers = $this->expected_headers[$file];

        // Init an array to store the inserted ids
        $ids = array();

        while (($data = fgetcsv($handle, 1000, ",")) <> FALSE)
        {
            // Check to make sure the file headers match the expected headers
            if ($row == 1 and $data <> $headers)
            {
                return FALSE;
            }
            elseif ($row > 1)
            {
                // Init the array
                $db_array = array();

                // Loop through each of the values in the row
                foreach ($headers as $key => $header)
                {
                    $db_array[$header] = ($data[$key] <> 'NULL') ? $data[$key] : '';
                }

                // Create a couple of default values if file is clients.csv
                if ($file == 'clients.csv')
                {
                    $db_array['client_date_created']  = date('Y-m-d');
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

    public function import_invoices()
    {
        // Open the file
        $handle = fopen('./uploads/import/invoices.csv', 'r');

        $row = 1;

        // Get the list of expected headers
        $headers = $this->expected_headers['invoices.csv'];

        // Init an array to store the inserted ids
        $ids = array();

        while (($data = fgetcsv($handle, 1000, ",")) <> FALSE)
        {
            // Init $record_error as false
            $record_error = FALSE;

            // Check to make sure the file headers match expected headers
            if ($row == 1 and $data <> $headers)
            {
                return FALSE;
            }
            elseif ($row > 1)
            {
                // Init the array
                $db_array = array();

                // Loop through each of the values in the row
                foreach ($headers as $key => $header)
                {
                    if ($header == 'user_email')
                    {
                        // Attempt to replace email address with user id
                        $this->db->where('user_email', $data[$key]);
                        $user = $this->db->get('fi_users');
                        if ($user->num_rows())
                        {
                            $header     = 'user_id';
                            $data[$key] = $user->row()->user_id;
                        }
                        else
                        {
                            // Email address not found
                            $record_error = TRUE;
                        }
                    }
                    elseif ($header == 'client_name')
                    {
                        // Replace client name with client id
                        $header = 'client_id';
                        $this->db->where('client_name', $data[$key]);
                        $client = $this->db->get('fi_clients');
                        if ($client->num_rows())
                        {
                            // Existing client found
                            $data[$key] = $client->row()->client_id;
                        }
                        else
                        {
                            // Existing client not found - create new client
                            $client_db_array = array(
                                'client_name'          => $data[$key],
                                'client_date_created'  => date('Y-m-d'),
                                'client_date_modified' => date('Y-m-d')
                            );

                            $this->db->insert('fi_clients', $client_db_array);
                            $data[$key] = $this->db->insert_id();
                        }
                    }
                    // Each invoice needs a url key
                    $db_array['invoice_url_key'] = $this->mdl_invoices->get_url_key();

                    // Assign the final value to the array
                    $db_array[$header] = ($data[$key] <> 'NULL') ? $data[$key] : '';
                }

                // Check for any record errors
                if (!$record_error)
                {
                    // No record errors exist - go ahead and create the invoice
                    $db_array['invoice_group_id'] = 0;
                    $ids[]                        = $this->mdl_invoices->create($db_array);
                }
            }

            $row++;
        }

        // Return the array of recorded ids
        return $ids;
    }

    public function import_invoice_items()
    {
        // Open the file
        $handle = fopen('./uploads/import/invoice_items.csv', 'r');

        $row = 1;

        // Get the list of expected headers
        $headers = $this->expected_headers['invoice_items.csv'];

        // Init an array to store the inserted ids
        $ids = array();

        while (($data = fgetcsv($handle, 1000, ",")) <> FALSE)
        {
            // Init record_error as false
            $record_error = FALSE;

            // Check to make sure the file headers match expected headers
            if ($row == 1 and $data <> $headers)
            {
                return FALSE;
            }
            elseif ($row > 1)
            {
                // Init the array
                $db_array = array();

                foreach ($headers as $key => $header)
                {
                    if ($header == 'invoice_number')
                    {
                        // Replace invoice_number with invoice_id
                        $this->db->where('invoice_number', $data[$key]);
                        $user = $this->db->get('fi_invoices');
                        if ($user->num_rows())
                        {
                            $header     = 'invoice_id';
                            $data[$key] = $user->row()->invoice_id;
                        }
                        else
                        {
                            $record_error = TRUE;
                        }
                    }
                    elseif ($header == 'item_tax_rate')
                    {
                        // Replace item_tax_rate with item_tax_rate_id
                        $header = 'item_tax_rate_id';
                        if ($data[$key] > 0)
                        {
                            $this->db->where('tax_rate_percent', $data[$key]);
                            $tax_rate = $this->db->get('fi_tax_rates');
                            if ($tax_rate->num_rows())
                            {
                                $data[$key] = $tax_rate->row()->tax_rate_id;
                            }
                            else
                            {
                                $this->db->insert('fi_tax_rates', array('tax_rate_name'    => $data[$key], 'tax_rate_percent' => $data[$key]));
                                $data[$key] = $this->db->insert_id();
                            }
                        }
                        else
                        {
                            $data[$key] = 0;
                        }
                    }

                    // Assign the final value to the array
                    $db_array[$header] = ($data[$key] <> 'NULL') ? $data[$key] : '';
                }

                if (!$record_error)
                {
                    // No errors, go ahead and create the record
                    $ids[] = $this->mdl_items->save($db_array['invoice_id'], NULL, $db_array);
                }
            }

            $row++;
        }

        return $ids;
    }

    public function import_payments()
    {
        $handle = fopen('./uploads/import/payments.csv', 'r');

        $row = 1;

        $headers = $this->expected_headers['payments.csv'];

        $ids = array();

        while (($data = fgetcsv($handle, 1000, ",")) <> FALSE)
        {
            $record_error = FALSE;

            if ($row == 1 and $data <> $headers)
            {
                return FALSE;
            }
            elseif ($row > 1)
            {
                $db_array = array();

                foreach ($headers as $key => $header)
                {
                    if ($header == 'invoice_number')
                    {
                        $this->db->where('invoice_number', $data[$key]);
                        $user = $this->db->get('fi_invoices');
                        if ($user->num_rows())
                        {
                            $header     = 'invoice_id';
                            $data[$key] = $user->row()->invoice_id;
                        }
                        else
                        {
                            $record_error = TRUE;
                        }
                    }
                    elseif ($header == 'payment_method')
                    {
                        $header = 'payment_method_id';

                        if ($data[$key])
                        {
                            $this->db->where('payment_method_name', $data[$key]);
                            $payment_method = $this->db->get('fi_payment_methods');
                            if ($payment_method->num_rows())
                            {
                                $data[$key] = $payment_method->row()->payment_method_id;
                            }
                            else
                            {
                                $this->db->insert('fi_payment_methods', array('payment_method_name' => $data[$key]));
                                $data[$key] = $this->db->insert_id();
                            }
                        }
                        else
                        {
                            // No payment method provided
                            $data[$key] = 0;
                        }
                    }

                    $db_array[$header] = ($data[$key] <> 'NULL') ? $data[$key] : '';
                }

                if (!$record_error)
                {
                    $ids[] = $this->mdl_payments->save(NULL, $db_array);
                }
            }

            $row++;
        }

        return $ids;
    }

    public function record_import_details($import_id, $table_name, $import_lang_key, $ids)
    {
        foreach ($ids as $id)
        {
            $db_array = array(
                'import_id'         => $import_id,
                'import_table_name' => $table_name,
                'import_lang_key'   => $import_lang_key,
                'import_record_id'  => $id
            );

            $this->db->insert('fi_import_details', $db_array);
        }
    }

    public function delete($import_id)
    {
        // Gather the import details
        $import_details = $this->db->where('import_id', $import_id)->get('fi_import_details')->result();

        // Loop through details and delete each of the imported records
        foreach ($import_details as $import_detail)
        {
            $this->db->query("DELETE FROM " . $import_detail->import_table_name . " WHERE " . $this->primary_keys[$import_detail->import_table_name] . ' = ' . $import_detail->import_record_id);
        }

        // Delete the master import record
        parent::delete($import_id);

        // Delete the detail records
        $this->db->where('import_id', $import_id);
        $this->db->delete('fi_import_details');

        // Delete any orphaned records
        $this->load->helper('orphan');
        delete_orphans();
    }

}

?>