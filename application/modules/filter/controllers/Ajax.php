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
class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    public function filter_invoices()
    {
        $this->load->model('invoices/mdl_invoices');

        $query = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = strtolower($keyword);
                $this->mdl_invoices->like("CONCAT_WS('^',LOWER(invoice_number),invoice_date_created,invoice_date_due,LOWER(client_name),invoice_total,invoice_balance)", $keyword);
            }
        }

        $data = array(
            'invoices' => $this->mdl_invoices->get()->result(),
            'invoice_statuses' => $this->mdl_invoices->statuses()
        );

        $this->layout->load_view('invoices/partial_invoice_table', $data);
    }

    public function filter_quotes()
    {
        $this->load->model('quotes/mdl_quotes');

        $query = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = strtolower($keyword);
                $this->mdl_quotes->like("CONCAT_WS('^',LOWER(quote_number),quote_date_created,quote_date_expires,LOWER(client_name),quote_total)", $keyword);
            }
        }

        $data = array(
            'quotes' => $this->mdl_quotes->get()->result(),
            'quote_statuses' => $this->mdl_quotes->statuses()
        );

        $this->layout->load_view('quotes/partial_quote_table', $data);
    }

    public function filter_clients()
    {
        $this->load->model('clients/mdl_clients');

        $query = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = trim(strtolower($keyword));
                $this->mdl_clients->like("CONCAT_WS('^',LOWER(client_name),LOWER(client_surname),LOWER(client_email),client_phone,client_active)", $keyword);
            }
        }

        $data = array(
            'records' => $this->mdl_clients->with_total_balance()->get()->result()
        );

        $this->layout->load_view('clients/partial_client_table', $data);
    }

    public function filter_custom_fields()
    {
        // custom table option name Normaly always here (it's ajax). Old school but work.
        $name = empty($_SERVER['HTTP_REFERER']) ? 'all' : basename($_SERVER['HTTP_REFERER']); // Todo: With CI?

        $this->load->model('custom_fields/mdl_custom_fields');

        $query = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = strtolower($keyword);
                // custom_field_location, custom_field_order
                $this->mdl_custom_fields->like("CONCAT_WS('^',custom_field_id, LOWER(custom_field_table), LOWER(custom_field_label), LOWER(custom_field_type))", $keyword);
            }
        }

        // Determine which name of table custom field to load
        $custom_tables = $this->mdl_custom_fields->custom_tables();
        if ($name != 'all' && in_array($name, $custom_tables))
        {
            $this->mdl_custom_fields->by_table_name($name);
        }
        $custom_fields = $this->mdl_custom_fields->get()->result();

        $this->load->model('custom_values/mdl_custom_values');
        $data = [
                'custom_fields'       => $custom_fields,
                'custom_tables'       => $custom_tables,
                'custom_value_fields' => $this->mdl_custom_values->custom_value_fields(),
        ];

        $this->layout->load_view('custom_fields/partial_custom_fields_table', $data);
    }

    public function filter_custom_values()
    {
        // custom values id Normaly always here (it's ajax). Old school but work.
        $id = empty($_SERVER['HTTP_REFERER']) ? 0 : basename($_SERVER['HTTP_REFERER']); // Todo: With CI?

        $this->load->model('custom_values/mdl_custom_values');

        $query = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = strtolower($keyword);
                $this->mdl_custom_values->like("CONCAT_WS('^',LOWER(custom_values_value))", $keyword);
            }
        }
        $this->mdl_custom_values->grouped(); // ->paginate(site_url('custom_values/index'), $page);
        $custom_values = $this->mdl_custom_values->get()->result();

        $data = ['id' => $id, 'custom_values' => $custom_values];
        $this->layout->load_view('custom_values/partial_custom_values_table', $data);
    }


    public function filter_custom_values_field()
    {
        $this->load->model('custom_values/mdl_custom_values');

        // custom values id Normaly always here (it's ajax). Old school but work.
        $id = empty($_SERVER['HTTP_REFERER']) ? 0 : basename($_SERVER['HTTP_REFERER']); // Todo: With CI?

        $query = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = strtolower($keyword);
                $this->mdl_custom_values->like("CONCAT_WS('^',LOWER(custom_values_value))", $keyword);
            }
        }

        $elements = $this->mdl_custom_values->get_by_fid($id)->result();
        // $this->load->model('custom_fields/mdl_custom_fields');
        $data = [
            'id'       => $id,
            'elements' => $elements,
            // 'custom_field_usage' => $this->mdl_custom_fields->used($id),
        ];
        $this->layout->load_view('custom_values/partial_custom_values_field', $data);
    }

    public function filter_projects()
    {
        $this->load->model('projects/mdl_projects');

        $query = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = strtolower($keyword);
                // client_id : Column 'client_id' in where clause is ambiguous (ip_clients.client_id or ip_project.client_id
                $this->mdl_projects->like("CONCAT_WS('^',project_id,LOWER(client_name),LOWER(project_name))", $keyword);
            }
        }

        $data = array(
            'projects' => $this->mdl_projects->get()->result()
        );

        $this->layout->load_view('projects/partial_projects_table', $data);
    }

    public function filter_tasks()
    {
        $this->load->model('tasks/mdl_tasks');

        $query = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = strtolower($keyword);
                // Column 'project_id' in where clause is ambiguous
                $this->mdl_tasks->like("CONCAT_WS('^',task_id,ip_tasks.project_id,LOWER(task_name),LOWER(task_description),LOWER(task_price),task_finish_date,LOWER(task_status),LOWER(tax_rate_id))", $keyword);
            }
        }

        $data = [
            'tasks' => $this->mdl_tasks->get()->result()
        ];

        $this->layout->load_view('tasks/partial_tasks_table', $data);
    }

    public function filter_products()
    {
        $this->load->model('products/mdl_products');

        $query = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        // Columns 'tax_rate_id' & 'unit_id' in where clause is ambiguous
/        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = strtolower($keyword);
                $this->mdl_products->like("CONCAT_WS('^',product_id,LOWER(family_name),product_sku,LOWER(product_name),LOWER(product_description),product_price,purchase_price,LOWER(provider_name),LOWER(tax_rate_name),LOWER(unit_name_plrl),product_tariff)", $keyword);
            }
        }

        $data = [
            'products' => $this->mdl_products->get()->result()
        ];

        $this->layout->load_view('products/partial_products_table', $data);
    }

    public function filter_payments()
    {
        $this->load->model('payments/mdl_payments');

        $query = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = strtolower($keyword);
                $this->mdl_payments->like("CONCAT_WS('^',payment_date,LOWER(invoice_number),LOWER(client_name),payment_amount,LOWER(payment_method_name),LOWER(payment_note))", $keyword);
            }
        }

        $data = [
            'payments' => $this->mdl_payments->get()->result()
        ];

        $this->layout->load_view('payments/partial_payment_table', $data);
    }

}
