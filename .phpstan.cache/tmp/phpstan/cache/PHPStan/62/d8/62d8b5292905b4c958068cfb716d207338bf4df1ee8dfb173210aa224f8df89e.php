<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionClass-arrayaccess
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\InternalLocatedSource',
      'data' => 
      array (
        'name' => 'ArrayAccess',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/Core/Core_c.stub',
        'extensionName' => 'Core',
        'aliasName' => NULL,
      ),
    ),
    'namespace' => NULL,
    'name' => 'ArrayAccess',
    'shortName' => 'ArrayAccess',
    'isInterface' => true,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Interface to provide accessing objects as arrays.
 * @link https://php.net/manual/en/class.arrayaccess.php
 * @template TKey
 * @template TValue
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 10,
    'endLine' => 76,
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
      'offsetExists' => 
      array (
        'name' => 'offsetExists',
        'parameters' => 
        array (
          'offset' => 
          array (
            'name' => 'offset',
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
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '[\'8.0\' => \'mixed\']',
                    'attributes' => 
                    array (
                      'startLine' => 26,
                      'endLine' => 26,
                      'startTokenPos' => 30,
                      'startFilePos' => 829,
                      'endTokenPos' => 36,
                      'endFilePos' => 846,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 26,
                      'endLine' => 26,
                      'startTokenPos' => 42,
                      'startFilePos' => 858,
                      'endTokenPos' => 42,
                      'endFilePos' => 859,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 26,
            'endLine' => 27,
            'startColumn' => 13,
            'endColumn' => 25,
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
 * Whether a offset exists
 * @link https://php.net/manual/en/arrayaccess.offsetexists.php
 * @param TKey $offset <p>
 * An offset to check for.
 * </p>
 * @return bool true on success or false on failure.
 * </p>
 * <p>
 * The return value will be casted to boolean if non-boolean was returned.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 24,
        'endLine' => 28,
        'startColumn' => 9,
        'endColumn' => 16,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'ArrayAccess',
        'implementingClassName' => 'ArrayAccess',
        'currentClassName' => 'ArrayAccess',
        'aliasName' => NULL,
      ),
      'offsetGet' => 
      array (
        'name' => 'offsetGet',
        'parameters' => 
        array (
          'offset' => 
          array (
            'name' => 'offset',
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
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '[\'8.0\' => \'mixed\']',
                    'attributes' => 
                    array (
                      'startLine' => 40,
                      'endLine' => 40,
                      'startTokenPos' => 72,
                      'startFilePos' => 1372,
                      'endTokenPos' => 78,
                      'endFilePos' => 1389,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 40,
                      'endLine' => 40,
                      'startTokenPos' => 84,
                      'startFilePos' => 1401,
                      'endTokenPos' => 84,
                      'endFilePos' => 1402,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 40,
            'endLine' => 41,
            'startColumn' => 13,
            'endColumn' => 25,
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
 * Offset to retrieve
 * @link https://php.net/manual/en/arrayaccess.offsetget.php
 * @param TKey $offset <p>
 * The offset to retrieve.
 * </p>
 * @return TValue Can return all value types.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 38,
        'endLine' => 42,
        'startColumn' => 9,
        'endColumn' => 17,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'ArrayAccess',
        'implementingClassName' => 'ArrayAccess',
        'currentClassName' => 'ArrayAccess',
        'aliasName' => NULL,
      ),
      'offsetSet' => 
      array (
        'name' => 'offsetSet',
        'parameters' => 
        array (
          'offset' => 
          array (
            'name' => 'offset',
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
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '[\'8.0\' => \'mixed\']',
                    'attributes' => 
                    array (
                      'startLine' => 57,
                      'endLine' => 57,
                      'startTokenPos' => 114,
                      'startFilePos' => 1973,
                      'endTokenPos' => 120,
                      'endFilePos' => 1990,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 57,
                      'endLine' => 57,
                      'startTokenPos' => 126,
                      'startFilePos' => 2002,
                      'endTokenPos' => 126,
                      'endFilePos' => 2003,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 57,
            'endLine' => 58,
            'startColumn' => 13,
            'endColumn' => 25,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '[\'8.0\' => \'mixed\']',
                    'attributes' => 
                    array (
                      'startLine' => 59,
                      'endLine' => 59,
                      'startTokenPos' => 138,
                      'startFilePos' => 2100,
                      'endTokenPos' => 144,
                      'endFilePos' => 2117,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 59,
                      'endLine' => 59,
                      'startTokenPos' => 150,
                      'startFilePos' => 2129,
                      'endTokenPos' => 150,
                      'endFilePos' => 2130,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 59,
            'endLine' => 60,
            'startColumn' => 13,
            'endColumn' => 24,
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
 * Offset to set
 * @link https://php.net/manual/en/arrayaccess.offsetset.php
 * @param TKey $offset <p>
 * The offset to assign the value to.
 * </p>
 * @param TValue $value <p>
 * The value to set.
 * </p>
 * @return void
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 55,
        'endLine' => 61,
        'startColumn' => 9,
        'endColumn' => 16,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'ArrayAccess',
        'implementingClassName' => 'ArrayAccess',
        'currentClassName' => 'ArrayAccess',
        'aliasName' => NULL,
      ),
      'offsetUnset' => 
      array (
        'name' => 'offsetUnset',
        'parameters' => 
        array (
          'offset' => 
          array (
            'name' => 'offset',
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
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '[\'8.0\' => \'mixed\']',
                    'attributes' => 
                    array (
                      'startLine' => 73,
                      'endLine' => 73,
                      'startTokenPos' => 180,
                      'startFilePos' => 2610,
                      'endTokenPos' => 186,
                      'endFilePos' => 2627,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 73,
                      'endLine' => 73,
                      'startTokenPos' => 192,
                      'startFilePos' => 2639,
                      'endTokenPos' => 192,
                      'endFilePos' => 2640,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 73,
            'endLine' => 74,
            'startColumn' => 13,
            'endColumn' => 25,
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
 * Offset to unset
 * @link https://php.net/manual/en/arrayaccess.offsetunset.php
 * @param TKey $offset <p>
 * The offset to unset.
 * </p>
 * @return void
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 71,
        'endLine' => 75,
        'startColumn' => 9,
        'endColumn' => 16,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'ArrayAccess',
        'implementingClassName' => 'ArrayAccess',
        'currentClassName' => 'ArrayAccess',
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