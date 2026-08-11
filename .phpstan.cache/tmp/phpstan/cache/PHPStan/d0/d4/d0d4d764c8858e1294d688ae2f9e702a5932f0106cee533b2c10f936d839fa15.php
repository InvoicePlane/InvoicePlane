<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionClass-soapclient
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\InternalLocatedSource',
      'data' => 
      array (
        'name' => 'SoapClient',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/soap/soap.stub',
        'extensionName' => 'soap',
        'aliasName' => NULL,
      ),
    ),
    'namespace' => NULL,
    'name' => 'SoapClient',
    'shortName' => 'SoapClient',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * The SoapClient class provides a client for SOAP 1.1, SOAP 1.2 servers. It can be used in WSDL
 * or non-WSDL mode.
 * @link https://php.net/manual/en/class.soapclient.php
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 9,
    'endLine' => 393,
    'startColumn' => 5,
    'endColumn' => 5,
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
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
          'wsdl' => 
          array (
            'name' => 'wsdl',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '[\'8.0\' => \'string|null\']',
                    'attributes' => 
                    array (
                      'startLine' => 136,
                      'endLine' => 136,
                      'startTokenPos' => 26,
                      'startFilePos' => 5397,
                      'endTokenPos' => 32,
                      'endFilePos' => 5420,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 136,
                      'endLine' => 136,
                      'startTokenPos' => 38,
                      'startFilePos' => 5432,
                      'endTokenPos' => 38,
                      'endFilePos' => 5433,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 136,
            'endLine' => 137,
            'startColumn' => 13,
            'endColumn' => 29,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 138,
                'endLine' => 138,
                'startTokenPos' => 55,
                'startFilePos' => 5497,
                'endTokenPos' => 56,
                'endFilePos' => 5498,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 138,
            'endLine' => 138,
            'startColumn' => 13,
            'endColumn' => 31,
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
 * SoapClient constructor
 * @link https://php.net/manual/en/soapclient.construct.php
 * @param string|null $wsdl <p>
 * URI of the WSDL file or <b>NULL</b> if working in
 * non-WSDL mode.
 * </p>
 * <p>
 * During development, WSDL caching may be disabled by the
 * use of the soap.wsdl_cache_ttl <i>php.ini</i> setting
 * otherwise changes made to the WSDL file will have no effect until
 * soap.wsdl_cache_ttl is expired.
 * </p>
 * @param array $options [optional] <p>
 * An array of options. If working in WSDL mode, this parameter is optional.
 * If working in non-WSDL mode, the location and
 * uri options must be set, where location
 * is the URL of the SOAP server to send the request to, and uri
 * is the target namespace of the SOAP service.
 * </p>
 * <p>
 * The style and use options only work in
 * non-WSDL mode. In WSDL mode, they come from the WSDL file.
 * </p>
 * <p>
 * The soap_version option should be one of either
 * <b>SOAP_1_1</b> or <b>SOAP_1_2</b> to
 * select SOAP 1.1 or 1.2, respectively. If omitted, 1.1 is used.
 * </p>
 * <p>
 * For HTTP authentication, the login and
 * password options can be used to supply credentials.
 * For making an HTTP connection through
 * a proxy server, the options proxy_host,
 * proxy_port, proxy_login
 * and proxy_password are also available.
 * For HTTPS client certificate authentication use
 * local_cert and passphrase options. An
 * authentication may be supplied in the authentication
 * option. The authentication method may be either
 * <b>SOAP_AUTHENTICATION_BASIC</b> (default) or
 * <b>SOAP_AUTHENTICATION_DIGEST</b>.
 * </p>
 * <p>
 * The compression option allows to use compression
 * of HTTP SOAP requests and responses.
 * </p>
 * <p>
 * The encoding option defines internal character
 * encoding. This option does not change the encoding of SOAP requests (it is
 * always utf-8), but converts strings into it.
 * </p>
 * <p>
 * The trace option enables tracing of request so faults
 * can be backtraced. This defaults to <b>FALSE</b>
 * </p>
 * <p>
 * The classmap option can be used to map some WSDL
 * types to PHP classes. This option must be an array with WSDL types
 * as keys and names of PHP classes as values.
 * </p>
 * <p>
 * Setting the boolean trace option enables use of the
 * methods
 * SoapClient->__getLastRequest,
 * SoapClient->__getLastRequestHeaders,
 * SoapClient->__getLastResponse and
 * SoapClient->__getLastResponseHeaders.
 * </p>
 * <p>
 * The exceptions option is a boolean value defining whether
 * soap errors throw exceptions of type
 * SoapFault.
 * </p>
 * <p>
 * The connection_timeout option defines a timeout in seconds
 * for the connection to the SOAP service. This option does not define a timeout
 * for services with slow responses. To limit the time to wait for calls to finish the
 * default_socket_timeout setting
 * is available.
 * </p>
 * <p>
 * The typemap option is an array of type mappings.
 * Type mapping is an array with keys type_name,
 * type_ns (namespace URI), from_xml
 * (callback accepting one string parameter) and to_xml
 * (callback accepting one object parameter).
 * </p>
 * <p>
 * The cache_wsdl option is one of
 * <b>WSDL_CACHE_NONE</b>,
 * <b>WSDL_CACHE_DISK</b>,
 * <b>WSDL_CACHE_MEMORY</b> or
 * <b>WSDL_CACHE_BOTH</b>.
 * </p>
 * <p>
 * The user_agent option specifies string to use in
 * User-Agent header.
 * </p>
 * <p>
 * The stream_context option is a resource
 * for context.
 * </p>
 * <p>
 * The features option is a bitmask of
 * <b>SOAP_SINGLE_ELEMENT_ARRAYS</b>,
 * <b>SOAP_USE_XSI_ARRAY_TYPE</b>,
 * <b>SOAP_WAIT_ONE_WAY_CALLS</b>.
 * </p>
 * <p>
 * The keep_alive option is a boolean value defining whether
 * to send the Connection: Keep-Alive header or
 * Connection: close.
 * </p>
 * <p>
 * The ssl_method option is one of
 * <b>SOAP_SSL_METHOD_TLS</b>,
 * <b>SOAP_SSL_METHOD_SSLv2</b>,
 * <b>SOAP_SSL_METHOD_SSLv3</b> or
 * <b>SOAP_SSL_METHOD_SSLv23</b>.
 * </p>
 * @throws SoapFault A SoapFault exception will be thrown if the wsdl URI cannot be loaded.
 * @since 5.0
 */',
        'startLine' => 135,
        'endLine' => 141,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'SoapClient',
        'implementingClassName' => 'SoapClient',
        'currentClassName' => 'SoapClient',
        'aliasName' => NULL,
      ),
      '__call' => 
      array (
        'name' => '__call',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
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
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '[\'8.0\' => \'string\']',
                    'attributes' => 
                    array (
                      'startLine' => 154,
                      'endLine' => 154,
                      'startTokenPos' => 84,
                      'startFilePos' => 5990,
                      'endTokenPos' => 90,
                      'endFilePos' => 6008,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 154,
                      'endLine' => 154,
                      'startTokenPos' => 96,
                      'startFilePos' => 6020,
                      'endTokenPos' => 96,
                      'endFilePos' => 6021,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 154,
            'endLine' => 155,
            'startColumn' => 13,
            'endColumn' => 24,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'args' => 
          array (
            'name' => 'args',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 156,
            'endLine' => 156,
            'startColumn' => 13,
            'endColumn' => 23,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'mixed',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Deprecated',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
          1 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * @link https://php.net/manual/en/soapclient.call.php
 * @param string $name
 * @param array $args
 * @return mixed
 * @since 5.0
 * @betterReflectionTentativeReturnType
 * @deprecated
 */',
        'startLine' => 151,
        'endLine' => 159,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'SoapClient',
        'implementingClassName' => 'SoapClient',
        'currentClassName' => 'SoapClient',
        'aliasName' => NULL,
      ),
      '__soapCall' => 
      array (
        'name' => '__soapCall',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
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
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '[\'8.0\' => \'string\']',
                    'attributes' => 
                    array (
                      'startLine' => 209,
                      'endLine' => 209,
                      'startTokenPos' => 134,
                      'startFilePos' => 8487,
                      'endTokenPos' => 140,
                      'endFilePos' => 8505,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 209,
                      'endLine' => 209,
                      'startTokenPos' => 146,
                      'startFilePos' => 8517,
                      'endTokenPos' => 146,
                      'endFilePos' => 8518,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 209,
            'endLine' => 210,
            'startColumn' => 13,
            'endColumn' => 24,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'args' => 
          array (
            'name' => 'args',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 211,
            'endLine' => 211,
            'startColumn' => 13,
            'endColumn' => 23,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => '\\null',
              'attributes' => 
              array (
                'startLine' => 213,
                'endLine' => 213,
                'startTokenPos' => 187,
                'startFilePos' => 8712,
                'endTokenPos' => 187,
                'endFilePos' => 8715,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'array',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '[\'8.0\' => \'array|null\']',
                    'attributes' => 
                    array (
                      'startLine' => 212,
                      'endLine' => 212,
                      'startTokenPos' => 163,
                      'startFilePos' => 8639,
                      'endTokenPos' => 169,
                      'endFilePos' => 8661,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 212,
                      'endLine' => 212,
                      'startTokenPos' => 175,
                      'startFilePos' => 8673,
                      'endTokenPos' => 175,
                      'endFilePos' => 8674,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 212,
            'endLine' => 213,
            'startColumn' => 13,
            'endColumn' => 38,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'inputHeaders' => 
          array (
            'name' => 'inputHeaders',
            'default' => 
            array (
              'code' => '\\null',
              'attributes' => 
              array (
                'startLine' => 214,
                'endLine' => 214,
                'startTokenPos' => 194,
                'startFilePos' => 8746,
                'endTokenPos' => 194,
                'endFilePos' => 8749,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 214,
            'endLine' => 214,
            'startColumn' => 13,
            'endColumn' => 32,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
          'outputHeaders' => 
          array (
            'name' => 'outputHeaders',
            'default' => 
            array (
              'code' => '\\null',
              'attributes' => 
              array (
                'startLine' => 215,
                'endLine' => 215,
                'startTokenPos' => 202,
                'startFilePos' => 8782,
                'endTokenPos' => 202,
                'endFilePos' => 8785,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => true,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 215,
            'endLine' => 215,
            'startColumn' => 13,
            'endColumn' => 34,
            'parameterIndex' => 4,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'mixed',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Calls a SOAP function
 * @link https://php.net/manual/en/soapclient.soapcall.php
 * @param string $name <p>
 * The name of the SOAP function to call.
 * </p>
 * @param array $args <p>
 * An array of the arguments to pass to the function. This can be either
 * an ordered or an associative array. Note that most SOAP servers require
 * parameter names to be provided, in which case this must be an
 * associative array.
 * </p>
 * @param array $options [optional] <p>
 * An associative array of options to pass to the client.
 * </p>
 * <p>
 * The location option is the URL of the remote Web service.
 * </p>
 * <p>
 * The uri option is the target namespace of the SOAP service.
 * </p>
 * <p>
 * The soapaction option is the action to call.
 * </p>
 * @param mixed $inputHeaders [optional] <p>
 * An array of headers to be sent along with the SOAP request.
 * </p>
 * @param array &$outputHeaders [optional] <p>
 * If supplied, this array will be filled with the headers from the SOAP response.
 * </p>
 * @return mixed SOAP functions may return one, or multiple values. If only one value is returned
 * by the SOAP function, the return value of __soapCall will be
 * a simple value (e.g. an integer, a string, etc). If multiple values are
 * returned, __soapCall will return
 * an associative array of named output parameters.
 * </p>
 * <p>
 * On error, if the SoapClient object was constructed with the exceptions
 * option set to <b>FALSE</b>, a SoapFault object will be returned. If this
 * option is not set, or is set to <b>TRUE</b>, then a SoapFault object will
 * be thrown as an exception.
 * @throws SoapFault A SoapFault exception will be thrown if an error occurs
 * and the SoapClient was constructed with the exceptions option not set, or
 * set to <b>TRUE</b>.
 * @since 5.0
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 207,
        'endLine' => 218,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'SoapClient',
        'implementingClassName' => 'SoapClient',
        'currentClassName' => 'SoapClient',
        'aliasName' => NULL,
      ),
      '__getLastRequest' => 
      array (
        'name' => '__getLastRequest',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'string',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Returns last SOAP request
 * @link https://php.net/manual/en/soapclient.getlastrequest.php
 * @return string|null The last SOAP request, as an XML string.
 * @since 5.0
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 226,
        'endLine' => 229,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'SoapClient',
        'implementingClassName' => 'SoapClient',
        'currentClassName' => 'SoapClient',
        'aliasName' => NULL,
      ),
      '__getLastResponse' => 
      array (
        'name' => '__getLastResponse',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'string',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Returns last SOAP response
 * @link https://php.net/manual/en/soapclient.getlastresponse.php
 * @return string|null The last SOAP response, as an XML string.
 * @since 5.0
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 237,
        'endLine' => 240,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'SoapClient',
        'implementingClassName' => 'SoapClient',
        'currentClassName' => 'SoapClient',
        'aliasName' => NULL,
      ),
      '__getLastRequestHeaders' => 
      array (
        'name' => '__getLastRequestHeaders',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'string',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Returns the SOAP headers from the last request
 * @link https://php.net/manual/en/soapclient.getlastrequestheaders.php
 * @return string|null The last SOAP request headers.
 * @since 5.0
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 248,
        'endLine' => 251,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'SoapClient',
        'implementingClassName' => 'SoapClient',
        'currentClassName' => 'SoapClient',
        'aliasName' => NULL,
      ),
      '__getLastResponseHeaders' => 
      array (
        'name' => '__getLastResponseHeaders',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'string',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Returns the SOAP headers from the last response
 * @link https://php.net/manual/en/soapclient.getlastresponseheaders.php
 * @return string|null The last SOAP response headers.
 * @since 5.0
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 259,
        'endLine' => 262,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'SoapClient',
        'implementingClassName' => 'SoapClient',
        'currentClassName' => 'SoapClient',
        'aliasName' => NULL,
      ),
      '__getFunctions' => 
      array (
        'name' => '__getFunctions',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'array',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Returns list of available SOAP functions
 * @link https://php.net/manual/en/soapclient.getfunctions.php
 * @return array|null The array of SOAP function prototypes, detailing the return type,
 * the function name and type-hinted parameters.
 * @since 5.0
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 271,
        'endLine' => 274,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'SoapClient',
        'implementingClassName' => 'SoapClient',
        'currentClassName' => 'SoapClient',
        'aliasName' => NULL,
      ),
      '__getTypes' => 
      array (
        'name' => '__getTypes',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'array',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Returns a list of SOAP types
 * @link https://php.net/manual/en/soapclient.gettypes.php
 * @return array|null The array of SOAP types, detailing all structures and types.
 * @since 5.0
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 282,
        'endLine' => 285,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'SoapClient',
        'implementingClassName' => 'SoapClient',
        'currentClassName' => 'SoapClient',
        'aliasName' => NULL,
      ),
      '__getCookies' => 
      array (
        'name' => '__getCookies',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Returns a list of all cookies
 * @link https://php.net/manual/en/soapclient.getcookies.php
 * @return array The array of all cookies
 * @since 5.4
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 293,
        'endLine' => 296,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'SoapClient',
        'implementingClassName' => 'SoapClient',
        'currentClassName' => 'SoapClient',
        'aliasName' => NULL,
      ),
      '__doRequest' => 
      array (
        'name' => '__doRequest',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
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
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '[\'8.0\' => \'string\']',
                    'attributes' => 
                    array (
                      'startLine' => 322,
                      'endLine' => 322,
                      'startTokenPos' => 382,
                      'startFilePos' => 12694,
                      'endTokenPos' => 388,
                      'endFilePos' => 12712,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 322,
                      'endLine' => 322,
                      'startTokenPos' => 394,
                      'startFilePos' => 12724,
                      'endTokenPos' => 394,
                      'endFilePos' => 12725,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 322,
            'endLine' => 323,
            'startColumn' => 13,
            'endColumn' => 27,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'location' => 
          array (
            'name' => 'location',
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
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '[\'8.0\' => \'string\']',
                    'attributes' => 
                    array (
                      'startLine' => 324,
                      'endLine' => 324,
                      'startTokenPos' => 406,
                      'startFilePos' => 12824,
                      'endTokenPos' => 412,
                      'endFilePos' => 12842,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 324,
                      'endLine' => 324,
                      'startTokenPos' => 418,
                      'startFilePos' => 12854,
                      'endTokenPos' => 418,
                      'endFilePos' => 12855,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 324,
            'endLine' => 325,
            'startColumn' => 13,
            'endColumn' => 28,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'action' => 
          array (
            'name' => 'action',
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
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '[\'8.0\' => \'string\']',
                    'attributes' => 
                    array (
                      'startLine' => 326,
                      'endLine' => 326,
                      'startTokenPos' => 430,
                      'startFilePos' => 12955,
                      'endTokenPos' => 436,
                      'endFilePos' => 12973,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 326,
                      'endLine' => 326,
                      'startTokenPos' => 442,
                      'startFilePos' => 12985,
                      'endTokenPos' => 442,
                      'endFilePos' => 12986,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 326,
            'endLine' => 327,
            'startColumn' => 13,
            'endColumn' => 26,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'version' => 
          array (
            'name' => 'version',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '[\'8.0\' => \'int\']',
                    'attributes' => 
                    array (
                      'startLine' => 328,
                      'endLine' => 328,
                      'startTokenPos' => 454,
                      'startFilePos' => 13084,
                      'endTokenPos' => 460,
                      'endFilePos' => 13099,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 328,
                      'endLine' => 328,
                      'startTokenPos' => 466,
                      'startFilePos' => 13111,
                      'endTokenPos' => 466,
                      'endFilePos' => 13112,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 328,
            'endLine' => 329,
            'startColumn' => 13,
            'endColumn' => 24,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'oneWay' => 
          array (
            'name' => 'oneWay',
            'default' => 
            array (
              'code' => '\\false',
              'attributes' => 
              array (
                'startLine' => 331,
                'endLine' => 331,
                'startTokenPos' => 500,
                'startFilePos' => 13271,
                'endTokenPos' => 500,
                'endFilePos' => 13275,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'bool',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '["8.0" => \'bool\']',
                    'attributes' => 
                    array (
                      'startLine' => 330,
                      'endLine' => 330,
                      'startTokenPos' => 478,
                      'startFilePos' => 13208,
                      'endTokenPos' => 484,
                      'endFilePos' => 13224,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'int\'',
                    'attributes' => 
                    array (
                      'startLine' => 330,
                      'endLine' => 330,
                      'startTokenPos' => 490,
                      'startFilePos' => 13236,
                      'endTokenPos' => 490,
                      'endFilePos' => 13240,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 330,
            'endLine' => 331,
            'startColumn' => 13,
            'endColumn' => 32,
            'parameterIndex' => 4,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'string',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Performs a SOAP request
 * @link https://php.net/manual/en/soapclient.dorequest.php
 * @param string $request <p>
 * The XML SOAP request.
 * </p>
 * @param string $location <p>
 * The URL to request.
 * </p>
 * @param string $action <p>
 * The SOAP action.
 * </p>
 * @param int $version <p>
 * The SOAP version.
 * </p>
 * @param bool|int $oneWay [optional] <p>
 * If $oneWay is set to 1, this method returns nothing.
 * Use this where a response is not expected.
 * </p>
 * @return string|null The XML SOAP response.
 * @since 5.0
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 320,
        'endLine' => 334,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'SoapClient',
        'implementingClassName' => 'SoapClient',
        'currentClassName' => 'SoapClient',
        'aliasName' => NULL,
      ),
      '__setCookie' => 
      array (
        'name' => '__setCookie',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
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
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '[\'8.0\' => \'string\']',
                    'attributes' => 
                    array (
                      'startLine' => 350,
                      'endLine' => 350,
                      'startTokenPos' => 528,
                      'startFilePos' => 13947,
                      'endTokenPos' => 534,
                      'endFilePos' => 13965,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 350,
                      'endLine' => 350,
                      'startTokenPos' => 540,
                      'startFilePos' => 13977,
                      'endTokenPos' => 540,
                      'endFilePos' => 13978,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 350,
            'endLine' => 351,
            'startColumn' => 13,
            'endColumn' => 24,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'value' => 
          array (
            'name' => 'value',
            'default' => 
            array (
              'code' => '\\null',
              'attributes' => 
              array (
                'startLine' => 353,
                'endLine' => 353,
                'startTokenPos' => 576,
                'startFilePos' => 14153,
                'endTokenPos' => 576,
                'endFilePos' => 14156,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '["8.0" => "string|null"]',
                    'attributes' => 
                    array (
                      'startLine' => 352,
                      'endLine' => 352,
                      'startTokenPos' => 552,
                      'startFilePos' => 14074,
                      'endTokenPos' => 558,
                      'endFilePos' => 14097,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '"string"',
                    'attributes' => 
                    array (
                      'startLine' => 352,
                      'endLine' => 352,
                      'startTokenPos' => 564,
                      'startFilePos' => 14109,
                      'endTokenPos' => 564,
                      'endFilePos' => 14116,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 352,
            'endLine' => 353,
            'startColumn' => 13,
            'endColumn' => 37,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
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
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * The __setCookie purpose
 * @link https://php.net/manual/en/soapclient.setcookie.php
 * @param string $name <p>
 * The name of the cookie.
 * </p>
 * @param string $value [optional] <p>
 * The value of the cookie. If not specified, the cookie will be deleted.
 * </p>
 * @return void No value is returned.
 * @since 5.0
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 348,
        'endLine' => 356,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'SoapClient',
        'implementingClassName' => 'SoapClient',
        'currentClassName' => 'SoapClient',
        'aliasName' => NULL,
      ),
      '__setLocation' => 
      array (
        'name' => '__setLocation',
        'parameters' => 
        array (
          'location' => 
          array (
            'name' => 'location',
            'default' => 
            array (
              'code' => '\\null',
              'attributes' => 
              array (
                'startLine' => 370,
                'endLine' => 370,
                'startTokenPos' => 627,
                'startFilePos' => 14800,
                'endTokenPos' => 627,
                'endFilePos' => 14803,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '[\'8.0\' => \'string|null\']',
                    'attributes' => 
                    array (
                      'startLine' => 369,
                      'endLine' => 369,
                      'startTokenPos' => 603,
                      'startFilePos' => 14724,
                      'endTokenPos' => 609,
                      'endFilePos' => 14747,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 369,
                      'endLine' => 369,
                      'startTokenPos' => 615,
                      'startFilePos' => 14759,
                      'endTokenPos' => 615,
                      'endFilePos' => 14760,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 369,
            'endLine' => 370,
            'startColumn' => 13,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'string',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Sets the location of the Web service to use
 * @link https://php.net/manual/en/soapclient.setlocation.php
 * @param string $location [optional] <p>
 * The new endpoint URL.
 * </p>
 * @return string|null The old endpoint URL.
 * @since 5.0
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 367,
        'endLine' => 373,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'SoapClient',
        'implementingClassName' => 'SoapClient',
        'currentClassName' => 'SoapClient',
        'aliasName' => NULL,
      ),
      '__setSoapHeaders' => 
      array (
        'name' => '__setSoapHeaders',
        'parameters' => 
        array (
          'headers' => 
          array (
            'name' => 'headers',
            'default' => 
            array (
              'code' => '\\null',
              'attributes' => 
              array (
                'startLine' => 389,
                'endLine' => 389,
                'startTokenPos' => 666,
                'startFilePos' => 15596,
                'endTokenPos' => 666,
                'endFilePos' => 15599,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\PhpStormStubsElementAvailable',
                'isRepeated' => false,
                'arguments' => 
                array (
                  'from' => 
                  array (
                    'code' => '\'7.0\'',
                    'attributes' => 
                    array (
                      'startLine' => 388,
                      'endLine' => 388,
                      'startTokenPos' => 658,
                      'startFilePos' => 15565,
                      'endTokenPos' => 658,
                      'endFilePos' => 15569,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 388,
            'endLine' => 389,
            'startColumn' => 13,
            'endColumn' => 27,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Sets SOAP headers for subsequent calls
 * @link https://php.net/manual/en/soapclient.setsoapheaders.php
 * @param mixed $headers <p>
 * The headers to be set. It could be <b>SoapHeader</b>
 * object or array of <b>SoapHeader</b> objects.
 * If not specified or set to <b>NULL</b>, the headers will be deleted.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @since 5.0
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 386,
        'endLine' => 392,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'SoapClient',
        'implementingClassName' => 'SoapClient',
        'currentClassName' => 'SoapClient',
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