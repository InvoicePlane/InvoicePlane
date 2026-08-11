<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/application/modules/guest/controllers/gateways/Stripe.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Stripe
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-ad0f7fae4c81d4f94991035c6fa90033d104e5ad23b3791706437cc4cd1c2c98',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Stripe',
        'filename' => '/var/www/projects/exprmt/application/modules/guest/controllers/gateways/Stripe.php',
      ),
    ),
    'namespace' => NULL,
    'name' => 'Stripe',
    'shortName' => 'Stripe',
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
    'startLine' => 18,
    'endLine' => 259,
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
      'stripe' => 
      array (
        'declaringClassName' => 'Stripe',
        'implementingClassName' => 'Stripe',
        'name' => 'stripe',
        'modifiers' => 2,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Stripe\\StripeClient',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 35,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'Mdl_settings' => 
      array (
        'declaringClassName' => 'Stripe',
        'implementingClassName' => 'Stripe',
        'name' => 'Mdl_settings',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 28,
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
        'startLine' => 25,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Stripe',
        'implementingClassName' => 'Stripe',
        'currentClassName' => 'Stripe',
        'aliasName' => NULL,
      ),
      'create_checkout_session' => 
      array (
        'name' => 'create_checkout_session',
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
            'startLine' => 45,
            'endLine' => 45,
            'startColumn' => 45,
            'endColumn' => 60,
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
 * Creates a checkout session on Stripe
 * that is then retrieved to execute the payment.
 *
 * @param string $invoice_url_key the url key that is used to retrive the invoice
 *
 * @return json the client secret in a json format
 */',
        'startLine' => 45,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Stripe',
        'implementingClassName' => 'Stripe',
        'currentClassName' => 'Stripe',
        'aliasName' => NULL,
      ),
      'callback' => 
      array (
        'name' => 'callback',
        'parameters' => 
        array (
          'checkout_session_id' => 
          array (
            'name' => 'checkout_session_id',
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
            'startLine' => 97,
            'endLine' => 97,
            'startColumn' => 30,
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
 * The callback endpoint called by stripe once the
 * card transaction has been completed or aborted
 * Handle exceptions Improved by @Matthias-Ab.
 *
 *
 * @return void
 */',
        'startLine' => 97,
        'endLine' => 218,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Stripe',
        'implementingClassName' => 'Stripe',
        'currentClassName' => 'Stripe',
        'aliasName' => NULL,
      ),
      'useTestHttpClientIfConfigured' => 
      array (
        'name' => 'useTestHttpClientIfConfigured',
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
        'docComment' => '/**
 * In the test environment only, replay a queue of canned HTTP responses
 * instead of calling the real Stripe API. \\Stripe\\ApiRequestor::setHttpClient()
 * is process-global by design (the SDK has no per-client HTTP override), which
 * is safe here because the test harness runs each request in its own PHP
 * subprocess. The queue is supplied by the test as a JSON-encoded array (via
 * AbstractTestCase::withEnvironment()) under STRIPE_MOCK_RESPONSES, each entry
 * shaped like [\'status\' => int, \'body\' => string]. Responses are consumed in
 * the order this controller calls the Stripe SDK.
 */',
        'startLine' => 230,
        'endLine' => 258,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'Stripe',
        'implementingClassName' => 'Stripe',
        'currentClassName' => 'Stripe',
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