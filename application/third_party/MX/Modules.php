<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

(defined('EXT')) OR define('EXT', '.php');

global $CFG;

/* get module locations from config settings or use the default module location and offset */
is_array(Modules::$locations = $CFG->item('modules_locations')) OR Modules::$locations = [
    APPPATH . 'modules/' => '../modules/',
];

/* PHP5 spl_autoload */
spl_autoload_register('Modules::autoload');

function myEach($arr) {
    $key = key($arr);
    $result = ($key === null) ? false : [$key, current($arr), 'key' => $key, 'value' => current($arr)];
    next($arr);
    return $result;
}


/**
 * @AllowDynamicProperties
 */
class Modules
{
    public static $routes, $registry, $locations;

    /**
     * Run a module controller method
     * Output from module is buffered and returned.
     **/
    public static function run($module)
    {
        $method = 'index';
	    $args = func_get_args();

        if (($pos = strrpos($module, '/')) != false) {
            $method = substr($module, $pos + 1);
            $module = substr($module, 0, $pos);
        }

        if ($class = self::load($module)) {
            if (method_exists($class, $method)) {
                ob_start();
                $output = call_user_func_array([$class, $method], array_slice($args, 1));
                $buffer = ob_get_clean();
                return ($output !== null) ? $output : $buffer;
            }
        }

        log_message('error', "Module controller failed to run: {$module}/{$method}");
    }

    /** Load a module controller **/
    public static function load($module)
    {
        if (is_array($module))
	{
                list($module, $params) = @myEach($module);
	}
        else
	{
		$params = null;
	}

        /* get the requested controller class name */
    	if ($module==null) {
    		$alias = '';
    	} else {
            $alias = strtolower(basename($module));
    	}

        /* create or return an existing controller from the registry */
        if (!isset(self::$registry[$alias])) {
            /* find the controller */
            if ($module == null) {
		    list($class) = CI::$APP->router->locate(array());
	    } else {
            list($class) = CI::$APP->router->locate(explode('/', $module));
	    }

            /* controller cannot be located */
            if (empty($class)) {
                return;
            }

            /* set the module directory */
            $path = APPPATH . 'controllers/' . CI::$APP->router->directory;

            /* load the controller class */
            $class = $class . CI::$APP->config->item('controller_suffix');
            self::load_file(ucfirst($class), $path);

            /* create and register the new controller */
            $controller = ucfirst($class);
            self::$registry[$alias] = new $controller($params);
        }

        return self::$registry[$alias];
    }

    /** Load a module file **/
    public static function load_file($file, $path, $type = 'other', $result = true)
    {
        $file = str_replace(EXT, '', $file);
        $location = $path . $file . EXT;

        if ($type === 'other') {
            if (class_exists($file, false)) {
                log_message('debug', "File already loaded: {$location}");
                return $result;
            }
            include_once $location;
        } else {
            /* load config or language array */
            include $location;

            if (!isset($$type) OR !is_array($$type)) {
                show_error("{$location} does not contain a valid {$type} array");
            }

            $result = $$type;
        }
        log_message('debug', "File loaded: {$location}");
        return $result;
    }

    /** Library base class autoload **/
    public static function autoload($class)
    {
        /* don't autoload CI_ prefixed classes or those using the config subclass_prefix */
        if (strstr($class, 'CI_') OR strstr($class, config_item('subclass_prefix'))) {
            return;
        }

        /* autoload Modular Extensions MX core classes */
        if (strstr($class, 'MX_')) {
            if (is_file($location = dirname(__FILE__) . '/' . substr($class, 3) . EXT)) {
                include_once $location;
                return;
            }
            show_error('Failed to load MX core class: ' . $class);
        }

        /* autoload core classes */
        if (is_file($location = APPPATH . 'core/' . ucfirst($class) . EXT)) {
            include_once $location;
            return;
        }

        /* autoload library classes */
        if (is_file($location = APPPATH . 'libraries/' . ucfirst($class) . EXT)) {
            include_once $location;
            return;
        }
    }

    /** Parse module routes **/
    public static function parse_routes($module, $uri)
    {
        /* load the route file */
        if (!isset(self::$routes[$module])) {
            if (list($path) = self::find('routes', $module, 'config/')) {
                $path && self::$routes[$module] = self::load_file('routes', $path, 'route');
            }
        }

        if (!isset(self::$routes[$module])) {
            return;
        }

        /* parse module routes */
        foreach (self::$routes[$module] as $key => $val) {
            $key = str_replace([':any', ':num'], ['.+', '[0-9]+'], $key);

            if (preg_match('#^' . $key . '$#', $uri)) {
                if (strpos($val, '$') !== false AND strpos($key, '(') !== false) {
                    $val = preg_replace('#^' . $key . '$#', $val, $uri);
                }
                return explode('/', $module . '/' . $val);
            }
        }
    }

    /**
     * Find a file
     * Scans for files located within modules directories.
     * Also scans application directories for models, plugins and views.
     * Generates fatal error if file not found.
     **/
    public static function find($file, $module, $base)
    {
        $segments = explode('/', $file);

        $file = array_pop($segments);
        $file_ext = (pathinfo($file, PATHINFO_EXTENSION)) ? $file : $file . EXT;

        $path = ltrim(implode('/', $segments) . '/', '/');
        $module ? $modules[$module] = $path : $modules = [];

        if (!empty($segments)) {
            $modules[array_shift($segments)] = ltrim(implode('/', $segments) . '/', '/');
        }

        foreach (Modules::$locations as $location => $offset) {
            foreach ($modules as $module => $subpath) {
                $fullpath = $location . $module . '/' . $base . $subpath;

                if ($base == 'libraries/' OR $base == 'models/') {
                    if (is_file($fullpath . ucfirst($file_ext))) {
                        return [$fullpath, ucfirst($file)];
                    }
                } else /* load non-class files */ {
                    if (is_file($fullpath . $file_ext)) {
                        return [$fullpath, $file];
                    }
                }
            }
        }

        return [false, $file];
    }
}
