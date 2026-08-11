<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-str_pad
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'name' => 'str_pad',
    'parameters' => 
    array (
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
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 22,
        'endColumn' => 35,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'length' => 
      array (
        'name' => 'length',
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
        ),
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 38,
        'endColumn' => 48,
        'parameterIndex' => 1,
        'isOptional' => false,
      ),
      'pad_string' => 
      array (
        'name' => 'pad_string',
        'default' => 
        array (
          'code' => '" "',
          'attributes' => 
          array (
            'startLine' => 30,
            'endLine' => 30,
            'startTokenPos' => 32,
            'startFilePos' => 995,
            'endTokenPos' => 32,
            'endFilePos' => 997,
          ),
        ),
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
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 51,
        'endColumn' => 74,
        'parameterIndex' => 2,
        'isOptional' => true,
      ),
      'pad_type' => 
      array (
        'name' => 'pad_type',
        'default' => 
        array (
          'code' => '\\STR_PAD_RIGHT',
          'attributes' => 
          array (
            'startLine' => 30,
            'endLine' => 30,
            'startTokenPos' => 41,
            'startFilePos' => 1016,
            'endTokenPos' => 41,
            'endFilePos' => 1028,
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
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 77,
        'endColumn' => 105,
        'parameterIndex' => 3,
        'isOptional' => true,
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
    ),
    'docComment' => '/**
 * Pad a string to a certain length with another string
 * @link https://php.net/manual/en/function.str-pad.php
 * @param string $string <p>
 * The input string.
 * </p>
 * @param int $length <p>
 * If the value of pad_length is negative,
 * less than, or equal to the length of the input string, no padding
 * takes place.
 * </p>
 * @param string $pad_string [optional] <p>
 * The pad_string may be truncated if the
 * required number of padding characters can\'t be evenly divided by the
 * pad_string\'s length.
 * </p>
 * @param int $pad_type [optional] <p>
 * Optional argument pad_type can be
 * STR_PAD_RIGHT, STR_PAD_LEFT,
 * or STR_PAD_BOTH. If
 * pad_type is not specified it is assumed to be
 * STR_PAD_RIGHT.
 * </p>
 * @return string the padded string.
 */',
    'startLine' => 29,
    'endLine' => 32,
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
        'name' => 'str_pad',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/standard/standard_2.stub',
        'extensionName' => 'standard',
        'aliasName' => NULL,
      ),
    ),
  ),
));