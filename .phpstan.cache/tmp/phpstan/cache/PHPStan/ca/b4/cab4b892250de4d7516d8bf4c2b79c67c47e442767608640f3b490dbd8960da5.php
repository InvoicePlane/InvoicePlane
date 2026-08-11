<?php declare(strict_types = 1);

// osfsl-/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/Util/RequestOptions.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Stripe\Util\RequestOptions
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-2ca315da5b4d326f8790a28b75d0bc861e5069aa6470fbc2550f8202fe975bed-8.4.24-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Stripe\\Util\\RequestOptions',
        'filename' => '/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/Util/RequestOptions.php',
      ),
    ),
    'namespace' => 'Stripe\\Util',
    'name' => 'Stripe\\Util\\RequestOptions',
    'shortName' => 'RequestOptions',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @phpstan-type RequestOptionsArray array{api_key?: string, idempotency_key?: string, stripe_account?: string, stripe_version?: string, api_base?: string }
 * @psalm-type RequestOptionsArray = array{api_key?: string, idempotency_key?: string, stripe_account?: string, stripe_version?: string, api_base?: string }
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 9,
    'endLine' => 177,
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
      'HEADERS_TO_PERSIST' => 
      array (
        'declaringClassName' => 'Stripe\\Util\\RequestOptions',
        'implementingClassName' => 'Stripe\\Util\\RequestOptions',
        'name' => 'HEADERS_TO_PERSIST',
        'modifiers' => 17,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'Stripe-Account\', \'Stripe-Version\']',
          'attributes' => 
          array (
            'startLine' => 14,
            'endLine' => 17,
            'startTokenPos' => 25,
            'startFilePos' => 517,
            'endTokenPos' => 33,
            'endFilePos' => 575,
          ),
        ),
        'docComment' => '/**
 * @var array<string> a list of headers that should be persisted across requests
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 14,
        'endLine' => 17,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'headers' => 
      array (
        'declaringClassName' => 'Stripe\\Util\\RequestOptions',
        'implementingClassName' => 'Stripe\\Util\\RequestOptions',
        'name' => 'headers',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/** @var array<string, string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 20,
        'endLine' => 20,
        'startColumn' => 5,
        'endColumn' => 20,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'apiKey' => 
      array (
        'declaringClassName' => 'Stripe\\Util\\RequestOptions',
        'implementingClassName' => 'Stripe\\Util\\RequestOptions',
        'name' => 'apiKey',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/** @var null|string */',
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 19,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'apiBase' => 
      array (
        'declaringClassName' => 'Stripe\\Util\\RequestOptions',
        'implementingClassName' => 'Stripe\\Util\\RequestOptions',
        'name' => 'apiBase',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/** @var null|string */',
        'attributes' => 
        array (
        ),
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 20,
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
          'key' => 
          array (
            'name' => 'key',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 33,
                'endLine' => 33,
                'startTokenPos' => 69,
                'startFilePos' => 901,
                'endTokenPos' => 69,
                'endFilePos' => 904,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 33,
            'endLine' => 33,
            'startColumn' => 33,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'headers' => 
          array (
            'name' => 'headers',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 33,
                'endLine' => 33,
                'startTokenPos' => 76,
                'startFilePos' => 918,
                'endTokenPos' => 77,
                'endFilePos' => 919,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 33,
            'endLine' => 33,
            'startColumn' => 46,
            'endColumn' => 58,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'base' => 
          array (
            'name' => 'base',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 33,
                'endLine' => 33,
                'startTokenPos' => 84,
                'startFilePos' => 930,
                'endTokenPos' => 84,
                'endFilePos' => 933,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 33,
            'endLine' => 33,
            'startColumn' => 61,
            'endColumn' => 72,
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
 * @param null|string $key
 * @param array<string, string> $headers
 * @param null|string $base
 */',
        'startLine' => 33,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\Util',
        'declaringClassName' => 'Stripe\\Util\\RequestOptions',
        'implementingClassName' => 'Stripe\\Util\\RequestOptions',
        'currentClassName' => 'Stripe\\Util\\RequestOptions',
        'aliasName' => NULL,
      ),
      '__debugInfo' => 
      array (
        'name' => '__debugInfo',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return array<string, string>
 */',
        'startLine' => 43,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\Util',
        'declaringClassName' => 'Stripe\\Util\\RequestOptions',
        'implementingClassName' => 'Stripe\\Util\\RequestOptions',
        'currentClassName' => 'Stripe\\Util\\RequestOptions',
        'aliasName' => NULL,
      ),
      'merge' => 
      array (
        'name' => 'merge',
        'parameters' => 
        array (
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
            'startLine' => 61,
            'endLine' => 61,
            'startColumn' => 27,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'strict' => 
          array (
            'name' => 'strict',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 61,
                'endLine' => 61,
                'startTokenPos' => 183,
                'startFilePos' => 1672,
                'endTokenPos' => 183,
                'endFilePos' => 1676,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 61,
            'endLine' => 61,
            'startColumn' => 37,
            'endColumn' => 51,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Unpacks an options array and merges it into the existing RequestOptions
 * object.
 *
 * @param null|array|RequestOptions|string $options a key => value array
 * @param bool $strict when true, forbid string form and arbitrary keys in array form
 *
 * @return RequestOptions
 */',
        'startLine' => 61,
        'endLine' => 73,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\Util',
        'declaringClassName' => 'Stripe\\Util\\RequestOptions',
        'implementingClassName' => 'Stripe\\Util\\RequestOptions',
        'currentClassName' => 'Stripe\\Util\\RequestOptions',
        'aliasName' => NULL,
      ),
      'discardNonPersistentHeaders' => 
      array (
        'name' => 'discardNonPersistentHeaders',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Discards all headers that we don\'t want to persist across requests.
 */',
        'startLine' => 78,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\Util',
        'declaringClassName' => 'Stripe\\Util\\RequestOptions',
        'implementingClassName' => 'Stripe\\Util\\RequestOptions',
        'currentClassName' => 'Stripe\\Util\\RequestOptions',
        'aliasName' => NULL,
      ),
      'parse' => 
      array (
        'name' => 'parse',
        'parameters' => 
        array (
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
            'startLine' => 97,
            'endLine' => 97,
            'startColumn' => 34,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'strict' => 
          array (
            'name' => 'strict',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 97,
                'endLine' => 97,
                'startTokenPos' => 367,
                'startFilePos' => 2834,
                'endTokenPos' => 367,
                'endFilePos' => 2838,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 97,
            'endLine' => 97,
            'startColumn' => 44,
            'endColumn' => 58,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Unpacks an options array into an RequestOptions object.
 *
 * @param null|array|RequestOptions|string $options a key => value array
 * @param bool $strict when true, forbid string form and arbitrary keys in array form
 *
 * @throws \\Stripe\\Exception\\InvalidArgumentException
 *
 * @return RequestOptions
 */',
        'startLine' => 97,
        'endLine' => 159,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe\\Util',
        'declaringClassName' => 'Stripe\\Util\\RequestOptions',
        'implementingClassName' => 'Stripe\\Util\\RequestOptions',
        'currentClassName' => 'Stripe\\Util\\RequestOptions',
        'aliasName' => NULL,
      ),
      'redactedApiKey' => 
      array (
        'name' => 'redactedApiKey',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/** @return string */',
        'startLine' => 162,
        'endLine' => 176,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Stripe\\Util',
        'declaringClassName' => 'Stripe\\Util\\RequestOptions',
        'implementingClassName' => 'Stripe\\Util\\RequestOptions',
        'currentClassName' => 'Stripe\\Util\\RequestOptions',
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