<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/application/modules/guest/controllers/gateways/Paypal.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Paypal
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-b1084c9cada750ded75413705600b85ecc359373c3fb7405307fba791e4d0884',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Paypal',
        'filename' => '/var/www/projects/exprmt/application/modules/guest/controllers/gateways/Paypal.php',
      ),
    ),
    'namespace' => NULL,
    'name' => 'Paypal',
    'shortName' => 'Paypal',
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
    'endLine' => 326,
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
        'docComment' => NULL,
        'startLine' => 19,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Paypal',
        'implementingClassName' => 'Paypal',
        'currentClassName' => 'Paypal',
        'aliasName' => NULL,
      ),
      'paypal_create_order' => 
      array (
        'name' => 'paypal_create_order',
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
            'startLine' => 37,
            'endLine' => 37,
            'startColumn' => 41,
            'endColumn' => 56,
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
 * Create the order on PayPal that is then processed when
 * the user inserts the payment method.
 *
 * @param string $invoice_url_key
 *
 * @return json the PayPal object to be loaded in the JS SDK script
 */',
        'startLine' => 37,
        'endLine' => 109,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Paypal',
        'implementingClassName' => 'Paypal',
        'currentClassName' => 'Paypal',
        'aliasName' => NULL,
      ),
      'paypal_capture_payment' => 
      array (
        'name' => 'paypal_capture_payment',
        'parameters' => 
        array (
          'order_id' => 
          array (
            'name' => 'order_id',
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
            'startLine' => 118,
            'endLine' => 118,
            'startColumn' => 44,
            'endColumn' => 59,
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
 * Capture the payment which is put on hold on PayPal
 * after the user has set the card details.
 *
 *
 * @return void
 */',
        'startLine' => 118,
        'endLine' => 313,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Paypal',
        'implementingClassName' => 'Paypal',
        'currentClassName' => 'Paypal',
        'aliasName' => NULL,
      ),
      '_create_client' => 
      array (
        'name' => '_create_client',
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
        'startLine' => 315,
        'endLine' => 325,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => NULL,
        'declaringClassName' => 'Paypal',
        'implementingClassName' => 'Paypal',
        'currentClassName' => 'Paypal',
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