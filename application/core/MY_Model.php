<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter CRUD Model 2
 * A base model providing CRUD, pagination and validation.
 *
 * Install this file as application/core/MY_Model.php
 *
 * @package     CodeIgniter
 * @author		Jesse Terry
 * @copyright	Copyright (c) 2012-2013, Jesse Terry
 * @link		http://developer13.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */
class MY_Model extends CI_Model
{
    public $table;
    public $primary_key;
    public $default_limit = 15;
    public $page_links;
    public $query;
    public $form_values = array();
    public $validation_errors;
    public $total_rows;
    public $date_created_field;
    public $date_modified_field;
    public $native_methods = array(
        'select', 'select_max', 'select_min', 'select_avg', 'select_sum', 'join',
        'where', 'or_where', 'where_in', 'or_where_in', 'where_not_in', 'or_where_not_in',
        'like', 'or_like', 'not_like', 'or_not_like', 'group_by', 'distinct', 'having',
        'or_having', 'order_by', 'limit'
    );
    public $total_pages = 0;
    public $current_page;
    public $next_page;
    public $previous_page;
    public $offset;
    public $next_offset;
    public $previous_offset;
    public $last_offset;
    public $id;
    public $filter = array();
    protected $default_validation_rules = 'validation_rules';
    protected $validation_rules;

    public function __call($name, $arguments)
    {
        if (substr($name, 0, 7) == 'filter_') {
            $this->filter[] = array(substr($name, 7), $arguments);
        } else {
            call_user_func_array(array($this->db, $name), $arguments);
        }
        return $this;
    }

    /**
     * Sets CI query object and automatically creates active record query
     * based on methods in child model.
     * $this->model_name->get()
     */
    public function get($include_defaults = true)
    {
        if ($include_defaults) {
            $this->set_defaults();
        }

        $this->run_filters();

        $this->query = $this->db->get($this->table);

        $this->filter = array();

        return $this;
    }

    /**
     * Query builder which listens to methods in child model
     *
     * @param array $exclude
     */
    private function set_defaults($exclude = array())
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

    private function run_filters()
    {
        foreach ($this->filter as $filter) {
            call_user_func_array(array($this->db, $filter[0]), $filter[1]);
        }

        /**
         * Clear the filter array since this should only be run once per model
         * execution
         */
        $this->filter = array();
    }

    /**
     * Call when paginating results
     * $this->model_name->paginate()
     *
     * @param $base_url
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

        $this->set_defaults();
        $this->run_filters();

        $this->db->limit($per_page, $this->offset);
        $this->query = $this->db->get($this->table);

        $this->total_rows = $this->db->query('SELECT FOUND_ROWS() AS num_rows')->row()->num_rows;
        $this->total_pages = ceil($this->total_rows / $per_page);
        $this->previous_offset = $this->offset - $per_page;
        $this->next_offset = $this->offset + $per_page;

        $config = array(
            'base_url' => $base_url,
            'total_rows' => $this->total_rows,
            'per_page' => $per_page
        );

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
     * @return null
     */
    public function save($id = null, $db_array = null)
    {
        if (!$db_array) {
            $db_array = $this->db_array();
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

            return $id;
        }
    }

    /**
     * Returns an array based on $_POST input matching the ruleset used to
     * validate the form submission.
     *
     * @return array
     */
    public function db_array()
    {
        $db_array = array();

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
    }

    /**
     * Returns the CI query result object
     * $this->model_name->get()->result();
     *
     * @return mixed
     */
    public function result()
    {
        return $this->query->result();
    }

    /**
     * Returns the CI query row object
     * $this->model_name->get()->row();
     *
     * @return mixed
     */
    public function row()
    {
        return $this->query->row();
    }

    /**
     * Returns CI query result array
     * $this->model_name->get()->result_array();
     *
     * @return mixed
     */
    public function result_array()
    {
        return $this->query->result_array();
    }

    /**
     * Returns CI query row array
     * $this->model_name->get()->row_array();
     *
     * @return mixed
     */
    public function row_array()
    {
        return $this->query->row_array();
    }

    /**
     * Returns CI query num_rows()
     * $this->model_name->get()->num_rows();
     *
     * @return mixed
     */
    public function num_rows()
    {
        return $this->query->num_rows();
    }

    /**
     * Used to retrieve record by ID and populate $this->form_values
     *
     * @param int $id
     * @return boolean|null
     */
    public function prep_form($id = null)
    {
        if (!$_POST and ($id)) {
            $row = $this->get_by_id($id);

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
     * Retrieves a single record based on primary key value
     *
     * @param $id
     * @return mixed
     */
    public function get_by_id($id)
    {
        return $this->where($this->primary_key, $id)->get()->row();
    }

    /**
     * Performs validation on submitted form. By default, looks for method in
     * child model called validation_rules, but can be forced to run validation
     * on any method in child model which returns array of validation rules.
     *
     * @param null|string $validation_rules
     * @return mixed
     */
    public function run_validation($validation_rules = null)
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
     * @param bool $escape
     * @return mixed|string
     */
    public function form_value($key, $escape = false)
    {
        $value = isset($this->form_values[$key]) ? $this->form_values[$key] : '';
        return $escape ? htmlspecialchars($value) : $value;
    }

    /**
     * @param string $key
     * @param $value
     */
    public function set_form_value($key, $value)
    {
        $this->form_values[$key] = $value;
    }

    /**
     * @param $id
     */
    public function set_id($id)
    {
        $this->id = $id;
    }
}
