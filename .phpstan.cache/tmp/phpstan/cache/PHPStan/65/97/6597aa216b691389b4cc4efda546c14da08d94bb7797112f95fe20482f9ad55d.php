<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionClass-throwable
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\InternalLocatedSource',
      'data' => 
      array (
        'name' => 'Throwable',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/Core/Core_c.stub',
        'extensionName' => 'Core',
        'aliasName' => NULL,
      ),
    ),
    'namespace' => NULL,
    'name' => 'Throwable',
    'shortName' => 'Throwable',
    'isInterface' => true,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Throwable is the base interface for any object that can be thrown via a throw statement in PHP 7,
 * including Error and Exception.
 * @link https://php.net/manual/en/class.throwable.php
 * @since 7.0
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 10,
    'endLine' => 78,
    'startColumn' => 5,
    'endColumn' => 5,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'Stringable',
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
      'getMessage' => 
      array (
        'name' => 'getMessage',
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
        ),
        'docComment' => '/**
 * Gets the message
 * @link https://php.net/manual/en/throwable.getmessage.php
 * @return string
 * @since 7.0
 */',
        'startLine' => 18,
        'endLine' => 18,
        'startColumn' => 9,
        'endColumn' => 45,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Throwable',
        'implementingClassName' => 'Throwable',
        'currentClassName' => 'Throwable',
        'aliasName' => NULL,
      ),
      'getCode' => 
      array (
        'name' => 'getCode',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Gets the exception code
 * @link https://php.net/manual/en/throwable.getcode.php
 * @return int <p>
 * Returns the exception code as integer in
 * {@see Exception} but possibly as other type in
 * {@see Exception} descendants (for example as
 * string in {@see PDOException}).
 * </p>
 * @since 7.0
 */',
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 9,
        'endColumn' => 34,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Throwable',
        'implementingClassName' => 'Throwable',
        'currentClassName' => 'Throwable',
        'aliasName' => NULL,
      ),
      'getFile' => 
      array (
        'name' => 'getFile',
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
        ),
        'docComment' => '/**
 * Gets the file in which the exception occurred
 * @link https://php.net/manual/en/throwable.getfile.php
 * @return string Returns the name of the file from which the object was thrown.
 * @since 7.0
 */',
        'startLine' => 37,
        'endLine' => 37,
        'startColumn' => 9,
        'endColumn' => 42,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Throwable',
        'implementingClassName' => 'Throwable',
        'currentClassName' => 'Throwable',
        'aliasName' => NULL,
      ),
      'getLine' => 
      array (
        'name' => 'getLine',
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
        ),
        'docComment' => '/**
 * Gets the line on which the object was instantiated
 * @link https://php.net/manual/en/throwable.getline.php
 * @return int Returns the line number where the thrown object was instantiated.
 * @since 7.0
 */',
        'startLine' => 44,
        'endLine' => 44,
        'startColumn' => 9,
        'endColumn' => 39,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Throwable',
        'implementingClassName' => 'Throwable',
        'currentClassName' => 'Throwable',
        'aliasName' => NULL,
      ),
      'getTrace' => 
      array (
        'name' => 'getTrace',
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
        ),
        'docComment' => '/**
 * Gets the stack trace
 * @link https://php.net/manual/en/throwable.gettrace.php
 * @return array <p>
 * Returns the stack trace as an array in the same format as
 * {@see debug_backtrace()}.
 * </p>
 * @since 7.0
 */',
        'startLine' => 54,
        'endLine' => 54,
        'startColumn' => 9,
        'endColumn' => 42,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Throwable',
        'implementingClassName' => 'Throwable',
        'currentClassName' => 'Throwable',
        'aliasName' => NULL,
      ),
      'getTraceAsString' => 
      array (
        'name' => 'getTraceAsString',
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
        ),
        'docComment' => '/**
 * Gets the stack trace as a string
 * @link https://php.net/manual/en/throwable.gettraceasstring.php
 * @return string Returns the stack trace as a string.
 * @since 7.0
 */',
        'startLine' => 61,
        'endLine' => 61,
        'startColumn' => 9,
        'endColumn' => 51,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Throwable',
        'implementingClassName' => 'Throwable',
        'currentClassName' => 'Throwable',
        'aliasName' => NULL,
      ),
      'getPrevious' => 
      array (
        'name' => 'getPrevious',
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
                  'name' => 'Throwable',
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
            'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
            'isRepeated' => false,
            'arguments' => 
            array (
              0 => 
              array (
                'code' => '[\'8.0\' => \'Throwable|null\']',
                'attributes' => 
                array (
                  'startLine' => 68,
                  'endLine' => 68,
                  'startTokenPos' => 104,
                  'startFilePos' => 2502,
                  'endTokenPos' => 110,
                  'endFilePos' => 2528,
                ),
              ),
              'default' => 
              array (
                'code' => '\'\'',
                'attributes' => 
                array (
                  'startLine' => 68,
                  'endLine' => 68,
                  'startTokenPos' => 116,
                  'startFilePos' => 2540,
                  'endTokenPos' => 116,
                  'endFilePos' => 2541,
                ),
              ),
            ),
          ),
        ),
        'docComment' => '/**
 * Returns the previous Throwable
 * @link https://php.net/manual/en/throwable.getprevious.php
 * @return null|Throwable Returns the previous {@see Throwable} if available, or <b>NULL</b> otherwise.
 * @since 7.0
 */',
        'startLine' => 68,
        'endLine' => 69,
        'startColumn' => 9,
        'endColumn' => 54,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Throwable',
        'implementingClassName' => 'Throwable',
        'currentClassName' => 'Throwable',
        'aliasName' => NULL,
      ),
      '__toString' => 
      array (
        'name' => '__toString',
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
            'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
            'isRepeated' => false,
            'arguments' => 
            array (
              0 => 
              array (
                'code' => '[\'8.0\' => \'string\']',
                'attributes' => 
                array (
                  'startLine' => 76,
                  'endLine' => 76,
                  'startTokenPos' => 139,
                  'startFilePos' => 2924,
                  'endTokenPos' => 145,
                  'endFilePos' => 2942,
                ),
              ),
              'default' => 
              array (
                'code' => '\'\'',
                'attributes' => 
                array (
                  'startLine' => 76,
                  'endLine' => 76,
                  'startTokenPos' => 151,
                  'startFilePos' => 2954,
                  'endTokenPos' => 151,
                  'endFilePos' => 2955,
                ),
              ),
            ),
          ),
        ),
        'docComment' => '/**
 * Gets a string representation of the thrown object
 * @link https://php.net/manual/en/throwable.tostring.php
 * @return string <p>Returns the string representation of the thrown object.</p>
 * @since 7.0
 */',
        'startLine' => 76,
        'endLine' => 77,
        'startColumn' => 9,
        'endColumn' => 45,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Throwable',
        'implementingClassName' => 'Throwable',
        'currentClassName' => 'Throwable',
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