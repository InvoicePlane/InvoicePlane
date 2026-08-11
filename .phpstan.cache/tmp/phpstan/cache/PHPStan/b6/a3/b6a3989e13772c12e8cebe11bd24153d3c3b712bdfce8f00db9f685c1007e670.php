<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-json_last_error_msg
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'name' => 'json_last_error_msg',
    'parameters' => 
    array (
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
              'startLine' => 10,
              'endLine' => 10,
              'startTokenPos' => 11,
              'startFilePos' => 384,
              'endTokenPos' => 11,
              'endFilePos' => 387,
            ),
          ),
        ),
      ),
    ),
    'docComment' => '/**
 * Returns the error string of the last json_encode() or json_decode() call, which did not specify <b>JSON_THROW_ON_ERROR</b>.
 * @link https://php.net/manual/en/function.json-last-error-msg.php
 * @return string Returns the error message on success, "No error" if no error has occurred.
 * @since 5.5
 */',
    'startLine' => 10,
    'endLine' => 13,
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
        'name' => 'json_last_error_msg',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/json/json.stub',
        'extensionName' => 'json',
        'aliasName' => NULL,
      ),
    ),
  ),
));