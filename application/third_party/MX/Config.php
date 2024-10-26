<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * @AllowDynamicProperties
 */
class MX_Config extends CI_Config
{
    public function load($file = '', $use_sections = false, $fail_gracefully = false, $_module = '')
    {
        if (in_array($file, $this->is_loaded, true)) {
            return $this->item($file);
        }

        $_module OR $_module = CI::$APP->router->fetch_module();
        list($path, $file) = Modules::find($file, $_module, 'config/');

        if ($path === false) {
            parent::load($file, $use_sections, $fail_gracefully);
            return $this->item($file);
        }

        if ($config = Modules::load_file($file, $path, 'config')) {
            /* reference to the config array */
            $current_config =& $this->config;

            if ($use_sections === true) {
                if (isset($current_config[$file])) {
                    $current_config[$file] = array_merge($current_config[$file], $config);
                } else {
                    $current_config[$file] = $config;
                }

            } else {
                $current_config = array_merge($current_config, $config);
            }

            $this->is_loaded[] = $file;
            unset($config);
            return $this->item($file);
        }
    }
}
