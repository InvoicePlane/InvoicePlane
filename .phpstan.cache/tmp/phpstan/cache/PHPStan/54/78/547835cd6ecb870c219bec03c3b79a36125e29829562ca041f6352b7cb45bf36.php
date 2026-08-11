<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-gmdate
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'name' => 'gmdate',
    'parameters' => 
    array (
      'format' => 
      array (
        'name' => 'format',
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
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 21,
        'endColumn' => 34,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'timestamp' => 
      array (
        'name' => 'timestamp',
        'default' => 
        array (
          'code' => '\\null',
          'attributes' => 
          array (
            'startLine' => 19,
            'endLine' => 19,
            'startTokenPos' => 50,
            'startFilePos' => 853,
            'endTokenPos' => 50,
            'endFilePos' => 856,
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
                  'name' => 'int',
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
        ),
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 37,
        'endColumn' => 58,
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
          0 => 
          array (
            'code' => '\\true',
            'attributes' => 
            array (
              'startLine' => 17,
              'endLine' => 17,
              'startTokenPos' => 11,
              'startFilePos' => 687,
              'endTokenPos' => 11,
              'endFilePos' => 690,
            ),
          ),
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
            'code' => '["8.0" => "string"]',
            'attributes' => 
            array (
              'startLine' => 18,
              'endLine' => 18,
              'startTokenPos' => 18,
              'startFilePos' => 752,
              'endTokenPos' => 24,
              'endFilePos' => 770,
            ),
          ),
          'default' => 
          array (
            'code' => '"string|false"',
            'attributes' => 
            array (
              'startLine' => 18,
              'endLine' => 18,
              'startTokenPos' => 30,
              'startFilePos' => 782,
              'endTokenPos' => 30,
              'endFilePos' => 795,
            ),
          ),
        ),
      ),
    ),
    'docComment' => '/**
 * Format a GMT/UTC date/time
 * @link https://php.net/manual/en/function.gmdate.php
 * @param string $format <p>
 * The format of the outputted date string. See the formatting
 * options for the date function.
 * </p>
 * @param int|null $timestamp [optional] Default value: time(). The optional timestamp parameter is an integer Unix timestamp
 * that defaults to the current local time if a timestamp is not given.
 * @return string|false a formatted date string. If a non-numeric value is used for
 * timestamp, false is returned and an
 * E_WARNING level error is emitted.
 */',
    'startLine' => 17,
    'endLine' => 21,
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
        'name' => 'gmdate',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/date/date.stub',
        'extensionName' => 'date',
        'aliasName' => NULL,
      ),
    ),
  ),
));