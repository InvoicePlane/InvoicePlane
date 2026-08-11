<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-preg_match
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'name' => 'preg_match',
    'parameters' => 
    array (
      'pattern' => 
      array (
        'name' => 'pattern',
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
        'startLine' => 156,
        'endLine' => 156,
        'startColumn' => 25,
        'endColumn' => 39,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'subject' => 
      array (
        'name' => 'subject',
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
        'startLine' => 156,
        'endLine' => 156,
        'startColumn' => 42,
        'endColumn' => 56,
        'parameterIndex' => 1,
        'isOptional' => false,
      ),
      'matches' => 
      array (
        'name' => 'matches',
        'default' => 
        array (
          'code' => '\\null',
          'attributes' => 
          array (
            'startLine' => 156,
            'endLine' => 156,
            'startTokenPos' => 30,
            'startFilePos' => 4462,
            'endTokenPos' => 30,
            'endFilePos' => 4465,
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
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'isVariadic' => false,
        'byRef' => true,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 156,
        'endLine' => 156,
        'startColumn' => 59,
        'endColumn' => 81,
        'parameterIndex' => 2,
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
            'startLine' => 156,
            'endLine' => 156,
            'startTokenPos' => 39,
            'startFilePos' => 4481,
            'endTokenPos' => 39,
            'endFilePos' => 4481,
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
        'startLine' => 156,
        'endLine' => 156,
        'startColumn' => 84,
        'endColumn' => 97,
        'parameterIndex' => 3,
        'isOptional' => true,
      ),
      'offset' => 
      array (
        'name' => 'offset',
        'default' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 156,
            'endLine' => 156,
            'startTokenPos' => 48,
            'startFilePos' => 4498,
            'endTokenPos' => 48,
            'endFilePos' => 4498,
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
        'startLine' => 156,
        'endLine' => 156,
        'startColumn' => 100,
        'endColumn' => 114,
        'parameterIndex' => 4,
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
              'name' => 'false',
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
 * Perform a regular expression match
 * @link https://php.net/manual/en/function.preg-match.php
 * @param string $pattern <p>
 * The pattern to search for, as a string.
 * </p>
 * @param string $subject <p>
 * The input string.
 * </p>
 * @param null|string[] &$matches [optional] <p>
 * If <i>matches</i> is provided, then it is filled with
 * the results of search. $matches[0] will contain the
 * text that matched the full pattern, $matches[1]
 * will have the text that matched the first captured parenthesized
 * subpattern, and so on.
 * </p>
 * @param int $flags [optional] <p>
 * <i>flags</i> can be the following flag:
 * <b>PREG_OFFSET_CAPTURE</b>
 * <blockquote>
 * If this flag is passed, for every occurring match the appendant string
 * offset will also be returned. Note that this changes the value of
 * <i>matches</i> into an array where every element is an
 * array consisting of the matched string at offset 0
 * and its string offset into <i>subject</i> at offset 1.
 * <pre>
 * <?php
 * preg_match(\'/(foo)(bar)(baz)/\', \'foobarbaz\', $matches, PREG_OFFSET_CAPTURE);
 * print_r($matches);
 * ?>
 * </pre>
 * The above example will output:
 * <pre>
 * Array
 * (
 *     [0] => Array
 *         (
 *             [0] => foobarbaz
 *             [1] => 0
 *         )
 *
 *     [1] => Array
 *         (
 *             [0] => foo
 *             [1] => 0
 *         )
 *
 *     [2] => Array
 *         (
 *             [0] => bar
 *             [1] => 3
 *         )
 *
 *     [3] => Array
 *         (
 *             [0] => baz
 *             [1] => 6
 *         )
 *
 * )
 * </pre>
 * </blockquote>
 * <b>PREG_UNMATCHED_AS_NULL</b>
 * <blockquote>
 * If this flag is passed, unmatched subpatterns are reported as NULL;
 * otherwise they are reported as an empty string.
 * <pre>
 * <?php
 * preg_match(\'/(a)(b)*(c)/\', \'ac\', $matches);
 * var_dump($matches);
 * preg_match(\'/(a)(b)*(c)/\', \'ac\', $matches, PREG_UNMATCHED_AS_NULL);
 * var_dump($matches);
 * ?>
 * </pre>
 * The above example will output:
 * <pre>
 * array(4) {
 *   [0]=>
 *   string(2) "ac"
 *   [1]=>
 *   string(1) "a"
 *   [2]=>
 *   string(0) ""
 *   [3]=>
 *   string(1) "c"
 * }
 * array(4) {
 *   [0]=>
 *   string(2) "ac"
 *   [1]=>
 *   string(1) "a"
 *   [2]=>
 *   NULL
 *   [3]=>
 *   string(1) "c"
 * }
 * </pre>
 * </blockquote>
 * @param int $offset [optional] <p>
 * Normally, the search starts from the beginning of the subject string.
 * The optional parameter <i>offset</i> can be used to
 * specify the alternate place from which to start the search (in bytes).
 * </p>
 * <p>
 * Using <i>offset</i> is not equivalent to passing
 * substr($subject, $offset) to
 * <b>preg_match</b> in place of the subject string,
 * because <i>pattern</i> can contain assertions such as
 * ^, $ or
 * (?&lt;=x). Compare:
 * <pre>
 * $subject = "abcdef";
 * $pattern = \'/^def/\';
 * preg_match($pattern, $subject, $matches, PREG_OFFSET_CAPTURE, 3);
 * print_r($matches);
 * </pre>
 * The above example will output:</p>
 * <pre>
 * Array
 * (
 * )
 * </pre>
 * <p>
 * while this example
 * </p>
 * <code>
 * $subject = "abcdef";
 * $pattern = \'/^def/\';
 * preg_match($pattern, substr($subject,3), $matches, PREG_OFFSET_CAPTURE);
 * print_r($matches);
 * </code>
 * <p>
 * will produce
 * </p>
 * <pre>
 * Array
 * (
 *     [0] => Array
 *         (
 *             [0] => def
 *             [1] => 0
 *         )
 * )
 * </pre>
 * Alternatively, to avoid using substr(), use the \\G assertion rather
 * than the ^ anchor, or the A modifier instead, both of which work with
 * the offset parameter.
 * </p>
 * @return int|false <b>preg_match</b> returns 1 if the <i>pattern</i>
 * matches given <i>subject</i>, 0 if it does not, or <b>FALSE</b>
 * if an error occurred.
 */',
    'startLine' => 156,
    'endLine' => 158,
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
        'name' => 'preg_match',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/pcre/pcre.stub',
        'extensionName' => 'pcre',
        'aliasName' => NULL,
      ),
    ),
  ),
));