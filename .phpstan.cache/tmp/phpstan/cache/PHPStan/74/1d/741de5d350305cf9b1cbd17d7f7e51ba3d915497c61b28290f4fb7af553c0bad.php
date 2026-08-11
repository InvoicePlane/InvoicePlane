<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/application/modules/guest/controllers/View.php-PHPStan\BetterReflection\Reflection\ReflectionClass-View
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-c02ea56bbd858d81d31362d1da76879b6653d4f94cab6c6af1092ab2edff2014',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'View',
        'filename' => '/var/www/projects/exprmt/application/modules/guest/controllers/View.php',
      ),
    ),
    'namespace' => NULL,
    'name' => 'View',
    'shortName' => 'View',
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
    'endLine' => 420,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Base_Controller',
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
 * Constructor - load file security helper for validation.
 */',
        'startLine' => 22,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'View',
        'implementingClassName' => 'View',
        'currentClassName' => 'View',
        'aliasName' => NULL,
      ),
      'invoice' => 
      array (
        'name' => 'invoice',
        'parameters' => 
        array (
          'invoice_url_key' => 
          array (
            'name' => 'invoice_url_key',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 31,
                'endLine' => 31,
                'startTokenPos' => 83,
                'startFilePos' => 680,
                'endTokenPos' => 83,
                'endFilePos' => 681,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 31,
            'endLine' => 31,
            'startColumn' => 29,
            'endColumn' => 49,
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
 * @param $invoice_url_key
 */',
        'startLine' => 31,
        'endLine' => 104,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'View',
        'implementingClassName' => 'View',
        'currentClassName' => 'View',
        'aliasName' => NULL,
      ),
      'generate_invoice_pdf' => 
      array (
        'name' => 'generate_invoice_pdf',
        'parameters' => 
        array (
          'invoice_url_key' => 
          array (
            'name' => 'invoice_url_key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 110,
            'endLine' => 110,
            'startColumn' => 42,
            'endColumn' => 57,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'stream' => 
          array (
            'name' => 'stream',
            'default' => 
            array (
              'code' => '\\true',
              'attributes' => 
              array (
                'startLine' => 110,
                'endLine' => 110,
                'startTokenPos' => 653,
                'startFilePos' => 3926,
                'endTokenPos' => 653,
                'endFilePos' => 3929,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 110,
            'endLine' => 110,
            'startColumn' => 60,
            'endColumn' => 73,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'invoice_template' => 
          array (
            'name' => 'invoice_template',
            'default' => 
            array (
              'code' => '\\null',
              'attributes' => 
              array (
                'startLine' => 110,
                'endLine' => 110,
                'startTokenPos' => 660,
                'startFilePos' => 3952,
                'endTokenPos' => 660,
                'endFilePos' => 3955,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 110,
            'endLine' => 110,
            'startColumn' => 76,
            'endColumn' => 99,
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
 * @param      $invoice_url_key
 * @param bool $stream
 */',
        'startLine' => 110,
        'endLine' => 131,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'View',
        'implementingClassName' => 'View',
        'currentClassName' => 'View',
        'aliasName' => NULL,
      ),
      'generate_sumex_pdf' => 
      array (
        'name' => 'generate_sumex_pdf',
        'parameters' => 
        array (
          'invoice_url_key' => 
          array (
            'name' => 'invoice_url_key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 137,
            'endLine' => 137,
            'startColumn' => 40,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'stream' => 
          array (
            'name' => 'stream',
            'default' => 
            array (
              'code' => '\\true',
              'attributes' => 
              array (
                'startLine' => 137,
                'endLine' => 137,
                'startTokenPos' => 824,
                'startFilePos' => 4844,
                'endTokenPos' => 824,
                'endFilePos' => 4847,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 137,
            'endLine' => 137,
            'startColumn' => 58,
            'endColumn' => 71,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'invoice_template' => 
          array (
            'name' => 'invoice_template',
            'default' => 
            array (
              'code' => '\\null',
              'attributes' => 
              array (
                'startLine' => 137,
                'endLine' => 137,
                'startTokenPos' => 831,
                'startFilePos' => 4870,
                'endTokenPos' => 831,
                'endFilePos' => 4873,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 137,
            'endLine' => 137,
            'startColumn' => 74,
            'endColumn' => 97,
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
 * @param      $invoice_url_key
 * @param bool $stream
 */',
        'startLine' => 137,
        'endLine' => 159,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'View',
        'implementingClassName' => 'View',
        'currentClassName' => 'View',
        'aliasName' => NULL,
      ),
      'quote' => 
      array (
        'name' => 'quote',
        'parameters' => 
        array (
          'quote_url_key' => 
          array (
            'name' => 'quote_url_key',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 164,
                'endLine' => 164,
                'startTokenPos' => 992,
                'startFilePos' => 5671,
                'endTokenPos' => 992,
                'endFilePos' => 5672,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 164,
            'endLine' => 164,
            'startColumn' => 27,
            'endColumn' => 45,
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
 * @param $quote_url_key
 */',
        'startLine' => 164,
        'endLine' => 222,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'View',
        'implementingClassName' => 'View',
        'currentClassName' => 'View',
        'aliasName' => NULL,
      ),
      'generate_quote_pdf' => 
      array (
        'name' => 'generate_quote_pdf',
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
            'startLine' => 228,
            'endLine' => 228,
            'startColumn' => 40,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'stream' => 
          array (
            'name' => 'stream',
            'default' => 
            array (
              'code' => '\\true',
              'attributes' => 
              array (
                'startLine' => 228,
                'endLine' => 228,
                'startTokenPos' => 1496,
                'startFilePos' => 8404,
                'endTokenPos' => 1496,
                'endFilePos' => 8407,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 228,
            'endLine' => 228,
            'startColumn' => 56,
            'endColumn' => 69,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'quote_template' => 
          array (
            'name' => 'quote_template',
            'default' => 
            array (
              'code' => '\\null',
              'attributes' => 
              array (
                'startLine' => 228,
                'endLine' => 228,
                'startTokenPos' => 1503,
                'startFilePos' => 8428,
                'endTokenPos' => 1503,
                'endFilePos' => 8431,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 228,
            'endLine' => 228,
            'startColumn' => 72,
            'endColumn' => 93,
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
 * @param      $quote_url_key
 * @param bool $stream
 */',
        'startLine' => 228,
        'endLine' => 245,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'View',
        'implementingClassName' => 'View',
        'currentClassName' => 'View',
        'aliasName' => NULL,
      ),
      'approve_quote' => 
      array (
        'name' => 'approve_quote',
        'parameters' => 
        array (
          'quote_url_key' => 
          array (
            'name' => 'quote_url_key',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 250,
            'endLine' => 250,
            'startColumn' => 35,
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
        'startLine' => 250,
        'endLine' => 270,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'View',
        'implementingClassName' => 'View',
        'currentClassName' => 'View',
        'aliasName' => NULL,
      ),
      'reject_quote' => 
      array (
        'name' => 'reject_quote',
        'parameters' => 
        array (
          'quote_url_key' => 
          array (
            'name' => 'quote_url_key',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 275,
            'endLine' => 275,
            'startColumn' => 34,
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
        'startLine' => 275,
        'endLine' => 295,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'View',
        'implementingClassName' => 'View',
        'currentClassName' => 'View',
        'aliasName' => NULL,
      ),
      'validate_guest_quote_access' => 
      array (
        'name' => 'validate_guest_quote_access',
        'parameters' => 
        array (
          'quote_url_key' => 
          array (
            'name' => 'quote_url_key',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 305,
            'endLine' => 305,
            'startColumn' => 50,
            'endColumn' => 70,
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
            'name' => 'object',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Validate guest user has access to a quote by URL key
 * Returns the quote object if valid, or shows error/404.
 *
 * @param string $quote_url_key The quote URL key
 *
 * @return object The quote object
 */',
        'startLine' => 305,
        'endLine' => 342,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'View',
        'implementingClassName' => 'View',
        'currentClassName' => 'View',
        'aliasName' => NULL,
      ),
      'get_attachments' => 
      array (
        'name' => 'get_attachments',
        'parameters' => 
        array (
          'url_key' => 
          array (
            'name' => 'url_key',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 349,
            'endLine' => 349,
            'startColumn' => 38,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Retail since 1.6.3.
 *
 * @param $url_key
 */',
        'startLine' => 349,
        'endLine' => 399,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'View',
        'implementingClassName' => 'View',
        'currentClassName' => 'View',
        'aliasName' => NULL,
      ),
      'has_discounts' => 
      array (
        'name' => 'has_discounts',
        'parameters' => 
        array (
          'items' => 
          array (
            'name' => 'items',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 410,
            'endLine' => 410,
            'startColumn' => 36,
            'endColumn' => 47,
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
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if in the array of items
 * that are provided, one or more items
 * have a discount.
 *
 * @param array $items
 *
 * @return bool
 */',
        'startLine' => 410,
        'endLine' => 419,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'View',
        'implementingClassName' => 'View',
        'currentClassName' => 'View',
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