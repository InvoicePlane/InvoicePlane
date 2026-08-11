<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/vendor/pocketarc/codeigniter/system/core/Loader.php-PHPStan\BetterReflection\Reflection\ReflectionClass-CI_Loader
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-76694347c270f50ee0a30f119c2e28bf1c35d7105118a7efd1332cc8a7caa446',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'CI_Loader',
        'filename' => '/var/www/projects/exprmt/vendor/pocketarc/codeigniter/system/core/Loader.php',
      ),
    ),
    'namespace' => NULL,
    'name' => 'CI_Loader',
    'shortName' => 'CI_Loader',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Loader Class
 *
 * Loads framework components.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Loader
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/userguide3/libraries/loader.html
 */',
    'attributes' => 
    array (
      0 => 
      array (
        'name' => 'AllowDynamicProperties',
        'isRepeated' => false,
        'arguments' => 
        array (
        ),
      ),
    ),
    'startLine' => 52,
    'endLine' => 1448,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      '_ci_ob_level' => 
      array (
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'name' => '_ci_ob_level',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * Nesting level of the output buffering mechanism
 *
 * @var	int
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 61,
        'endLine' => 61,
        'startColumn' => 2,
        'endColumn' => 25,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_ci_view_paths' => 
      array (
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'name' => '_ci_view_paths',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'array(\\VIEWPATH => \\TRUE)',
          'attributes' => 
          array (
            'startLine' => 68,
            'endLine' => 68,
            'startTokenPos' => 45,
            'startFilePos' => 2340,
            'endTokenPos' => 52,
            'endFilePos' => 2362,
          ),
        ),
        'docComment' => '/**
 * List of paths to load views from
 *
 * @var	array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 68,
        'endLine' => 68,
        'startColumn' => 2,
        'endColumn' => 53,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_ci_library_paths' => 
      array (
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'name' => '_ci_library_paths',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'array(\\APPPATH, \\BASEPATH)',
          'attributes' => 
          array (
            'startLine' => 75,
            'endLine' => 75,
            'startTokenPos' => 63,
            'startFilePos' => 2468,
            'endTokenPos' => 69,
            'endFilePos' => 2491,
          ),
        ),
        'docComment' => '/**
 * List of paths to load libraries from
 *
 * @var	array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 75,
        'endLine' => 75,
        'startColumn' => 2,
        'endColumn' => 57,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_ci_model_paths' => 
      array (
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'name' => '_ci_model_paths',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'array(\\APPPATH)',
          'attributes' => 
          array (
            'startLine' => 82,
            'endLine' => 82,
            'startTokenPos' => 80,
            'startFilePos' => 2592,
            'endTokenPos' => 83,
            'endFilePos' => 2605,
          ),
        ),
        'docComment' => '/**
 * List of paths to load models from
 *
 * @var	array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 82,
        'endLine' => 82,
        'startColumn' => 2,
        'endColumn' => 45,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_ci_helper_paths' => 
      array (
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'name' => '_ci_helper_paths',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'array(\\APPPATH, \\BASEPATH)',
          'attributes' => 
          array (
            'startLine' => 89,
            'endLine' => 89,
            'startTokenPos' => 94,
            'startFilePos' => 2708,
            'endTokenPos' => 100,
            'endFilePos' => 2731,
          ),
        ),
        'docComment' => '/**
 * List of paths to load helpers from
 *
 * @var	array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 89,
        'endLine' => 89,
        'startColumn' => 2,
        'endColumn' => 56,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_ci_cached_vars' => 
      array (
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'name' => '_ci_cached_vars',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'array()',
          'attributes' => 
          array (
            'startLine' => 96,
            'endLine' => 96,
            'startTokenPos' => 111,
            'startFilePos' => 2823,
            'endTokenPos' => 113,
            'endFilePos' => 2829,
          ),
        ),
        'docComment' => '/**
 * List of cached variables
 *
 * @var	array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 96,
        'endLine' => 96,
        'startColumn' => 2,
        'endColumn' => 38,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_ci_load_vars_stack' => 
      array (
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'name' => '_ci_load_vars_stack',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'array()',
          'attributes' => 
          array (
            'startLine' => 103,
            'endLine' => 103,
            'startTokenPos' => 124,
            'startFilePos' => 2995,
            'endTokenPos' => 126,
            'endFilePos' => 3001,
          ),
        ),
        'docComment' => '/**
 * Stack of variable arrays to provide nested _ci_load calls with all variables from parent calls
 *
 * @var	array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 103,
        'endLine' => 103,
        'startColumn' => 2,
        'endColumn' => 42,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_ci_classes' => 
      array (
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'name' => '_ci_classes',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'array()',
          'attributes' => 
          array (
            'startLine' => 110,
            'endLine' => 110,
            'startTokenPos' => 137,
            'startFilePos' => 3087,
            'endTokenPos' => 139,
            'endFilePos' => 3093,
          ),
        ),
        'docComment' => '/**
 * List of loaded classes
 *
 * @var	array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 110,
        'endLine' => 110,
        'startColumn' => 2,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_ci_models' => 
      array (
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'name' => '_ci_models',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'array()',
          'attributes' => 
          array (
            'startLine' => 117,
            'endLine' => 117,
            'startTokenPos' => 150,
            'startFilePos' => 3177,
            'endTokenPos' => 152,
            'endFilePos' => 3183,
          ),
        ),
        'docComment' => '/**
 * List of loaded models
 *
 * @var	array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 117,
        'endLine' => 117,
        'startColumn' => 2,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_ci_helpers' => 
      array (
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'name' => '_ci_helpers',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'array()',
          'attributes' => 
          array (
            'startLine' => 124,
            'endLine' => 124,
            'startTokenPos' => 163,
            'startFilePos' => 3269,
            'endTokenPos' => 165,
            'endFilePos' => 3275,
          ),
        ),
        'docComment' => '/**
 * List of loaded helpers
 *
 * @var	array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 124,
        'endLine' => 124,
        'startColumn' => 2,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_ci_varmap' => 
      array (
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'name' => '_ci_varmap',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'array(\'unit_test\' => \'unit\', \'user_agent\' => \'agent\')',
          'attributes' => 
          array (
            'startLine' => 131,
            'endLine' => 134,
            'startTokenPos' => 176,
            'startFilePos' => 3365,
            'endTokenPos' => 192,
            'endFilePos' => 3424,
          ),
        ),
        'docComment' => '/**
 * List of class name mappings
 *
 * @var	array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 131,
        'endLine' => 134,
        'startColumn' => 2,
        'endColumn' => 3,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Class constructor
 *
 * Sets component load paths, gets the initial output buffering level.
 *
 * @return	void
 */',
        'startLine' => 145,
        'endLine' => 151,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      'initialize' => 
      array (
        'name' => 'initialize',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Initializer
 *
 * @todo	Figure out a way to move this to the constructor
 *		without breaking *package_path*() methods.
 * @uses	CI_Loader::_ci_autoloader()
 * @used-by	CI_Controller::__construct()
 * @return	void
 */',
        'startLine' => 164,
        'endLine' => 167,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      'is_loaded' => 
      array (
        'name' => 'is_loaded',
        'parameters' => 
        array (
          'class' => 
          array (
            'name' => 'class',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 181,
            'endLine' => 181,
            'startColumn' => 28,
            'endColumn' => 33,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Is Loaded
 *
 * A utility method to test if a class is in the self::$_ci_classes array.
 *
 * @used-by	Mainly used by Form Helper function _get_validation_object().
 *
 * @param 	string		$class	Class name to check for
 * @return 	string|bool	Class object name if loaded or FALSE
 */',
        'startLine' => 181,
        'endLine' => 184,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      'library' => 
      array (
        'name' => 'library',
        'parameters' => 
        array (
          'library' => 
          array (
            'name' => 'library',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 199,
            'endLine' => 199,
            'startColumn' => 26,
            'endColumn' => 33,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => '\\NULL',
              'attributes' => 
              array (
                'startLine' => 199,
                'endLine' => 199,
                'startTokenPos' => 319,
                'startFilePos' => 5115,
                'endTokenPos' => 319,
                'endFilePos' => 5118,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 199,
            'endLine' => 199,
            'startColumn' => 36,
            'endColumn' => 49,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'object_name' => 
          array (
            'name' => 'object_name',
            'default' => 
            array (
              'code' => '\\NULL',
              'attributes' => 
              array (
                'startLine' => 199,
                'endLine' => 199,
                'startTokenPos' => 326,
                'startFilePos' => 5136,
                'endTokenPos' => 326,
                'endFilePos' => 5139,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 199,
            'endLine' => 199,
            'startColumn' => 52,
            'endColumn' => 70,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Library Loader
 *
 * Loads and instantiates libraries.
 * Designed to be called from application controllers.
 *
 * @param	mixed	$library	Library name
 * @param	array	$params		Optional parameters to pass to the library class constructor
 * @param	string	$object_name	An optional object name to assign to
 * @return	object
 */',
        'startLine' => 199,
        'endLine' => 229,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      'model' => 
      array (
        'name' => 'model',
        'parameters' => 
        array (
          'model' => 
          array (
            'name' => 'model',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 243,
            'endLine' => 243,
            'startColumn' => 24,
            'endColumn' => 29,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'name' => 
          array (
            'name' => 'name',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 243,
                'endLine' => 243,
                'startTokenPos' => 497,
                'startFilePos' => 5977,
                'endTokenPos' => 497,
                'endFilePos' => 5978,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 243,
            'endLine' => 243,
            'startColumn' => 32,
            'endColumn' => 41,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'db_conn' => 
          array (
            'name' => 'db_conn',
            'default' => 
            array (
              'code' => '\\FALSE',
              'attributes' => 
              array (
                'startLine' => 243,
                'endLine' => 243,
                'startTokenPos' => 504,
                'startFilePos' => 5992,
                'endTokenPos' => 504,
                'endFilePos' => 5996,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 243,
            'endLine' => 243,
            'startColumn' => 44,
            'endColumn' => 59,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Model Loader
 *
 * Loads and instantiates models.
 *
 * @param	mixed	$model		Model name
 * @param	string	$name		An optional object name to assign to
 * @param	bool	$db_conn	An optional database connection configuration to initialize
 * @return	object
 */',
        'startLine' => 243,
        'endLine' => 371,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      'database' => 
      array (
        'name' => 'database',
        'parameters' => 
        array (
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 383,
                'endLine' => 383,
                'startTokenPos' => 1300,
                'startFilePos' => 9567,
                'endTokenPos' => 1300,
                'endFilePos' => 9568,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 383,
            'endLine' => 383,
            'startColumn' => 27,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'return' => 
          array (
            'name' => 'return',
            'default' => 
            array (
              'code' => '\\FALSE',
              'attributes' => 
              array (
                'startLine' => 383,
                'endLine' => 383,
                'startTokenPos' => 1307,
                'startFilePos' => 9581,
                'endTokenPos' => 1307,
                'endFilePos' => 9585,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 383,
            'endLine' => 383,
            'startColumn' => 41,
            'endColumn' => 55,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Database Loader
 *
 * @param	mixed	$params		Database configuration options
 * @param	bool	$return 	Whether to return the database object
 * @return	object|bool	Database object if $return is set to TRUE,
 *					FALSE on failure, CI_Loader instance in any other case
 */',
        'startLine' => 383,
        'endLine' => 408,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      'dbutil' => 
      array (
        'name' => 'dbutil',
        'parameters' => 
        array (
          'db' => 
          array (
            'name' => 'db',
            'default' => 
            array (
              'code' => '\\NULL',
              'attributes' => 
              array (
                'startLine' => 419,
                'endLine' => 419,
                'startTokenPos' => 1455,
                'startFilePos' => 10390,
                'endTokenPos' => 1455,
                'endFilePos' => 10393,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 419,
            'endLine' => 419,
            'startColumn' => 25,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'return' => 
          array (
            'name' => 'return',
            'default' => 
            array (
              'code' => '\\FALSE',
              'attributes' => 
              array (
                'startLine' => 419,
                'endLine' => 419,
                'startTokenPos' => 1462,
                'startFilePos' => 10406,
                'endTokenPos' => 1462,
                'endFilePos' => 10410,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 419,
            'endLine' => 419,
            'startColumn' => 37,
            'endColumn' => 51,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Load the Database Utilities Class
 *
 * @param	object	$db	Database object
 * @param	bool	$return	Whether to return the DB Utilities class object or not
 * @return	object
 */',
        'startLine' => 419,
        'endLine' => 440,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      'dbforge' => 
      array (
        'name' => 'dbforge',
        'parameters' => 
        array (
          'db' => 
          array (
            'name' => 'db',
            'default' => 
            array (
              'code' => '\\NULL',
              'attributes' => 
              array (
                'startLine' => 451,
                'endLine' => 451,
                'startTokenPos' => 1632,
                'startFilePos' => 11167,
                'endTokenPos' => 1632,
                'endFilePos' => 11170,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 451,
            'endLine' => 451,
            'startColumn' => 26,
            'endColumn' => 35,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'return' => 
          array (
            'name' => 'return',
            'default' => 
            array (
              'code' => '\\FALSE',
              'attributes' => 
              array (
                'startLine' => 451,
                'endLine' => 451,
                'startTokenPos' => 1639,
                'startFilePos' => 11183,
                'endTokenPos' => 1639,
                'endFilePos' => 11187,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 451,
            'endLine' => 451,
            'startColumn' => 38,
            'endColumn' => 52,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Load the Database Forge Class
 *
 * @param	object	$db	Database object
 * @param	bool	$return	Whether to return the DB Forge class object or not
 * @return	object
 */',
        'startLine' => 451,
        'endLine' => 484,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      'view' => 
      array (
        'name' => 'view',
        'parameters' => 
        array (
          'view' => 
          array (
            'name' => 'view',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 500,
            'endLine' => 500,
            'startColumn' => 23,
            'endColumn' => 27,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'vars' => 
          array (
            'name' => 'vars',
            'default' => 
            array (
              'code' => 'array()',
              'attributes' => 
              array (
                'startLine' => 500,
                'endLine' => 500,
                'startTokenPos' => 1901,
                'startFilePos' => 12395,
                'endTokenPos' => 1903,
                'endFilePos' => 12401,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 500,
            'endLine' => 500,
            'startColumn' => 30,
            'endColumn' => 44,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'return' => 
          array (
            'name' => 'return',
            'default' => 
            array (
              'code' => '\\FALSE',
              'attributes' => 
              array (
                'startLine' => 500,
                'endLine' => 500,
                'startTokenPos' => 1910,
                'startFilePos' => 12414,
                'endTokenPos' => 1910,
                'endFilePos' => 12418,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 500,
            'endLine' => 500,
            'startColumn' => 47,
            'endColumn' => 61,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * View Loader
 *
 * Loads "view" files.
 *
 * @param	string	$view	View name
 * @param	array	$vars	An associative array of data
 *				to be extracted for use in the view
 * @param	bool	$return	Whether to return the view output
 *				or leave it to the Output class
 * @return	object|string
 */',
        'startLine' => 500,
        'endLine' => 516,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      'file' => 
      array (
        'name' => 'file',
        'parameters' => 
        array (
          'path' => 
          array (
            'name' => 'path',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 527,
            'endLine' => 527,
            'startColumn' => 23,
            'endColumn' => 27,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'return' => 
          array (
            'name' => 'return',
            'default' => 
            array (
              'code' => '\\FALSE',
              'attributes' => 
              array (
                'startLine' => 527,
                'endLine' => 527,
                'startTokenPos' => 2081,
                'startFilePos' => 13185,
                'endTokenPos' => 2081,
                'endFilePos' => 13189,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 527,
            'endLine' => 527,
            'startColumn' => 30,
            'endColumn' => 44,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Generic File Loader
 *
 * @param	string	$path	File path
 * @param	bool	$return	Whether to return the file output
 * @return	object|string
 */',
        'startLine' => 527,
        'endLine' => 530,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      'vars' => 
      array (
        'name' => 'vars',
        'parameters' => 
        array (
          'vars' => 
          array (
            'name' => 'vars',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 546,
            'endLine' => 546,
            'startColumn' => 23,
            'endColumn' => 27,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'val' => 
          array (
            'name' => 'val',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 546,
                'endLine' => 546,
                'startTokenPos' => 2129,
                'startFilePos' => 13756,
                'endTokenPos' => 2129,
                'endFilePos' => 13757,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 546,
            'endLine' => 546,
            'startColumn' => 30,
            'endColumn' => 38,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set Variables
 *
 * Once variables are set they become available within
 * the controller class and its "view" files.
 *
 * @param	array|object|string	$vars
 *					An associative array or object containing values
 *					to be set, or a value\'s name if string
 * @param 	string	$val	Value to set, only used if $vars is a string
 * @return	object
 */',
        'startLine' => 546,
        'endLine' => 558,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      'clear_vars' => 
      array (
        'name' => 'clear_vars',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Clear Cached Variables
 *
 * Clears the cached variables.
 *
 * @return	CI_Loader
 */',
        'startLine' => 569,
        'endLine' => 573,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      'get_var' => 
      array (
        'name' => 'get_var',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 585,
            'endLine' => 585,
            'startColumn' => 26,
            'endColumn' => 29,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get Variable
 *
 * Check if a variable is set and retrieve it.
 *
 * @param	string	$key	Variable name
 * @return	mixed	The variable or NULL if not found
 */',
        'startLine' => 585,
        'endLine' => 588,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      'get_vars' => 
      array (
        'name' => 'get_vars',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get Variables
 *
 * Retrieves all loaded variables.
 *
 * @return	array
 */',
        'startLine' => 599,
        'endLine' => 602,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      'helper' => 
      array (
        'name' => 'helper',
        'parameters' => 
        array (
          'helpers' => 
          array (
            'name' => 'helpers',
            'default' => 
            array (
              'code' => 'array()',
              'attributes' => 
              array (
                'startLine' => 612,
                'endLine' => 612,
                'startTokenPos' => 2313,
                'startFilePos' => 15036,
                'endTokenPos' => 2315,
                'endFilePos' => 15042,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 612,
            'endLine' => 612,
            'startColumn' => 25,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Helper Loader
 *
 * @param	string|string[]	$helpers	Helper name(s)
 * @return	object
 */',
        'startLine' => 612,
        'endLine' => 675,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      'helpers' => 
      array (
        'name' => 'helpers',
        'parameters' => 
        array (
          'helpers' => 
          array (
            'name' => 'helpers',
            'default' => 
            array (
              'code' => 'array()',
              'attributes' => 
              array (
                'startLine' => 689,
                'endLine' => 689,
                'startTokenPos' => 2744,
                'startFilePos' => 17125,
                'endTokenPos' => 2746,
                'endFilePos' => 17131,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 689,
            'endLine' => 689,
            'startColumn' => 26,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Load Helpers
 *
 * An alias for the helper() method in case the developer has
 * written the plural form of it.
 *
 * @uses	CI_Loader::helper()
 * @param	string|string[]	$helpers	Helper name(s)
 * @return	object
 */',
        'startLine' => 689,
        'endLine' => 692,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      'language' => 
      array (
        'name' => 'language',
        'parameters' => 
        array (
          'files' => 
          array (
            'name' => 'files',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 705,
            'endLine' => 705,
            'startColumn' => 27,
            'endColumn' => 32,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'lang' => 
          array (
            'name' => 'lang',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 705,
                'endLine' => 705,
                'startTokenPos' => 2780,
                'startFilePos' => 17477,
                'endTokenPos' => 2780,
                'endFilePos' => 17478,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 705,
            'endLine' => 705,
            'startColumn' => 35,
            'endColumn' => 44,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Language Loader
 *
 * Loads language files.
 *
 * @param	string|string[]	$files	List of language file names to load
 * @param	string		Language name
 * @return	object
 */',
        'startLine' => 705,
        'endLine' => 709,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      'config' => 
      array (
        'name' => 'config',
        'parameters' => 
        array (
          'file' => 
          array (
            'name' => 'file',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 724,
            'endLine' => 724,
            'startColumn' => 25,
            'endColumn' => 29,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'use_sections' => 
          array (
            'name' => 'use_sections',
            'default' => 
            array (
              'code' => '\\FALSE',
              'attributes' => 
              array (
                'startLine' => 724,
                'endLine' => 724,
                'startTokenPos' => 2824,
                'startFilePos' => 18107,
                'endTokenPos' => 2824,
                'endFilePos' => 18111,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 724,
            'endLine' => 724,
            'startColumn' => 32,
            'endColumn' => 52,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'fail_gracefully' => 
          array (
            'name' => 'fail_gracefully',
            'default' => 
            array (
              'code' => '\\FALSE',
              'attributes' => 
              array (
                'startLine' => 724,
                'endLine' => 724,
                'startTokenPos' => 2831,
                'startFilePos' => 18133,
                'endTokenPos' => 2831,
                'endFilePos' => 18137,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 724,
            'endLine' => 724,
            'startColumn' => 55,
            'endColumn' => 78,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Config Loader
 *
 * Loads a config file (an alias for CI_Config::load()).
 *
 * @uses	CI_Config::load()
 * @param	string	$file			Configuration file name
 * @param	bool	$use_sections		Whether configuration values should be loaded into their own section
 * @param	bool	$fail_gracefully	Whether to just return FALSE or display an error message
 * @return	bool	TRUE if the file was loaded correctly or FALSE on failure
 */',
        'startLine' => 724,
        'endLine' => 727,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      'driver' => 
      array (
        'name' => 'driver',
        'parameters' => 
        array (
          'library' => 
          array (
            'name' => 'library',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 743,
            'endLine' => 743,
            'startColumn' => 25,
            'endColumn' => 32,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => '\\NULL',
              'attributes' => 
              array (
                'startLine' => 743,
                'endLine' => 743,
                'startTokenPos' => 2875,
                'startFilePos' => 18738,
                'endTokenPos' => 2875,
                'endFilePos' => 18741,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 743,
            'endLine' => 743,
            'startColumn' => 35,
            'endColumn' => 48,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'object_name' => 
          array (
            'name' => 'object_name',
            'default' => 
            array (
              'code' => '\\NULL',
              'attributes' => 
              array (
                'startLine' => 743,
                'endLine' => 743,
                'startTokenPos' => 2882,
                'startFilePos' => 18759,
                'endTokenPos' => 2882,
                'endFilePos' => 18762,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 743,
            'endLine' => 743,
            'startColumn' => 51,
            'endColumn' => 69,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Driver Loader
 *
 * Loads a driver library.
 *
 * @param	string|string[]	$library	Driver name(s)
 * @param	array		$params		Optional parameters to pass to the driver
 * @param	string		$object_name	An optional object name to assign to
 *
 * @return	object|bool	Object or FALSE on failure if $library is a string
 *				and $object_name is set. CI_Loader instance otherwise.
 */',
        'startLine' => 743,
        'endLine' => 780,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      'add_package_path' => 
      array (
        'name' => 'add_package_path',
        'parameters' => 
        array (
          'path' => 
          array (
            'name' => 'path',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 799,
            'endLine' => 799,
            'startColumn' => 35,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'view_cascade' => 
          array (
            'name' => 'view_cascade',
            'default' => 
            array (
              'code' => '\\TRUE',
              'attributes' => 
              array (
                'startLine' => 799,
                'endLine' => 799,
                'startTokenPos' => 3085,
                'startFilePos' => 20042,
                'endTokenPos' => 3085,
                'endFilePos' => 20045,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 799,
            'endLine' => 799,
            'startColumn' => 42,
            'endColumn' => 61,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Add Package Path
 *
 * Prepends a parent path to the library, model, helper and config
 * path arrays.
 *
 * @see	CI_Loader::$_ci_library_paths
 * @see	CI_Loader::$_ci_model_paths
 * @see CI_Loader::$_ci_helper_paths
 * @see CI_Config::$_config_paths
 *
 * @param	string	$path		Path to add
 * @param 	bool	$view_cascade	(default: TRUE)
 * @return	object
 */',
        'startLine' => 799,
        'endLine' => 814,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      'get_package_paths' => 
      array (
        'name' => 'get_package_paths',
        'parameters' => 
        array (
          'include_base' => 
          array (
            'name' => 'include_base',
            'default' => 
            array (
              'code' => '\\FALSE',
              'attributes' => 
              array (
                'startLine' => 826,
                'endLine' => 826,
                'startTokenPos' => 3209,
                'startFilePos' => 20754,
                'endTokenPos' => 3209,
                'endFilePos' => 20758,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 826,
            'endLine' => 826,
            'startColumn' => 36,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get Package Paths
 *
 * Return a list of all package paths.
 *
 * @param	bool	$include_base	Whether to include BASEPATH (default: FALSE)
 * @return	array
 */',
        'startLine' => 826,
        'endLine' => 829,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      'remove_package_path' => 
      array (
        'name' => 'remove_package_path',
        'parameters' => 
        array (
          'path' => 
          array (
            'name' => 'path',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 843,
                'endLine' => 843,
                'startTokenPos' => 3253,
                'startFilePos' => 21250,
                'endTokenPos' => 3253,
                'endFilePos' => 21251,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 843,
            'endLine' => 843,
            'startColumn' => 38,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Remove Package Path
 *
 * Remove a path from the library, model, helper and/or config
 * path arrays if it exists. If no path is provided, the most recently
 * added path will be removed removed.
 *
 * @param	string	$path	Path to remove
 * @return	object
 */',
        'startLine' => 843,
        'endLine' => 885,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      '_ci_load' => 
      array (
        'name' => '_ci_load',
        'parameters' => 
        array (
          '_ci_data' => 
          array (
            'name' => '_ci_data',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 902,
            'endLine' => 902,
            'startColumn' => 30,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Internal CI Data Loader
 *
 * Used to load views and files.
 *
 * Variables are prefixed with _ci_ to avoid symbol collision with
 * variables made available to view files.
 *
 * @used-by	CI_Loader::view()
 * @used-by	CI_Loader::file()
 * @param	array	$_ci_data	Data to load
 * @return	object
 */',
        'startLine' => 902,
        'endLine' => 1034,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      '_ci_load_library' => 
      array (
        'name' => '_ci_load_library',
        'parameters' => 
        array (
          'class' => 
          array (
            'name' => 'class',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1049,
            'endLine' => 1049,
            'startColumn' => 38,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => '\\NULL',
              'attributes' => 
              array (
                'startLine' => 1049,
                'endLine' => 1049,
                'startTokenPos' => 4202,
                'startFilePos' => 27439,
                'endTokenPos' => 4202,
                'endFilePos' => 27442,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1049,
            'endLine' => 1049,
            'startColumn' => 46,
            'endColumn' => 59,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'object_name' => 
          array (
            'name' => 'object_name',
            'default' => 
            array (
              'code' => '\\NULL',
              'attributes' => 
              array (
                'startLine' => 1049,
                'endLine' => 1049,
                'startTokenPos' => 4209,
                'startFilePos' => 27460,
                'endTokenPos' => 4209,
                'endFilePos' => 27463,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1049,
            'endLine' => 1049,
            'startColumn' => 62,
            'endColumn' => 80,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Internal CI Library Loader
 *
 * @used-by	CI_Loader::library()
 * @uses	CI_Loader::_ci_init_library()
 *
 * @param	string	$class		Class name to load
 * @param	mixed	$params		Optional parameters to pass to the class constructor
 * @param	string	$object_name	Optional object name to assign to
 * @return	void
 */',
        'startLine' => 1049,
        'endLine' => 1128,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      '_ci_load_stock_library' => 
      array (
        'name' => '_ci_load_stock_library',
        'parameters' => 
        array (
          'library_name' => 
          array (
            'name' => 'library_name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1144,
            'endLine' => 1144,
            'startColumn' => 44,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'file_path' => 
          array (
            'name' => 'file_path',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1144,
            'endLine' => 1144,
            'startColumn' => 59,
            'endColumn' => 68,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1144,
            'endLine' => 1144,
            'startColumn' => 71,
            'endColumn' => 77,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'object_name' => 
          array (
            'name' => 'object_name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1144,
            'endLine' => 1144,
            'startColumn' => 80,
            'endColumn' => 91,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Internal CI Stock Library Loader
 *
 * @used-by	CI_Loader::_ci_load_library()
 * @uses	CI_Loader::_ci_init_library()
 *
 * @param	string	$library_name	Library name to load
 * @param	string	$file_path	Path to the library filename, relative to libraries/
 * @param	mixed	$params		Optional parameters to pass to the class constructor
 * @param	string	$object_name	Optional object name to assign to
 * @return	void
 */',
        'startLine' => 1144,
        'endLine' => 1212,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      '_ci_init_library' => 
      array (
        'name' => '_ci_init_library',
        'parameters' => 
        array (
          'class' => 
          array (
            'name' => 'class',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1231,
            'endLine' => 1231,
            'startColumn' => 38,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'prefix' => 
          array (
            'name' => 'prefix',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1231,
            'endLine' => 1231,
            'startColumn' => 46,
            'endColumn' => 52,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'config' => 
          array (
            'name' => 'config',
            'default' => 
            array (
              'code' => '\\FALSE',
              'attributes' => 
              array (
                'startLine' => 1231,
                'endLine' => 1231,
                'startTokenPos' => 5142,
                'startFilePos' => 32754,
                'endTokenPos' => 5142,
                'endFilePos' => 32758,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1231,
            'endLine' => 1231,
            'startColumn' => 55,
            'endColumn' => 69,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'object_name' => 
          array (
            'name' => 'object_name',
            'default' => 
            array (
              'code' => '\\NULL',
              'attributes' => 
              array (
                'startLine' => 1231,
                'endLine' => 1231,
                'startTokenPos' => 5149,
                'startFilePos' => 32776,
                'endTokenPos' => 5149,
                'endFilePos' => 32779,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1231,
            'endLine' => 1231,
            'startColumn' => 72,
            'endColumn' => 90,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Internal CI Library Instantiator
 *
 * @used-by	CI_Loader::_ci_load_stock_library()
 * @used-by	CI_Loader::_ci_load_library()
 *
 * @param	string		$class		Class name
 * @param	string		$prefix		Class name prefix
 * @param	array|null|bool	$config		Optional configuration to pass to the class constructor:
 *						FALSE to skip;
 *						NULL to search in config paths;
 *						array containing configuration data
 * @param	string		$object_name	Optional object name to assign to
 * @return	void
 */',
        'startLine' => 1231,
        'endLine' => 1319,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      '_ci_autoloader' => 
      array (
        'name' => '_ci_autoloader',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * CI Autoloader
 *
 * Loads component listed in the config/autoload.php file.
 *
 * @used-by	CI_Loader::initialize()
 * @return	void
 */',
        'startLine' => 1331,
        'endLine' => 1400,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      '_ci_prepare_view_vars' => 
      array (
        'name' => '_ci_prepare_view_vars',
        'parameters' => 
        array (
          'vars' => 
          array (
            'name' => 'vars',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1413,
            'endLine' => 1413,
            'startColumn' => 43,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Prepare variables for _ci_vars, to be later extract()-ed inside views
 *
 * Converts objects to associative arrays and filters-out internal
 * variable names (i.e. keys prefixed with \'_ci_\').
 *
 * @param	mixed	$vars
 * @return	array
 */',
        'startLine' => 1413,
        'endLine' => 1431,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
      '_ci_get_component' => 
      array (
        'name' => '_ci_get_component',
        'parameters' => 
        array (
          'component' => 
          array (
            'name' => 'component',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1443,
            'endLine' => 1443,
            'startColumn' => 40,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => true,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * CI Component getter
 *
 * Get a reference to a specific library or model.
 *
 * @param 	string	$component	Component name
 * @return	bool
 */',
        'startLine' => 1443,
        'endLine' => 1447,
        'startColumn' => 2,
        'endColumn' => 2,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => NULL,
        'declaringClassName' => 'CI_Loader',
        'implementingClassName' => 'CI_Loader',
        'currentClassName' => 'CI_Loader',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));