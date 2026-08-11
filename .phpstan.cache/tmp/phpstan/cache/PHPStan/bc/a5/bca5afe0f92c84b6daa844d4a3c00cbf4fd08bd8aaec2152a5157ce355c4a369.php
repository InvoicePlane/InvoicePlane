<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/application/modules/invoices/models/Mdl_invoice_amounts.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Mdl_Invoice_Amounts
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-4312a098909a2984e4184273d4c875bb3d69e1e7281547478f7d42f8ecf94442',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Mdl_Invoice_Amounts',
        'filename' => '/var/www/projects/exprmt/application/modules/invoices/models/Mdl_invoice_amounts.php',
      ),
    ),
    'namespace' => NULL,
    'name' => 'Mdl_Invoice_Amounts',
    'shortName' => 'Mdl_Invoice_Amounts',
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
    'endLine' => 443,
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
        'declaringClassName' => 'Mdl_Invoice_Amounts',
        'implementingClassName' => 'Mdl_Invoice_Amounts',
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
            'startFilePos' => 452,
            'endTokenPos' => 48,
            'endFilePos' => 452,
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
        'declaringClassName' => 'Mdl_Invoice_Amounts',
        'implementingClassName' => 'Mdl_Invoice_Amounts',
        'currentClassName' => 'Mdl_Invoice_Amounts',
        'aliasName' => NULL,
      ),
      'calculate' => 
      array (
        'name' => 'calculate',
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
            'startLine' => 51,
            'endLine' => 51,
            'startColumn' => 31,
            'endColumn' => 41,
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
            'startLine' => 51,
            'endLine' => 51,
            'startColumn' => 44,
            'endColumn' => 59,
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
 * IP_INVOICE_AMOUNTS
 * invoice_amount_id
 * invoice_id
 * invoice_item_subtotal    SUM(item_subtotal)
 * invoice_item_tax_total   SUM(item_tax_total)
 * invoice_tax_total
 * invoice_total            invoice_item_subtotal + invoice_item_tax_total + invoice_tax_total
 * invoice_paid
 * invoice_balance          invoice_total - invoice_paid.
 *
 * IP_INVOICE_ITEM_AMOUNTS
 * item_amount_id
 * item_id
 * item_tax_rate_id
 * item_subtotal            item_quantity * item_price
 * item_tax_total           item_subtotal * tax_rate_percent
 * item_total               item_subtotal + item_tax_total
 *
 * @param $invoice_id
 * @param $global_discount
 */',
        'startLine' => 51,
        'endLine' => 134,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Invoice_Amounts',
        'implementingClassName' => 'Mdl_Invoice_Amounts',
        'currentClassName' => 'Mdl_Invoice_Amounts',
        'aliasName' => NULL,
      ),
      'calculate_discount' => 
      array (
        'name' => 'calculate_discount',
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
            'startLine' => 142,
            'endLine' => 142,
            'startColumn' => 40,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'invoice_total' => 
          array (
            'name' => 'invoice_total',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 142,
            'endLine' => 142,
            'startColumn' => 53,
            'endColumn' => 66,
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
 * @param $invoice_id
 * @param $invoice_total
 *
 * @return float
 */',
        'startLine' => 142,
        'endLine' => 154,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Invoice_Amounts',
        'implementingClassName' => 'Mdl_Invoice_Amounts',
        'currentClassName' => 'Mdl_Invoice_Amounts',
        'aliasName' => NULL,
      ),
      'get_global_discount' => 
      array (
        'name' => 'get_global_discount',
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
            'startLine' => 163,
            'endLine' => 163,
            'startColumn' => 41,
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
 * legacy_calculation false: Need global_discount to recalculate invoice amounts - since v1.6.3.
 *
 * @param $invoice_id
 *
 * return global_discount
 */',
        'startLine' => 163,
        'endLine' => 174,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Invoice_Amounts',
        'implementingClassName' => 'Mdl_Invoice_Amounts',
        'currentClassName' => 'Mdl_Invoice_Amounts',
        'aliasName' => NULL,
      ),
      'calculate_invoice_taxes' => 
      array (
        'name' => 'calculate_invoice_taxes',
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
            'startLine' => 179,
            'endLine' => 179,
            'startColumn' => 45,
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
 * @param $invoice_id
 */',
        'startLine' => 179,
        'endLine' => 249,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Invoice_Amounts',
        'implementingClassName' => 'Mdl_Invoice_Amounts',
        'currentClassName' => 'Mdl_Invoice_Amounts',
        'aliasName' => NULL,
      ),
      'get_total_invoiced' => 
      array (
        'name' => 'get_total_invoiced',
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
                'startLine' => 254,
                'endLine' => 254,
                'startTokenPos' => 1402,
                'startFilePos' => 10547,
                'endTokenPos' => 1402,
                'endFilePos' => 10550,
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
            'startColumn' => 40,
            'endColumn' => 53,
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
        'startLine' => 254,
        'endLine' => 288,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Invoice_Amounts',
        'implementingClassName' => 'Mdl_Invoice_Amounts',
        'currentClassName' => 'Mdl_Invoice_Amounts',
        'aliasName' => NULL,
      ),
      'get_total_paid' => 
      array (
        'name' => 'get_total_paid',
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
                'startLine' => 293,
                'endLine' => 293,
                'startTokenPos' => 1544,
                'startFilePos' => 12463,
                'endTokenPos' => 1544,
                'endFilePos' => 12466,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 293,
            'endLine' => 293,
            'startColumn' => 36,
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
 * @return mixed
 */',
        'startLine' => 293,
        'endLine' => 324,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Invoice_Amounts',
        'implementingClassName' => 'Mdl_Invoice_Amounts',
        'currentClassName' => 'Mdl_Invoice_Amounts',
        'aliasName' => NULL,
      ),
      'get_total_balance' => 
      array (
        'name' => 'get_total_balance',
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
                'startLine' => 329,
                'endLine' => 329,
                'startTokenPos' => 1686,
                'startFilePos' => 14274,
                'endTokenPos' => 1686,
                'endFilePos' => 14277,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 329,
            'endLine' => 329,
            'startColumn' => 39,
            'endColumn' => 52,
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
        'startLine' => 329,
        'endLine' => 359,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Invoice_Amounts',
        'implementingClassName' => 'Mdl_Invoice_Amounts',
        'currentClassName' => 'Mdl_Invoice_Amounts',
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
                'startLine' => 366,
                'endLine' => 366,
                'startTokenPos' => 1828,
                'startFilePos' => 16147,
                'endTokenPos' => 1828,
                'endFilePos' => 16148,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 366,
            'endLine' => 366,
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
        'startLine' => 366,
        'endLine' => 442,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Invoice_Amounts',
        'implementingClassName' => 'Mdl_Invoice_Amounts',
        'currentClassName' => 'Mdl_Invoice_Amounts',
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