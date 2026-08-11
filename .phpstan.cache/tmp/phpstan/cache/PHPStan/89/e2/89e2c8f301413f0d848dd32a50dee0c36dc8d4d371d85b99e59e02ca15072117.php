<?php declare(strict_types = 1);

// osfsl-/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/HttpClient/ClientInterface.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Stripe\HttpClient\ClientInterface
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-0984f1b02bc70359a58fcde6a2cb684362e0bf07eca789ab333229051bbb2877-8.4.24-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Stripe\\HttpClient\\ClientInterface',
        'filename' => '/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/HttpClient/ClientInterface.php',
      ),
    ),
    'namespace' => 'Stripe\\HttpClient',
    'name' => 'Stripe\\HttpClient\\ClientInterface',
    'shortName' => 'ClientInterface',
    'isInterface' => true,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 5,
    'endLine' => 22,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
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
      'request' => 
      array (
        'name' => 'request',
        'parameters' => 
        array (
          'method' => 
          array (
            'name' => 'method',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 21,
            'endLine' => 21,
            'startColumn' => 29,
            'endColumn' => 35,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'absUrl' => 
          array (
            'name' => 'absUrl',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 21,
            'endLine' => 21,
            'startColumn' => 38,
            'endColumn' => 44,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'headers' => 
          array (
            'name' => 'headers',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 21,
            'endLine' => 21,
            'startColumn' => 47,
            'endColumn' => 54,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 21,
            'endLine' => 21,
            'startColumn' => 57,
            'endColumn' => 63,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'hasFile' => 
          array (
            'name' => 'hasFile',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 21,
            'endLine' => 21,
            'startColumn' => 66,
            'endColumn' => 73,
            'parameterIndex' => 4,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param \'delete\'|\'get\'|\'post\' $method The HTTP method being used
 * @param string $absUrl The URL being requested, including domain and protocol
 * @param array $headers Headers to be used in the request (full strings, not KV pairs)
 * @param array $params KV pairs for parameters. Can be nested for arrays and hashes
 * @param bool $hasFile Whether or not $params references a file (via an @ prefix or
 *                         CURLFile)
 *
 * @throws \\Stripe\\Exception\\ApiConnectionException
 * @throws \\Stripe\\Exception\\UnexpectedValueException
 *
 * @return array an array whose first element is raw request body, second
 *    element is HTTP status code and third array of HTTP headers
 */',
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 75,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\HttpClient',
        'declaringClassName' => 'Stripe\\HttpClient\\ClientInterface',
        'implementingClassName' => 'Stripe\\HttpClient\\ClientInterface',
        'currentClassName' => 'Stripe\\HttpClient\\ClientInterface',
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