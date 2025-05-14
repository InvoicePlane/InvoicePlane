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
class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    public function filter_invoices()
    {
        $this->load->model('invoices/mdl_invoices');

        $query    = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                $this->mdl_invoices->like("CONCAT_WS('^',LOWER(invoice_number),invoice_date_created,invoice_date_due,LOWER(client_title),LOWER(client_name),LOWER(client_surname),invoice_total,invoice_balance)", $keyword);
            }
        }

        $data = [
            'invoices'         => $this->mdl_invoices->get()->result(),
            'invoice_statuses' => $this->mdl_invoices->statuses(),
        ];

        $this->layout->load_view('invoices/partial_invoice_table', $data);
    }

    public function filter_quotes()
    {
        $this->load->model('quotes/mdl_quotes');

        $query    = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                $this->mdl_quotes->like("CONCAT_WS('^',LOWER(quote_number),quote_date_created,quote_date_expires,LOWER(client_title),LOWER(client_name),LOWER(client_surname),quote_total)", $keyword);
            }
        }

        $data = [
            'quotes'         => $this->mdl_quotes->get()->result(),
            'quote_statuses' => $this->mdl_quotes->statuses(),
        ];

        $this->layout->load_view('quotes/partial_quote_table', $data);
    }

    public function filter_clients()
    {
        $this->load->model('clients/mdl_clients');

        $query    = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_trim(mb_strtolower($keyword));
                $this->mdl_clients->like("CONCAT_WS('^',LOWER(client_title),LOWER(client_name),LOWER(client_surname),LOWER(client_email),client_phone,client_active)", $keyword);
            }
        }

        $data = [
            'records'        => $this->mdl_clients->with_total_balance()->get()->result(),
            'einvoicing' => get_setting('einvoicing'),
        ];

        $this->layout->load_view('clients/partial_client_table', $data);
    }

    public function filter_custom_fields()
    {
        // custom table option name Normaly always here (it's ajax). Old school but work.
        $name = empty($_SERVER['HTTP_REFERER']) ? 'all' : basename($_SERVER['HTTP_REFERER']); // Todo: With CI?

        $this->load->model('custom_fields/mdl_custom_fields');

        $query    = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                // custom_field_location, custom_field_order
                $this->mdl_custom_fields->like("CONCAT_WS('^',custom_field_id, LOWER(custom_field_table), LOWER(custom_field_label), LOWER(custom_field_type))", $keyword);
            }
        }

        // Determine which name of table custom field to load
        $custom_tables = $this->mdl_custom_fields->custom_tables();
        if ($name != 'all' && in_array($name, $custom_tables)) {
            $this->mdl_custom_fields->by_table_name($name);
        }

        $custom_fields = $this->mdl_custom_fields->get()->result();

        $this->load->model('custom_values/mdl_custom_values');
        $data = [
            'custom_fields'       => $custom_fields,
            'custom_tables'       => $custom_tables,
            'custom_value_fields' => $this->mdl_custom_values->custom_value_fields(),
            'positions'           => $this->mdl_custom_fields->get_positions(true),
        ];

        $this->layout->load_view('custom_fields/partial_custom_fields_table', $data);
    }

    public function filter_custom_values()
    {
        // custom values id Normaly always here (it's ajax). Old school but work.
        $id = empty($_SERVER['HTTP_REFERER']) ? 0 : basename($_SERVER['HTTP_REFERER']); // Todo: With CI?

        $this->load->model(
            [
                'custom_values/mdl_custom_values',
                'custom_fields/mdl_custom_fields',
            ]
        );

        $query    = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                $this->mdl_custom_values->like("CONCAT_WS('^',LOWER(custom_values_value), LOWER(custom_field_table), LOWER(custom_field_label), LOWER(custom_field_type))", $keyword);
            }
        }

        $this->mdl_custom_values->grouped();
        $custom_values = $this->mdl_custom_values->get()->result();

        $data = [
            'id'            => $id,
            'custom_values' => $custom_values,
            'custom_tables' => $this->mdl_custom_fields->custom_tables(),
            'positions'     => $this->mdl_custom_fields->get_positions(true),
        ];
        $this->layout->load_view('custom_values/partial_custom_values_table', $data);
    }

    public function filter_custom_values_field()
    {
        $this->load->model('custom_values/mdl_custom_values');

        // custom values id Normaly always here (it's ajax). Old school but work.
        $id = empty($_SERVER['HTTP_REFERER']) ? 0 : basename($_SERVER['HTTP_REFERER']); // Todo: With CI?

        $query    = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                $this->mdl_custom_values->like("CONCAT_WS('^',custom_values_id,LOWER(custom_values_value))", $keyword);
            }
        }

        $elements = $this->mdl_custom_values->get_by_fid($id)->result();
        $data     = [
            'id'       => $id,
            'elements' => $elements,
        ];
        $this->layout->load_view('custom_values/partial_custom_values_field', $data);
    }

    public function filter_projects()
    {
        $this->load->model('projects/mdl_projects');

        $query    = $this->input->post('filter_query');
        $keywords = explode(' ', $query);
        // client_id : Column 'client_id' in where clause is ambiguous (ip_clients.client_id or ip_project.client_id
        // Not showed in frontend table
        // project_id,client_id,
        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                $this->mdl_projects->like("CONCAT_WS('^',LOWER(client_title),LOWER(client_name),LOWER(client_surname),LOWER(project_name))", $keyword);
            }
        }

        $data = [
            'projects' => $this->mdl_projects->get()->result(),
        ];

        $this->layout->load_view('projects/partial_projects_table', $data);
    }

    public function filter_tasks()
    {
        $this->load->model('tasks/mdl_tasks');

        $query    = $this->input->post('filter_query');
        $keywords = explode(' ', $query);
        // Column 'project_id' in where clause is ambiguous
        // Not showed in frontend table:
        // task_id,ip_tasks.project_id,LOWER(task_description),
        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                $this->mdl_tasks->like("CONCAT_WS('^',LOWER(task_name),LOWER(project_name),LOWER(task_price),task_finish_date,LOWER(task_status),LOWER(tax_rate_id))", $keyword);
            }
        }

        $data = [
            'tasks'         => $this->mdl_tasks->get()->result(),
            'task_statuses' => $this->mdl_tasks->statuses(),
        ];

        $this->layout->load_view('tasks/partial_tasks_table', $data);
    }

    public function filter_products()
    {
        $this->load->model('products/mdl_products');

        $query    = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        // Columns 'tax_rate_id' & 'unit_id' in where clause is ambiguous
        // Not showed in frontend table:
        // product_id,LOWER(family_name),purchase_price,LOWER(provider_name),LOWER(tax_rate_name),LOWER(unit_name_plrl),
        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                $this->mdl_products->like("CONCAT_WS('^',product_sku,LOWER(family_name),LOWER(product_name),LOWER(product_description),product_price,product_tariff)", $keyword);
            }
        }

        $data = [
            'products' => $this->mdl_products->get()->result(),
        ];

        $this->layout->load_view('products/partial_products_table', $data);
    }

    public function filter_users()
    {
        $this->load->model('users/mdl_users');

        $query    = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        // Not used: user_id    user_type   user_active user_date_modified  user_language   user_password   user_psalt  user_passwordreset_token
        // Not showed in frontend table:
        // user_date_created,LOWER(user_company),LOWER(user_address_1),LOWER(user_address_2),LOWER(user_city),LOWER(user_state),LOWER(user_zip),LOWER(user_country),
        // LOWER(user_invoicing_contact),LOWER(user_phone),LOWER(user_fax),LOWER(user_mobile),LOWER(user_web),
        // LOWER(user_vat_id),LOWER(user_tax_code),LOWER(user_all_clients),LOWER(user_subscribernumber),LOWER(user_bank),LOWER(user_iban),LOWER(user_bic),LOWER(user_remittance_text),LOWER(user_gln),LOWER(user_rcc)

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                $this->mdl_users->like("CONCAT_WS('^', LOWER(user_name), LOWER(user_email))", $keyword);
            }
        }

        $data = [
            'users'      => $this->mdl_users->get()->result(),
            'user_types' => $this->mdl_users->user_types(),
        ];

        $this->layout->load_view('users/partial_users_table', $data);
    }

    public function filter_families()
    {
        $this->load->model('families/mdl_families');

        $query    = $this->input->post('filter_query');
        $keywords = explode(' ', $query);
        // Not showed in frontend table:
        // family_id,
        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                $this->mdl_families->like("CONCAT_WS('^',LOWER(family_name))", $keyword);
            }
        }

        $data = [
            'families' => $this->mdl_families->get()->result(),
        ];

        $this->layout->load_view('families/partial_families_table', $data);
    }

    public function filter_invoices_recuring()
    {
        $this->load->model('invoices/mdl_invoices_recurring');

        $query    = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        // invoice_recurring_id invoice_id
        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                $this->mdl_invoices_recurring->like("CONCAT_WS('^',recur_start_date,recur_end_date,recur_next_date,recur_frequency,LOWER(invoice_number),LOWER(client_title),LOWER(client_name),LOWER(client_surname))", $keyword);
            }
        }

        $data = [
            'recur_frequencies'  => $this->mdl_invoices_recurring->recur_frequencies,
            'recurring_invoices' => $this->mdl_invoices_recurring->get()->result(),
        ];

        $this->layout->load_view('invoices/partial_invoices_recurring_table', $data);
    }

    public function filter_online_logs()
    {
        $this->load->model('payments/mdl_payment_logs');

        $query    = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                $this->mdl_payment_logs->like("CONCAT_WS('^',merchant_response_id,LOWER(invoice_number),merchant_response_successful,merchant_response_date,LOWER(merchant_response_driver),LOWER(merchant_response),LOWER(merchant_response_reference))", $keyword);
            }
        }

        $data = [
            'payment_logs' => $this->mdl_payment_logs->get()->result(),
        ];

        $this->layout->load_view('payments/partial_online_logs_table', $data);
    }

    public function filter_archives()
    {
        $this->load->model('invoices/mdl_invoices');

        $data = [
            'invoices_archive' => $this->mdl_invoices->get_archives($this->input->post('filter_query')),
        ];

        $this->layout->load_view('invoices/partial_invoice_archive', $data);
    }

    public function filter_payments()
    {
        $this->load->model('payments/mdl_payments');

        $query    = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                $this->mdl_payments->like("CONCAT_WS('^',payment_date,LOWER(invoice_number),LOWER(client_title),LOWER(client_name),LOWER(client_surname),payment_amount,LOWER(payment_method_name),LOWER(payment_note))", $keyword);
            }
        }

        $data = [
            'payments' => $this->mdl_payments->get()->result(),
        ];

        $this->layout->load_view('payments/partial_payments_table', $data);
    }
}
