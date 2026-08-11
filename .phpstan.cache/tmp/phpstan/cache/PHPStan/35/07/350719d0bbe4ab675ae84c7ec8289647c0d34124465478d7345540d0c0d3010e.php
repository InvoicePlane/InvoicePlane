<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/application/modules/quotes/models/Mdl_quote_item_amounts.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Mdl_Quote_Item_Amounts
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-a9a818c0f4e5a1427bd78123623535a288b299975cb67255341342bf7f261321',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Mdl_Quote_Item_Amounts',
        'filename' => '/var/www/projects/exprmt/application/modules/quotes/models/Mdl_quote_item_amounts.php',
      ),
    ),
    'namespace' => NULL,
    'name' => 'Mdl_Quote_Item_Amounts',
    'shortName' => 'Mdl_Quote_Item_Amounts',
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
    'endLine' => 73,
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
    ),
    'immediateMethods' => 
    array (
      'calculate' => 
      array (
        'name' => 'calculate',
        'parameters' => 
        array (
          'item_id' => 
          array (
            'name' => 'item_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 29,
            'endLine' => 29,
            'startColumn' => 31,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'global_discount' => 
          array (
            'name' => 'global_discount',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => true,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 29,
            'endLine' => 29,
            'startColumn' => 41,
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
 * item_amount_id
 * item_id
 * item_subtotal (item_quantity * item_price)
 * item_tax_total
 * item_total ((item_quantity * item_price) + item_tax_total).
 *
 * @param $item_id
 * @param $global_discount
 */',
        'startLine' => 29,
        'endLine' => 72,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Quote_Item_Amounts',
        'implementingClassName' => 'Mdl_Quote_Item_Amounts',
        'currentClassName' => 'Mdl_Quote_Item_Amounts',
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