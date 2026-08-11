<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-json_encode
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'name' => 'json_encode',
    'parameters' => 
    array (
      'value' => 
      array (
        'name' => 'value',
        'default' => NULL,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'mixed',
            'isIdentifier' => true,
          ),
        ),
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 38,
        'endLine' => 38,
        'startColumn' => 26,
        'endColumn' => 37,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'flags' => 
      array (
        'name' => 'flags',
        'default' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 38,
            'endLine' => 38,
            'startTokenPos' => 23,
            'startFilePos' => 1362,
            'endTokenPos' => 23,
            'endFilePos' => 1362,
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
        'startLine' => 38,
        'endLine' => 38,
        'startColumn' => 40,
        'endColumn' => 53,
        'parameterIndex' => 1,
        'isOptional' => true,
      ),
      'depth' => 
      array (
        'name' => 'depth',
        'default' => 
        array (
          'code' => '512',
          'attributes' => 
          array (
            'startLine' => 38,
            'endLine' => 38,
            'startTokenPos' => 32,
            'startFilePos' => 1378,
            'endTokenPos' => 32,
            'endFilePos' => 1380,
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
        'startLine' => 38,
        'endLine' => 38,
        'startColumn' => 56,
        'endColumn' => 71,
        'parameterIndex' => 2,
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
              'name' => 'false',
              'isIdentifier' => true,
            ),
          ),
        ),
      ),
    ),
    'attributes' => 
    array (
    ),
    'docComment' => '/**
 * (PHP 5 &gt;= 5.2.0, PECL json &gt;= 1.2.0)<br/>
 * Returns the JSON representation of a value
 * @link https://php.net/manual/en/function.json-encode.php
 * @param mixed $value <p>
 * The <i>value</i> being encoded. Can be any type except
 * a resource.
 * </p>
 * <p>
 * All string data must be UTF-8 encoded.
 * </p>
 * <p>PHP implements a superset of
 * JSON - it will also encode and decode scalar types and <b>NULL</b>. The JSON standard
 * only supports these values when they are nested inside an array or an object.
 * </p>
 * @param int $flags [optional] <p>
 * Bitmask consisting of <b>JSON_HEX_QUOT</b>,
 * <b>JSON_HEX_TAG</b>,
 * <b>JSON_HEX_AMP</b>,
 * <b>JSON_HEX_APOS</b>,
 * <b>JSON_NUMERIC_CHECK</b>,
 * <b>JSON_PRETTY_PRINT</b>,
 * <b>JSON_UNESCAPED_SLASHES</b>,
 * <b>JSON_FORCE_OBJECT</b>,
 * <b>JSON_UNESCAPED_UNICODE</b>.
 * <b>JSON_THROW_ON_ERROR</b> The behaviour of these
 * constants is described on
 * the JSON constants page.
 * </p>
 * @param int $depth [optional] <p>
 * Set the maximum depth. Must be greater than zero.
 * </p>
 * @return string|false a JSON encoded string on success or <b>FALSE</b> on failure.
 */',
    'startLine' => 38,
    'endLine' => 40,
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
        'name' => 'json_encode',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/json/json.stub',
        'extensionName' => 'json',
        'aliasName' => NULL,
      ),
    ),
  ),
));