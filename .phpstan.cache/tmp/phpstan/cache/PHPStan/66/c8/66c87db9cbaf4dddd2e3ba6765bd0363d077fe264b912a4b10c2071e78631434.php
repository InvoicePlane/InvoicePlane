<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-dirname
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'name' => 'dirname',
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
        'startLine' => 27,
        'endLine' => 27,
        'startColumn' => 9,
        'endColumn' => 20,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'levels' => 
      array (
        'name' => 'levels',
        'default' => 
        array (
          'code' => '1',
          'attributes' => 
          array (
            'startLine' => 29,
            'endLine' => 29,
            'startTokenPos' => 38,
            'startFilePos' => 944,
            'endTokenPos' => 38,
            'endFilePos' => 944,
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
                'code' => '\'7.0\'',
                'attributes' => 
                array (
                  'startLine' => 28,
                  'endLine' => 28,
                  'startTokenPos' => 28,
                  'startFilePos' => 914,
                  'endTokenPos' => 28,
                  'endFilePos' => 918,
                ),
              ),
            ),
          ),
        ),
        'startLine' => 28,
        'endLine' => 29,
        'startColumn' => 9,
        'endColumn' => 23,
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
        ),
      ),
    ),
    'docComment' => '/**
 * Returns a parent directory\'s path
 * @link https://php.net/manual/en/function.dirname.php
 * @param string $path <p>
 * A path.
 * </p>
 * <p>
 * On Windows, both slash (/) and backslash
 * (\\) are used as directory separator character. In
 * other environments, it is the forward slash (/).
 * </p>
 * @param int $levels <p>
 * The number of parent directories to go up.
 * This must be an integer greater than 0.
 * </p>
 * @return string the name of the directory. If there are no slashes in
 * path, a dot (\'.\') is returned,
 * indicating the current directory. Otherwise, the returned string is
 * path with any trailing
 * /component removed.
 */',
    'startLine' => 25,
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
        'name' => 'dirname',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/standard/standard_1.stub',
        'extensionName' => 'standard',
        'aliasName' => NULL,
      ),
    ),
  ),
));