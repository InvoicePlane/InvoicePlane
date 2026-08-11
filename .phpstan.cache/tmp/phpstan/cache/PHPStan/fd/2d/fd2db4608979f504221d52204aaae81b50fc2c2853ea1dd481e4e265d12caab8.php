<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/application/modules/import/models/Mdl_import.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Mdl_Import
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-44a84fe977b0ffb580ef5ab8ac85647e253783674ecab33d6e4efea4c51b9553',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Mdl_Import',
        'filename' => '/var/www/projects/exprmt/application/modules/import/models/Mdl_import.php',
      ),
    ),
    'namespace' => NULL,
    'name' => 'Mdl_Import',
    'shortName' => 'Mdl_Import',
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
    'endLine' => 460,
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
        'declaringClassName' => 'Mdl_Import',
        'implementingClassName' => 'Mdl_Import',
        'name' => 'table',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'ip_imports\'',
          'attributes' => 
          array (
            'startLine' => 19,
            'endLine' => 19,
            'startTokenPos' => 46,
            'startFilePos' => 408,
            'endTokenPos' => 46,
            'endFilePos' => 419,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'primary_key' => 
      array (
        'declaringClassName' => 'Mdl_Import',
        'implementingClassName' => 'Mdl_Import',
        'name' => 'primary_key',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'ip_imports.import_id\'',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 21,
            'startTokenPos' => 55,
            'startFilePos' => 449,
            'endTokenPos' => 55,
            'endFilePos' => 470,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 49,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'expected_headers' => 
      array (
        'declaringClassName' => 'Mdl_Import',
        'implementingClassName' => 'Mdl_Import',
        'name' => 'expected_headers',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'clients.csv\' => [\'client_name\', \'client_address_1\', \'client_address_2\', \'client_city\', \'client_state\', \'client_zip\', \'client_country\', \'client_phone\', \'client_fax\', \'client_mobile\', \'client_email\', \'client_web\', \'client_vat_id\', \'client_tax_code\', \'client_active\'], \'invoices.csv\' => [\'user_email\', \'client_name\', \'invoice_date_created\', \'invoice_date_due\', \'invoice_number\', \'invoice_terms\'], \'invoice_items.csv\' => [\'invoice_number\', \'item_tax_rate\', \'item_date_added\', \'item_name\', \'item_description\', \'item_quantity\', \'item_price\'], \'payments.csv\' => [\'invoice_number\', \'payment_method\', \'payment_date\', \'payment_amount\', \'payment_note\']]',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 65,
            'startTokenPos' => 64,
            'startFilePos' => 505,
            'endTokenPos' => 201,
            'endFilePos' => 1627,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 65,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'primary_keys' => 
      array (
        'declaringClassName' => 'Mdl_Import',
        'implementingClassName' => 'Mdl_Import',
        'name' => 'primary_keys',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'ip_clients\' => \'client_id\', \'ip_invoices\' => \'invoice_id\', \'ip_invoice_items\' => \'item_id\', \'ip_payments\' => \'payment_id\']',
          'attributes' => 
          array (
            'startLine' => 67,
            'endLine' => 72,
            'startTokenPos' => 210,
            'startFilePos' => 1658,
            'endTokenPos' => 240,
            'endFilePos' => 1836,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 67,
        'endLine' => 72,
        'startColumn' => 5,
        'endColumn' => 6,
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
 * Mdl_Import constructor.
 */',
        'startLine' => 77,
        'endLine' => 77,
        'startColumn' => 5,
        'endColumn' => 36,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Import',
        'implementingClassName' => 'Mdl_Import',
        'currentClassName' => 'Mdl_Import',
        'aliasName' => NULL,
      ),
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
        'startLine' => 79,
        'endLine' => 86,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Import',
        'implementingClassName' => 'Mdl_Import',
        'currentClassName' => 'Mdl_Import',
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
        'startLine' => 88,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Import',
        'implementingClassName' => 'Mdl_Import',
        'currentClassName' => 'Mdl_Import',
        'aliasName' => NULL,
      ),
      'start_import' => 
      array (
        'name' => 'start_import',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 93,
        'endLine' => 99,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Import',
        'implementingClassName' => 'Mdl_Import',
        'currentClassName' => 'Mdl_Import',
        'aliasName' => NULL,
      ),
      'import_data' => 
      array (
        'name' => 'import_data',
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
            'startLine' => 107,
            'endLine' => 107,
            'startColumn' => 33,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'table' => 
          array (
            'name' => 'table',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 107,
            'endLine' => 107,
            'startColumn' => 40,
            'endColumn' => 45,
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
 * @param $file
 * @param $table
 *
 * @return array|bool
 */',
        'startLine' => 107,
        'endLine' => 165,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Import',
        'implementingClassName' => 'Mdl_Import',
        'currentClassName' => 'Mdl_Import',
        'aliasName' => NULL,
      ),
      'import_invoices' => 
      array (
        'name' => 'import_invoices',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return array|bool
 */',
        'startLine' => 170,
        'endLine' => 250,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Import',
        'implementingClassName' => 'Mdl_Import',
        'currentClassName' => 'Mdl_Import',
        'aliasName' => NULL,
      ),
      'import_invoice_items' => 
      array (
        'name' => 'import_invoice_items',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return array|bool
 */',
        'startLine' => 255,
        'endLine' => 334,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Import',
        'implementingClassName' => 'Mdl_Import',
        'currentClassName' => 'Mdl_Import',
        'aliasName' => NULL,
      ),
      'import_payments' => 
      array (
        'name' => 'import_payments',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return array|bool
 */',
        'startLine' => 339,
        'endLine' => 399,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Import',
        'implementingClassName' => 'Mdl_Import',
        'currentClassName' => 'Mdl_Import',
        'aliasName' => NULL,
      ),
      'record_import_details' => 
      array (
        'name' => 'record_import_details',
        'parameters' => 
        array (
          'import_id' => 
          array (
            'name' => 'import_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 407,
            'endLine' => 407,
            'startColumn' => 43,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'table_name' => 
          array (
            'name' => 'table_name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 407,
            'endLine' => 407,
            'startColumn' => 55,
            'endColumn' => 65,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'import_lang_key' => 
          array (
            'name' => 'import_lang_key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 407,
            'endLine' => 407,
            'startColumn' => 68,
            'endColumn' => 83,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'ids' => 
          array (
            'name' => 'ids',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 407,
            'endLine' => 407,
            'startColumn' => 86,
            'endColumn' => 89,
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
 * @param $import_id
 * @param $table_name
 * @param $import_lang_key
 * @param $ids
 */',
        'startLine' => 407,
        'endLine' => 419,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Import',
        'implementingClassName' => 'Mdl_Import',
        'currentClassName' => 'Mdl_Import',
        'aliasName' => NULL,
      ),
      'delete' => 
      array (
        'name' => 'delete',
        'parameters' => 
        array (
          'import_id' => 
          array (
            'name' => 'import_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 424,
            'endLine' => 424,
            'startColumn' => 28,
            'endColumn' => 37,
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
 * @param int $import_id
 */',
        'startLine' => 424,
        'endLine' => 459,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Import',
        'implementingClassName' => 'Mdl_Import',
        'currentClassName' => 'Mdl_Import',
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