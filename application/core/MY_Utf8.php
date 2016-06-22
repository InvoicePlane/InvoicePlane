<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package        CodeIgniter
 * @author        EllisLab Dev Team
 * @copyright        Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @copyright        Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license        http://codeigniter.com/user_guide/license.html
 * @link        http://codeigniter.com
 * @since        Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Utf8 Class
 *
 * Provides support for UTF-8 environments
 *
 * @package        CodeIgniter
 * @subpackage    Libraries
 * @category    UTF-8
 * @author        EllisLab Dev Team
 * @link        http://codeigniter.com/user_guide/libraries/utf8.html
 */
class MY_Utf8 extends CI_Utf8
{

    /**
     * Constructor
     *
     * InvoicePlane: Hack for PHPUnit (tested on PHPUnit 3.7.21)
     * Constructor based on 'CI_VERSION', '2.2.1'
     *
     * Determines if UTF-8 support is to be enabled
     *
     */
    function __construct()
    {
        log_message('debug', 'MY Utf8 Initialized - Hack for PHPUnit to run');

        // global $CFG;
        $CFG =& load_class('Config', 'core');

        // rest should be the same as in CI_Utf8::__construct()

        if (
            preg_match('/./u', 'é') === 1                    // PCRE must support UTF-8
            AND function_exists('iconv')                    // iconv must be installed
            AND ini_get('mbstring.func_overload') != 1        // Multibyte string function overloading cannot be enabled
            AND $CFG->item('charset') == 'UTF-8'            // Application charset must be UTF-8
        ) {
            log_message('debug', 'UTF-8 Support Enabled');

            define('UTF8_ENABLED', true);

            // set internal encoding for multibyte string functions if necessary
            // and set a flag so we don't have to repeatedly use extension_loaded()
            // or function_exists()
            if (extension_loaded('mbstring')) {
                define('MB_ENABLED', true);
                mb_internal_encoding('UTF-8');
            } else {
                define('MB_ENABLED', false);
            }
        } else {
            log_message('debug', 'UTF-8 Support Disabled');
            define('UTF8_ENABLED', false);
        }
    }
}
// End Utf8 Class

/* End of file Utf8.php */
/* Location: ./system/core/Utf8.php */