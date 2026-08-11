<?php declare(strict_types = 1);

// osfsl-/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/BaseStripeClient.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Stripe\BaseStripeClient
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-5b5621e5a02dd56805672b3ead4d5b065c81da924bec4dc7590ac0aaed3cfc0b-8.4.24-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Stripe\\BaseStripeClient',
        'filename' => '/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/BaseStripeClient.php',
      ),
    ),
    'namespace' => 'Stripe',
    'name' => 'Stripe\\BaseStripeClient',
    'shortName' => 'BaseStripeClient',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 5,
    'endLine' => 330,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'Stripe\\StripeClientInterface',
      1 => 'Stripe\\StripeStreamingClientInterface',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'DEFAULT_API_BASE' => 
      array (
        'declaringClassName' => 'Stripe\\BaseStripeClient',
        'implementingClassName' => 'Stripe\\BaseStripeClient',
        'name' => 'DEFAULT_API_BASE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'https://api.stripe.com\'',
          'attributes' => 
          array (
            'startLine' => 8,
            'endLine' => 8,
            'startTokenPos' => 28,
            'startFilePos' => 202,
            'endTokenPos' => 28,
            'endFilePos' => 225,
          ),
        ),
        'docComment' => '/** @var string default base URL for Stripe\'s API */',
        'attributes' => 
        array (
        ),
        'startLine' => 8,
        'endLine' => 8,
        'startColumn' => 5,
        'endColumn' => 54,
      ),
      'DEFAULT_CONNECT_BASE' => 
      array (
        'declaringClassName' => 'Stripe\\BaseStripeClient',
        'implementingClassName' => 'Stripe\\BaseStripeClient',
        'name' => 'DEFAULT_CONNECT_BASE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'https://connect.stripe.com\'',
          'attributes' => 
          array (
            'startLine' => 11,
            'endLine' => 11,
            'startTokenPos' => 39,
            'startFilePos' => 325,
            'endTokenPos' => 39,
            'endFilePos' => 352,
          ),
        ),
        'docComment' => '/** @var string default base URL for Stripe\'s OAuth API */',
        'attributes' => 
        array (
        ),
        'startLine' => 11,
        'endLine' => 11,
        'startColumn' => 5,
        'endColumn' => 62,
      ),
      'DEFAULT_FILES_BASE' => 
      array (
        'declaringClassName' => 'Stripe\\BaseStripeClient',
        'implementingClassName' => 'Stripe\\BaseStripeClient',
        'name' => 'DEFAULT_FILES_BASE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'https://files.stripe.com\'',
          'attributes' => 
          array (
            'startLine' => 14,
            'endLine' => 14,
            'startTokenPos' => 50,
            'startFilePos' => 450,
            'endTokenPos' => 50,
            'endFilePos' => 475,
          ),
        ),
        'docComment' => '/** @var string default base URL for Stripe\'s Files API */',
        'attributes' => 
        array (
        ),
        'startLine' => 14,
        'endLine' => 14,
        'startColumn' => 5,
        'endColumn' => 58,
      ),
      'DEFAULT_CONFIG' => 
      array (
        'declaringClassName' => 'Stripe\\BaseStripeClient',
        'implementingClassName' => 'Stripe\\BaseStripeClient',
        'name' => 'DEFAULT_CONFIG',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'api_key\' => null, \'app_info\' => null, \'client_id\' => null, \'stripe_account\' => null, \'stripe_version\' => \\Stripe\\Util\\ApiVersion::CURRENT, \'api_base\' => self::DEFAULT_API_BASE, \'connect_base\' => self::DEFAULT_CONNECT_BASE, \'files_base\' => self::DEFAULT_FILES_BASE]',
          'attributes' => 
          array (
            'startLine' => 17,
            'endLine' => 26,
            'startTokenPos' => 61,
            'startFilePos' => 549,
            'endTokenPos' => 127,
            'endFilePos' => 885,
          ),
        ),
        'docComment' => '/** @var array<string, null|string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 17,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
    ),
    'immediateProperties' => 
    array (
      'config' => 
      array (
        'declaringClassName' => 'Stripe\\BaseStripeClient',
        'implementingClassName' => 'Stripe\\BaseStripeClient',
        'name' => 'config',
        'modifiers' => 4,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/** @var array<string, mixed> */',
        'attributes' => 
        array (
        ),
        'startLine' => 29,
        'endLine' => 29,
        'startColumn' => 5,
        'endColumn' => 20,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'defaultOpts' => 
      array (
        'declaringClassName' => 'Stripe\\BaseStripeClient',
        'implementingClassName' => 'Stripe\\BaseStripeClient',
        'name' => 'defaultOpts',
        'modifiers' => 4,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/** @var \\Stripe\\Util\\RequestOptions */',
        'attributes' => 
        array (
        ),
        'startLine' => 32,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 25,
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
          'config' => 
          array (
            'name' => 'config',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 64,
                'endLine' => 64,
                'startTokenPos' => 156,
                'startFilePos' => 2891,
                'endTokenPos' => 157,
                'endFilePos' => 2892,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 64,
            'endLine' => 64,
            'startColumn' => 33,
            'endColumn' => 44,
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
 * Initializes a new instance of the {@link BaseStripeClient} class.
 *
 * The constructor takes a single argument. The argument can be a string, in which case it
 * should be the API key. It can also be an array with various configuration settings.
 *
 * Configuration settings include the following options:
 *
 * - api_key (null|string): the Stripe API key, to be used in regular API requests.
 * - app_info (null|array): information to identify a plugin that integrates Stripe using this library.
 *                          Expects: array{name: string, version?: string, url?: string, partner_id?: string}
 * - client_id (null|string): the Stripe client ID, to be used in OAuth requests.
 * - stripe_account (null|string): a Stripe account ID. If set, all requests sent by the client
 *   will automatically use the {@code Stripe-Account} header with that account ID.
 * - stripe_version (null|string): a Stripe API version. If set, all requests sent by the client
 *   will include the {@code Stripe-Version} header with that API version.
 *
 * The following configuration settings are also available, though setting these should rarely be necessary
 * (only useful if you want to send requests to a mock server like stripe-mock):
 *
 * - api_base (string): the base URL for regular API requests. Defaults to
 *   {@link DEFAULT_API_BASE}.
 * - connect_base (string): the base URL for OAuth requests. Defaults to
 *   {@link DEFAULT_CONNECT_BASE}.
 * - files_base (string): the base URL for file creation requests. Defaults to
 *   {@link DEFAULT_FILES_BASE}.
 *
 * @param array<string, mixed>|string $config the API key as a string, or an array containing
 *   the client configuration settings
 */',
        'startLine' => 64,
        'endLine' => 81,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\BaseStripeClient',
        'implementingClassName' => 'Stripe\\BaseStripeClient',
        'currentClassName' => 'Stripe\\BaseStripeClient',
        'aliasName' => NULL,
      ),
      'getApiKey' => 
      array (
        'name' => 'getApiKey',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Gets the API key used by the client to send requests.
 *
 * @return null|string the API key used by the client to send requests
 */',
        'startLine' => 88,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\BaseStripeClient',
        'implementingClassName' => 'Stripe\\BaseStripeClient',
        'currentClassName' => 'Stripe\\BaseStripeClient',
        'aliasName' => NULL,
      ),
      'getClientId' => 
      array (
        'name' => 'getClientId',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Gets the client ID used by the client in OAuth requests.
 *
 * @return null|string the client ID used by the client in OAuth requests
 */',
        'startLine' => 98,
        'endLine' => 101,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\BaseStripeClient',
        'implementingClassName' => 'Stripe\\BaseStripeClient',
        'currentClassName' => 'Stripe\\BaseStripeClient',
        'aliasName' => NULL,
      ),
      'getApiBase' => 
      array (
        'name' => 'getApiBase',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Gets the base URL for Stripe\'s API.
 *
 * @return string the base URL for Stripe\'s API
 */',
        'startLine' => 108,
        'endLine' => 111,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\BaseStripeClient',
        'implementingClassName' => 'Stripe\\BaseStripeClient',
        'currentClassName' => 'Stripe\\BaseStripeClient',
        'aliasName' => NULL,
      ),
      'getConnectBase' => 
      array (
        'name' => 'getConnectBase',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Gets the base URL for Stripe\'s OAuth API.
 *
 * @return string the base URL for Stripe\'s OAuth API
 */',
        'startLine' => 118,
        'endLine' => 121,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\BaseStripeClient',
        'implementingClassName' => 'Stripe\\BaseStripeClient',
        'currentClassName' => 'Stripe\\BaseStripeClient',
        'aliasName' => NULL,
      ),
      'getFilesBase' => 
      array (
        'name' => 'getFilesBase',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Gets the base URL for Stripe\'s Files API.
 *
 * @return string the base URL for Stripe\'s Files API
 */',
        'startLine' => 128,
        'endLine' => 131,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\BaseStripeClient',
        'implementingClassName' => 'Stripe\\BaseStripeClient',
        'currentClassName' => 'Stripe\\BaseStripeClient',
        'aliasName' => NULL,
      ),
      'getAppInfo' => 
      array (
        'name' => 'getAppInfo',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Gets the app info for this client.
 *
 * @return null|array information to identify a plugin that integrates Stripe using this library
 */',
        'startLine' => 138,
        'endLine' => 141,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\BaseStripeClient',
        'implementingClassName' => 'Stripe\\BaseStripeClient',
        'currentClassName' => 'Stripe\\BaseStripeClient',
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
            'startLine' => 153,
            'endLine' => 153,
            'startColumn' => 29,
            'endColumn' => 35,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'path' => 
          array (
            'name' => 'path',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 153,
            'endLine' => 153,
            'startColumn' => 38,
            'endColumn' => 42,
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
            'startLine' => 153,
            'endLine' => 153,
            'startColumn' => 45,
            'endColumn' => 51,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'opts' => 
          array (
            'name' => 'opts',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 153,
            'endLine' => 153,
            'startColumn' => 54,
            'endColumn' => 58,
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
 * Sends a request to Stripe\'s API.
 *
 * @param \'delete\'|\'get\'|\'post\' $method the HTTP method
 * @param string $path the path of the request
 * @param array $params the parameters of the request
 * @param array|\\Stripe\\Util\\RequestOptions $opts the special modifiers of the request
 *
 * @return \\Stripe\\StripeObject the object returned by Stripe\'s API
 */',
        'startLine' => 153,
        'endLine' => 164,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\BaseStripeClient',
        'implementingClassName' => 'Stripe\\BaseStripeClient',
        'currentClassName' => 'Stripe\\BaseStripeClient',
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
            'startLine' => 177,
            'endLine' => 177,
            'startColumn' => 35,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'path' => 
          array (
            'name' => 'path',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 177,
            'endLine' => 177,
            'startColumn' => 44,
            'endColumn' => 48,
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
            'startLine' => 177,
            'endLine' => 177,
            'startColumn' => 51,
            'endColumn' => 72,
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
            'startLine' => 177,
            'endLine' => 177,
            'startColumn' => 75,
            'endColumn' => 81,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'opts' => 
          array (
            'name' => 'opts',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 177,
            'endLine' => 177,
            'startColumn' => 84,
            'endColumn' => 88,
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
 * Sends a request to Stripe\'s API, passing chunks of the streamed response
 * into a user-provided $readBodyChunkCallable callback.
 *
 * @param \'delete\'|\'get\'|\'post\' $method the HTTP method
 * @param string $path the path of the request
 * @param callable $readBodyChunkCallable a function that will be called
 * @param array $params the parameters of the request
 * @param array|\\Stripe\\Util\\RequestOptions $opts the special modifiers of the request
 * with chunks of bytes from the body if the request is successful
 */',
        'startLine' => 177,
        'endLine' => 183,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\BaseStripeClient',
        'implementingClassName' => 'Stripe\\BaseStripeClient',
        'currentClassName' => 'Stripe\\BaseStripeClient',
        'aliasName' => NULL,
      ),
      'requestCollection' => 
      array (
        'name' => 'requestCollection',
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
            'startLine' => 195,
            'endLine' => 195,
            'startColumn' => 39,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'path' => 
          array (
            'name' => 'path',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 195,
            'endLine' => 195,
            'startColumn' => 48,
            'endColumn' => 52,
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
            'startLine' => 195,
            'endLine' => 195,
            'startColumn' => 55,
            'endColumn' => 61,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'opts' => 
          array (
            'name' => 'opts',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 195,
            'endLine' => 195,
            'startColumn' => 64,
            'endColumn' => 68,
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
 * Sends a request to Stripe\'s API.
 *
 * @param \'delete\'|\'get\'|\'post\' $method the HTTP method
 * @param string $path the path of the request
 * @param array $params the parameters of the request
 * @param array|\\Stripe\\Util\\RequestOptions $opts the special modifiers of the request
 *
 * @return \\Stripe\\Collection of ApiResources
 */',
        'startLine' => 195,
        'endLine' => 207,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\BaseStripeClient',
        'implementingClassName' => 'Stripe\\BaseStripeClient',
        'currentClassName' => 'Stripe\\BaseStripeClient',
        'aliasName' => NULL,
      ),
      'requestSearchResult' => 
      array (
        'name' => 'requestSearchResult',
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
            'startLine' => 219,
            'endLine' => 219,
            'startColumn' => 41,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'path' => 
          array (
            'name' => 'path',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 219,
            'endLine' => 219,
            'startColumn' => 50,
            'endColumn' => 54,
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
            'startLine' => 219,
            'endLine' => 219,
            'startColumn' => 57,
            'endColumn' => 63,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'opts' => 
          array (
            'name' => 'opts',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 219,
            'endLine' => 219,
            'startColumn' => 66,
            'endColumn' => 70,
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
 * Sends a request to Stripe\'s API.
 *
 * @param \'delete\'|\'get\'|\'post\' $method the HTTP method
 * @param string $path the path of the request
 * @param array $params the parameters of the request
 * @param array|\\Stripe\\Util\\RequestOptions $opts the special modifiers of the request
 *
 * @return \\Stripe\\SearchResult of ApiResources
 */',
        'startLine' => 219,
        'endLine' => 231,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\BaseStripeClient',
        'implementingClassName' => 'Stripe\\BaseStripeClient',
        'currentClassName' => 'Stripe\\BaseStripeClient',
        'aliasName' => NULL,
      ),
      'apiKeyForRequest' => 
      array (
        'name' => 'apiKeyForRequest',
        'parameters' => 
        array (
          'opts' => 
          array (
            'name' => 'opts',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 240,
            'endLine' => 240,
            'startColumn' => 39,
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
 * @param \\Stripe\\Util\\RequestOptions $opts
 *
 * @throws \\Stripe\\Exception\\AuthenticationException
 *
 * @return string
 */',
        'startLine' => 240,
        'endLine' => 253,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\BaseStripeClient',
        'implementingClassName' => 'Stripe\\BaseStripeClient',
        'currentClassName' => 'Stripe\\BaseStripeClient',
        'aliasName' => NULL,
      ),
      'validateConfig' => 
      array (
        'name' => 'validateConfig',
        'parameters' => 
        array (
          'config' => 
          array (
            'name' => 'config',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 260,
            'endLine' => 260,
            'startColumn' => 37,
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
 * @param array<string, mixed> $config
 *
 * @throws \\Stripe\\Exception\\InvalidArgumentException
 */',
        'startLine' => 260,
        'endLine' => 329,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\BaseStripeClient',
        'implementingClassName' => 'Stripe\\BaseStripeClient',
        'currentClassName' => 'Stripe\\BaseStripeClient',
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