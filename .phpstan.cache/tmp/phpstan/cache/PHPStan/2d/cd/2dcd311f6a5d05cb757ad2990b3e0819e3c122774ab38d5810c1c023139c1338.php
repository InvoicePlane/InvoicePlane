<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-extension_loaded
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'name' => 'extension_loaded',
    'parameters' => 
    array (
      'extension' => 
      array (
        'name' => 'extension',
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
        'startLine' => 37,
        'endLine' => 37,
        'startColumn' => 31,
        'endColumn' => 47,
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
        'name' => 'bool',
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
 * Find out whether an extension is loaded
 * @link https://php.net/manual/en/function.extension-loaded.php
 * @param string $extension <p>
 * The extension name.
 * </p>
 * <p>
 * You can see the names of various extensions by using
 * <b>phpinfo</b> or if you\'re using the
 * CGI or CLI version of
 * PHP you can use the -m switch to
 * list all available extensions:
 * <pre>
 * $ php -m
 * [PHP Modules]
 * xml
 * tokenizer
 * standard
 * sockets
 * session
 * posix
 * pcre
 * overload
 * mysql
 * mbstring
 * ctype
 * [Zend Modules]
 * </pre>
 * </p>
 * @return bool true if the extension identified by <i>name</i>
 * is loaded, false otherwise.
 */',
    'startLine' => 36,
    'endLine' => 39,
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
        'name' => 'extension_loaded',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/Core/Core.stub',
        'extensionName' => 'Core',
        'aliasName' => NULL,
      ),
    ),
  ),
));