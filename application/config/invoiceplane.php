<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Allow deletion of invoices after they have been sent
|--------------------------------------------------------------------------
|
| As it is forbidden in many countries to delete invoices that have been
| sent to a customer the deletion is disabled by default. If you need
| this function anyway you can enable invoice deletion by setting the
| value to true
|
*/
$config['enable_invoice_deletion'] = env_bool('ENABLE_INVOICE_DELETION', false);

/*
|--------------------------------------------------------------------------
| Disable the read-only mode
|--------------------------------------------------------------------------
|
| As it is forbidden in many countries to alter invoices that have been
| sent to a customer the ability to alter invoices is disabled by default.
| If you need this function anyway you can enable invoice deletion by
| setting the value to true
|
*/
$config['disable_read_only'] = env_bool('DISABLE_READ_ONLY', false);
