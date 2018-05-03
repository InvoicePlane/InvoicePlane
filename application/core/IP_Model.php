<?php

use Carbon\Carbon;

/**
 * Class IP_Model
 *
 * @package      InvoicePlane
 * @author       InvoicePlane Developers & Contributors
 * @copyright    Copyright (c) 2012 - 2018, InvoicePlane (https://invoiceplane.com/)
 * @license      http://opensource.org/licenses/MIT	MIT License
 * @link         https://invoiceplane.com
 */
class IP_Model extends CI_Model
{
    /** @var string DB table of the model */
    public $table;

    /** @var string Primary key of the models DB table */
    public $primary_key;

    /** @var int Default amount of items to get of a model */
    public $default_limit = 15;

    public $page_links;

    public $query;

    /** @var array */
    public $form_values = [];

    public $validation_errors;

    public $total_rows;

    public $date_created_field;

    public $date_modified_field;

    public $date_deleted_field;

    public $date_fields = [
        'created_at',
        'modified_at',
    ];

    /** @var array */
    public $native_methods = [
        'select',
        'select_max',
        'select_min',
        'select_avg',
        'select_sum',
        'join',
        'where',
        'or_where',
        'where_in',
        'or_where_in',
        'where_not_in',
        'or_where_not_in',
        'like',
        'or_like',
        'not_like',
        'or_not_like',
        'group_by',
        'distinct',
        'having',
        'or_having',
        'order_by',
        'limit',
    ];

    /** @var int */
    public $total_pages = 0;

    /** @var int */
    public $current_page;

    /** @var int */
    public $next_page;

    /** @var int */
    public $previous_page;

    /** @var int */
    public $offset;

    /** @var int */
    public $next_offset;

    /** @var int */
    public $previous_offset;

    /** @var int */
    public $last_offset;

    /** @var int */
    public $id;

    /** @var array */
    public $filter = [];

    /** @var string */
    protected $default_validation_rules = 'validation_rules';

    /** @var array Array that holds the basic validation rules for the model */
    protected $validation_rules;

    /**
     * Form_Validation_Model constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('form_validation');
        $this->form_validation->CI =& $this;
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return $this
     */
    public function __call($name, $arguments)
    {
        if (substr($name, 0, 7) == 'filter_') {
            $this->filter[] = [substr($name, 7), $arguments];
        } else {
            call_user_func_array([$this->db, $name], $arguments);
        }

        return $this;
    }

