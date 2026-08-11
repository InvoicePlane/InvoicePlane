<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-mkdir
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'name' => 'mkdir',
    'parameters' => 
    array (
      'directory' => 
      array (
        'name' => 'directory',
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
        'startColumn' => 20,
        'endColumn' => 36,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'permissions' => 
      array (
        'name' => 'permissions',
        'default' => 
        array (
          'code' => '0777',
          'attributes' => 
          array (
            'startLine' => 30,
            'endLine' => 30,
            'startTokenPos' => 23,
            'startFilePos' => 1058,
            'endTokenPos' => 23,
            'endFilePos' => 1061,
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
        'startColumn' => 39,
        'endColumn' => 61,
        'parameterIndex' => 1,
        'isOptional' => true,
      ),
      'recursive' => 
      array (
        'name' => 'recursive',
        'default' => 
        array (
          'code' => '\\false',
          'attributes' => 
          array (
            'startLine' => 30,
            'endLine' => 30,
            'startTokenPos' => 32,
            'startFilePos' => 1082,
            'endTokenPos' => 32,
            'endFilePos' => 1086,
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
        ),
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 64,
        'endColumn' => 86,
        'parameterIndex' => 2,
        'isOptional' => true,
      ),
      'context' => 
      array (
        'name' => 'context',
        'default' => 
        array (
          'code' => '\\null',
          'attributes' => 
          array (
            'startLine' => 30,
            'endLine' => 30,
            'startTokenPos' => 39,
            'startFilePos' => 1100,
            'endTokenPos' => 39,
            'endFilePos' => 1103,
          ),
        ),
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 89,
        'endColumn' => 103,
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
        'name' => 'bool',
        'isIdentifier' => true,
      ),
    ),
    'attributes' => 
    array (
    ),
    'docComment' => '/**
 * Attempts to create the directory specified by pathname.
 * @link https://php.net/manual/en/function.mkdir.php
 * @param string $directory <p>
 * The directory path.
 * </p>
 * @param int $permissions [optional] <p>
 * The mode is 0777 by default, which means the widest possible
 * access. For more information on modes, read the details
 * on the chmod page.
 * </p>
 * <p>
 * mode is ignored on Windows.
 * </p>
 * <p>
 * Note that you probably want to specify the mode as an octal number,
 * which means it should have a leading zero. The mode is also modified
 * by the current umask, which you can change using
 * umask().
 * </p>
 * @param bool $recursive [optional] <p>
 * Allows the creation of nested directories specified in the pathname. Default to false.
 * </p>
 * @param resource $context [optional]
 * @return bool true on success or false on failure.
 */',
    'startLine' => 30,
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
        'name' => 'mkdir',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/standard/standard_5.stub',
        'extensionName' => 'standard',
        'aliasName' => NULL,
      ),
    ),
  ),
));