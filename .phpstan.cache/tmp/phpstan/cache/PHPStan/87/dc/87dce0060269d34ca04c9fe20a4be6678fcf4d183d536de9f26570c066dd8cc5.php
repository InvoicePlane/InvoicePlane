<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/application/modules/guest/controllers/Payment_information.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Payment_Information
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-c91cf00c7cd0992190039d74cc596cb7631ad59f57b3289d3d52128324d55f7d',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Payment_Information',
        'filename' => '/var/www/projects/exprmt/application/modules/guest/controllers/Payment_information.php',
      ),
    ),
    'namespace' => NULL,
    'name' => 'Payment_Information',
    'shortName' => 'Payment_Information',
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
    'endLine' => 141,
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
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Payment_Information',
        'implementingClassName' => 'Payment_Information',
        'currentClassName' => 'Payment_Information',
        'aliasName' => NULL,
      ),
      'form' => 
      array (
        'name' => 'form',
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
            'startLine' => 26,
            'endLine' => 26,
            'startColumn' => 26,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'payment_provider' => 
          array (
            'name' => 'payment_provider',
            'default' => 
            array (
              'code' => '\\null',
              'attributes' => 
              array (
                'startLine' => 26,
                'endLine' => 26,
                'startTokenPos' => 82,
                'startFilePos' => 593,
                'endTokenPos' => 82,
                'endFilePos' => 596,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 26,
            'endLine' => 26,
            'startColumn' => 44,
            'endColumn' => 67,
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
        'startLine' => 26,
        'endLine' => 104,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Payment_Information',
        'implementingClassName' => 'Payment_Information',
        'currentClassName' => 'Payment_Information',
        'aliasName' => NULL,
      ),
      'stripe' => 
      array (
        'name' => 'stripe',
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
            'startLine' => 112,
            'endLine' => 112,
            'startColumn' => 28,
            'endColumn' => 43,
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
 * Load the stripe payments page
 * with the pertinent data.
 *
 * @return View the stripe page view
 */',
        'startLine' => 112,
        'endLine' => 120,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Payment_Information',
        'implementingClassName' => 'Payment_Information',
        'currentClassName' => 'Payment_Information',
        'aliasName' => NULL,
      ),
      'paypal' => 
      array (
        'name' => 'paypal',
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
            'startLine' => 130,
            'endLine' => 130,
            'startColumn' => 28,
            'endColumn' => 43,
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
 * Create the order on PayPal and load the
 * paypal payments page.
 *
 * @param string $invoice_url_key
 *
 * @return View the paypal page view
 */',
        'startLine' => 130,
        'endLine' => 140,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Payment_Information',
        'implementingClassName' => 'Payment_Information',
        'currentClassName' => 'Payment_Information',
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