<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-gettype
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'name' => 'gettype',
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
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 22,
        'endColumn' => 33,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
    ),
    'returnsReference' => false,
    'returnType' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
      'data' => 
      array (
        'name' => 'string',
        'isIdentifier' => true,
      ),
    ),
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
        'name' => 'JetBrains\\PhpStorm\\ExpectedValues',
        'isRepeated' => false,
        'arguments' => 
        array (
          0 => 
          array (
            'code' => '["boolean", "integer", "double", "string", "array", "object", "resource", "NULL", "unknown type", "resource (closed)"]',
            'attributes' => 
            array (
              'startLine' => 25,
              'endLine' => 25,
              'startTokenPos' => 15,
              'startFilePos' => 646,
              'endTokenPos' => 44,
              'endFilePos' => 763,
            ),
          ),
        ),
      ),
    ),
    'docComment' => '/**
 * Get the type of a variable
 * @link https://php.net/manual/en/function.gettype.php
 * @param mixed $value <p>
 * The variable being type checked.
 * </p>
 * @return string Possibles values for the returned string are:
 * "boolean"
 * "integer"
 * "double" (for historical reasons "double" is
 * returned in case of a float, and not simply
 * "float")
 * "string"
 * "array"
 * "object"
 * "resource"
 * "NULL"
 * "unknown type"
 * "resource (closed)" since 7.2.0
 */',
    'startLine' => 24,
    'endLine' => 28,
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
        'name' => 'gettype',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/standard/standard_5.stub',
        'extensionName' => 'standard',
        'aliasName' => NULL,
      ),
    ),
  ),
));