    /**
     * Sets CI query object and automatically creates active record query
     * based on methods in child model.
     * $this->model_name->get()
     *
     * @param bool $include_defaults
     *
     * @return $this
     */
    public function get($include_defaults = true)
    {
        if ($include_defaults) {
            $this->set_defaults();
        }

        $this->run_filters();
        $this->query = $this->db->get($this->table);
        $this->filter = [];

        return $this;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Retrieves a single record based on primary key value
     *
     * @param $id
     *
     * @return mixed
     */
    public function getById($id)
    {
        return $this->where($this->primary_key, $id)->get()->row();
    }

    /**
     * Query builder which listens to methods in child model
     *
     * @param array $exclude
     */
    private function setDefaults($exclude = [])
    {
        $native_methods = $this->native_methods;

        foreach ($exclude as $unset_method) {
            unset($native_methods[array_search($unset_method, $native_methods)]);
        }

        foreach ($native_methods as $native_method) {
            $native_method = 'default_' . $native_method;

            if (method_exists($this, $native_method)) {
                $this->$native_method();
            }
        }
    }

    private function runFilters()
    {
        foreach ($this->filter as $filter) {
            call_user_func_array([$this->db, $filter[0]], $filter[1]);
        }

        // Clear the filter array since this should only be run once per model execution
        $this->filter = [];
    }

    /**
     * Call when paginating results
     * $this->model_name->paginate()
     *
     * @param     $base_url
     * @param int $offset
     * @param int $uri_segment
     */
    public function paginate($base_url, $offset = 0, $uri_segment = 3)
    {
        $this->load->helper('url');
        $this->load->library('pagination');

        $this->offset = $offset;
        $default_list_limit = $this->mdl_settings->setting('default_list_limit');
        $per_page = (empty($default_list_limit) ? $this->default_limit : $default_list_limit);

        $this->setDefaults();
        $this->runFilters();

        $this->db->limit($per_page, $this->offset);
        $this->query = $this->db->get($this->table);

        $this->total_rows = $this->db->query('SELECT FOUND_ROWS() AS num_rows')->row()->num_rows;
        $this->total_pages = ceil($this->total_rows / $per_page);
        $this->previous_offset = $this->offset - $per_page;
        $this->next_offset = $this->offset + $per_page;

        $config = [
            'base_url' => $base_url,
            'total_rows' => $this->total_rows,
            'per_page' => $per_page,
        ];

        $this->last_offset = ($this->total_pages * $per_page) - $per_page;

        if ($this->config->item('pagination_style')) {
            $config = array_merge($config, $this->config->item('pagination_style'));
        }

        $this->pagination->initialize($config);

        $this->page_links = $this->pagination->create_links();
    }

    /**
     * Function to save an entry to the database
     *
     * @param null $id
     * @param null $db_array
     *
     * @return null
     */
    public function save($id = null, $db_array = null)
    {
        if (!$db_array) {
            $db_array = $this->data();
        }

        $datetime = date('Y-m-d H:i:s');

        if (!$id) {
            if ($this->date_created_field) {
                if (is_array($db_array)) {
                    $db_array[$this->date_created_field] = $datetime;

                    if ($this->date_modified_field) {
                        $db_array[$this->date_modified_field] = $datetime;
                    }
                } else {
                    $db_array->{$this->date_created_field} = $datetime;

                    if ($this->date_modified_field) {
                        $db_array->{$this->date_modified_field} = $datetime;
                    }
                }
            } elseif ($this->date_modified_field) {
                if (is_array($db_array)) {
                    $db_array[$this->date_modified_field] = $datetime;
                } else {
                    $db_array->{$this->date_modified_field} = $datetime;
                }
            }

            $this->db->insert($this->table, $db_array);

            $this->session->set_flashdata('alert_success', trans('record_successfully_created'));

            return $this->db->insert_id();

        } else {
            if ($this->date_modified_field) {
                if (is_array($db_array)) {
                    $db_array[$this->date_modified_field] = $datetime;
                } else {
                    $db_array->{$this->date_modified_field} = $datetime;
                }
            }

            $this->db->where($this->primary_key, $id);
            $this->db->update($this->table, $db_array);

            $this->session->set_flashdata('alert_success', trans('record_successfully_updated'));

            return $id;
        }
    }

    /**
     * Returns an array based on $_POST input matching the ruleset used to
     * validate the form submission.
     *
     * @return array
     */
    public function data()
    {
        $db_array = [];

        $validation_rules = $this->{$this->validation_rules}();

        foreach ($this->input->post() as $key => $value) {
            if (array_key_exists($key, $validation_rules)) {
                $db_array[$key] = $value;
            }
        }

        return $db_array;
    }

    /**
     * Deletes a record based on primary key value
     * $this->model_name->delete(5);
     *
     * @param $id
     */
    public function delete($id)
    {
        $this->db->where($this->primary_key, $id);
        $this->db->delete($this->table);

        $this->session->set_flashdata('alert_success', trans('record_successfully_deleted'));
    }

    /**
     * Manually join database items that are related to the current item
     * Instead of using database joins via SQL we use this method to
     * push related models into a new property.
     * This will allow us to be more clear in templates, trim database
     * fields and prevent field collisions across different tables.
     *
     * Instead of something like this:
     *      $invoice->invoice_id
     *      $invoice->invoice_number
     *      $invoice->client_id
     *      $invoice->client_name
     * we get something like this:
     *      $invoice->id
     *      $invoice->number
     *      $invoice->client->id
     *      $invoice->client->name
     * or the following scheme for multiple result items
     *      $invoice->id
     *      $invoice->number
     *      $invoice->items[
     *          {
     *              id => 1
     *              name => Item Name
     *          }
     *      ]
     *
     * The method automatically determines if the result needs to be an
     * array or object.
     *
     * @param array|object $item
     * @param bool         $single
     * @return array
     */
    public function joinModels($item, $single = false)
    {
        // Only join if the item model has joins specified
        if (isset($this->joins) && !empty($this->joins)) {
            $models = $this->joins;
            $models = is_array($models) ? $models : [$models];

            foreach ($models as $model => $foreign_key) {
                // Load the specified model
                $model = strtolower($model);
                $this->load->model($model);

                // The join key specifies how the property should be named
                // @TODO join key needs to be specified somewhere?!
                $join_key = str_replace('mdl_', '', $model);
                $join_key = $single ? rtrim($join_key, 's') : $join_key;

                // Get the primary key which is most likely to be the item ID
                $item_id = is_array($item) ? $item[$this->primary_key] : $item->{$this->primary_key};

                // Prepare the query with a simple where() statement
                $join_query = $this->$model->where($foreign_key, $item_id)->get();

                // If the item is an array, also return the join items as arrays
                // Also if a single entity should be returned we select the proper database query
                if (is_array($item)) {
                    $item[$join_key] = $single ? $join_query->rowArray() : $join_query->resultArray();
                } else {
                    $item->$join_key = $single ? $join_query->row() : $join_query->result();
                }
            }
        }

        return $item;
    }

    /**
     * Returns the CI query result object with joined entries
     * $this->model_name->get()->result();
     *
     * @return mixed
     */
    public function result()
    {
        $items = $this->query->result();
        $return = [];

        foreach ($items as $item) {
            $this->processDateFields($item);
            $return[] = $this->joinModels($item);
        }

        return $return;
    }

    /**
     * Returns the CI query row object with joined entries
     * $this->model_name->get()->row();
     *
     * @return mixed
     */
    public function row()
    {
        $item = $this->query->row();

        $this->processDateFields($item);

        return $this->joinModels($item, true);
    }

    /**
     * Returns CI query result array with joined entries
     * $this->model_name->get()->resultArray();
     *
     * @return mixed
     */
    public function resultArray()
    {
        $items = $this->query->result_array();
        $return = [];

        foreach ($items as $item) {
            $item = $this->processDateFields($item);
            $return[] = $this->joinModels($item);
        }

        return $return;
    }

    /**
     * Returns CI query row array
     * $this->model_name->get()->rowArray();
     *
     * @return mixed
     */
    public function rowArray()
    {
        $item = $this->query->row_array();

        $item = $this->processDateFields($item);

        return $this->joinModels($item, true);
    }

    /**
     * Convert specified date fields of a model into Carbon powered fields
     * for more functions out-of-the-box
     *
     * @param mixed $item
     *
     * @return object|array
     */
    public function processDateFields($item)
    {
        if ($this->date_fields) {
            if (!is_array($this->date_fields)) {
                $this->date_fields = [$this->date_fields];
            }

            // Loop trough all items and convert basic DATETIME MySQL fields into
            // Carbon instances
            foreach ($this->date_fields as $field) {
                // Process object items
                if (is_object($item)) {
                    if (isset($item->$field) && !is_null($item->$field)) {
                        $item->$field = Carbon::parse($item->$field);
                    }
                }

                // Process array items
                if (is_array($item)) {
                    if (isset($item[$field]) && !is_null($item[$field])) {
                        $item[$field] = Carbon::parse($item[$field]);
                    }
                }
            }
        }

        return $item;
    }

    /**
     * Returns CI query num_rows()
     * $this->model_name->get()->count();
     *
     * @return mixed
     */
    public function count()
    {
        return $this->query->num_rows();
    }

    /**
     * Used to retrieve record by ID and populate $this->form_values
     *
     * @param int $id
     *
     * @return boolean|null
     */
    public function prepareForm($id = null)
    {
        if (!$_POST && $id) {
            $row = $this->getById($id);

            if ($row) {
                foreach ($row as $key => $value) {
                    $this->form_values[$key] = $value;
                }

                return true;
            }

            return false;
        } elseif (!$id) {
            return true;
        }
    }

    /**
     * Performs validation on submitted form. By default, looks for method in
     * child model called validation_rules, but can be forced to run validation
     * on any method in child model which returns array of validation rules.
     *
     * @param null|string $validation_rules
     *
     * @return mixed
     */
    public function runValidation($validation_rules = null)
    {
        if (!$validation_rules) {
            $validation_rules = $this->default_validation_rules;
        }

        foreach (array_keys($_POST) as $key) {
            $this->form_values[$key] = $this->input->post($key);
        }

        if (method_exists($this, $validation_rules)) {
            $this->validation_rules = $validation_rules;

            $this->load->library('form_validation');

            $this->form_validation->set_rules($this->$validation_rules());

            $run = $this->form_validation->run();

            $this->validation_errors = validation_errors();

            return $run;
        }
    }

    /**
     * Returns the assigned form value to a form input element
     *
     * @param string $key
     * @param bool   $escape
     *
     * @return mixed|string
     */
    public function formValue($key, $escape = false)
    {
        $value = isset($this->form_values[$key]) ? $this->form_values[$key] : '';
        return $escape ? htmlspecialchars($value) : $value;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setFormValue($key, $value)
    {
        $this->form_values[$key] = $value;
    }
}
