<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-addcslashes
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'name' => 'addcslashes',
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
        'startLine' => 59,
        'endLine' => 59,
        'startColumn' => 26,
        'endColumn' => 39,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'characters' => 
      array (
        'name' => 'characters',
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
        'startLine' => 59,
        'endLine' => 59,
        'startColumn' => 42,
        'endColumn' => 59,
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
 * Quote string with slashes in a C style
 * @link https://php.net/manual/en/function.addcslashes.php
 * @param string $string <p>
 * The string to be escaped.
 * </p>
 * @param string $characters <p>
 * A list of characters to be escaped. If characters contains characters \\n, \\r etc., they are converted in C-like style,
 * while other non-alphanumeric characters with ASCII codes lower than 32 and higher than 126 converted to octal representation.
 * </p>
 * <p>
 * When you define a sequence of characters in the characters argument make sure that you know what characters come
 * between the characters that you set as the start and end of the range.
 * </p>
 * <pre>
 * <?php
 * echo addcslashes(\'foo[ ]\', \'A..z\');
 * // output:  \\f\\o\\o\\[ \\]
 * // All upper and lower-case letters will be escaped
 * // ... but so will the [\\]^_`
 * ?>
 * </pre>
 * <p>
 * Also, if the first character in a range has a higher ASCII value
 * than the second character in the range, no range will be
 * constructed. Only the start, end and period characters will be
 * escaped. Use the ord function to find the
 * ASCII value for a character.
 * </p>
 * <pre>
 * <?php
 * echo addcslashes("zoo[\'.\']", \'z..A\');
 * // output:  \\zoo[\'\\.\']
 * ?>
 * </pre>
 * <p>
 * Be careful if you choose to escape characters 0, a, b, f, n, r, t and v.
 * They will be converted to \\0, \\a, \\b, \\f, \\n, \\r, \\t and \\v, all of which are predefined escape sequences in C.
 * Many of these sequences are also defined in other C-derived languages, including PHP, meaning that you may not get
 * the desired result if you use the output of addcslashes() to generate code in those languages with these characters
 * defined in characters.
 * </p>
 * @return string the escaped string.
 * <p>
 * Example usage:
 * </p>
 * <code>
 * <?php
 * $not_escaped = "PHP isThirty\\nYears Old!\\tYay to the Elephant!\\n";
 * $escaped = addcslashes($not_escaped, "\\0..\\37!@\\177..\\377");
 * echo $escaped;
 * ?>
 * </code>
 */',
    'startLine' => 58,
    'endLine' => 61,
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
        'name' => 'addcslashes',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/standard/standard_1.stub',
        'extensionName' => 'standard',
        'aliasName' => NULL,
      ),
    ),
  ),
));