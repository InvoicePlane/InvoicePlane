<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionClass-resourcebundle
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\InternalLocatedSource',
      'data' => 
      array (
        'name' => 'ResourceBundle',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/intl/intl.stub',
        'extensionName' => 'intl',
        'aliasName' => NULL,
      ),
    ),
    'namespace' => NULL,
    'name' => 'ResourceBundle',
    'shortName' => 'ResourceBundle',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 4,
    'endLine' => 136,
    'startColumn' => 5,
    'endColumn' => 5,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'IteratorAggregate',
      1 => 'Countable',
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
          'locale' => 
          array (
            'name' => 'locale',
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
                      'startLine' => 14,
                      'endLine' => 14,
                      'startTokenPos' => 35,
                      'startFilePos' => 680,
                      'endTokenPos' => 41,
                      'endFilePos' => 703,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 14,
                      'endLine' => 14,
                      'startTokenPos' => 47,
                      'startFilePos' => 715,
                      'endTokenPos' => 47,
                      'endFilePos' => 716,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 14,
            'endLine' => 15,
            'startColumn' => 13,
            'endColumn' => 31,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'bundle' => 
          array (
            'name' => 'bundle',
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
                      'startLine' => 16,
                      'endLine' => 16,
                      'startTokenPos' => 61,
                      'startFilePos' => 819,
                      'endTokenPos' => 67,
                      'endFilePos' => 842,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 16,
                      'endLine' => 16,
                      'startTokenPos' => 73,
                      'startFilePos' => 854,
                      'endTokenPos' => 73,
                      'endFilePos' => 855,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 16,
            'endLine' => 17,
            'startColumn' => 13,
            'endColumn' => 31,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'fallback' => 
          array (
            'name' => 'fallback',
            'default' => 
            array (
              'code' => '\\true',
              'attributes' => 
              array (
                'startLine' => 19,
                'endLine' => 19,
                'startTokenPos' => 109,
                'startFilePos' => 1020,
                'endTokenPos' => 109,
                'endFilePos' => 1023,
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
                    'code' => '[\'8.0\' => \'bool\']',
                    'attributes' => 
                    array (
                      'startLine' => 18,
                      'endLine' => 18,
                      'startTokenPos' => 87,
                      'startFilePos' => 958,
                      'endTokenPos' => 93,
                      'endFilePos' => 974,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 18,
                      'endLine' => 18,
                      'startTokenPos' => 99,
                      'startFilePos' => 986,
                      'endTokenPos' => 99,
                      'endFilePos' => 987,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 18,
            'endLine' => 19,
            'startColumn' => 13,
            'endColumn' => 33,
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
        ),
        'docComment' => '/**
 * @link https://www.php.net/manual/en/resourcebundle.create.php
 * @param string $locale <p>Locale for which the resources should be loaded (locale name, e.g. en_CA).</p>
 * @param string $bundle <p>The directory where the data is stored or the name of the .dat file.</p>
 * @param bool $fallback [optional] <p>Whether locale should match exactly or fallback to parent locale is allowed.</p>
 */',
        'startLine' => 12,
        'endLine' => 22,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'ResourceBundle',
        'implementingClassName' => 'ResourceBundle',
        'currentClassName' => 'ResourceBundle',
        'aliasName' => NULL,
      ),
      'create' => 
      array (
        'name' => 'create',
        'parameters' => 
        array (
          'locale' => 
          array (
            'name' => 'locale',
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
                      'startLine' => 41,
                      'endLine' => 41,
                      'startTokenPos' => 135,
                      'startFilePos' => 1965,
                      'endTokenPos' => 141,
                      'endFilePos' => 1988,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 41,
                      'endLine' => 41,
                      'startTokenPos' => 147,
                      'startFilePos' => 2000,
                      'endTokenPos' => 147,
                      'endFilePos' => 2001,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 41,
            'endLine' => 42,
            'startColumn' => 13,
            'endColumn' => 31,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'bundle' => 
          array (
            'name' => 'bundle',
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
                      'startLine' => 43,
                      'endLine' => 43,
                      'startTokenPos' => 161,
                      'startFilePos' => 2104,
                      'endTokenPos' => 167,
                      'endFilePos' => 2127,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 43,
                      'endLine' => 43,
                      'startTokenPos' => 173,
                      'startFilePos' => 2139,
                      'endTokenPos' => 173,
                      'endFilePos' => 2140,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 43,
            'endLine' => 44,
            'startColumn' => 13,
            'endColumn' => 31,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'fallback' => 
          array (
            'name' => 'fallback',
            'default' => 
            array (
              'code' => '\\true',
              'attributes' => 
              array (
                'startLine' => 46,
                'endLine' => 46,
                'startTokenPos' => 209,
                'startFilePos' => 2305,
                'endTokenPos' => 209,
                'endFilePos' => 2308,
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
                    'code' => '[\'8.0\' => \'bool\']',
                    'attributes' => 
                    array (
                      'startLine' => 45,
                      'endLine' => 45,
                      'startTokenPos' => 187,
                      'startFilePos' => 2243,
                      'endTokenPos' => 193,
                      'endFilePos' => 2259,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 45,
                      'endLine' => 45,
                      'startTokenPos' => 199,
                      'startFilePos' => 2271,
                      'endTokenPos' => 199,
                      'endFilePos' => 2272,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 45,
            'endLine' => 46,
            'startColumn' => 13,
            'endColumn' => 33,
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
                  'name' => 'ResourceBundle',
                  'isIdentifier' => false,
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
 * (PHP &gt;= 5.3.2, PECL intl &gt;= 2.0.0)<br/>
 * Create a resource bundle
 * @link https://php.net/manual/en/resourcebundle.create.php
 * @param string $locale <p>
 * Locale for which the resources should be loaded (locale name, e.g. en_CA).
 * </p>
 * @param string $bundle <p>
 * The directory where the data is stored or the name of the .dat file.
 * </p>
 * @param bool $fallback [optional] <p>
 * Whether locale should match exactly or fallback to parent locale is allowed.
 * </p>
 * @return ResourceBundle|null <b>ResourceBundle</b> object or <b>null</b> on error.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 39,
        'endLine' => 49,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => NULL,
        'declaringClassName' => 'ResourceBundle',
        'implementingClassName' => 'ResourceBundle',
        'currentClassName' => 'ResourceBundle',
        'aliasName' => NULL,
      ),
      'get' => 
      array (
        'name' => 'get',
        'parameters' => 
        array (
          'index' => 
          array (
            'name' => 'index',
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
                      'name' => 'int',
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
                    'code' => '[\'8.4\' => \'string|int\']',
                    'attributes' => 
                    array (
                      'startLine' => 67,
                      'endLine' => 67,
                      'startTokenPos' => 260,
                      'startFilePos' => 3330,
                      'endTokenPos' => 266,
                      'endFilePos' => 3352,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 67,
                      'endLine' => 67,
                      'startTokenPos' => 272,
                      'startFilePos' => 3364,
                      'endTokenPos' => 272,
                      'endFilePos' => 3365,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 67,
            'endLine' => 68,
            'startColumn' => 13,
            'endColumn' => 29,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'fallback' => 
          array (
            'name' => 'fallback',
            'default' => 
            array (
              'code' => '\\true',
              'attributes' => 
              array (
                'startLine' => 70,
                'endLine' => 70,
                'startTokenPos' => 308,
                'startFilePos' => 3528,
                'endTokenPos' => 308,
                'endFilePos' => 3531,
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
                    'code' => '[\'8.0\' => \'bool\']',
                    'attributes' => 
                    array (
                      'startLine' => 69,
                      'endLine' => 69,
                      'startTokenPos' => 286,
                      'startFilePos' => 3466,
                      'endTokenPos' => 292,
                      'endFilePos' => 3482,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 69,
                      'endLine' => 69,
                      'startTokenPos' => 298,
                      'startFilePos' => 3494,
                      'endTokenPos' => 298,
                      'endFilePos' => 3495,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 69,
            'endLine' => 70,
            'startColumn' => 13,
            'endColumn' => 33,
            'parameterIndex' => 1,
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
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
          2 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
            'isRepeated' => false,
            'arguments' => 
            array (
              0 => 
              array (
                'code' => '[\'8.4\' => \'ResourceBundle|array|string|int|null\']',
                'attributes' => 
                array (
                  'startLine' => 65,
                  'endLine' => 65,
                  'startTokenPos' => 234,
                  'startFilePos' => 3165,
                  'endTokenPos' => 240,
                  'endFilePos' => 3213,
                ),
              ),
              'default' => 
              array (
                'code' => '\'mixed\'',
                'attributes' => 
                array (
                  'startLine' => 65,
                  'endLine' => 65,
                  'startTokenPos' => 246,
                  'startFilePos' => 3225,
                  'endTokenPos' => 246,
                  'endFilePos' => 3231,
                ),
              ),
            ),
          ),
        ),
        'docComment' => '/**
 * (PHP &gt;= 5.3.2, PECL intl &gt;= 2.0.0)<br/>
 * Get data from the bundle
 * @link https://php.net/manual/en/resourcebundle.get.php
 * @param string|int $index <p>
 * Data index, must be string or integer.
 * </p>
 * @param bool $fallback
 * @return mixed the data located at the index or <b>NULL</b> on error. Strings, integers and binary data strings
 * are returned as corresponding PHP types, integer array is returned as PHP array. Complex types are
 * returned as <b>ResourceBundle</b> object.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 63,
        'endLine' => 73,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'ResourceBundle',
        'implementingClassName' => 'ResourceBundle',
        'currentClassName' => 'ResourceBundle',
        'aliasName' => NULL,
      ),
      'count' => 
      array (
        'name' => 'count',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
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
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * (PHP &gt;= 5.3.2, PECL intl &gt;= 2.0.0)<br/>
 * Get number of elements in the bundle
 * @link https://php.net/manual/en/resourcebundle.count.php
 * @return int<0,max> number of elements in the bundle.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 81,
        'endLine' => 85,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'ResourceBundle',
        'implementingClassName' => 'ResourceBundle',
        'currentClassName' => 'ResourceBundle',
        'aliasName' => NULL,
      ),
      'getLocales' => 
      array (
        'name' => 'getLocales',
        'parameters' => 
        array (
          'bundle' => 
          array (
            'name' => 'bundle',
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
                      'startLine' => 99,
                      'endLine' => 99,
                      'startTokenPos' => 359,
                      'startFilePos' => 4666,
                      'endTokenPos' => 365,
                      'endFilePos' => 4684,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 99,
                      'endLine' => 99,
                      'startTokenPos' => 371,
                      'startFilePos' => 4696,
                      'endTokenPos' => 371,
                      'endFilePos' => 4697,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 99,
            'endLine' => 100,
            'startColumn' => 13,
            'endColumn' => 26,
            'parameterIndex' => 0,
            'isOptional' => false,
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
                  'name' => 'array',
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
 * (PHP &gt;= 5.3.2, PECL intl &gt;= 2.0.0)<br/>
 * Get supported locales
 * @link https://php.net/manual/en/resourcebundle.locales.php
 * @param string $bundle <p>
 * Path of ResourceBundle for which to get available locales, or
 * empty string for default locales list.
 * </p>
 * @return array|false the list of locales supported by the bundle.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 97,
        'endLine' => 103,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => NULL,
        'declaringClassName' => 'ResourceBundle',
        'implementingClassName' => 'ResourceBundle',
        'currentClassName' => 'ResourceBundle',
        'aliasName' => NULL,
      ),
      'getErrorCode' => 
      array (
        'name' => 'getErrorCode',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
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
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * (PHP &gt;= 5.3.2, PECL intl &gt;= 2.0.0)<br/>
 * Get bundle\'s last error code.
 * @link https://php.net/manual/en/resourcebundle.geterrorcode.php
 * @return int error code from last bundle object call.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 111,
        'endLine' => 115,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'ResourceBundle',
        'implementingClassName' => 'ResourceBundle',
        'currentClassName' => 'ResourceBundle',
        'aliasName' => NULL,
      ),
      'getErrorMessage' => 
      array (
        'name' => 'getErrorMessage',
        'parameters' => 
        array (
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
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * (PHP &gt;= 5.3.2, PECL intl &gt;= 2.0.0)<br/>
 * Get bundle\'s last error message.
 * @link https://php.net/manual/en/resourcebundle.geterrormessage.php
 * @return string error message from last bundle object\'s call.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 123,
        'endLine' => 127,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'ResourceBundle',
        'implementingClassName' => 'ResourceBundle',
        'currentClassName' => 'ResourceBundle',
        'aliasName' => NULL,
      ),
      'getIterator' => 
      array (
        'name' => 'getIterator',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Iterator',
            'isIdentifier' => false,
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
        ),
        'docComment' => '/**
 * @return Iterator
 * @since 8.0
 */',
        'startLine' => 132,
        'endLine' => 135,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'ResourceBundle',
        'implementingClassName' => 'ResourceBundle',
        'currentClassName' => 'ResourceBundle',
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