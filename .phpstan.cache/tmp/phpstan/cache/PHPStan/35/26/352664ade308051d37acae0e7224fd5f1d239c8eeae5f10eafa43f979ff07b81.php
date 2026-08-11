<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-preg_replace_callback
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'name' => 'preg_replace_callback',
    'parameters' => 
    array (
      'pattern' => 
      array (
        'name' => 'pattern',
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
                  'name' => 'array',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'string',
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
        'startLine' => 74,
        'endLine' => 74,
        'startColumn' => 9,
        'endColumn' => 29,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'callback' => 
      array (
        'name' => 'callback',
        'default' => NULL,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'callable',
            'isIdentifier' => true,
          ),
        ),
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 75,
        'endLine' => 75,
        'startColumn' => 9,
        'endColumn' => 26,
        'parameterIndex' => 1,
        'isOptional' => false,
      ),
      'subject' => 
      array (
        'name' => 'subject',
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
                  'name' => 'array',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'string',
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
        'startLine' => 76,
        'endLine' => 76,
        'startColumn' => 9,
        'endColumn' => 29,
        'parameterIndex' => 2,
        'isOptional' => false,
      ),
      'limit' => 
      array (
        'name' => 'limit',
        'default' => 
        array (
          'code' => '-1',
          'attributes' => 
          array (
            'startLine' => 77,
            'endLine' => 77,
            'startTokenPos' => 38,
            'startFilePos' => 2708,
            'endTokenPos' => 39,
            'endFilePos' => 2709,
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
        'startLine' => 77,
        'endLine' => 77,
        'startColumn' => 9,
        'endColumn' => 23,
        'parameterIndex' => 3,
        'isOptional' => true,
      ),
      'count' => 
      array (
        'name' => 'count',
        'default' => 
        array (
          'code' => '\\null',
          'attributes' => 
          array (
            'startLine' => 78,
            'endLine' => 78,
            'startTokenPos' => 47,
            'startFilePos' => 2730,
            'endTokenPos' => 47,
            'endFilePos' => 2733,
          ),
        ),
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => true,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 78,
        'endLine' => 78,
        'startColumn' => 9,
        'endColumn' => 22,
        'parameterIndex' => 4,
        'isOptional' => true,
      ),
      'flags' => 
      array (
        'name' => 'flags',
        'default' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 80,
            'endLine' => 80,
            'startTokenPos' => 66,
            'startFilePos' => 2840,
            'endTokenPos' => 66,
            'endFilePos' => 2840,
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
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\PhpStormStubsElementAvailable',
            'isRepeated' => false,
            'arguments' => 
            array (
              'from' => 
              array (
                'code' => '\'7.4\'',
                'attributes' => 
                array (
                  'startLine' => 79,
                  'endLine' => 79,
                  'startTokenPos' => 56,
                  'startFilePos' => 2811,
                  'endTokenPos' => 56,
                  'endFilePos' => 2815,
                ),
              ),
            ),
          ),
        ),
        'startLine' => 79,
        'endLine' => 80,
        'startColumn' => 9,
        'endColumn' => 22,
        'parameterIndex' => 5,
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
              'name' => 'array',
              'isIdentifier' => true,
            ),
          ),
          1 => 
          array (
            'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
            'data' => 
            array (
              'name' => 'string',
              'isIdentifier' => true,
            ),
          ),
          2 => 
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
    ),
    'docComment' => '/**
 * Perform a regular expression search and replace using a callback
 * @link https://php.net/manual/en/function.preg-replace-callback.php
 * @param string|string[] $pattern <p>
 * The pattern to search for. It can be either a string or an array with
 * strings.
 * </p>
 * @param callable $callback <p>
 * A callback that will be called and passed an array of matched elements
 * in the <i>subject</i> string. The callback should
 * return the replacement string. This is the callback signature:
 * </p>
 * <p>
 * string<b>handler</b>
 * <b>array<i>matches</i></b>
 * </p>
 * <p>
 * You\'ll often need the <i>callback</i> function
 * for a <b>preg_replace_callback</b> in just one place.
 * In this case you can use an
 * anonymous function to
 * declare the callback within the call to
 * <b>preg_replace_callback</b>. By doing it this way
 * you have all information for the call in one place and do not
 * clutter the function namespace with a callback function\'s name
 * not used anywhere else.
 * </p>
 * <p>
 * <b>preg_replace_callback</b> and
 * anonymous function
 * <code>
 * /* a unix-style command line filter to convert uppercase
 * * letters at the beginning of paragraphs to lowercase * /
 * $fp = fopen("php://stdin", "r") or die("can\'t read stdin");
 * while (!feof($fp)) {
 * $line = fgets($fp);
 * $line = preg_replace_callback(
 * \'|<p>\\s*\\w|\',
 * function ($matches) {
 * return strtolower($matches[0]);
 * },
 * $line
 * );
 * echo $line;
 * }
 * fclose($fp);
 * </code>
 * </p>
 * @param string|string[] $subject <p>
 * The string or an array with strings to search and replace.
 * </p>
 * @param int $limit [optional] <p>
 * The maximum possible replacements for each pattern in each
 * <i>subject</i> string. Defaults to
 * -1 (no limit).
 * </p>
 * @param int &$count [optional] <p>
 * If specified, this variable will be filled with the number of
 * replacements done.
 * </p>
 * @param int $flags [optional]
 * @return string|string[]|null <b>preg_replace_callback</b> returns an array if the
 * <i>subject</i> parameter is an array, or a string
 * otherwise. On errors the return value is <b>NULL</b>
 * </p>
 * <p>
 * If matches are found, the new subject will be returned, otherwise
 * <i>subject</i> will be returned unchanged.
 */',
    'startLine' => 73,
    'endLine' => 83,
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
        'name' => 'preg_replace_callback',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/pcre/pcre.stub',
        'extensionName' => 'pcre',
        'aliasName' => NULL,
      ),
    ),
  ),
));