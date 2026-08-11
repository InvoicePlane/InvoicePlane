<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionClass-datetimeinterface
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\InternalLocatedSource',
      'data' => 
      array (
        'name' => 'DateTimeInterface',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/date/date_c.stub',
        'extensionName' => 'date',
        'aliasName' => NULL,
      ),
    ),
    'namespace' => NULL,
    'name' => 'DateTimeInterface',
    'shortName' => 'DateTimeInterface',
    'isInterface' => true,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @since 5.5
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 7,
    'endLine' => 155,
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
      'ATOM' => 
      array (
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'name' => 'ATOM',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'Y-m-d\\TH:i:sP\'',
          'attributes' => 
          array (
            'startLine' => 12,
            'endLine' => 12,
            'startTokenPos' => 24,
            'startFilePos' => 165,
            'endTokenPos' => 24,
            'endFilePos' => 179,
          ),
        ),
        'docComment' => '/**
 * @since 7.2
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 12,
        'endLine' => 12,
        'startColumn' => 9,
        'endColumn' => 44,
      ),
      'COOKIE' => 
      array (
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'name' => 'COOKIE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'l, d-M-Y H:i:s T\'',
          'attributes' => 
          array (
            'startLine' => 16,
            'endLine' => 16,
            'startTokenPos' => 37,
            'startFilePos' => 258,
            'endTokenPos' => 37,
            'endFilePos' => 275,
          ),
        ),
        'docComment' => '/**
 * @since 7.2
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 16,
        'endLine' => 16,
        'startColumn' => 9,
        'endColumn' => 49,
      ),
      'ISO8601' => 
      array (
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'name' => 'ISO8601',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'Y-m-d\\TH:i:sO\'',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 23,
            'startTokenPos' => 50,
            'startFilePos' => 564,
            'endTokenPos' => 50,
            'endFilePos' => 578,
          ),
        ),
        'docComment' => '/**
 * This format is not compatible with ISO-8601, but is left this way for backward compatibility reasons.
 * Use DateTime::ATOM or DATE_ATOM for compatibility with ISO-8601 instead.
 * @since 7.2
 * 
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 9,
        'endColumn' => 47,
      ),
      'ISO8601_EXPANDED' => 
      array (
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'name' => 'ISO8601_EXPANDED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\\DATE_ISO8601_EXPANDED',
          'attributes' => 
          array (
            'startLine' => 27,
            'endLine' => 27,
            'startTokenPos' => 63,
            'startFilePos' => 667,
            'endTokenPos' => 63,
            'endFilePos' => 688,
          ),
        ),
        'docComment' => '/**
 * @since 8.2
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 27,
        'endLine' => 27,
        'startColumn' => 9,
        'endColumn' => 63,
      ),
      'RFC822' => 
      array (
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'name' => 'RFC822',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'D, d M y H:i:s O\'',
          'attributes' => 
          array (
            'startLine' => 31,
            'endLine' => 31,
            'startTokenPos' => 76,
            'startFilePos' => 767,
            'endTokenPos' => 76,
            'endFilePos' => 784,
          ),
        ),
        'docComment' => '/**
 * @since 7.2
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 9,
        'endColumn' => 49,
      ),
      'RFC850' => 
      array (
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'name' => 'RFC850',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'l, d-M-y H:i:s T\'',
          'attributes' => 
          array (
            'startLine' => 35,
            'endLine' => 35,
            'startTokenPos' => 89,
            'startFilePos' => 863,
            'endTokenPos' => 89,
            'endFilePos' => 880,
          ),
        ),
        'docComment' => '/**
 * @since 7.2
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 35,
        'endLine' => 35,
        'startColumn' => 9,
        'endColumn' => 49,
      ),
      'RFC1036' => 
      array (
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'name' => 'RFC1036',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'D, d M y H:i:s O\'',
          'attributes' => 
          array (
            'startLine' => 39,
            'endLine' => 39,
            'startTokenPos' => 102,
            'startFilePos' => 960,
            'endTokenPos' => 102,
            'endFilePos' => 977,
          ),
        ),
        'docComment' => '/**
 * @since 7.2
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 39,
        'endLine' => 39,
        'startColumn' => 9,
        'endColumn' => 50,
      ),
      'RFC1123' => 
      array (
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'name' => 'RFC1123',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'D, d M Y H:i:s O\'',
          'attributes' => 
          array (
            'startLine' => 43,
            'endLine' => 43,
            'startTokenPos' => 115,
            'startFilePos' => 1057,
            'endTokenPos' => 115,
            'endFilePos' => 1074,
          ),
        ),
        'docComment' => '/**
 * @since 7.2
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 43,
        'endLine' => 43,
        'startColumn' => 9,
        'endColumn' => 50,
      ),
      'RFC2822' => 
      array (
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'name' => 'RFC2822',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'D, d M Y H:i:s O\'',
          'attributes' => 
          array (
            'startLine' => 47,
            'endLine' => 47,
            'startTokenPos' => 128,
            'startFilePos' => 1154,
            'endTokenPos' => 128,
            'endFilePos' => 1171,
          ),
        ),
        'docComment' => '/**
 * @since 7.2
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 47,
        'endLine' => 47,
        'startColumn' => 9,
        'endColumn' => 50,
      ),
      'RFC3339' => 
      array (
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'name' => 'RFC3339',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'Y-m-d\\TH:i:sP\'',
          'attributes' => 
          array (
            'startLine' => 51,
            'endLine' => 51,
            'startTokenPos' => 141,
            'startFilePos' => 1251,
            'endTokenPos' => 141,
            'endFilePos' => 1265,
          ),
        ),
        'docComment' => '/**
 * @since 7.2
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 51,
        'endLine' => 51,
        'startColumn' => 9,
        'endColumn' => 47,
      ),
      'RFC3339_EXTENDED' => 
      array (
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'name' => 'RFC3339_EXTENDED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'Y-m-d\\TH:i:s.vP\'',
          'attributes' => 
          array (
            'startLine' => 55,
            'endLine' => 55,
            'startTokenPos' => 154,
            'startFilePos' => 1354,
            'endTokenPos' => 154,
            'endFilePos' => 1370,
          ),
        ),
        'docComment' => '/**
 * @since 7.2
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 55,
        'endLine' => 55,
        'startColumn' => 9,
        'endColumn' => 58,
      ),
      'RFC7231' => 
      array (
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'name' => 'RFC7231',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'D, d M Y H:i:s \\G\\M\\T\'',
          'attributes' => 
          array (
            'startLine' => 60,
            'endLine' => 60,
            'startTokenPos' => 177,
            'startFilePos' => 1506,
            'endTokenPos' => 177,
            'endFilePos' => 1528,
          ),
        ),
        'docComment' => '/**
 * @since 7.2
 */',
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Deprecated',
            'isRepeated' => false,
            'arguments' => 
            array (
              'since' => 
              array (
                'code' => '\'8.5\'',
                'attributes' => 
                array (
                  'startLine' => 59,
                  'endLine' => 59,
                  'startTokenPos' => 165,
                  'startFilePos' => 1467,
                  'endTokenPos' => 165,
                  'endFilePos' => 1471,
                ),
              ),
            ),
          ),
        ),
        'startLine' => 59,
        'endLine' => 60,
        'startColumn' => 9,
        'endColumn' => 55,
      ),
      'RSS' => 
      array (
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'name' => 'RSS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'D, d M Y H:i:s O\'',
          'attributes' => 
          array (
            'startLine' => 64,
            'endLine' => 64,
            'startTokenPos' => 190,
            'startFilePos' => 1604,
            'endTokenPos' => 190,
            'endFilePos' => 1621,
          ),
        ),
        'docComment' => '/**
 * @since 7.2
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 64,
        'endLine' => 64,
        'startColumn' => 9,
        'endColumn' => 46,
      ),
      'W3C' => 
      array (
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'name' => 'W3C',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'Y-m-d\\TH:i:sP\'',
          'attributes' => 
          array (
            'startLine' => 68,
            'endLine' => 68,
            'startTokenPos' => 203,
            'startFilePos' => 1697,
            'endTokenPos' => 203,
            'endFilePos' => 1711,
          ),
        ),
        'docComment' => '/**
 * @since 7.2
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 68,
        'endLine' => 68,
        'startColumn' => 9,
        'endColumn' => 43,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'diff' => 
      array (
        'name' => 'diff',
        'parameters' => 
        array (
          'targetObject' => 
          array (
            'name' => 'targetObject',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'DateTimeInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 83,
            'endLine' => 83,
            'startColumn' => 13,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'absolute' => 
          array (
            'name' => 'absolute',
            'default' => 
            array (
              'code' => '\\false',
              'attributes' => 
              array (
                'startLine' => 85,
                'endLine' => 85,
                'startTokenPos' => 251,
                'startFilePos' => 2585,
                'endTokenPos' => 251,
                'endFilePos' => 2589,
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
                      'startLine' => 84,
                      'endLine' => 84,
                      'startTokenPos' => 229,
                      'startFilePos' => 2523,
                      'endTokenPos' => 235,
                      'endFilePos' => 2539,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 84,
                      'endLine' => 84,
                      'startTokenPos' => 241,
                      'startFilePos' => 2551,
                      'endTokenPos' => 241,
                      'endFilePos' => 2552,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 84,
            'endLine' => 85,
            'startColumn' => 13,
            'endColumn' => 34,
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
            'name' => 'DateInterval',
            'isIdentifier' => false,
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
 * (PHP 5 &gt;=5.5.0)<br/>
 * Returns the difference between two DateTime objects
 * @link https://secure.php.net/manual/en/datetime.diff.php
 * @param DateTimeInterface $targetObject <p>The date to compare to.</p>
 * @param bool $absolute <p>Should the interval be forced to be positive?</p>
 * @return DateInterval
 * The https://secure.php.net/manual/en/class.dateinterval.php DateInterval} object representing the
 * difference between the two dates.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 81,
        'endLine' => 86,
        'startColumn' => 9,
        'endColumn' => 25,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'currentClassName' => 'DateTimeInterface',
        'aliasName' => NULL,
      ),
      'format' => 
      array (
        'name' => 'format',
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
                      'startLine' => 102,
                      'endLine' => 102,
                      'startTokenPos' => 282,
                      'startFilePos' => 3365,
                      'endTokenPos' => 288,
                      'endFilePos' => 3383,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 102,
                      'endLine' => 102,
                      'startTokenPos' => 294,
                      'startFilePos' => 3395,
                      'endTokenPos' => 294,
                      'endFilePos' => 3396,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 102,
            'endLine' => 103,
            'startColumn' => 13,
            'endColumn' => 26,
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
                  'startLine' => 99,
                  'endLine' => 99,
                  'startTokenPos' => 264,
                  'startFilePos' => 3206,
                  'endTokenPos' => 264,
                  'endFilePos' => 3209,
                ),
              ),
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
 * (PHP 5 &gt;=5.5.0)<br/>
 * Returns date formatted according to given format
 * @link https://secure.php.net/manual/en/datetime.format.php
 * @param string $format <p>
 * Format accepted by  {@link https://secure.php.net/manual/en/function.date.php date()}.
 * </p>
 * @return string
 * Returns the formatted date string on success or <b>FALSE</b> on failure.
 * Since PHP8, it always returns <b>STRING</b>.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 99,
        'endLine' => 104,
        'startColumn' => 9,
        'endColumn' => 18,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'currentClassName' => 'DateTimeInterface',
        'aliasName' => NULL,
      ),
      'getOffset' => 
      array (
        'name' => 'getOffset',
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
            'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
            'isRepeated' => false,
            'arguments' => 
            array (
              0 => 
              array (
                'code' => '["8.0" => "int"]',
                'attributes' => 
                array (
                  'startLine' => 113,
                  'endLine' => 113,
                  'startTokenPos' => 313,
                  'startFilePos' => 3834,
                  'endTokenPos' => 319,
                  'endFilePos' => 3849,
                ),
              ),
              'default' => 
              array (
                'code' => '"int|false"',
                'attributes' => 
                array (
                  'startLine' => 113,
                  'endLine' => 113,
                  'startTokenPos' => 325,
                  'startFilePos' => 3861,
                  'endTokenPos' => 325,
                  'endFilePos' => 3871,
                ),
              ),
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
 * (PHP 5 &gt;=5.5.0)<br/>
 * Returns the timezone offset
 * @return int|false
 * Returns the timezone offset in seconds from UTC on success
 * or <b>FALSE</b> on failure. Since PHP8, it always returns <b>INT</b>.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 113,
        'endLine' => 115,
        'startColumn' => 9,
        'endColumn' => 41,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'currentClassName' => 'DateTimeInterface',
        'aliasName' => NULL,
      ),
      'getTimestamp' => 
      array (
        'name' => 'getTimestamp',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
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
          1 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
            'isRepeated' => false,
            'arguments' => 
            array (
              0 => 
              array (
                'code' => '[\'8.1\' => \'int\']',
                'attributes' => 
                array (
                  'startLine' => 124,
                  'endLine' => 124,
                  'startTokenPos' => 354,
                  'startFilePos' => 4313,
                  'endTokenPos' => 360,
                  'endFilePos' => 4328,
                ),
              ),
              'default' => 
              array (
                'code' => '\'int|false\'',
                'attributes' => 
                array (
                  'startLine' => 124,
                  'endLine' => 124,
                  'startTokenPos' => 366,
                  'startFilePos' => 4340,
                  'endTokenPos' => 366,
                  'endFilePos' => 4350,
                ),
              ),
            ),
          ),
        ),
        'docComment' => '/**
 * (PHP 5 &gt;=5.5.0)<br/>
 * Gets the Unix timestamp
 * @return int
 * Returns the Unix timestamp representing the date.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 123,
        'endLine' => 125,
        'startColumn' => 9,
        'endColumn' => 39,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'currentClassName' => 'DateTimeInterface',
        'aliasName' => NULL,
      ),
      'getTimezone' => 
      array (
        'name' => 'getTimezone',
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
                  'name' => 'DateTimeZone',
                  'isIdentifier' => false,
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
 * (PHP 5 &gt;=5.5.0)<br/>
 * Return time zone relative to given DateTime
 * @link https://secure.php.net/manual/en/datetime.gettimezone.php
 * @return DateTimeZone|false
 * Returns a {@link https://secure.php.net/manual/en/class.datetimezone.php DateTimeZone} object on success
 * or <b>FALSE</b> on failure.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 135,
        'endLine' => 136,
        'startColumn' => 9,
        'endColumn' => 59,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'currentClassName' => 'DateTimeInterface',
        'aliasName' => NULL,
      ),
      '__wakeup' => 
      array (
        'name' => '__wakeup',
        'parameters' => 
        array (
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
          1 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Deprecated',
            'isRepeated' => false,
            'arguments' => 
            array (
              'since' => 
              array (
                'code' => '\'8.5\'',
                'attributes' => 
                array (
                  'startLine' => 145,
                  'endLine' => 145,
                  'startTokenPos' => 411,
                  'startFilePos' => 5304,
                  'endTokenPos' => 411,
                  'endFilePos' => 5308,
                ),
              ),
            ),
          ),
        ),
        'docComment' => '/**
 * (PHP 5 &gt;=5.5.0)<br/>
 * The __wakeup handler
 * @link https://secure.php.net/manual/en/datetime.wakeup.php
 * @return void Initializes a DateTime object.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 144,
        'endLine' => 146,
        'startColumn' => 9,
        'endColumn' => 41,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'currentClassName' => 'DateTimeInterface',
        'aliasName' => NULL,
      ),
      '__serialize' => 
      array (
        'name' => '__serialize',
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
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\PhpStormStubsElementAvailable',
            'isRepeated' => false,
            'arguments' => 
            array (
              'from' => 
              array (
                'code' => '\'8.2\'',
                'attributes' => 
                array (
                  'startLine' => 147,
                  'endLine' => 147,
                  'startTokenPos' => 433,
                  'startFilePos' => 5429,
                  'endTokenPos' => 433,
                  'endFilePos' => 5433,
                ),
              ),
            ),
          ),
        ),
        'docComment' => NULL,
        'startLine' => 147,
        'endLine' => 148,
        'startColumn' => 9,
        'endColumn' => 45,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'currentClassName' => 'DateTimeInterface',
        'aliasName' => NULL,
      ),
      '__unserialize' => 
      array (
        'name' => '__unserialize',
        'parameters' => 
        array (
          'data' => 
          array (
            'name' => 'data',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 150,
            'endLine' => 150,
            'startColumn' => 39,
            'endColumn' => 49,
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
            'name' => 'JetBrains\\PhpStorm\\Internal\\PhpStormStubsElementAvailable',
            'isRepeated' => false,
            'arguments' => 
            array (
              'from' => 
              array (
                'code' => '\'8.2\'',
                'attributes' => 
                array (
                  'startLine' => 149,
                  'endLine' => 149,
                  'startTokenPos' => 455,
                  'startFilePos' => 5558,
                  'endTokenPos' => 455,
                  'endFilePos' => 5562,
                ),
              ),
            ),
          ),
        ),
        'docComment' => NULL,
        'startLine' => 149,
        'endLine' => 150,
        'startColumn' => 9,
        'endColumn' => 57,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'currentClassName' => 'DateTimeInterface',
        'aliasName' => NULL,
      ),
      'getMicrosecond' => 
      array (
        'name' => 'getMicrosecond',
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
 * @since 8.4
 */',
        'startLine' => 154,
        'endLine' => 154,
        'startColumn' => 9,
        'endColumn' => 46,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'DateTimeInterface',
        'implementingClassName' => 'DateTimeInterface',
        'currentClassName' => 'DateTimeInterface',
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