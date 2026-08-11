<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-pathinfo
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'name' => 'pathinfo',
    'parameters' => 
    array (
      'path' => 
      array (
        'name' => 'path',
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
        'startColumn' => 9,
        'endColumn' => 20,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'flags' => 
      array (
        'name' => 'flags',
        'default' => 
        array (
          'code' => '\\PATHINFO_ALL',
          'attributes' => 
          array (
            'startLine' => 32,
            'endLine' => 32,
            'startTokenPos' => 86,
            'startFilePos' => 1245,
            'endTokenPos' => 86,
            'endFilePos' => 1256,
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
            'name' => 'JetBrains\\PhpStorm\\ExpectedValues',
            'isRepeated' => false,
            'arguments' => 
            array (
              'flags' => 
              array (
                'code' => '[\\PATHINFO_DIRNAME, \\PATHINFO_BASENAME, \\PATHINFO_EXTENSION, \\PATHINFO_FILENAME]',
                'attributes' => 
                array (
                  'startLine' => 31,
                  'endLine' => 31,
                  'startTokenPos' => 65,
                  'startFilePos' => 1145,
                  'endTokenPos' => 76,
                  'endFilePos' => 1220,
                ),
              ),
            ),
          ),
        ),
        'startLine' => 31,
        'endLine' => 32,
        'startColumn' => 9,
        'endColumn' => 33,
        'parameterIndex' => 1,
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
        ),
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
              'startLine' => 27,
              'endLine' => 27,
              'startTokenPos' => 11,
              'startFilePos' => 905,
              'endTokenPos' => 11,
              'endFilePos' => 908,
            ),
          ),
        ),
      ),
      1 => 
      array (
        'name' => 'JetBrains\\PhpStorm\\ArrayShape',
        'isRepeated' => false,
        'arguments' => 
        array (
          0 => 
          array (
            'code' => '[\'dirname\' => \'string\', \'basename\' => \'string\', \'extension\' => \'string\', \'filename\' => \'string\']',
            'attributes' => 
            array (
              'startLine' => 28,
              'endLine' => 28,
              'startTokenPos' => 18,
              'startFilePos' => 949,
              'endTokenPos' => 45,
              'endFilePos' => 1044,
            ),
          ),
        ),
      ),
    ),
    'docComment' => '/**
 * Returns information about a file path
 * @link https://php.net/manual/en/function.pathinfo.php
 * @param string $path <p>
 * The path being checked.
 * </p>
 * @param int $flags [optional] <p>
 * You can specify which elements are returned with optional parameter
 * options. It composes from
 * PATHINFO_DIRNAME,
 * PATHINFO_BASENAME,
 * PATHINFO_EXTENSION and
 * PATHINFO_FILENAME. It
 * defaults to return all elements.
 * </p>
 * @return string|array{dirname: string, basename: string, extension: string, filename: string} The following associative array elements are returned:
 * dirname, basename,
 * extension (if any), and filename.
 * </p>
 * <p>
 * If options is used, this function will return a
 * string if not all elements are requested.
 */',
    'startLine' => 27,
    'endLine' => 35,
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
        'name' => 'pathinfo',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/standard/standard_1.stub',
        'extensionName' => 'standard',
        'aliasName' => NULL,
      ),
    ),
  ),
));