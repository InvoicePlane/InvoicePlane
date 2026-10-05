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
class Mdl_Setup extends CI_Model
{
    public $errors = [];

    /**
     * @return bool
     */
    public function install_tables()
    {
        $file_contents = file_get_contents(APPPATH . 'modules/setup/sql/000_1.0.0.sql');

        $this->execute_contents($file_contents, '000_1.0.0.sql');

        $this->save_version('000_1.0.0.sql');

        if ($this->errors) {
            return false;
        }

        $this->install_default_data();

        $this->install_default_settings();

        return true;
    }

    public function install_default_data()
    {
        $this->db->insert('ip_invoice_groups', [
            'invoice_group_name'    => 'Invoice Default',
            'invoice_group_next_id' => 1,
        ]);

        $this->db->insert('ip_invoice_groups', [
            'invoice_group_name'    => 'Quote Default',
            'invoice_group_prefix'  => 'QUO',
            'invoice_group_next_id' => 1,
        ]);

        $this->db->insert('ip_payment_methods', [
            'payment_method_name' => 'Cash',
        ]);

        $this->db->insert('ip_payment_methods', [
            'payment_method_name' => 'Credit Card',
        ]);
    }

    /**
     * @return bool
     */
    public function upgrade_tables()
    {
        // Collect the available SQL files
        $sql_files = directory_map(APPPATH . 'modules/setup/sql', true);

        // Sort them so they're in natural order
        sort($sql_files);

        // Unset the installer
        unset($sql_files[0]);

        // Loop through the files and take appropriate action
        foreach ($sql_files as $sql_file) {
            if (mb_substr($sql_file, -4) !== '.sql') {
                continue;
            }

            $this->db->where('version_file', $sql_file);
            $update_applied = $this->db->get('ip_versions');

            if ($update_applied->num_rows()) {
                continue;
            }

            $file_contents = file_get_contents(APPPATH . 'modules/setup/sql/' . $sql_file);
            $this->execute_contents($file_contents, $sql_file);
            $this->save_version($sql_file);

            $upgrade_method = 'upgrade_' . str_replace('.', '_', mb_substr($sql_file, 0, -4));

            if ( ! method_exists($this, $upgrade_method)) {
                continue;
            }

            // A hook that returns false did not finish its data change. Forget the version
            // so "Try again" repeats the hook instead of skipping a migration that is
            // already recorded as applied.
            if ($this->{$upgrade_method}() === false) {
                $this->db->where('version_file', $sql_file)->delete('ip_versions');
            }
        }

        if ($this->errors) {
            return false;
        }

        $this->install_default_settings();

        return true;
    }

    /**
     * ===========================================
     * Place upgrade functions here
     * e.g. if table rows have to be converted
     * public function upgrade_010_1_0_1() { ... }.
     */
    public function upgrade_006_1_2_0()
    {
        /* Update alert to notify about the changes with invoice deletion and credit invoices
         * but only display the warning when the previous version is 1.1.2 or lower and it's an update
         * therefore check if it's an update, if the time difference between v1.1.2 and v1.2.0 is
         * greater than 100 and if v1.2.0 was not installed within this update process
         */
        $this->db->where_in('version_file', ['006_1.2.0.sql', '005_1.1.2.sql']);
        $versions     = $this->db->get('ip_versions')->result();
        $upgrade_diff = $versions[1]->version_date_applied - $versions[0]->version_date_applied;

        if ($this->session->userdata('is_upgrade') && $upgrade_diff > 100 && $versions[1]->version_date_applied > (time() - 100)) {
            $setup_notice = [
                'type'    => 'alert-danger',
                'content' => trans('setup_v120_alert'),
            ];
            $this->session->set_userdata('setup_notice', $setup_notice);
        }
    }

    public function upgrade_019_1_4_7()
    {
        /* Update alert to set the session configuration $config['sess_use_database'] = false to true
         * but only display the warning when the previous version is 1.4.6 or lower and it's an update
         * (see above for details)
         */
        $this->db->where_in('version_file', ['018_1.4.6.sql', '019_1.4.7.sql']);
        $versions     = $this->db->get('ip_versions')->result();
        $upgrade_diff = $versions[1]->version_date_applied - $versions[0]->version_date_applied;

        if ($this->session->userdata('is_upgrade') && $upgrade_diff > 100 && $versions[1]->version_date_applied > (time() - 100)) {
            $setup_notice = [
                'type'    => 'alert-danger',
                'content' => trans('setup_v147_alert'),
            ];
            $this->session->set_userdata('setup_notice', $setup_notice);
        }
    }

