<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/application/modules/invoices/models/Mdl_invoices_recurring.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Mdl_Invoices_Recurring
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-3da532b297fe1bee731c8b76943b72085fd9cea85c8da1b3ce177911b30c4027',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Mdl_Invoices_Recurring',
        'filename' => '/var/www/projects/exprmt/application/modules/invoices/models/Mdl_invoices_recurring.php',
      ),
    ),
    'namespace' => NULL,
    'name' => 'Mdl_Invoices_Recurring',
    'shortName' => 'Mdl_Invoices_Recurring',
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
    'endLine' => 159,
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
        'declaringClassName' => 'Mdl_Invoices_Recurring',
        'implementingClassName' => 'Mdl_Invoices_Recurring',
        'name' => 'table',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'ip_invoices_recurring\'',
          'attributes' => 
          array (
            'startLine' => 19,
            'endLine' => 19,
            'startTokenPos' => 46,
            'startFilePos' => 420,
            'endTokenPos' => 46,
            'endFilePos' => 442,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 5,
        'endColumn' => 44,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'primary_key' => 
      array (
        'declaringClassName' => 'Mdl_Invoices_Recurring',
        'implementingClassName' => 'Mdl_Invoices_Recurring',
        'name' => 'primary_key',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'ip_invoices_recurring.invoice_recurring_id\'',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 21,
            'startTokenPos' => 55,
            'startFilePos' => 472,
            'endTokenPos' => 55,
            'endFilePos' => 515,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 71,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'recur_frequencies' => 
      array (
        'declaringClassName' => 'Mdl_Invoices_Recurring',
        'implementingClassName' => 'Mdl_Invoices_Recurring',
        'name' => 'recur_frequencies',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'1D\' => \'calendar_day_1\', \'2D\' => \'calendar_day_2\', \'3D\' => \'calendar_day_3\', \'4D\' => \'calendar_day_4\', \'5D\' => \'calendar_day_5\', \'6D\' => \'calendar_day_6\', \'15D\' => \'calendar_day_15\', \'30D\' => \'calendar_day_30\', \'7D\' => \'calendar_week_1\', \'14D\' => \'calendar_week_2\', \'21D\' => \'calendar_week_3\', \'28D\' => \'calendar_week_4\', \'1M\' => \'calendar_month_1\', \'2M\' => \'calendar_month_2\', \'3M\' => \'calendar_month_3\', \'4M\' => \'calendar_month_4\', \'5M\' => \'calendar_month_5\', \'6M\' => \'calendar_month_6\', \'7M\' => \'calendar_month_7\', \'8M\' => \'calendar_month_8\', \'9M\' => \'calendar_month_9\', \'10M\' => \'calendar_month_10\', \'11M\' => \'calendar_month_11\', \'1Y\' => \'calendar_year_1\', \'2Y\' => \'calendar_year_2\', \'3Y\' => \'calendar_year_3\', \'4Y\' => \'calendar_year_4\', \'5Y\' => \'calendar_year_5\']',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 52,
            'startTokenPos' => 64,
            'startFilePos' => 551,
            'endTokenPos' => 262,
            'endFilePos' => 1572,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 52,
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
        'startLine' => 54,
        'endLine' => 62,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Invoices_Recurring',
        'implementingClassName' => 'Mdl_Invoices_Recurring',
        'currentClassName' => 'Mdl_Invoices_Recurring',
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
        'startLine' => 64,
        'endLine' => 67,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Invoices_Recurring',
        'implementingClassName' => 'Mdl_Invoices_Recurring',
        'currentClassName' => 'Mdl_Invoices_Recurring',
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
        'startLine' => 69,
        'endLine' => 73,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Invoices_Recurring',
        'implementingClassName' => 'Mdl_Invoices_Recurring',
        'currentClassName' => 'Mdl_Invoices_Recurring',
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
        'startLine' => 78,
        'endLine' => 100,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Invoices_Recurring',
        'implementingClassName' => 'Mdl_Invoices_Recurring',
        'currentClassName' => 'Mdl_Invoices_Recurring',
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
        'startLine' => 105,
        'endLine' => 115,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Invoices_Recurring',
        'implementingClassName' => 'Mdl_Invoices_Recurring',
        'currentClassName' => 'Mdl_Invoices_Recurring',
        'aliasName' => NULL,
      ),
      'stop' => 
      array (
        'name' => 'stop',
        'parameters' => 
        array (
          'invoice_recurring_id' => 
          array (
            'name' => 'invoice_recurring_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 120,
            'endLine' => 120,
            'startColumn' => 26,
            'endColumn' => 46,
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
 * @param $invoice_recurring_id
 */',
        'startLine' => 120,
        'endLine' => 129,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Invoices_Recurring',
        'implementingClassName' => 'Mdl_Invoices_Recurring',
        'currentClassName' => 'Mdl_Invoices_Recurring',
        'aliasName' => NULL,
      ),
      'active' => 
      array (
        'name' => 'active',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Sets filter to only recurring invoices which should be generated now.
 *
 * @return \\Mdl_Invoices_Recurring
 */',
        'startLine' => 136,
        'endLine' => 141,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Invoices_Recurring',
        'implementingClassName' => 'Mdl_Invoices_Recurring',
        'currentClassName' => 'Mdl_Invoices_Recurring',
        'aliasName' => NULL,
      ),
      'set_next_recur_date' => 
      array (
        'name' => 'set_next_recur_date',
        'parameters' => 
        array (
          'invoice_recurring_id' => 
          array (
            'name' => 'invoice_recurring_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 146,
            'endLine' => 146,
            'startColumn' => 41,
            'endColumn' => 61,
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
 * @param $invoice_recurring_id
 */',
        'startLine' => 146,
        'endLine' => 158,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Invoices_Recurring',
        'implementingClassName' => 'Mdl_Invoices_Recurring',
        'currentClassName' => 'Mdl_Invoices_Recurring',
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