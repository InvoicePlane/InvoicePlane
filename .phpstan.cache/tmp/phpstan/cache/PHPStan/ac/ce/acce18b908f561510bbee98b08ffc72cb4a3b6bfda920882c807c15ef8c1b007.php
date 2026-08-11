<?php declare(strict_types = 1);

// osfsl-/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/ApiRequestor.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Stripe\ApiRequestor
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-f3ac9451ddb18b6d78e52f3657fffd188f708d4c06cd644d29dbc0d24a74b6d3-8.4.24-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Stripe\\ApiRequestor',
        'filename' => '/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/ApiRequestor.php',
      ),
    ),
    'namespace' => 'Stripe',
    'name' => 'Stripe\\ApiRequestor',
    'shortName' => 'ApiRequestor',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Class ApiRequestor.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 8,
    'endLine' => 636,
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
      '_apiKey' => 
      array (
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'name' => '_apiKey',
        'modifiers' => 4,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * @var null|string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 13,
        'endLine' => 13,
        'startColumn' => 5,
        'endColumn' => 21,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_apiBase' => 
      array (
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'name' => '_apiBase',
        'modifiers' => 4,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 18,
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 22,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_appInfo' => 
      array (
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'name' => '_appInfo',
        'modifiers' => 4,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * @var null|array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 22,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_httpClient' => 
      array (
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'name' => '_httpClient',
        'modifiers' => 20,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * @var HttpClient\\ClientInterface
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 28,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 32,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_streamingHttpClient' => 
      array (
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'name' => '_streamingHttpClient',
        'modifiers' => 20,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * @var HttpClient\\StreamingClientInterface
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 32,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 41,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'requestTelemetry' => 
      array (
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'name' => 'requestTelemetry',
        'modifiers' => 20,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * @var RequestTelemetry
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 37,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 37,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'OPTIONS_KEYS' => 
      array (
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'name' => 'OPTIONS_KEYS',
        'modifiers' => 20,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'api_key\', \'idempotency_key\', \'stripe_account\', \'stripe_version\', \'api_base\']',
          'attributes' => 
          array (
            'startLine' => 39,
            'endLine' => 39,
            'startTokenPos' => 71,
            'startFilePos' => 577,
            'endTokenPos' => 85,
            'endFilePos' => 654,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 39,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 114,
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
          'apiKey' => 
          array (
            'name' => 'apiKey',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 48,
                'endLine' => 48,
                'startTokenPos' => 100,
                'startFilePos' => 859,
                'endTokenPos' => 100,
                'endFilePos' => 862,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 48,
            'endLine' => 48,
            'startColumn' => 33,
            'endColumn' => 46,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'apiBase' => 
          array (
            'name' => 'apiBase',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 48,
                'endLine' => 48,
                'startTokenPos' => 107,
                'startFilePos' => 876,
                'endTokenPos' => 107,
                'endFilePos' => 879,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 48,
            'endLine' => 48,
            'startColumn' => 49,
            'endColumn' => 63,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'appInfo' => 
          array (
            'name' => 'appInfo',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 48,
                'endLine' => 48,
                'startTokenPos' => 114,
                'startFilePos' => 893,
                'endTokenPos' => 114,
                'endFilePos' => 896,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 48,
            'endLine' => 48,
            'startColumn' => 66,
            'endColumn' => 80,
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
 * ApiRequestor constructor.
 *
 * @param null|string $apiKey
 * @param null|string $apiBase
 * @param null|array $appInfo
 */',
        'startLine' => 48,
        'endLine' => 56,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'currentClassName' => 'Stripe\\ApiRequestor',
        'aliasName' => NULL,
      ),
      '_telemetryJson' => 
      array (
        'name' => '_telemetryJson',
        'parameters' => 
        array (
          'requestTelemetry' => 
          array (
            'name' => 'requestTelemetry',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 67,
            'endLine' => 67,
            'startColumn' => 44,
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
 * Creates a telemetry json blob for use in \'X-Stripe-Client-Telemetry\' headers.
 *
 * @static
 *
 * @param RequestTelemetry $requestTelemetry
 *
 * @return string
 */',
        'startLine' => 67,
        'endLine' => 86,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 20,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'currentClassName' => 'Stripe\\ApiRequestor',
        'aliasName' => NULL,
      ),
      '_encodeObjects' => 
      array (
        'name' => '_encodeObjects',
        'parameters' => 
        array (
          'd' => 
          array (
            'name' => 'd',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 95,
            'endLine' => 95,
            'startColumn' => 44,
            'endColumn' => 45,
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
 * @static
 *
 * @param ApiResource|array|bool|mixed $d
 *
 * @return ApiResource|array|mixed|string
 */',
        'startLine' => 95,
        'endLine' => 116,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 20,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'currentClassName' => 'Stripe\\ApiRequestor',
        'aliasName' => NULL,
      ),
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
            'startLine' => 129,
            'endLine' => 129,
            'startColumn' => 29,
            'endColumn' => 35,
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
            'startLine' => 129,
            'endLine' => 129,
            'startColumn' => 38,
            'endColumn' => 41,
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
                'startLine' => 129,
                'endLine' => 129,
                'startTokenPos' => 469,
                'startFilePos' => 2970,
                'endTokenPos' => 469,
                'endFilePos' => 2973,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 129,
            'endLine' => 129,
            'startColumn' => 44,
            'endColumn' => 57,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'headers' => 
          array (
            'name' => 'headers',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 129,
                'endLine' => 129,
                'startTokenPos' => 476,
                'startFilePos' => 2987,
                'endTokenPos' => 476,
                'endFilePos' => 2990,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 129,
            'endLine' => 129,
            'startColumn' => 60,
            'endColumn' => 74,
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
                'startLine' => 129,
                'endLine' => 129,
                'startTokenPos' => 483,
                'startFilePos' => 3002,
                'endTokenPos' => 484,
                'endFilePos' => 3003,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 129,
            'endLine' => 129,
            'startColumn' => 77,
            'endColumn' => 87,
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
 * @param \'delete\'|\'get\'|\'post\' $method
 * @param string     $url
 * @param null|array $params
 * @param null|array $headers
 * @param string[] $usage
 *
 * @throws Exception\\ApiErrorException
 *
 * @return array tuple containing (ApiReponse, API key)
 */',
        'startLine' => 129,
        'endLine' => 139,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'currentClassName' => 'Stripe\\ApiRequestor',
        'aliasName' => NULL,
      ),
      'requestStream' => 
      array (
        'name' => 'requestStream',
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
            'startLine' => 151,
            'endLine' => 151,
            'startColumn' => 35,
            'endColumn' => 41,
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
            'startLine' => 151,
            'endLine' => 151,
            'startColumn' => 44,
            'endColumn' => 47,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'readBodyChunkCallable' => 
          array (
            'name' => 'readBodyChunkCallable',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 151,
            'endLine' => 151,
            'startColumn' => 50,
            'endColumn' => 71,
            'parameterIndex' => 2,
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
                'startLine' => 151,
                'endLine' => 151,
                'startTokenPos' => 623,
                'startFilePos' => 3753,
                'endTokenPos' => 623,
                'endFilePos' => 3756,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 151,
            'endLine' => 151,
            'startColumn' => 74,
            'endColumn' => 87,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
          'headers' => 
          array (
            'name' => 'headers',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 151,
                'endLine' => 151,
                'startTokenPos' => 630,
                'startFilePos' => 3770,
                'endTokenPos' => 630,
                'endFilePos' => 3773,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 151,
            'endLine' => 151,
            'startColumn' => 90,
            'endColumn' => 104,
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
                'startLine' => 151,
                'endLine' => 151,
                'startTokenPos' => 637,
                'startFilePos' => 3785,
                'endTokenPos' => 638,
                'endFilePos' => 3786,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 151,
            'endLine' => 151,
            'startColumn' => 107,
            'endColumn' => 117,
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
 * @param \'delete\'|\'get\'|\'post\' $method
 * @param string     $url
 * @param callable $readBodyChunkCallable
 * @param null|array $params
 * @param null|array $headers
 * @param string[] $usage
 *
 * @throws Exception\\ApiErrorException
 */',
        'startLine' => 151,
        'endLine' => 160,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'currentClassName' => 'Stripe\\ApiRequestor',
        'aliasName' => NULL,
      ),
      'handleErrorResponse' => 
      array (
        'name' => 'handleErrorResponse',
        'parameters' => 
        array (
          'rbody' => 
          array (
            'name' => 'rbody',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 171,
            'endLine' => 171,
            'startColumn' => 41,
            'endColumn' => 46,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'rcode' => 
          array (
            'name' => 'rcode',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 171,
            'endLine' => 171,
            'startColumn' => 49,
            'endColumn' => 54,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'rheaders' => 
          array (
            'name' => 'rheaders',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 171,
            'endLine' => 171,
            'startColumn' => 57,
            'endColumn' => 65,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'resp' => 
          array (
            'name' => 'resp',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 171,
            'endLine' => 171,
            'startColumn' => 68,
            'endColumn' => 72,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param string $rbody a JSON string
 * @param int $rcode
 * @param array $rheaders
 * @param array $resp
 *
 * @throws Exception\\UnexpectedValueException
 * @throws Exception\\ApiErrorException
 */',
        'startLine' => 171,
        'endLine' => 191,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'currentClassName' => 'Stripe\\ApiRequestor',
        'aliasName' => NULL,
      ),
      '_specificAPIError' => 
      array (
        'name' => '_specificAPIError',
        'parameters' => 
        array (
          'rbody' => 
          array (
            'name' => 'rbody',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 204,
            'endLine' => 204,
            'startColumn' => 47,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'rcode' => 
          array (
            'name' => 'rcode',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 204,
            'endLine' => 204,
            'startColumn' => 55,
            'endColumn' => 60,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'rheaders' => 
          array (
            'name' => 'rheaders',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 204,
            'endLine' => 204,
            'startColumn' => 63,
            'endColumn' => 71,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'resp' => 
          array (
            'name' => 'resp',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 204,
            'endLine' => 204,
            'startColumn' => 74,
            'endColumn' => 78,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'errorData' => 
          array (
            'name' => 'errorData',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 204,
            'endLine' => 204,
            'startColumn' => 81,
            'endColumn' => 90,
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
 * @static
 *
 * @param string $rbody
 * @param int    $rcode
 * @param array  $rheaders
 * @param array  $resp
 * @param array  $errorData
 *
 * @return Exception\\ApiErrorException
 */',
        'startLine' => 204,
        'endLine' => 242,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 20,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'currentClassName' => 'Stripe\\ApiRequestor',
        'aliasName' => NULL,
      ),
      '_specificOAuthError' => 
      array (
        'name' => '_specificOAuthError',
        'parameters' => 
        array (
          'rbody' => 
          array (
            'name' => 'rbody',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 255,
            'endLine' => 255,
            'startColumn' => 49,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'rcode' => 
          array (
            'name' => 'rcode',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 255,
            'endLine' => 255,
            'startColumn' => 57,
            'endColumn' => 62,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'rheaders' => 
          array (
            'name' => 'rheaders',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 255,
            'endLine' => 255,
            'startColumn' => 65,
            'endColumn' => 73,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'resp' => 
          array (
            'name' => 'resp',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 255,
            'endLine' => 255,
            'startColumn' => 76,
            'endColumn' => 80,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'errorCode' => 
          array (
            'name' => 'errorCode',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 255,
            'endLine' => 255,
            'startColumn' => 83,
            'endColumn' => 92,
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
 * @static
 *
 * @param bool|string $rbody
 * @param int         $rcode
 * @param array       $rheaders
 * @param array       $resp
 * @param string      $errorCode
 *
 * @return Exception\\OAuth\\OAuthErrorException
 */',
        'startLine' => 255,
        'endLine' => 281,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 20,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'currentClassName' => 'Stripe\\ApiRequestor',
        'aliasName' => NULL,
      ),
      '_formatAppInfo' => 
      array (
        'name' => '_formatAppInfo',
        'parameters' => 
        array (
          'appInfo' => 
          array (
            'name' => 'appInfo',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 290,
            'endLine' => 290,
            'startColumn' => 44,
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
 * @static
 *
 * @param null|array $appInfo
 *
 * @return null|string
 */',
        'startLine' => 290,
        'endLine' => 305,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 20,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'currentClassName' => 'Stripe\\ApiRequestor',
        'aliasName' => NULL,
      ),
      '_isDisabled' => 
      array (
        'name' => '_isDisabled',
        'parameters' => 
        array (
          'disableFunctionsOutput' => 
          array (
            'name' => 'disableFunctionsOutput',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 315,
            'endLine' => 315,
            'startColumn' => 41,
            'endColumn' => 63,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'functionName' => 
          array (
            'name' => 'functionName',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 315,
            'endLine' => 315,
            'startColumn' => 66,
            'endColumn' => 78,
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
 * @static
 *
 * @param string $disableFunctionsOutput - String value of the \'disable_function\' setting, as output by \\ini_get(\'disable_functions\')
 * @param string $functionName - Name of the function we are interesting in seeing whether or not it is disabled
 *
 * @return bool
 */',
        'startLine' => 315,
        'endLine' => 325,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 20,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'currentClassName' => 'Stripe\\ApiRequestor',
        'aliasName' => NULL,
      ),
      '_defaultHeaders' => 
      array (
        'name' => '_defaultHeaders',
        'parameters' => 
        array (
          'apiKey' => 
          array (
            'name' => 'apiKey',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 336,
            'endLine' => 336,
            'startColumn' => 45,
            'endColumn' => 51,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'clientInfo' => 
          array (
            'name' => 'clientInfo',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 336,
                'endLine' => 336,
                'startTokenPos' => 1854,
                'startFilePos' => 10649,
                'endTokenPos' => 1854,
                'endFilePos' => 10652,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 336,
            'endLine' => 336,
            'startColumn' => 54,
            'endColumn' => 71,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'appInfo' => 
          array (
            'name' => 'appInfo',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 336,
                'endLine' => 336,
                'startTokenPos' => 1861,
                'startFilePos' => 10666,
                'endTokenPos' => 1861,
                'endFilePos' => 10669,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 336,
            'endLine' => 336,
            'startColumn' => 74,
            'endColumn' => 88,
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
 * @static
 *
 * @param string     $apiKey the Stripe API key, to be used in regular API requests
 * @param null       $clientInfo client user agent information
 * @param null       $appInfo information to identify a plugin that integrates Stripe using this library
 *
 * @return array
 */',
        'startLine' => 336,
        'endLine' => 367,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 20,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'currentClassName' => 'Stripe\\ApiRequestor',
        'aliasName' => NULL,
      ),
      '_prepareRequest' => 
      array (
        'name' => '_prepareRequest',
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
            'startLine' => 369,
            'endLine' => 369,
            'startColumn' => 38,
            'endColumn' => 44,
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
            'startLine' => 369,
            'endLine' => 369,
            'startColumn' => 47,
            'endColumn' => 50,
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
            'startLine' => 369,
            'endLine' => 369,
            'startColumn' => 53,
            'endColumn' => 59,
            'parameterIndex' => 2,
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
            'startLine' => 369,
            'endLine' => 369,
            'startColumn' => 62,
            'endColumn' => 69,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 369,
        'endLine' => 444,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'currentClassName' => 'Stripe\\ApiRequestor',
        'aliasName' => NULL,
      ),
      '_requestRaw' => 
      array (
        'name' => '_requestRaw',
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
            'startLine' => 458,
            'endLine' => 458,
            'startColumn' => 34,
            'endColumn' => 40,
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
            'startLine' => 458,
            'endLine' => 458,
            'startColumn' => 43,
            'endColumn' => 46,
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
            'startLine' => 458,
            'endLine' => 458,
            'startColumn' => 49,
            'endColumn' => 55,
            'parameterIndex' => 2,
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
            'startLine' => 458,
            'endLine' => 458,
            'startColumn' => 58,
            'endColumn' => 65,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'usage' => 
          array (
            'name' => 'usage',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 458,
            'endLine' => 458,
            'startColumn' => 68,
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
 * @param \'delete\'|\'get\'|\'post\' $method
 * @param string $url
 * @param array $params
 * @param array $headers
 * @param string[] $usage
 *
 * @throws Exception\\AuthenticationException
 * @throws Exception\\ApiConnectionException
 *
 * @return array
 */',
        'startLine' => 458,
        'endLine' => 485,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'currentClassName' => 'Stripe\\ApiRequestor',
        'aliasName' => NULL,
      ),
      '_requestRawStreaming' => 
      array (
        'name' => '_requestRawStreaming',
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
            'startLine' => 500,
            'endLine' => 500,
            'startColumn' => 43,
            'endColumn' => 49,
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
            'startLine' => 500,
            'endLine' => 500,
            'startColumn' => 52,
            'endColumn' => 55,
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
            'startLine' => 500,
            'endLine' => 500,
            'startColumn' => 58,
            'endColumn' => 64,
            'parameterIndex' => 2,
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
            'startLine' => 500,
            'endLine' => 500,
            'startColumn' => 67,
            'endColumn' => 74,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'usage' => 
          array (
            'name' => 'usage',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 500,
            'endLine' => 500,
            'startColumn' => 77,
            'endColumn' => 82,
            'parameterIndex' => 4,
            'isOptional' => false,
          ),
          'readBodyChunkCallable' => 
          array (
            'name' => 'readBodyChunkCallable',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 500,
            'endLine' => 500,
            'startColumn' => 85,
            'endColumn' => 106,
            'parameterIndex' => 5,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param \'delete\'|\'get\'|\'post\' $method
 * @param string $url
 * @param array $params
 * @param array $headers
 * @param string[] $usage
 * @param callable $readBodyChunkCallable
 *
 * @throws Exception\\AuthenticationException
 * @throws Exception\\ApiConnectionException
 *
 * @return array
 */',
        'startLine' => 500,
        'endLine' => 527,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'currentClassName' => 'Stripe\\ApiRequestor',
        'aliasName' => NULL,
      ),
      '_processResourceParam' => 
      array (
        'name' => '_processResourceParam',
        'parameters' => 
        array (
          'resource' => 
          array (
            'name' => 'resource',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 536,
            'endLine' => 536,
            'startColumn' => 44,
            'endColumn' => 52,
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
 * @param resource $resource
 *
 * @throws Exception\\InvalidArgumentException
 *
 * @return \\CURLFile|string
 */',
        'startLine' => 536,
        'endLine' => 553,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'currentClassName' => 'Stripe\\ApiRequestor',
        'aliasName' => NULL,
      ),
      '_interpretResponse' => 
      array (
        'name' => '_interpretResponse',
        'parameters' => 
        array (
          'rbody' => 
          array (
            'name' => 'rbody',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 565,
            'endLine' => 565,
            'startColumn' => 41,
            'endColumn' => 46,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'rcode' => 
          array (
            'name' => 'rcode',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 565,
            'endLine' => 565,
            'startColumn' => 49,
            'endColumn' => 54,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'rheaders' => 
          array (
            'name' => 'rheaders',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 565,
            'endLine' => 565,
            'startColumn' => 57,
            'endColumn' => 65,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param string $rbody
 * @param int    $rcode
 * @param array  $rheaders
 *
 * @throws Exception\\UnexpectedValueException
 * @throws Exception\\ApiErrorException
 *
 * @return array
 */',
        'startLine' => 565,
        'endLine' => 581,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'currentClassName' => 'Stripe\\ApiRequestor',
        'aliasName' => NULL,
      ),
      'setHttpClient' => 
      array (
        'name' => 'setHttpClient',
        'parameters' => 
        array (
          'client' => 
          array (
            'name' => 'client',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 588,
            'endLine' => 588,
            'startColumn' => 42,
            'endColumn' => 48,
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
 * @static
 *
 * @param HttpClient\\ClientInterface $client
 */',
        'startLine' => 588,
        'endLine' => 591,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'currentClassName' => 'Stripe\\ApiRequestor',
        'aliasName' => NULL,
      ),
      'setStreamingHttpClient' => 
      array (
        'name' => 'setStreamingHttpClient',
        'parameters' => 
        array (
          'client' => 
          array (
            'name' => 'client',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 598,
            'endLine' => 598,
            'startColumn' => 51,
            'endColumn' => 57,
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
 * @static
 *
 * @param HttpClient\\StreamingClientInterface $client
 */',
        'startLine' => 598,
        'endLine' => 601,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'currentClassName' => 'Stripe\\ApiRequestor',
        'aliasName' => NULL,
      ),
      'resetTelemetry' => 
      array (
        'name' => 'resetTelemetry',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @static
 *
 * Resets any stateful telemetry data
 */',
        'startLine' => 608,
        'endLine' => 611,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'currentClassName' => 'Stripe\\ApiRequestor',
        'aliasName' => NULL,
      ),
      'httpClient' => 
      array (
        'name' => 'httpClient',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return HttpClient\\ClientInterface
 */',
        'startLine' => 616,
        'endLine' => 623,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'currentClassName' => 'Stripe\\ApiRequestor',
        'aliasName' => NULL,
      ),
      'streamingHttpClient' => 
      array (
        'name' => 'streamingHttpClient',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return HttpClient\\StreamingClientInterface
 */',
        'startLine' => 628,
        'endLine' => 635,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\ApiRequestor',
        'implementingClassName' => 'Stripe\\ApiRequestor',
        'currentClassName' => 'Stripe\\ApiRequestor',
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