    public function upgrade_023_1_5_0()
    {
        $res          = $this->db->query('SELECT * FROM ip_custom_fields');
        $drop_columns = [];

        $tables = [
            'client',
            'invoice',
            'quote',
            'payment',
            'user',
        ];

        if ($res->num_rows()) {
            foreach ($res->result() as $row) {
                $drop_columns[] = [
                    'field_id' => $row->custom_field_id,
                    'column'   => $row->custom_field_column,
                    'table'    => $row->custom_field_table,
                ];
            }
        }

        // Create tables
        $this->db->query('CREATE TABLE `ip_client_custom_new`
            (
                `client_custom_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT ,
                `client_id` INT NOT NULL, `client_custom_fieldid` INT NOT NULL,
                `client_custom_fieldvalue` TEXT NULL ,
                UNIQUE (client_id, client_custom_fieldid)
            );');

        $this->db->query('CREATE TABLE `ip_invoice_custom_new`
            (
            `invoice_custom_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT ,
            `invoice_id` INT NOT NULL, `invoice_custom_fieldid` INT NOT NULL,
            `invoice_custom_fieldvalue` TEXT NULL ,
            UNIQUE (invoice_id, invoice_custom_fieldid)
            );');

        $this->db->query('CREATE TABLE `ip_quote_custom_new`
            (
                `quote_custom_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT ,
                `quote_id` INT NOT NULL, `quote_custom_fieldid` INT NOT NULL,
                `quote_custom_fieldvalue` TEXT NULL ,
                UNIQUE (quote_id, quote_custom_fieldid)
            );');

        $this->db->query('CREATE TABLE `ip_payment_custom_new`
            (
                `payment_custom_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT ,
                `payment_id` INT NOT NULL, `payment_custom_fieldid` INT NOT NULL,
                `payment_custom_fieldvalue` TEXT NULL ,
                UNIQUE (payment_id, payment_custom_fieldid)
            );');

        $this->db->query('CREATE TABLE `ip_user_custom_new`
            (
                `user_custom_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT ,
                `user_id` INT NOT NULL, `user_custom_fieldid` INT NOT NULL,
                `user_custom_fieldvalue` TEXT NULL ,
                UNIQUE (user_id, user_custom_fieldid)
            );');

        // Security (CWE-89): $value['table'] is a stored custom_field_table value
        // that is interpolated below as a raw table identifier. A pre-1.5.0 install
        // could have had an arbitrary value stored through the (then-unguarded)
        // custom-field workflow, so restrict it to the known custom tables.
        $allowed_custom_tables = [];
        foreach ($tables as $allowed_table) {
            $allowed_custom_tables[] = 'ip_' . $allowed_table . '_custom';
        }

        // Migrate Data
        foreach ($drop_columns as $value) {
            if ( ! in_array($value['table'], $allowed_custom_tables, true)) {
                $this->load->helper('file_security');
                log_message('error', 'Skipping custom field with invalid table during 1.5.0 upgrade: ' . sanitize_for_logging((string) $value['table']));
                continue;
            }

            $res = $this->db->query('SELECT * FROM ' . $value['table']);

            preg_match('/^ip_(.*?)_custom$/i', $value['table'], $matches);
            $table_type = $matches[1];
            $table_name = $value['table'] . '_new';

            if ($res->num_rows()) {
                foreach ($res->result() as $row) {
                    $escaped_table_type = $this->db->escape($row->{$table_type . '_id'});
                    $escaped_column     = $this->db->escape($row->{$value['column']});

                    $query = "INSERT INTO {$table_name}
                        (" . $table_type . '_id, ' . $table_type . '_custom_fieldid, ' . $table_type . "_custom_fieldvalue)
                        VALUES (
                            {$escaped_table_type},
                            (
                                SELECT custom_field_id
                                FROM ip_custom_fields
                                WHERE ip_custom_fields.custom_field_column = " . $this->db->escape($value['column']) . "
                            ),
                            {$escaped_column}
                        )";

                    $this->db->query($query);
                }
            }
        }

        // Drop old cloumns, and rename new ones
        foreach ($tables as $table) {
            $this->db->query('DROP TABLE IF EXISTS `ip_' . $table . '_custom`');
            $query = 'RENAME TABLE `ip_' . $table . '_custom_new` TO `ip_' . $table . '_custom`';
            $this->db->query($query);
        }

        $this->db->query('ALTER TABLE ip_custom_fields DROP COLUMN custom_field_column');
    }

    public function upgrade_029_1_5_6()
    {
        // The following code will determine if the ip_users table has an existing user_all_clients column
        // If the table already has the column it will be shown in any user query, so get one now
        $test_user = $this->db->query('SELECT * FROM `ip_users` ORDER BY `user_id` ASC LIMIT 1')->row();

        // Add new user key if applicable
        if ( ! isset($test_user->user_all_clients)) {
            $this->db->query('ALTER TABLE `ip_users`
              ADD `user_all_clients` INT(1) NOT NULL DEFAULT 0
              AFTER `user_psalt`;');
        }

        // Copy the invoice pdf footer to the new quote pdf footer setting
        $this->load->model('settings/mdl_settings');
        $this->mdl_settings->load_settings();
        $this->load->helper('settings');

        $this->mdl_settings->save('pdf_quote_footer', get_setting('pdf_invoice_footer'));
    }

    public function upgrade_036_1_6()
    {
        //upgrade the recurring invoices data and replace 0000-00-00 invalid date with null in order to be compliant
        //with the MySQL >= 5.8 defautl SQL Strict mode that is activated by default.
        //migrate the dates data from 0000-00-00 to NULL in order to allow SQL Strict mode. Because the new
        //mysql default mode, the change must be done by PHP logic.

        //**recur_end_date**
        $rows_recur_end_date = $this->db->query('SELECT * FROM `ip_invoices_recurring`');
        foreach ($rows_recur_end_date->result() as $row) {
            if ($row->recur_end_date == '0000-00-00') {
                $this->db->set('recur_end_date', null)->where('invoice_recurring_id', $row->invoice_recurring_id)->update('ip_invoices_recurring');
            }

            if ($row->recur_next_date == '0000-00-00') {
                $this->db->set('recur_next_date', null)->where('invoice_recurring_id', $row->invoice_recurring_id)->update('ip_invoices_recurring');
            }
        }

        //**client_bdate**
        $rows_client_bdate = $this->db->query('SELECT * FROM `ip_clients`');
        foreach ($rows_client_bdate->result() as $row_bdate) {
            if ($row_bdate->client_birthdate == '0000-00-00') {
                $this->db->set('client_birthdate', null)->where('client_id', $row_bdate->client_id)->update('ip_clients');
            }
        }
    }

    public function upgrade_039_1_6_3()
    {
        //**Set languages to lowercase & replace include_zugferd setting to einvoicing**
        $einvoicing = '0';
        $step       = 2;
        $rows       = $this->db->query('SELECT * FROM `ip_settings`');
        foreach ($rows->result() as $row) {
            // Set default_language to lowercase
            if ($row->setting_key == 'default_language') {
                $this->db->set('setting_value', mb_strtolower($row->setting_value))->where('setting_id', $row->setting_id)->update('ip_settings');
                $step--;
            }

            // include_zugferd > einvoicing
            if ($row->setting_key == 'include_zugferd') {
                $einvoicing = $row->setting_value;
                $this->db->set('setting_key', 'einvoicing')->where('setting_id', $row->setting_id)->update('ip_settings');
                $step--;
            }

            // All Steps Ok
            if ( ! $step) {
                break;
            }
        }

        // Set all users languages to lowercase
        $rows = $this->db->query('SELECT * FROM `ip_users`');
        foreach ($rows->result() as $row) {
            if ($row->user_language != 'system') {
                $this->db->set('user_language', mb_strtolower($row->user_language))->where('user_id', $row->user_id)->update('ip_users');
            }
        }

        if ($einvoicing == '1') {
            // einvoicing is on, Enable Zugferd v1.0 for all clients
            $data = ['client_einvoicing_active' => '1', 'client_einvoicing_version' => 'Zugferdv10'];
            $rows = $this->db->query('SELECT * FROM `ip_clients`');
            foreach ($rows->result() as $row) {
                if ($row->client_active == '1') {
                    $this->db->update('ip_clients', $data, ['client_id' => $row->client_id]);
                }
            }
        } else {
            // Delete Zugferd lib & conf
            $filename = 'Zugferdv10';
            $files[]  = APPPATH . 'libraries/XMLtemplates/' . $filename . 'Xml.php';
            $files[]  = APPPATH . 'helpers/XMLconfigs/' . $filename . '.php';
            foreach ($files as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }

    public function upgrade_043_1_7_2()
    {
        if ( ! $this->session->userdata('is_upgrade')) {
            return;
        }

        $this->load->model('settings/mdl_settings');
        $this->mdl_settings->load_settings();
        $this->load->model('invoices/mdl_templates');

        $missing_allowlisted_template_settings = $this->mdl_templates->get_missing_allowlisted_template_settings();

        if (empty($missing_allowlisted_template_settings)) {
            return;
        }

        $template_list = '<ul>';
        foreach ($missing_allowlisted_template_settings as $ipconfig_key => $template_names) {
            $template_list .= '<li><code>' . html_escape($ipconfig_key) . '</code>: '
                . html_escape(implode(', ', $template_names)) . '</li>';
        }
        $template_list .= '</ul>';

        $setup_notice = [
            'type'    => 'alert-warning',
            'content' => '<strong>' . trans('custom_templates_upgrade_required') . '</strong><br>'
                . trans('custom_templates_upgrade_required_message') . '<br>'
                . trans('custom_templates_upgrade_required_ipconfig')
                . $template_list
                . trans('custom_templates_upgrade_required_docs'),
        ];

        $this->session->set_userdata('setup_notice', $setup_notice);
    }

    public function upgrade_046_innodb_conversion(): bool
    {
        return $this->convert_tables_to_innodb();
    }

    /**
     * Convert every remaining MyISAM table in this schema to InnoDB.
     *
     * MyISAM has no transactions, no crash recovery and locks whole tables on write.
     * The application already assumes otherwise: Cron::recur() wraps recurring invoice
     * creation in trans_start()/trans_complete(), which silently does nothing under
     * MyISAM, so a failed invoice copy could advance the recurring schedule without
     * ever generating the invoice. Converting makes that rollback real.
     *
     * The table list is read from information_schema instead of being hardcoded:
     * ip_sessions and ip_login_log were created without an ENGINE clause and inherit
     * the server default, installs may have been converted by hand already, and an
     * install upgrading from an old version may not have every table yet. Only tables
     * that exist and are actually MyISAM are touched, so re-running is a no-op.
     *
     * Note for large installs: ALTER TABLE ... ENGINE rebuilds the table and holds a
     * write lock for its duration. Take a backup first. This method increases PHP's
     * max_execution_time to prevent timeout on large table conversions, and retries
     * conversion if a lock wait timeout is encountered.
     *
     * @return bool false when a table failed to convert, so the migration is retried
     */
    private function convert_tables_to_innodb(): bool
    {
        // Increase timeout for large table conversions (3600s = 1 hour max per table).
        // ALTER TABLE can take a very long time on large tables; default PHP timeout
        // (30s) would kill the upgrade. Store the original to restore after conversion.
        $original_timeout = ini_get('max_execution_time');
        if ($original_timeout !== false && (int) $original_timeout > 0) {
            set_time_limit(3600);
        }

        $this->db->db_debug = IP_DEBUG;

        $tables = $this->db->query(
            "SELECT TABLE_NAME AS table_name
               FROM information_schema.TABLES
              WHERE TABLE_SCHEMA = DATABASE()
                AND ENGINE = 'MyISAM'"
        );

        $error = $this->db->error();
        if ($error['code'] !== 0) {
            // Typically a permissions problem reading information_schema. Not fatal:
            // the upgrade should not be blocked by an optional storage-engine change.
            // Retrying would fail the same way every time, so the migration stays recorded.
            $this->errors[] = 'Could not read table engines, skipped InnoDB conversion: ' . $error['message'];

            return true;
        }

        if ( ! $tables) {
            return true;
        }

        $converted = 0;
        $failed    = 0;
        foreach ($tables->result() as $table) {
            $table_name = (string) $table->table_name;

            // The name comes from information_schema rather than user input, but it is
            // interpolated into DDL that cannot be parameterised, so it is checked
            // against the expected shape before being used as an identifier.
            if (preg_match('/^ip_[a-z0-9_]+$/i', $table_name) !== 1) {
                continue;
            }

            $this->db->db_debug = IP_DEBUG;

            // Retry once on lock wait timeout (error 1205), which is common on live
            // databases when a long-running query holds a lock the ALTER needs.
            $attempt      = 0;
            $max_attempts = 2;
            while ($attempt < $max_attempts) {
                $attempt++;
                $this->db->query('ALTER TABLE `' . $table_name . '` ENGINE=InnoDB');
                $error = $this->db->error();

                if ($error['code'] === 0) {
                    // Success
                    break;
                }

                // MySQL error 1205 = "Lock wait timeout exceeded"
                if ($error['code'] === 1205 && $attempt < $max_attempts) {
                    // Brief wait before retry, so the lock holder has time to finish
                    sleep(2);
                    continue;
                }

                // Other error or final attempt failed
                if ($attempt === $max_attempts) {
                    $this->errors[] = 'Could not convert ' . $table_name . ' to InnoDB: ' . $error['message'];
                    $failed++;
                }
            }

            if ($error['code'] === 0) {
                $converted++;
            }
        }

        if ($converted > 0) {
            log_message('info', '[Setup] Converted ' . $converted . ' table(s) from MyISAM to InnoDB');
        }

        return $failed === 0;
    }

    /**
     * @param string $contents
     */
    private function execute_contents(string|bool $contents, string $source = '')
    {
        if ( ! is_string($contents)) {
            $this->errors[] = 'Setup aborted: could not read migration SQL'
                . ($source !== '' ? " '" . $source . "'" : '')
                . ' (file_get_contents() returned ' . gettype($contents) . ' instead of a string).';

            return;
        }

        $this->load->helper('sql');
        $commands = split_sql_statements($contents);

        foreach ($commands as $command) {
            $this->db->db_debug = IP_DEBUG;

            $this->db->query($command . ';');

            $error = $this->db->error();
            if ($error['code'] !== 0) {
                $this->errors[] = $error['message'];
            }
        }
    }

    /**
     * @param $sql_file
     */
    private function save_version($sql_file)
    {
        $version_db_array = [
            'version_date_applied' => time(),
            'version_file'         => $sql_file,
            'version_sql_errors'   => count($this->errors),
        ];

        $this->db->insert('ip_versions', $version_db_array);
    }

    private function install_default_settings()
    {
        $this->load->helper('ip_security');

        $default_settings = [
            'default_language'             => $this->session->userdata('ip_lang'),
            'date_format'                  => 'm/d/Y',
            'currency_symbol'              => '$',
            'currency_symbol_placement'    => 'before',
            'currency_code'                => 'USD',
            'invoices_due_after'           => 30,
            'quotes_expire_after'          => 15,
            'default_invoice_group'        => 3,
            'default_quote_group'          => 4,
            'thousands_separator'          => ',',
            'decimal_point'                => '.',
            'cron_key'                     => generate_secure_token(8),
            'tax_rate_decimal_places'      => 2,
            'pdf_invoice_template'         => 'InvoicePlane',
            'pdf_invoice_template_paid'    => 'InvoicePlane - paid',
            'pdf_invoice_template_overdue' => 'InvoicePlane - overdue',
            'pdf_quote_template'           => 'InvoicePlane',
            'public_invoice_template'      => 'InvoicePlane_Web',
            'public_quote_template'        => 'InvoicePlane_Web',
            'disable_sidebar'              => 1,
            // Payment reminders ship switched off with no offsets configured, so an
            // upgrade never starts mailing clients on its own. Enabling it is an
            // explicit choice in Settings > Invoices.
            'invoice_reminders_enabled'    => 0,
            'invoice_reminder_days_before' => '',
            'invoice_reminder_days_after'  => '',
            'invoice_reminder_repeat_days' => 0,
            'invoice_reminder_max_total'   => 10,
        ];

        foreach ($default_settings as $setting_key => $setting_value) {
            $this->db->where('setting_key', $setting_key);

            if ( ! $this->db->get('ip_settings')->num_rows()) {
                $db_array = [
                    'setting_key'   => $setting_key,
                    'setting_value' => $setting_value,
                ];

                $this->db->insert('ip_settings', $db_array);
            }
        }
    }
}
