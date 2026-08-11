<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-filter_var
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'name' => 'filter_var',
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
        'startLine' => 58,
        'endLine' => 58,
        'startColumn' => 25,
        'endColumn' => 36,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'filter' => 
      array (
        'name' => 'filter',
        'default' => 
        array (
          'code' => '\\FILTER_DEFAULT',
          'attributes' => 
          array (
            'startLine' => 58,
            'endLine' => 58,
            'startTokenPos' => 27,
            'startFilePos' => 2229,
            'endTokenPos' => 27,
            'endFilePos' => 2242,
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
        'startLine' => 58,
        'endLine' => 58,
        'startColumn' => 39,
        'endColumn' => 66,
        'parameterIndex' => 1,
        'isOptional' => true,
      ),
      'options' => 
      array (
        'name' => 'options',
        'default' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 58,
            'endLine' => 58,
            'startTokenPos' => 38,
            'startFilePos' => 2266,
            'endTokenPos' => 38,
            'endFilePos' => 2266,
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
                  'name' => 'array',
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
        ),
        'startLine' => 58,
        'endLine' => 58,
        'startColumn' => 69,
        'endColumn' => 90,
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
        'name' => 'mixed',
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
 * Filters a variable with a specified filter
 * @link https://php.net/manual/en/function.filter-var.php
 * @param mixed $value <p>
 * Value to filter.
 * </p>
 * @param int $filter [optional] <p>
 * The ID of the filter to apply. The
 * manual page lists the available filters.
 * </p>
 * @param array|int $options <p>
 * Associative array of options or bitwise disjunction of flags. If filter
 * accepts options, flags can be provided in "flags" field of array. For
 * the "callback" filter, callable type should be passed. The
 * callback must accept one argument, the value to be filtered, and return
 * the value after filtering/sanitizing it.
 * </p>
 * <p>
 * <code>
 * // for filters that accept options, use this format
 * $options = array(
 * \'options\' => array(
 * \'default\' => 3, // value to return if the filter fails
 * // other options here
 * \'min_range\' => 0
 * ),
 * \'flags\' => FILTER_FLAG_ALLOW_OCTAL,
 * );
 * $var = filter_var(\'0755\', FILTER_VALIDATE_INT, $options);
 * // for filter that only accept flags, you can pass them directly
 * $var = filter_var(\'oops\', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
 * // for filter that only accept flags, you can also pass as an array
 * $var = filter_var(\'oops\', FILTER_VALIDATE_BOOLEAN,
 * array(\'flags\' => FILTER_NULL_ON_FAILURE));
 * // callback validate filter
 * function foo($value)
 * {
 * // Expected format: Surname, GivenNames
 * if (strpos($value, ", ") === false) return false;
 * list($surname, $givennames) = explode(", ", $value, 2);
 * $empty = (empty($surname) || empty($givennames));
 * $notstrings = (!is_string($surname) || !is_string($givennames));
 * if ($empty || $notstrings) {
 * return false;
 * } else {
 * return $value;
 * }
 * }
 * $var = filter_var(\'Doe, Jane Sue\', FILTER_CALLBACK, array(\'options\' => \'foo\'));
 * </code>
 * </p>
 * @return mixed the filtered data, or <b>FALSE</b> if the filter fails.
 */',
    'startLine' => 57,
    'endLine' => 60,
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
        'name' => 'filter_var',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/filter/filter.stub',
        'extensionName' => 'filter',
        'aliasName' => NULL,
      ),
    ),
  ),
));