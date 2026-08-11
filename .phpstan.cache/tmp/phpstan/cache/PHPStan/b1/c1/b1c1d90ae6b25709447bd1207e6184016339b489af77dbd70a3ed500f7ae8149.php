<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/application/modules/users/models/Mdl_users.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Mdl_Users
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-cdb65b37eb54c755b1ecfd1ad43696c5ef69d061cf65306a00dae5c5920843be',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Mdl_Users',
        'filename' => '/var/www/projects/exprmt/application/modules/users/models/Mdl_users.php',
      ),
    ),
    'namespace' => NULL,
    'name' => 'Mdl_Users',
    'shortName' => 'Mdl_Users',
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
    'endLine' => 379,
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
      'PROTECTED_FIELDS' => 
      array (
        'declaringClassName' => 'Mdl_Users',
        'implementingClassName' => 'Mdl_Users',
        'name' => 'PROTECTED_FIELDS',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'user_type\', \'user_active\', \'user_psalt\']',
          'attributes' => 
          array (
            'startLine' => 24,
            'endLine' => 24,
            'startTokenPos' => 50,
            'startFilePos' => 645,
            'endTokenPos' => 58,
            'endFilePos' => 686,
          ),
        ),
        'docComment' => '/**
 * Fields that must never be written from raw POST data regardless of
 * validation rules. Controllers that legitimately need to set these
 * must build and pass their own $db_array to save().
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 80,
      ),
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'Mdl_Users',
        'implementingClassName' => 'Mdl_Users',
        'name' => 'table',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'ip_users\'',
          'attributes' => 
          array (
            'startLine' => 26,
            'endLine' => 26,
            'startTokenPos' => 67,
            'startFilePos' => 710,
            'endTokenPos' => 67,
            'endFilePos' => 719,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 26,
        'endLine' => 26,
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
        'declaringClassName' => 'Mdl_Users',
        'implementingClassName' => 'Mdl_Users',
        'name' => 'primary_key',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'ip_users.user_id\'',
          'attributes' => 
          array (
            'startLine' => 28,
            'endLine' => 28,
            'startTokenPos' => 76,
            'startFilePos' => 749,
            'endTokenPos' => 76,
            'endFilePos' => 766,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 28,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 45,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'date_created_field' => 
      array (
        'declaringClassName' => 'Mdl_Users',
        'implementingClassName' => 'Mdl_Users',
        'name' => 'date_created_field',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'user_date_created\'',
          'attributes' => 
          array (
            'startLine' => 30,
            'endLine' => 30,
            'startTokenPos' => 85,
            'startFilePos' => 803,
            'endTokenPos' => 85,
            'endFilePos' => 821,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 53,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'date_modified_field' => 
      array (
        'declaringClassName' => 'Mdl_Users',
        'implementingClassName' => 'Mdl_Users',
        'name' => 'date_modified_field',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'user_date_modified\'',
          'attributes' => 
          array (
            'startLine' => 32,
            'endLine' => 32,
            'startTokenPos' => 94,
            'startFilePos' => 859,
            'endTokenPos' => 94,
            'endFilePos' => 878,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 32,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 55,
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
      'user_types' => 
      array (
        'name' => 'user_types',
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
        'startLine' => 37,
        'endLine' => 43,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Users',
        'implementingClassName' => 'Mdl_Users',
        'currentClassName' => 'Mdl_Users',
        'aliasName' => NULL,
      ),
      'default_select' => 
      array (
        'name' => 'default_select',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 45,
        'endLine' => 48,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Users',
        'implementingClassName' => 'Mdl_Users',
        'currentClassName' => 'Mdl_Users',
        'aliasName' => NULL,
      ),
      'default_order_by' => 
      array (
        'name' => 'default_order_by',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 50,
        'endLine' => 53,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Users',
        'implementingClassName' => 'Mdl_Users',
        'currentClassName' => 'Mdl_Users',
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
        'startLine' => 58,
        'endLine' => 166,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Users',
        'implementingClassName' => 'Mdl_Users',
        'currentClassName' => 'Mdl_Users',
        'aliasName' => NULL,
      ),
      'get_latest' => 
      array (
        'name' => 'get_latest',
        'parameters' => 
        array (
          'amount' => 
          array (
            'name' => 'amount',
            'default' => 
            array (
              'code' => '20',
              'attributes' => 
              array (
                'startLine' => 173,
                'endLine' => 173,
                'startTokenPos' => 831,
                'startFilePos' => 4999,
                'endTokenPos' => 831,
                'endFilePos' => 5000,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 173,
            'endLine' => 173,
            'startColumn' => 32,
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
 * @param int $amount
 *
 * @return mixed
 */',
        'startLine' => 173,
        'endLine' => 181,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Users',
        'implementingClassName' => 'Mdl_Users',
        'currentClassName' => 'Mdl_Users',
        'aliasName' => NULL,
      ),
      'validation_rules_existing' => 
      array (
        'name' => 'validation_rules_existing',
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
        'startLine' => 186,
        'endLine' => 280,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Users',
        'implementingClassName' => 'Mdl_Users',
        'currentClassName' => 'Mdl_Users',
        'aliasName' => NULL,
      ),
      'validation_rules_change_password' => 
      array (
        'name' => 'validation_rules_change_password',
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
        'startLine' => 285,
        'endLine' => 299,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Users',
        'implementingClassName' => 'Mdl_Users',
        'currentClassName' => 'Mdl_Users',
        'aliasName' => NULL,
      ),
      'db_array' => 
      array (
        'name' => 'db_array',
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
        'startLine' => 304,
        'endLine' => 325,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Users',
        'implementingClassName' => 'Mdl_Users',
        'currentClassName' => 'Mdl_Users',
        'aliasName' => NULL,
      ),
      'save_change_password' => 
      array (
        'name' => 'save_change_password',
        'parameters' => 
        array (
          'user_id' => 
          array (
            'name' => 'user_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 331,
            'endLine' => 331,
            'startColumn' => 42,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'password' => 
          array (
            'name' => 'password',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 331,
            'endLine' => 331,
            'startColumn' => 52,
            'endColumn' => 60,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param $user_id
 * @param $password
 */',
        'startLine' => 331,
        'endLine' => 347,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Users',
        'implementingClassName' => 'Mdl_Users',
        'currentClassName' => 'Mdl_Users',
        'aliasName' => NULL,
      ),
      'save' => 
      array (
        'name' => 'save',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => 
            array (
              'code' => '\\null',
              'attributes' => 
              array (
                'startLine' => 352,
                'endLine' => 352,
                'startTokenPos' => 1776,
                'startFilePos' => 10093,
                'endTokenPos' => 1776,
                'endFilePos' => 10096,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 352,
            'endLine' => 352,
            'startColumn' => 26,
            'endColumn' => 35,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'db_array' => 
          array (
            'name' => 'db_array',
            'default' => 
            array (
              'code' => '\\null',
              'attributes' => 
              array (
                'startLine' => 352,
                'endLine' => 352,
                'startTokenPos' => 1783,
                'startFilePos' => 10111,
                'endTokenPos' => 1783,
                'endFilePos' => 10114,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 352,
            'endLine' => 352,
            'startColumn' => 38,
            'endColumn' => 53,
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
 * @return int|null
 */',
        'startLine' => 352,
        'endLine' => 367,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Users',
        'implementingClassName' => 'Mdl_Users',
        'currentClassName' => 'Mdl_Users',
        'aliasName' => NULL,
      ),
      'delete' => 
      array (
        'name' => 'delete',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 372,
            'endLine' => 372,
            'startColumn' => 28,
            'endColumn' => 30,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param int $id
 */',
        'startLine' => 372,
        'endLine' => 378,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Users',
        'implementingClassName' => 'Mdl_Users',
        'currentClassName' => 'Mdl_Users',
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