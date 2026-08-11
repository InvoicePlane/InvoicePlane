<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/application/modules/quotes/models/Mdl_quotes.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Mdl_Quotes
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-eb0928c0c43a9eecc51fa7fa2f9f0927eaf6974a500e952e30a5b88f22ef537b',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Mdl_Quotes',
        'filename' => '/var/www/projects/exprmt/application/modules/quotes/models/Mdl_quotes.php',
      ),
    ),
    'namespace' => NULL,
    'name' => 'Mdl_Quotes',
    'shortName' => 'Mdl_Quotes',
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
    'endLine' => 639,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Response_Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Password_Encryption_Trait',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'name' => 'table',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'ip_quotes\'',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 21,
            'startTokenPos' => 51,
            'startFilePos' => 444,
            'endTokenPos' => 51,
            'endFilePos' => 454,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 32,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'primary_key' => 
      array (
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'name' => 'primary_key',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'ip_quotes.quote_id\'',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 23,
            'startTokenPos' => 60,
            'startFilePos' => 484,
            'endTokenPos' => 60,
            'endFilePos' => 503,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 47,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'date_modified_field' => 
      array (
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'name' => 'date_modified_field',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'quote_date_modified\'',
          'attributes' => 
          array (
            'startLine' => 25,
            'endLine' => 25,
            'startTokenPos' => 69,
            'startFilePos' => 541,
            'endTokenPos' => 69,
            'endFilePos' => 561,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 25,
        'endLine' => 25,
        'startColumn' => 5,
        'endColumn' => 56,
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
      'statuses' => 
      array (
        'name' => 'statuses',
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
        'startLine' => 30,
        'endLine' => 64,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
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
        'startLine' => 66,
        'endLine' => 107,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
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
                'startLine' => 109,
                'endLine' => 109,
                'startTokenPos' => 326,
                'startFilePos' => 3318,
                'endTokenPos' => 326,
                'endFilePos' => 3321,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 109,
            'endLine' => 109,
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
                'startLine' => 109,
                'endLine' => 109,
                'startTokenPos' => 333,
                'startFilePos' => 3336,
                'endTokenPos' => 333,
                'endFilePos' => 3339,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 109,
            'endLine' => 109,
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
        'docComment' => NULL,
        'startLine' => 109,
        'endLine' => 120,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'encrypt_quote_password' => 
      array (
        'name' => 'encrypt_quote_password',
        'parameters' => 
        array (
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
            'startLine' => 122,
            'endLine' => 122,
            'startColumn' => 44,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'string',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 122,
        'endLine' => 125,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'decrypt_quote_password' => 
      array (
        'name' => 'decrypt_quote_password',
        'parameters' => 
        array (
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
            'startLine' => 127,
            'endLine' => 127,
            'startColumn' => 44,
            'endColumn' => 52,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 127,
        'endLine' => 130,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
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
        'startLine' => 132,
        'endLine' => 135,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'default_join' => 
      array (
        'name' => 'default_join',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 137,
        'endLine' => 143,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
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
        'startLine' => 148,
        'endLine' => 176,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'validation_rules_save_quote' => 
      array (
        'name' => 'validation_rules_save_quote',
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
        'startLine' => 181,
        'endLine' => 204,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'create' => 
      array (
        'name' => 'create',
        'parameters' => 
        array (
          'db_array' => 
          array (
            'name' => 'db_array',
            'default' => 
            array (
              'code' => '\\null',
              'attributes' => 
              array (
                'startLine' => 209,
                'endLine' => 209,
                'startTokenPos' => 926,
                'startFilePos' => 6476,
                'endTokenPos' => 926,
                'endFilePos' => 6479,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 209,
            'endLine' => 209,
            'startColumn' => 28,
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
 * @return int|null
 */',
        'startLine' => 209,
        'endLine' => 241,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'copy_quote' => 
      array (
        'name' => 'copy_quote',
        'parameters' => 
        array (
          'source_id' => 
          array (
            'name' => 'source_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 249,
            'endLine' => 249,
            'startColumn' => 32,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'target_id' => 
          array (
            'name' => 'target_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 249,
            'endLine' => 249,
            'startColumn' => 44,
            'endColumn' => 53,
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
 * Copies quote items, tax rates, etc from source to target.
 *
 * @param int $source_id
 * @param int $target_id
 */',
        'startLine' => 249,
        'endLine' => 311,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
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
        'startLine' => 316,
        'endLine' => 348,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'get_date_due' => 
      array (
        'name' => 'get_date_due',
        'parameters' => 
        array (
          'quote_date_created' => 
          array (
            'name' => 'quote_date_created',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 353,
            'endLine' => 353,
            'startColumn' => 34,
            'endColumn' => 52,
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
 * @param string $quote_date_created
 */',
        'startLine' => 353,
        'endLine' => 359,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'get_quote_number' => 
      array (
        'name' => 'get_quote_number',
        'parameters' => 
        array (
          'invoice_group_id' => 
          array (
            'name' => 'invoice_group_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 366,
            'endLine' => 366,
            'startColumn' => 38,
            'endColumn' => 54,
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
 * @param $invoice_group_id
 *
 * @return mixed
 */',
        'startLine' => 366,
        'endLine' => 371,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'get_url_key' => 
      array (
        'name' => 'get_url_key',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return string
 */',
        'startLine' => 376,
        'endLine' => 382,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'get_invoice_group_id' => 
      array (
        'name' => 'get_invoice_group_id',
        'parameters' => 
        array (
          'invoice_id' => 
          array (
            'name' => 'invoice_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 389,
            'endLine' => 389,
            'startColumn' => 42,
            'endColumn' => 52,
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
 * @param $invoice_id
 *
 * @return mixed
 */',
        'startLine' => 389,
        'endLine' => 394,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'delete' => 
      array (
        'name' => 'delete',
        'parameters' => 
        array (
          'quote_id' => 
          array (
            'name' => 'quote_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 399,
            'endLine' => 399,
            'startColumn' => 28,
            'endColumn' => 36,
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
 * @param int $quote_id
 */',
        'startLine' => 399,
        'endLine' => 405,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'is_draft' => 
      array (
        'name' => 'is_draft',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return $this
 */',
        'startLine' => 410,
        'endLine' => 415,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'is_sent' => 
      array (
        'name' => 'is_sent',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return $this
 */',
        'startLine' => 420,
        'endLine' => 425,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'is_viewed' => 
      array (
        'name' => 'is_viewed',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return $this
 */',
        'startLine' => 430,
        'endLine' => 435,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'is_approved' => 
      array (
        'name' => 'is_approved',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return $this
 */',
        'startLine' => 440,
        'endLine' => 445,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'is_rejected' => 
      array (
        'name' => 'is_rejected',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return $this
 */',
        'startLine' => 450,
        'endLine' => 455,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'is_canceled' => 
      array (
        'name' => 'is_canceled',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return $this
 */',
        'startLine' => 460,
        'endLine' => 465,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'is_open' => 
      array (
        'name' => 'is_open',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Used by guest module; includes only sent and viewed.
 *
 * @return $this
 */',
        'startLine' => 472,
        'endLine' => 477,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'guest_visible' => 
      array (
        'name' => 'guest_visible',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return $this
 */',
        'startLine' => 482,
        'endLine' => 487,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'by_client' => 
      array (
        'name' => 'by_client',
        'parameters' => 
        array (
          'client_id' => 
          array (
            'name' => 'client_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 494,
            'endLine' => 494,
            'startColumn' => 31,
            'endColumn' => 40,
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
 * @param $client_id
 *
 * @return $this
 */',
        'startLine' => 494,
        'endLine' => 499,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'approve_quote_by_key' => 
      array (
        'name' => 'approve_quote_by_key',
        'parameters' => 
        array (
          'quote_url_key' => 
          array (
            'name' => 'quote_url_key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 504,
            'endLine' => 504,
            'startColumn' => 42,
            'endColumn' => 55,
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
 * @param $quote_url_key
 */',
        'startLine' => 504,
        'endLine' => 510,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'reject_quote_by_key' => 
      array (
        'name' => 'reject_quote_by_key',
        'parameters' => 
        array (
          'quote_url_key' => 
          array (
            'name' => 'quote_url_key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 515,
            'endLine' => 515,
            'startColumn' => 41,
            'endColumn' => 54,
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
 * @param $quote_url_key
 */',
        'startLine' => 515,
        'endLine' => 521,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'approve_quote_by_id' => 
      array (
        'name' => 'approve_quote_by_id',
        'parameters' => 
        array (
          'quote_id' => 
          array (
            'name' => 'quote_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 526,
            'endLine' => 526,
            'startColumn' => 41,
            'endColumn' => 49,
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
 * @param $quote_id
 */',
        'startLine' => 526,
        'endLine' => 532,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'reject_quote_by_id' => 
      array (
        'name' => 'reject_quote_by_id',
        'parameters' => 
        array (
          'quote_id' => 
          array (
            'name' => 'quote_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 537,
            'endLine' => 537,
            'startColumn' => 40,
            'endColumn' => 48,
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
 * @param $quote_id
 */',
        'startLine' => 537,
        'endLine' => 543,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'mark_viewed' => 
      array (
        'name' => 'mark_viewed',
        'parameters' => 
        array (
          'quote_id' => 
          array (
            'name' => 'quote_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 548,
            'endLine' => 548,
            'startColumn' => 33,
            'endColumn' => 41,
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
 * @param $quote_id
 */',
        'startLine' => 548,
        'endLine' => 560,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'mark_sent' => 
      array (
        'name' => 'mark_sent',
        'parameters' => 
        array (
          'quote_id' => 
          array (
            'name' => 'quote_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 565,
            'endLine' => 565,
            'startColumn' => 31,
            'endColumn' => 39,
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
 * @param $quote_id
 */',
        'startLine' => 565,
        'endLine' => 577,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'generate_quote_number_if_applicable' => 
      array (
        'name' => 'generate_quote_number_if_applicable',
        'parameters' => 
        array (
          'quote_id' => 
          array (
            'name' => 'quote_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 582,
            'endLine' => 582,
            'startColumn' => 57,
            'endColumn' => 65,
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
 * @param $quote_id
 */',
        'startLine' => 582,
        'endLine' => 594,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
        'aliasName' => NULL,
      ),
      'can_user_access' => 
      array (
        'name' => 'can_user_access',
        'parameters' => 
        array (
          'quote_id' => 
          array (
            'name' => 'quote_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 605,
            'endLine' => 605,
            'startColumn' => 37,
            'endColumn' => 45,
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
 * Check if the current user has access to this quote.
 *
 * Security: Prevents IDOR vulnerabilities for quote access.
 *
 * @param int $quote_id The quote ID to check
 *
 * @return bool True if user has access, false otherwise
 */',
        'startLine' => 605,
        'endLine' => 638,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quotes',
        'implementingClassName' => 'Mdl_Quotes',
        'currentClassName' => 'Mdl_Quotes',
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