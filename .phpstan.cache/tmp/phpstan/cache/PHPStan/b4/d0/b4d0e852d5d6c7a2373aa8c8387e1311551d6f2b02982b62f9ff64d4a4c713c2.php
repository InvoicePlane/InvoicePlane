<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-version_compare
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'name' => 'version_compare',
    'parameters' => 
    array (
      'version1' => 
      array (
        'name' => 'version1',
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
        'startLine' => 43,
        'endLine' => 43,
        'startColumn' => 9,
        'endColumn' => 24,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'version2' => 
      array (
        'name' => 'version2',
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
        'startLine' => 44,
        'endLine' => 44,
        'startColumn' => 9,
        'endColumn' => 24,
        'parameterIndex' => 1,
        'isOptional' => false,
      ),
      'operator' => 
      array (
        'name' => 'operator',
        'default' => 
        array (
          'code' => '\\null',
          'attributes' => 
          array (
            'startLine' => 46,
            'endLine' => 46,
            'startTokenPos' => 117,
            'startFilePos' => 1623,
            'endTokenPos' => 117,
            'endFilePos' => 1626,
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
            'name' => 'JetBrains\\PhpStorm\\ExpectedValues',
            'isRepeated' => false,
            'arguments' => 
            array (
              'values' => 
              array (
                'code' => '["<", "lt", "<=", "le", ">", "gt", ">=", "ge", "==", "=", "eq", "!=", "<>", "ne"]',
                'attributes' => 
                array (
                  'startLine' => 45,
                  'endLine' => 45,
                  'startTokenPos' => 65,
                  'startFilePos' => 1511,
                  'endTokenPos' => 106,
                  'endFilePos' => 1591,
                ),
              ),
            ),
          ),
        ),
        'startLine' => 45,
        'endLine' => 46,
        'startColumn' => 9,
        'endColumn' => 32,
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
              'name' => 'int',
              'isIdentifier' => true,
            ),
          ),
          1 => 
          array (
            'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
            'data' => 
            array (
              'name' => 'bool',
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
            'code' => '[-1, 0, 1, \\false, \\true]',
            'attributes' => 
            array (
              'startLine' => 40,
              'endLine' => 40,
              'startTokenPos' => 15,
              'startFilePos' => 1271,
              'endTokenPos' => 30,
              'endFilePos' => 1293,
            ),
          ),
        ),
      ),
      2 => 
      array (
        'name' => 'JetBrains\\PhpStorm\\Internal\\PhpStormStubsElementAvailable',
        'isRepeated' => false,
        'arguments' => 
        array (
          'from' => 
          array (
            'code' => '\'8.0\'',
            'attributes' => 
            array (
              'startLine' => 41,
              'endLine' => 41,
              'startTokenPos' => 40,
              'startFilePos' => 1368,
              'endTokenPos' => 40,
              'endFilePos' => 1372,
            ),
          ),
        ),
      ),
    ),
    'docComment' => '/**
 * Compares two "PHP-standardized" version number strings
 * @link https://php.net/manual/en/function.version-compare.php
 * @param string $version1 <p>
 * First version number.
 * </p>
 * @param string $version2 <p>
 * Second version number.
 * </p>
 * @param string|null $operator [optional] <p>
 * If you specify the third optional operator
 * argument, you can test for a particular relationship. The
 * possible operators are: &lt;,
 * lt, &lt;=,
 * le, &gt;,
 * gt, &gt;=,
 * ge, ==,
 * =, eq,
 * !=, &lt;&gt;,
 * ne respectively.
 * </p>
 * <p>
 * This parameter is case-sensitive, so values should be lowercase.
 * </p>
 * @return int|bool By default, version_compare returns
 * -1 if the first version is lower than the second,
 * 0 if they are equal, and
 * 1 if the second is lower.
 * </p>
 * <p>
 * When using the optional operator argument, the
 * function will return true if the relationship is the one specified
 * by the operator, false otherwise.
 * @throws ValueError when a non-supported operator is provided.
 */',
    'startLine' => 39,
    'endLine' => 49,
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
        'name' => 'version_compare',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/standard/standard_9.stub',
        'extensionName' => 'standard',
        'aliasName' => NULL,
      ),
    ),
  ),
));