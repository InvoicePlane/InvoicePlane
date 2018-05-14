<?php

/**
 * Class Mdl_Test
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018, InvoicePlane
 * @license     http://opensource.org/licenses/MIT     MIT License
 * @link        https://invoiceplane.com
 */
class Mdl_Setup extends IP_Model
{
    public $errors = [];

    /**
     * Load the base migration file and execute it
     *
     * @return bool
     */
    public function run_base_migration()
    {
        $file_contents = file_get_contents(APPPATH . 'modules/setup/database/migrations/base.sql');

        $this->execute_contents($file_contents);

        if ($this->errors) {
            return false;
        }

        return true;
    }

    /**
     * Load the seed files and execute them
     */
    public function run_seeds()
    {
        // Load the Aauth library
        $this->load->library('vendor/Aauth');

        require_once(APPPATH . 'modules/setup/database/seeds/permissions.php');
        require_once(APPPATH . 'modules/setup/database/seeds/roles.php');
    }

    /**
     * @param string $contents
     */
    private function execute_contents($contents)
    {
        $commands = explode(';', $contents);

        foreach ($commands as $command) {
            if (trim($command)) {
                if (!$this->db->query(trim($command) . ';')) {
                    $this->errors[] = $this->db->_error_message();
                }
            }
        }
    }
}
