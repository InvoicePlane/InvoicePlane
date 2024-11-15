<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/** load the CI class for Modular Extensions **/
require dirname(__FILE__) . '/Base.php';

#[AllowDynamicProperties]
class MX_Controller
{
    public $autoload = [];
    public $load;

    public function __construct()
    {
        if (CI::$APP->config->item('controller_suffix')==null) $class = str_replace('', '', get_class($this));
        else $class = str_replace(CI::$APP->config->item('controller_suffix'), '', get_class($this));

        log_message('debug', $class . " MX_Controller Initialized");
        Modules::$registry[strtolower($class)] = $this;

        /* copy a loader instance and initialize */
        $this->load = clone load_class('Loader');
        $this->load->initialize($this);

        /* autoload module items */
        $this->load->_autoloader($this->autoload);
    }

    public function __get($class)
    {
        return CI::$APP->$class;
    }
}
