<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

// Hack for PHPUnit - hide HTML output when ENVIRONMENT == 'testing'
$hook['display_override'] = array(
    'class' => 'DisplayHook',
    'function' => 'captureOutput',
    'filename' => 'DisplayHook.php',
    'filepath' => 'hooks'
);

$hook['pre_controller'] = array(
    'class' => 'SetTimezoneClass',
    'function' => 'setTimezone',
    'filename' => 'SetTimezoneClass.php',
    'filepath' => 'hooks'
);


/* End of file hooks.php */
/* Location: ./application/config/hooks.php */