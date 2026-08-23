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
class Settings extends Admin_Controller
{
    private const MIN_TAX_RATE_DECIMALS = 2;

    private const MAX_TAX_RATE_DECIMALS = 3;

    /**
     * Settings constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_settings');
        $this->load->library('crypt');
        $this->load->library('form_validation');
        $this->load->helper('payments_helper');
        $this->load->helper('file_security');

        // Security: Check for SVG logos and display warnings
        $this->check_svg_logos();
    }

    public function index()
    {
        // Get the payment gateway configurations
        $this->config->load('payment_gateways');
        $gateways = $this->config->item('payment_gateways');

        // Get the number formats configurations
        $this->config->load('number_formats');
        $number_formats = $this->config->item('number_formats');

        // Save input if request is POSt
        if ($this->input->post('settings')) {
            $settings = $this->input->post('settings');

            $settings = $this->handleTaxRateDecimalPlaces($settings);

            // Build array of all settings to save in a single batch operation
            $batch_settings = [];

            foreach ($settings as $key => $value) {
                if (str_contains($key, 'field_is_password') || str_contains($key, 'field_is_amount')) {
                    // Skip all meta fields
                    continue;
                }

                if (isset($settings[$key . '_field_is_password']) && empty($value)) {
                    // Password field, but empty value, let's skip it
                    continue;
                }

                if (isset($settings[$key . '_field_is_password']) && $value !== '') {
                    // Encrypt passwords but don't save empty passwords
                    $batch_settings[$key] = $this->crypt->encode(trim($value));
                } elseif (isset($settings[$key . '_field_is_amount'])) {
                    // Format amount inputs
                    $batch_settings[$key] = standardize_amount($value);
                } else {
                    // Security: Validate logo filename settings to prevent path traversal
                    if ($key === 'invoice_logo' || $key === 'login_logo') {
                        if ( ! empty($value)) {
                            $validation = validate_safe_filename($value);
                            if ( ! $validation['valid']) {
                                log_message('error', sprintf('Path traversal attempt blocked in %s setting (hash: %s, error: %s)', sanitize_for_logging($key), $validation['hash'], sanitize_for_logging($validation['error'])));
                                $this->session->set_flashdata('alert_error', trans('invalid_filename'));
                                redirect('settings');
                            }
                        }
                    }
                    $batch_settings[$key] = $value;
                }

                if ($key === 'number_format') {
                    // Set thousands_separator and decimal_point according to number_format
                    $batch_settings['decimal_point'] = $number_formats[$value]['decimal_point'];
                    $batch_settings['thousands_separator'] = $number_formats[$value]['thousands_separator'];
                }
            }

            // Save all settings in a single batch operation (reduces ~30-40 queries to 2-3 queries)
            $this->mdl_settings->save_batch($batch_settings);

            $upload_config = [
                'upload_path'   => './uploads/',
                'allowed_types' => 'gif|jpg|jpeg|png', // Invoice quote logo image - SVG removed for security
                'max_size'      => '9999',
                'max_width'     => '9999',
                'max_height'    => '9999',
            ];

            // Check for invoice logo upload
            if ($_FILES['invoice_logo']['name']) {
                // Security: Check for SVG files before attempting upload
                $file_extension = mb_strtolower(pathinfo($_FILES['invoice_logo']['name'], PATHINFO_EXTENSION));
                if ($file_extension === 'svg') {
                    log_message('warning', 'SVG upload attempt blocked for invoice_logo by user ' . $this->session->userdata('user_id') . ': ' . sanitize_for_logging(basename($_FILES['invoice_logo']['name'])));
                    $this->session->set_flashdata('alert_error', trans('svg_upload_blocked_security'));
                    redirect('settings');
                }

                $this->load->library('upload', $upload_config);

                if ( ! $this->upload->do_upload('invoice_logo')) {
                    $this->session->set_flashdata('alert_error', $this->upload->display_errors());
                    redirect('settings');
                }

                $upload_data = $this->upload->data();

                // Security: Strip EXIF metadata from uploaded logo
                $this->strip_logo_metadata($upload_data['full_path'], 'invoice_logo');

                $this->mdl_settings->save('invoice_logo', $upload_data['file_name']);
            }

            // Check for login logo upload
            if ($_FILES['login_logo']['name']) {
                // Security: Check for SVG files before attempting upload
                $file_extension = mb_strtolower(pathinfo($_FILES['login_logo']['name'], PATHINFO_EXTENSION));
                if ($file_extension === 'svg') {
                    log_message('warning', 'SVG upload attempt blocked for login_logo by user ' . $this->session->userdata('user_id') . ': ' . sanitize_for_logging(basename($_FILES['login_logo']['name'])));
                    $this->session->set_flashdata('alert_error', trans('svg_upload_blocked_security'));
                    redirect('settings');
                }

                $this->load->library('upload', $upload_config);

                if ( ! $this->upload->do_upload('login_logo')) {
                    $this->session->set_flashdata('alert_error', $this->upload->display_errors());
                    redirect('settings');
                }

                $upload_data = $this->upload->data();

                // Security: Strip EXIF metadata from uploaded logo
                $this->strip_logo_metadata($upload_data['full_path'], 'login_logo');

                $this->mdl_settings->save('login_logo', $upload_data['file_name']);
            }

            $this->session->set_flashdata('alert_success', trans('settings_successfully_saved'));

            redirect('settings');
        }

        // Load required resources
        $this->load->model([
            'invoice_groups/mdl_invoice_groups',
            'tax_rates/mdl_tax_rates',
            'email_templates/mdl_email_templates',
            'payment_methods/mdl_payment_methods',
            'invoices/mdl_templates',
            'custom_fields/mdl_invoice_custom',
            'custom_fields/mdl_custom_fields',
        ]);

        // Collect the list of templates
        $pdf_invoice_templates                 = $this->mdl_templates->get_invoice_templates('pdf');
        $public_invoice_templates              = $this->mdl_templates->get_invoice_templates('public');
        $pdf_quote_templates                   = $this->mdl_templates->get_quote_templates('pdf');
        $public_quote_templates                = $this->mdl_templates->get_quote_templates('public');
        $missing_allowlisted_template_settings = $this->mdl_templates->get_missing_allowlisted_template_settings();

        // Get all themes
        $available_themes = $this->mdl_settings->get_themes();

        // Set data in the layout
        $this->layout->set(
            [
                'invoice_groups'                        => $this->mdl_invoice_groups->get()->result(),
                'tax_rates'                             => $this->mdl_tax_rates->get()->result(),
                'payment_methods'                       => $this->mdl_payment_methods->get()->result(),
                'public_invoice_templates'              => $public_invoice_templates,
                'pdf_invoice_templates'                 => $pdf_invoice_templates,
                'public_quote_templates'                => $public_quote_templates,
                'pdf_quote_templates'                   => $pdf_quote_templates,
                'missing_allowlisted_template_settings' => $missing_allowlisted_template_settings,
                'languages'                             => get_available_languages(),
                'countries'                             => get_country_list(trans('cldr')),
                'date_formats'                          => date_formats(),
                'current_date'                          => new DateTime(),
                'available_themes'                      => $available_themes,
                'email_templates_quote'                 => $this->mdl_email_templates->where('email_template_type', 'quote')->get()->result(),
                'email_templates_invoice'               => $this->mdl_email_templates->where('email_template_type', 'invoice')->get()->result(),
                'custom_fields'                         => ['ip_invoice_custom' => $this->mdl_custom_fields->by_table('ip_invoice_custom')->get()->result()],
                'gateway_drivers'                       => $gateways,
                'number_formats'                        => $number_formats,
                'gateway_currency_codes'                => get_currencies(),
                'first_days_of_weeks'                   => ['0' => lang('sunday'), '1' => lang('monday')],
                'legacy_calculation'                    => config_item('legacy_calculation'),
            ]
        );

        $this->layout->buffer('content', 'settings/index');
        $this->layout->render();
    }

    /**
     * Remove a logo file with security validation.
     *
     * Security: Validates that the logo file path is safe and within the uploads directory
     * to prevent arbitrary file deletion attacks. Requires POST request and valid CSRF token.
     *
     * @param string $type Logo type ('invoice' or 'login')
     */
    public function remove_logo(string $type)
    {
        if ( ! $this->ensure_valid_post_request('settings')) {
            return;
        }

        // Security: Validate type parameter against allowed values
        $allowed_types = ['invoice', 'login'];
        if ( ! in_array($type, $allowed_types, true)) {
            log_message('error', sprintf(
                'Invalid logo type specified: %s by user %s',
                sanitize_for_logging($type),
                sanitize_for_logging((string) $this->session->userdata('user_id'))
            ));
            $this->session->set_flashdata('alert_error', trans('invalid_file_path'));
            redirect('settings');
        }

        // Get the logo filename from settings
        $logo_filename = get_setting($type . '_logo');

        // If no logo is configured, nothing to delete
        if (empty($logo_filename)) {
            $this->session->set_flashdata('alert_success', trans($type . '_logo_removed'));
            redirect('settings');
        }

        // Security: Validate the logo filename is safe and within uploads directory
        $uploads_dir = './uploads/';
        $validation = validate_file_access($logo_filename, $uploads_dir);

        if ( ! $validation['valid']) {
            // Special case: File not found is a legitimate scenario (manual deletion, disk cleanup)
            // Allow clearing the stale database setting to prevent DB/disk inconsistency
            // This is distinct from security validation failures which indicate attack attempts
            if ($validation['error'] === 'file_not_found') {
                log_message('info', sprintf(
                    'Clearing stale logo setting for type=%s (file not found) by user %s',
                    sanitize_for_logging($type),
                    sanitize_for_logging((string) $this->session->userdata('user_id'))
                ));
                $this->mdl_settings->save($type . '_logo', '');
                $this->session->set_flashdata('alert_success', trans($type . '_logo_removed'));
                redirect('settings');
            }

            // Security: Log the invalid attempt with hash for investigation
            log_message('error', sprintf(
                'Invalid logo removal attempt for type=%s (hash: %s, error: %s) by user %s',
                sanitize_for_logging($type),
                sanitize_for_logging((string) $validation['hash']),
                sanitize_for_logging((string) ($validation['error'] ?? 'unknown')),
                sanitize_for_logging((string) $this->session->userdata('user_id'))
            ));

            $this->session->set_flashdata('alert_error', trans(
                ($validation['error'] ?? '') === 'path_outside_directory'
                    ? 'invalid_file_path_outside_allowed_directory'
                    : 'invalid_file_path'
            ));
            redirect('settings');
        }

        // Security: Use the validated path from validation result
        // Attempt to delete the file and verify success
        // Note: file_exists() check serves dual purpose:
        //   1. TOCTOU protection - file could be deleted between validation and unlink
        //   2. Graceful handling - allows DB cleanup if file already removed
        if (file_exists($validation['path'])) {
            $deleted = unlink($validation['path']);
            if ( ! $deleted) {
                // Re-check file existence - if file is gone (race delete), proceed with cleanup
                if (file_exists($validation['path'])) {
                    log_message('error', sprintf(
                        'Failed to remove logo file for type=%s by user %s',
                        sanitize_for_logging($type),
                        sanitize_for_logging((string) $this->session->userdata('user_id'))
                    ));
                    $this->session->set_flashdata('alert_error', trans('failure'));
                    redirect('settings');
                }
            }
        }

        // Only clear DB setting after successful file deletion (or if file doesn't exist)
        $this->mdl_settings->save($type . '_logo', '');

        $this->session->set_flashdata('alert_success', trans($type . '_logo_removed'));

        redirect('settings');
    }

