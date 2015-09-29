<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
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
 * @since        Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * URI Class
 *
 * Parses URIs and determines routing
 *
 * @package        CodeIgniter
 * @subpackage    Libraries
 * @category    URI
 * @author        EllisLab Dev Team
 * @link        http://codeigniter.com/user_guide/libraries/uri.html
 */
class MY_URI extends CI_URI
{

    /**
     * Get the URI String
     *
     * @access    private
     * @return    string
     */
    function _fetch_uri_string()
    {
        log_message('debug', "MY URI - Hack for PHPUnit to run");

        if (ENVIRONMENT == 'testing') {
            $this->uri_string = '';
            return;
        }

        parent::_fetch_uri_string();
    }
}
// END URI Class

/* End of file URI.php */
/* Location: ./system/core/URI.php */