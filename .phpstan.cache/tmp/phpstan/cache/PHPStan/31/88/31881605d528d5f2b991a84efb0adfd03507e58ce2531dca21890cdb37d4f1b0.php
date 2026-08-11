<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/application/modules/units/models/Mdl_units.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Mdl_Units
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-e8273dc0103e53a70caf46483229a5f64c61d03d5a25da5ebe34a297b8bc1542',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Mdl_Units',
        'filename' => '/var/www/projects/exprmt/application/modules/units/models/Mdl_units.php',
      ),
    ),
    'namespace' => NULL,
    'name' => 'Mdl_Units',
    'shortName' => 'Mdl_Units',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
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
    'startLine' => 16,
    'endLine' => 76,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Response_Model',
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
      'table' => 
      array (
        'declaringClassName' => 'Mdl_Units',
        'implementingClassName' => 'Mdl_Units',
        'name' => 'table',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'ip_units\'',
          'attributes' => 
          array (
            'startLine' => 19,
            'endLine' => 19,
            'startTokenPos' => 46,
            'startFilePos' => 407,
            'endTokenPos' => 46,
            'endFilePos' => 416,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'primary_key' => 
      array (
        'declaringClassName' => 'Mdl_Units',
        'implementingClassName' => 'Mdl_Units',
        'name' => 'primary_key',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'ip_units.unit_id\'',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 21,
            'startTokenPos' => 55,
            'startFilePos' => 446,
            'endTokenPos' => 55,
            'endFilePos' => 463,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 45,
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
      'default_select' => 
      array (
        'name' => 'default_select',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 23,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Units',
        'implementingClassName' => 'Mdl_Units',
        'currentClassName' => 'Mdl_Units',
        'aliasName' => NULL,
      ),
      'default_order_by' => 
      array (
        'name' => 'default_order_by',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 28,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Units',
        'implementingClassName' => 'Mdl_Units',
        'currentClassName' => 'Mdl_Units',
        'aliasName' => NULL,
      ),
      'get_name' => 
      array (
        'name' => 'get_name',
        'parameters' => 
        array (
          'unit_id' => 
          array (
            'name' => 'unit_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 42,
            'endLine' => 42,
            'startColumn' => 30,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'quantity' => 
          array (
            'name' => 'quantity',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 42,
            'endLine' => 42,
            'startColumn' => 40,
            'endColumn' => 48,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return either the singular unit name or the plural unit name,
 * depending on the quantity.
 *
 * @param $unit_id
 * @param $quantity
 *
 * @return mixed
 */',
        'startLine' => 42,
        'endLine' => 56,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Units',
        'implementingClassName' => 'Mdl_Units',
        'currentClassName' => 'Mdl_Units',
        'aliasName' => NULL,
      ),
      'validation_rules' => 
      array (
        'name' => 'validation_rules',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return array
 */',
        'startLine' => 61,
        'endLine' => 75,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Units',
        'implementingClassName' => 'Mdl_Units',
        'currentClassName' => 'Mdl_Units',
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