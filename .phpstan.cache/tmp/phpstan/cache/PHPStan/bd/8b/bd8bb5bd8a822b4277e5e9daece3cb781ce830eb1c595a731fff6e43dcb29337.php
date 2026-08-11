<?php declare(strict_types = 1);

// osfsl-/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/ApiOperations/Request.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Stripe\ApiOperations\Request
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-d3a36d3606a549ccd0a0dcaa34c48cd55fa3ed4736177553aaac237f011ce703-8.4.24-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Stripe\\ApiOperations\\Request',
        'filename' => '/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/ApiOperations/Request.php',
      ),
    ),
    'namespace' => 'Stripe\\ApiOperations',
    'name' => 'Stripe\\ApiOperations\\Request',
    'shortName' => 'Request',
    'isInterface' => false,
    'isTrait' => true,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Trait for resources that need to make API requests.
 *
 * This trait should only be applied to classes that derive from StripeObject.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 10,
    'endLine' => 132,
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
      '_validateParams' => 
      array (
        'name' => '_validateParams',
        'parameters' => 
        array (
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 17,
                'endLine' => 17,
                'startTokenPos' => 29,
                'startFilePos' => 450,
                'endTokenPos' => 29,
                'endFilePos' => 453,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 17,
            'endLine' => 17,
            'startColumn' => 47,
            'endColumn' => 60,
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
 * @param null|array|mixed $params The list of parameters to validate
 *
 * @throws \\Stripe\\Exception\\InvalidArgumentException if $params exists and is not an array
 */',
        'startLine' => 17,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 18,
        'namespace' => 'Stripe\\ApiOperations',
        'declaringClassName' => 'Stripe\\ApiOperations\\Request',
        'implementingClassName' => 'Stripe\\ApiOperations\\Request',
        'currentClassName' => 'Stripe\\ApiOperations\\Request',
        'aliasName' => NULL,
      ),
      '_request' => 
      array (
        'name' => '_request',
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
            'startLine' => 40,
            'endLine' => 40,
            'startColumn' => 33,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'url' => 
          array (
            'name' => 'url',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 40,
            'endLine' => 40,
            'startColumn' => 42,
            'endColumn' => 45,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 40,
                'endLine' => 40,
                'startTokenPos' => 101,
                'startFilePos' => 1450,
                'endTokenPos' => 102,
                'endFilePos' => 1451,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 40,
            'endLine' => 40,
            'startColumn' => 48,
            'endColumn' => 59,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 40,
                'endLine' => 40,
                'startTokenPos' => 109,
                'startFilePos' => 1465,
                'endTokenPos' => 109,
                'endFilePos' => 1468,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 40,
            'endLine' => 40,
            'startColumn' => 62,
            'endColumn' => 76,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
          'usage' => 
          array (
            'name' => 'usage',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 40,
                'endLine' => 40,
                'startTokenPos' => 116,
                'startFilePos' => 1480,
                'endTokenPos' => 117,
                'endFilePos' => 1481,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 40,
            'endLine' => 40,
            'startColumn' => 79,
            'endColumn' => 89,
            'parameterIndex' => 4,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param \'delete\'|\'get\'|\'post\' $method HTTP method (\'get\', \'post\', etc.)
 * @param string $url URL for the request
 * @param array $params list of parameters for the request
 * @param null|array|string $options
 * @param string[] $usage names of tracked behaviors associated with this request
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return array tuple containing (the JSON response, $options)
 */',
        'startLine' => 40,
        'endLine' => 47,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Stripe\\ApiOperations',
        'declaringClassName' => 'Stripe\\ApiOperations\\Request',
        'implementingClassName' => 'Stripe\\ApiOperations\\Request',
        'currentClassName' => 'Stripe\\ApiOperations\\Request',
        'aliasName' => NULL,
      ),
      '_requestPage' => 
      array (
        'name' => '_requestPage',
        'parameters' => 
        array (
          'url' => 
          array (
            'name' => 'url',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 60,
            'endLine' => 60,
            'startColumn' => 44,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'resultClass' => 
          array (
            'name' => 'resultClass',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 60,
            'endLine' => 60,
            'startColumn' => 50,
            'endColumn' => 61,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 60,
                'endLine' => 60,
                'startTokenPos' => 208,
                'startFilePos' => 2326,
                'endTokenPos' => 208,
                'endFilePos' => 2329,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 60,
            'endLine' => 60,
            'startColumn' => 64,
            'endColumn' => 77,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 60,
                'endLine' => 60,
                'startTokenPos' => 215,
                'startFilePos' => 2343,
                'endTokenPos' => 215,
                'endFilePos' => 2346,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 60,
            'endLine' => 60,
            'startColumn' => 80,
            'endColumn' => 94,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
          'usage' => 
          array (
            'name' => 'usage',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 60,
                'endLine' => 60,
                'startTokenPos' => 222,
                'startFilePos' => 2358,
                'endTokenPos' => 223,
                'endFilePos' => 2359,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 60,
            'endLine' => 60,
            'startColumn' => 97,
            'endColumn' => 107,
            'parameterIndex' => 4,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param string $url URL for the request
 * @param class-string< \\Stripe\\SearchResult|\\Stripe\\Collection > $resultClass indicating what type of paginated result is returned
 * @param null|array $params list of parameters for the request
 * @param null|array|string $options
 * @param string[] $usage names of tracked behaviors associated with this request
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Collection|\\Stripe\\SearchResult
 */',
        'startLine' => 60,
        'endLine' => 75,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 18,
        'namespace' => 'Stripe\\ApiOperations',
        'declaringClassName' => 'Stripe\\ApiOperations\\Request',
        'implementingClassName' => 'Stripe\\ApiOperations\\Request',
        'currentClassName' => 'Stripe\\ApiOperations\\Request',
        'aliasName' => NULL,
      ),
      '_requestStream' => 
      array (
        'name' => '_requestStream',
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
            'startLine' => 87,
            'endLine' => 87,
            'startColumn' => 39,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'url' => 
          array (
            'name' => 'url',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 87,
            'endLine' => 87,
            'startColumn' => 48,
            'endColumn' => 51,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'readBodyChunk' => 
          array (
            'name' => 'readBodyChunk',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 87,
            'endLine' => 87,
            'startColumn' => 54,
            'endColumn' => 67,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 87,
                'endLine' => 87,
                'startTokenPos' => 375,
                'startFilePos' => 3524,
                'endTokenPos' => 376,
                'endFilePos' => 3525,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 87,
            'endLine' => 87,
            'startColumn' => 70,
            'endColumn' => 81,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 87,
                'endLine' => 87,
                'startTokenPos' => 383,
                'startFilePos' => 3539,
                'endTokenPos' => 383,
                'endFilePos' => 3542,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 87,
            'endLine' => 87,
            'startColumn' => 84,
            'endColumn' => 98,
            'parameterIndex' => 4,
            'isOptional' => true,
          ),
          'usage' => 
          array (
            'name' => 'usage',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 87,
                'endLine' => 87,
                'startTokenPos' => 390,
                'startFilePos' => 3554,
                'endTokenPos' => 391,
                'endFilePos' => 3555,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 87,
            'endLine' => 87,
            'startColumn' => 101,
            'endColumn' => 111,
            'parameterIndex' => 5,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param \'delete\'|\'get\'|\'post\' $method HTTP method (\'get\', \'post\', etc.)
 * @param string $url URL for the request
 * @param callable $readBodyChunk function that will receive chunks of data from a successful request body
 * @param array $params list of parameters for the request
 * @param null|array|string $options
 * @param string[] $usage names of tracked behaviors associated with this request
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 */',
        'startLine' => 87,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Stripe\\ApiOperations',
        'declaringClassName' => 'Stripe\\ApiOperations\\Request',
        'implementingClassName' => 'Stripe\\ApiOperations\\Request',
        'currentClassName' => 'Stripe\\ApiOperations\\Request',
        'aliasName' => NULL,
      ),
      '_staticRequest' => 
      array (
        'name' => '_staticRequest',
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
            'startLine' => 104,
            'endLine' => 104,
            'startColumn' => 46,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'url' => 
          array (
            'name' => 'url',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 104,
            'endLine' => 104,
            'startColumn' => 55,
            'endColumn' => 58,
            'parameterIndex' => 1,
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
            'startLine' => 104,
            'endLine' => 104,
            'startColumn' => 61,
            'endColumn' => 67,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'options' => 
          array (
            'name' => 'options',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 104,
            'endLine' => 104,
            'startColumn' => 70,
            'endColumn' => 77,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'usage' => 
          array (
            'name' => 'usage',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 104,
                'endLine' => 104,
                'startTokenPos' => 461,
                'startFilePos' => 4286,
                'endTokenPos' => 462,
                'endFilePos' => 4287,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 104,
            'endLine' => 104,
            'startColumn' => 80,
            'endColumn' => 90,
            'parameterIndex' => 4,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param \'delete\'|\'get\'|\'post\' $method HTTP method (\'get\', \'post\', etc.)
 * @param string $url URL for the request
 * @param array $params list of parameters for the request
 * @param null|array|string $options
 * @param string[] $usage names of tracked behaviors associated with this request
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return array tuple containing (the JSON response, $options)
 */',
        'startLine' => 104,
        'endLine' => 113,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 18,
        'namespace' => 'Stripe\\ApiOperations',
        'declaringClassName' => 'Stripe\\ApiOperations\\Request',
        'implementingClassName' => 'Stripe\\ApiOperations\\Request',
        'currentClassName' => 'Stripe\\ApiOperations\\Request',
        'aliasName' => NULL,
      ),
      '_staticStreamingRequest' => 
      array (
        'name' => '_staticStreamingRequest',
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
            'startLine' => 125,
            'endLine' => 125,
            'startColumn' => 55,
            'endColumn' => 61,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'url' => 
          array (
            'name' => 'url',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 125,
            'endLine' => 125,
            'startColumn' => 64,
            'endColumn' => 67,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'readBodyChunk' => 
          array (
            'name' => 'readBodyChunk',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 125,
            'endLine' => 125,
            'startColumn' => 70,
            'endColumn' => 83,
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
            'startLine' => 125,
            'endLine' => 125,
            'startColumn' => 86,
            'endColumn' => 92,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'options' => 
          array (
            'name' => 'options',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 125,
            'endLine' => 125,
            'startColumn' => 95,
            'endColumn' => 102,
            'parameterIndex' => 4,
            'isOptional' => false,
          ),
          'usage' => 
          array (
            'name' => 'usage',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 125,
                'endLine' => 125,
                'startTokenPos' => 604,
                'startFilePos' => 5341,
                'endTokenPos' => 605,
                'endFilePos' => 5342,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 125,
            'endLine' => 125,
            'startColumn' => 105,
            'endColumn' => 115,
            'parameterIndex' => 5,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param \'delete\'|\'get\'|\'post\' $method HTTP method (\'get\', \'post\', etc.)
 * @param string $url URL for the request
 * @param callable $readBodyChunk function that will receive chunks of data from a successful request body
 * @param array $params list of parameters for the request
 * @param null|array|string $options
 * @param string[] $usage names of tracked behaviors associated with this request
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 */',
        'startLine' => 125,
        'endLine' => 131,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 18,
        'namespace' => 'Stripe\\ApiOperations',
        'declaringClassName' => 'Stripe\\ApiOperations\\Request',
        'implementingClassName' => 'Stripe\\ApiOperations\\Request',
        'currentClassName' => 'Stripe\\ApiOperations\\Request',
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