    /**
     * Check for SVG logos and display security warnings
     * This provides a soft migration path for existing SVG logos.
     */
    private function check_svg_logos()
    {
        $logos_to_check = ['login_logo', 'invoice_logo'];

        foreach ($logos_to_check as $logo_setting) {
            $logo_file = get_setting($logo_setting);
            if ($logo_file) {
                $extension = mb_strtolower(pathinfo($logo_file, PATHINFO_EXTENSION));
                if ($extension === 'svg') {
                    $this->session->set_flashdata(
                        'alert_warning',
                        trans('svg_logo_blocked_security') . ' '
                        . trans('please_remove_and_reupload')
                    );
                }
            }
        }
    }

    /**
     * Validate and persist tax rate decimal places setting, including schema changes.
     *
     * @param array $settings
     *
     * @return array settings array, with tax_rate_decimal_places removed if it was processed
     */
    private function handleTaxRateDecimalPlaces(array $settings): array
    {
        if ( ! array_key_exists('tax_rate_decimal_places', $settings)) {
            return $settings;
        }

        $this->load->library('settings/TaxRateDecimalPlacesProcessor', [], 'tax_rate_decimal_places_processor');
        $processor = $this->tax_rate_decimal_places_processor;
        $decimal_places_input = $settings['tax_rate_decimal_places'];

        try {
            $decimal_places = $processor->validateAndNormalize(
                $decimal_places_input,
                self::MIN_TAX_RATE_DECIMALS,
                self::MAX_TAX_RATE_DECIMALS
            );
        } catch (InvalidArgumentException $exception) {
            log_message(
                'error',
                sprintf(
                    'Invalid tax rate decimal places (must be %d-%d): %s',
                    self::MIN_TAX_RATE_DECIMALS,
                    self::MAX_TAX_RATE_DECIMALS,
                    sanitize_for_logging((string) $decimal_places_input)
                )
            );
            $this->session->set_flashdata('alert_error', trans('invalid_tax_rate_decimal_places'));
            redirect('settings');
        }

        // Only execute if the setting is different
        $current_decimal_places = (int) $this->mdl_settings->setting('tax_rate_decimal_places', self::MIN_TAX_RATE_DECIMALS);
        if ($processor->shouldAlterSchema($current_decimal_places, $decimal_places)) {
            $this->db->trans_begin();

            // Note: ALTER TABLE requires direct query execution as Query Builder
            // does not support DDL statements. The integer validation above combined with
            // sprintf using %d ensures the value cannot alter the SQL structure.
            $ddl_query = sprintf(
                'ALTER TABLE `ip_tax_rates` CHANGE `tax_rate_percent` `tax_rate_percent` DECIMAL(5, %d) NOT NULL',
                $decimal_places
            );

            $ddl_result = $this->db->query($ddl_query);
            $ddl_error = $this->db->error();
            if ($ddl_result === false || (isset($ddl_error['code']) && (int) $ddl_error['code'] !== 0)) {
                $this->db->trans_rollback();
                log_message(
                    'error',
                    sprintf(
                        'Failed to alter ip_tax_rates for tax_rate_decimal_places=%s (code %s): %s',
                        sanitize_for_logging((string) $decimal_places),
                        isset($ddl_error['code']) ? sanitize_for_logging((string) $ddl_error['code']) : 'unknown',
                        isset($ddl_error['message']) ? sanitize_for_logging($ddl_error['message']) : 'unknown'
                    )
                );
                $this->session->set_flashdata('alert_error', trans('failed_to_update_tax_rate_decimal_places'));
                redirect('settings');
            }

            $this->mdl_settings->save('tax_rate_decimal_places', (string) $decimal_places);

            $save_error = $this->db->error();
            if (isset($save_error['code']) && (int) $save_error['code'] !== 0) {
                $this->db->trans_rollback();
                log_message(
                    'error',
                    sprintf(
                        'Failed to save tax_rate_decimal_places after schema change: %s',
                        isset($save_error['message']) ? sanitize_for_logging($save_error['message']) : 'unknown'
                    )
                );
                $this->session->set_flashdata('alert_error', trans('failed_to_update_tax_rate_decimal_places'));
                redirect('settings');
            }

            $this->db->trans_commit();
        }

        // Remove the entry to avoid double-processing in the general settings loop.
        unset($settings['tax_rate_decimal_places']);

        return $settings;
    }

    /**
     * Strip EXIF metadata from uploaded logo file.
     *
     * @param string $filePath The full path to the uploaded file
     * @param string $logoType The type of logo (invoice_logo or login_logo)
     *
     * @return void
     */
    private function strip_logo_metadata(string $filePath, string $logoType): void
    {
        $result = strip_exif_metadata($filePath);

        if ( ! $result['success'] && ! isset($result['skipped'])) {
            // Log the error but don't fail the upload - the file is already uploaded
            log_message('warning', 'Failed to strip EXIF metadata from ' . sanitize_for_logging($logoType) . ': ' . $result['error']);
        } elseif ($result['success'] && ! isset($result['skipped'])) {
            // Successfully stripped EXIF metadata
            log_message('debug', 'EXIF metadata stripped from ' . sanitize_for_logging($logoType));
        }
    }
}
