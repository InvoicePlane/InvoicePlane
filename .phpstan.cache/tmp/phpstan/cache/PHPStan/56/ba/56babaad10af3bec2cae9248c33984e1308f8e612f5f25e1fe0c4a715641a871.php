<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/application/modules/quotes/models/Mdl_quote_amounts.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Mdl_Quote_Amounts
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-0726e6079a5002f86d081491d66d13961496dbf5918afd7cf9eca2c79cede384',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Mdl_Quote_Amounts',
        'filename' => '/var/www/projects/exprmt/application/modules/quotes/models/Mdl_quote_amounts.php',
      ),
    ),
    'namespace' => NULL,
    'name' => 'Mdl_Quote_Amounts',
    'shortName' => 'Mdl_Quote_Amounts',
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
    'endLine' => 343,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'CI_Model',
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
      'decimal_places' => 
      array (
        'declaringClassName' => 'Mdl_Quote_Amounts',
        'implementingClassName' => 'Mdl_Quote_Amounts',
        'name' => 'decimal_places',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '2',
          'attributes' => 
          array (
            'startLine' => 22,
            'endLine' => 22,
            'startTokenPos' => 48,
            'startFilePos' => 450,
            'endTokenPos' => 48,
            'endFilePos' => 450,
          ),
        ),
        'docComment' => '/**
 * @var int
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 31,
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
        'docComment' => NULL,
        'startLine' => 24,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quote_Amounts',
        'implementingClassName' => 'Mdl_Quote_Amounts',
        'currentClassName' => 'Mdl_Quote_Amounts',
        'aliasName' => NULL,
      ),
      'calculate' => 
      array (
        'name' => 'calculate',
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
            'startLine' => 49,
            'endLine' => 49,
            'startColumn' => 31,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'global_discount' => 
          array (
            'name' => 'global_discount',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 49,
            'endLine' => 49,
            'startColumn' => 42,
            'endColumn' => 57,
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
 * IP_QUOTE_AMOUNTS
 * quote_amount_id
 * quote_id
 * quote_item_subtotal      SUM(item_subtotal)
 * quote_item_tax_total     SUM(item_tax_total)
 * quote_tax_total
 * quote_total              quote_item_subtotal + quote_item_tax_total + quote_tax_total.
 *
 * IP_QUOTE_ITEM_AMOUNTS
 * item_amount_id
 * item_id
 * item_tax_rate_id
 * item_subtotal             item_quantity * item_price
 * item_tax_total            item_subtotal * tax_rate_percent
 * item_total                item_subtotal + item_tax_total
 *
 * @param $quote_id
 * @param $global_discount
 */',
        'startLine' => 49,
        'endLine' => 94,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quote_Amounts',
        'implementingClassName' => 'Mdl_Quote_Amounts',
        'currentClassName' => 'Mdl_Quote_Amounts',
        'aliasName' => NULL,
      ),
      'calculate_discount' => 
      array (
        'name' => 'calculate_discount',
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
            'startLine' => 102,
            'endLine' => 102,
            'startColumn' => 40,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'quote_total' => 
          array (
            'name' => 'quote_total',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 102,
            'endLine' => 102,
            'startColumn' => 51,
            'endColumn' => 62,
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
 * @param $quote_id
 * @param $quote_total
 *
 * @return float
 */',
        'startLine' => 102,
        'endLine' => 115,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quote_Amounts',
        'implementingClassName' => 'Mdl_Quote_Amounts',
        'currentClassName' => 'Mdl_Quote_Amounts',
        'aliasName' => NULL,
      ),
      'get_global_discount' => 
      array (
        'name' => 'get_global_discount',
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
            'startLine' => 124,
            'endLine' => 124,
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
 * legacy_calculation is false: Need global_discount to recalculate quote amounts - since v1.6.3.
 *
 * @param $quote_id
 *
 * return global_discount
 */',
        'startLine' => 124,
        'endLine' => 135,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quote_Amounts',
        'implementingClassName' => 'Mdl_Quote_Amounts',
        'currentClassName' => 'Mdl_Quote_Amounts',
        'aliasName' => NULL,
      ),
      'calculate_quote_taxes' => 
      array (
        'name' => 'calculate_quote_taxes',
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
            'startLine' => 140,
            'endLine' => 140,
            'startColumn' => 43,
            'endColumn' => 51,
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
        'startLine' => 140,
        'endLine' => 208,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quote_Amounts',
        'implementingClassName' => 'Mdl_Quote_Amounts',
        'currentClassName' => 'Mdl_Quote_Amounts',
        'aliasName' => NULL,
      ),
      'get_total_quoted' => 
      array (
        'name' => 'get_total_quoted',
        'parameters' => 
        array (
          'period' => 
          array (
            'name' => 'period',
            'default' => 
            array (
              'code' => '\\null',
              'attributes' => 
              array (
                'startLine' => 213,
                'endLine' => 213,
                'startTokenPos' => 1056,
                'startFilePos' => 8382,
                'endTokenPos' => 1056,
                'endFilePos' => 8385,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 213,
            'endLine' => 213,
            'startColumn' => 38,
            'endColumn' => 51,
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
 * @return mixed
 */',
        'startLine' => 213,
        'endLine' => 247,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quote_Amounts',
        'implementingClassName' => 'Mdl_Quote_Amounts',
        'currentClassName' => 'Mdl_Quote_Amounts',
        'aliasName' => NULL,
      ),
      'get_status_totals' => 
      array (
        'name' => 'get_status_totals',
        'parameters' => 
        array (
          'period' => 
          array (
            'name' => 'period',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 254,
                'endLine' => 254,
                'startTokenPos' => 1198,
                'startFilePos' => 10261,
                'endTokenPos' => 1198,
                'endFilePos' => 10262,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 254,
            'endLine' => 254,
            'startColumn' => 39,
            'endColumn' => 50,
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
 * @param string $period
 *
 * @return array
 */',
        'startLine' => 254,
        'endLine' => 342,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quote_Amounts',
        'implementingClassName' => 'Mdl_Quote_Amounts',
        'currentClassName' => 'Mdl_Quote_Amounts',
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