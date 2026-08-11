<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-header
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'name' => 'header',
    'parameters' => 
    array (
      'header' => 
      array (
        'name' => 'header',
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
        'startLine' => 60,
        'endLine' => 60,
        'startColumn' => 21,
        'endColumn' => 34,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'replace' => 
      array (
        'name' => 'replace',
        'default' => 
        array (
          'code' => '\\true',
          'attributes' => 
          array (
            'startLine' => 60,
            'endLine' => 60,
            'startTokenPos' => 23,
            'startFilePos' => 2314,
            'endTokenPos' => 23,
            'endFilePos' => 2317,
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
        ),
        'startLine' => 60,
        'endLine' => 60,
        'startColumn' => 37,
        'endColumn' => 56,
        'parameterIndex' => 1,
        'isOptional' => true,
      ),
      'response_code' => 
      array (
        'name' => 'response_code',
        'default' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 60,
            'endLine' => 60,
            'startTokenPos' => 32,
            'startFilePos' => 2341,
            'endTokenPos' => 32,
            'endFilePos' => 2341,
          ),
        ),
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
        ),
        'startLine' => 60,
        'endLine' => 60,
        'startColumn' => 59,
        'endColumn' => 80,
        'parameterIndex' => 2,
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
    ),
    'docComment' => '/**
 * Send a raw HTTP header
 * @link https://php.net/manual/en/function.header.php
 * @param string $header <p>
 * The header string.
 * </p>
 * <p>
 * There are two special-case header calls. The first is a header that starts with the string "HTTP/"
 * (case is not significant), which will be used to figure out the HTTP status code to send. For example,
 * if you have configured Apache to use a PHP script to handle requests for missing files (using the ErrorDocument directive),
 * you may want to make sure that your script generates the proper status code.
 * </p>
 * <p>
 * Example:
 * <code>
 * <?php
 * // This example illustrates the "HTTP/" special case
 * // Better alternatives in typical use cases include:
 * // 1. header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
 * //    (to override http status messages for clients that are still using HTTP/1.0)
 * // 2. http_response_code(404); (to use the default message)
 * header("HTTP/1.1 404 Not Found");
 * ?>
 * </code>
 * </p>
 * <p>
 * The second special case is the "Location:" header. Not only does
 * it send this header back to the browser, but it also returns a
 * REDIRECT (302) status code to the browser
 * unless the 201 or
 * a 3xx status code has already been set.
 * </p>
 * <p>Example</p>
 * <code>
 * header("Location: http://www.example.com/");
 * exit;
 * </code>
 * @param bool $replace [optional] <p>
 * The optional replace parameter indicates
 * whether the header should replace a previous similar header, or
 * add a second header of the same type. By default it will replace,
 * but if you pass in false as the second argument you can force
 * multiple headers of the same type.
 * </p>
 * <p>For example:</p>
 * <code>
 * <?php
 * header(\'WWW-Authenticate: Negotiate\');
 * header(\'WWW-Authenticate: NTLM\', false);
 * ?>
 * </code>
 * @param int $response_code <p>
 * Forces the HTTP response code to the specified value. Note that this parameter only has an effect if the header is not empty.
 * </p>
 * @return void
 */',
    'startLine' => 60,
    'endLine' => 62,
    'startColumn' => 5,
    'endColumn' => 5,
    'couldThrow' => false,
    'isClosure' => false,
    'isGenerator' => false,
    'isVariadic' => false,
    'isStatic' => false,
    'namespace' => NULL,
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\InternalLocatedSource',
      'data' => 
      array (
        'name' => 'header',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/standard/standard_4.stub',
        'extensionName' => 'standard',
        'aliasName' => NULL,
      ),
    ),
  ),
));