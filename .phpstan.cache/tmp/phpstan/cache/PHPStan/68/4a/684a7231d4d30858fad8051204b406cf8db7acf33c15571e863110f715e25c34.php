<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-explode
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'name' => 'explode',
    'parameters' => 
    array (
      'separator' => 
      array (
        'name' => 'separator',
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
        'startLine' => 35,
        'endLine' => 35,
        'startColumn' => 22,
        'endColumn' => 38,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'string' => 
      array (
        'name' => 'string',
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
        'startLine' => 35,
        'endLine' => 35,
        'startColumn' => 41,
        'endColumn' => 54,
        'parameterIndex' => 1,
        'isOptional' => false,
      ),
      'limit' => 
      array (
        'name' => 'limit',
        'default' => 
        array (
          'code' => '\\PHP_INT_MAX',
          'attributes' => 
          array (
            'startLine' => 35,
            'endLine' => 35,
            'startTokenPos' => 51,
            'startFilePos' => 1248,
            'endTokenPos' => 51,
            'endFilePos' => 1258,
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
        'startLine' => 35,
        'endLine' => 35,
        'startColumn' => 57,
        'endColumn' => 80,
        'parameterIndex' => 2,
        'isOptional' => true,
      ),
    ),
    'returnsReference' => false,
    'returnType' => NULL,
    'attributes' => 
    array (
      0 => 
      array (
        'name' => 'JetBrains\\PhpStorm\\Pure',
        'isRepeated' => false,
        'arguments' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
        'isRepeated' => false,
        'arguments' => 
        array (
          0 => 
          array (
            'code' => '["8.0" => "string[]"]',
            'attributes' => 
            array (
              'startLine' => 34,
              'endLine' => 34,
              'startTokenPos' => 15,
              'startFilePos' => 1128,
              'endTokenPos' => 21,
              'endFilePos' => 1148,
            ),
          ),
          'default' => 
          array (
            'code' => '"string[]|false"',
            'attributes' => 
            array (
              'startLine' => 34,
              'endLine' => 34,
              'startTokenPos' => 27,
              'startFilePos' => 1160,
              'endTokenPos' => 27,
              'endFilePos' => 1175,
            ),
          ),
        ),
      ),
    ),
    'docComment' => '/**
 * Split a string by a string
 * @link https://php.net/manual/en/function.explode.php
 * @param string $separator <p>
 * The boundary string.
 * </p>
 * @param string $string <p>
 * The input string.
 * </p>
 * @param int $limit [optional] <p>
 * If limit is set and positive, the returned array will contain
 * a maximum of limit elements with the last
 * element containing the rest of string.
 * </p>
 * <p>
 * If the limit parameter is negative, all components
 * except the last -limit are returned.
 * </p>
 * <p>
 * If the limit parameter is zero, then this is treated as 1.
 * </p>
 * @return string[]|false If separator is an empty string (""),
 * explode will return false.
 * If separator contains a value that is not
 * contained in string and a negative
 * limit is used, then an empty array will be
 * returned. For any other limit, an array containing
 * string will be returned.
 */',
    'startLine' => 33,
    'endLine' => 37,
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
        'name' => 'explode',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/standard/standard_1.stub',
        'extensionName' => 'standard',
        'aliasName' => NULL,
      ),
    ),
  ),
));