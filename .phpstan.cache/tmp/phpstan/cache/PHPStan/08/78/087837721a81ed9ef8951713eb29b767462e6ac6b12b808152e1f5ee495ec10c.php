<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-openssl_random_pseudo_bytes
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'name' => 'openssl_random_pseudo_bytes',
    'parameters' => 
    array (
      'length' => 
      array (
        'name' => 'length',
        'default' => NULL,
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
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 42,
        'endColumn' => 52,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'strong_result' => 
      array (
        'name' => 'strong_result',
        'default' => 
        array (
          'code' => '\\null',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 21,
            'startTokenPos' => 41,
            'startFilePos' => 1241,
            'endTokenPos' => 41,
            'endFilePos' => 1244,
          ),
        ),
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => true,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 55,
        'endColumn' => 76,
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
        'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
        'isRepeated' => false,
        'arguments' => 
        array (
          0 => 
          array (
            'code' => '["7.4" => "string"]',
            'attributes' => 
            array (
              'startLine' => 20,
              'endLine' => 20,
              'startTokenPos' => 11,
              'startFilePos' => 1122,
              'endTokenPos' => 17,
              'endFilePos' => 1140,
            ),
          ),
          'default' => 
          array (
            'code' => '"string|false"',
            'attributes' => 
            array (
              'startLine' => 20,
              'endLine' => 20,
              'startTokenPos' => 23,
              'startFilePos' => 1152,
              'endTokenPos' => 23,
              'endFilePos' => 1165,
            ),
          ),
        ),
      ),
    ),
    'docComment' => '/**
 * Generates a string of pseudo-random bytes, with the number of bytes determined by the length parameter.
 * <p>It also indicates if a cryptographically strong algorithm was used to produce the pseudo-random bytes,
 * and does this via the optional crypto_strong parameter. It\'s rare for this to be FALSE, but some systems may be broken or old.</p>
 * @link https://php.net/manual/en/function.openssl-random-pseudo-bytes.php
 * @param positive-int $length <p>
 * The length of the desired string of bytes. Must be a positive integer. PHP will
 * try to cast this parameter to a non-null integer to use it.
 * </p>
 * @param bool &$strong_result [optional]<p>
 * If passed into the function, this will hold a boolean value that determines
 * if the algorithm used was "cryptographically strong", e.g., safe for usage with GPG,
 * passwords, etc. true if it did, otherwise false
 * </p>
 * @return string|false the generated string of bytes on success, or false on failure.
 */',
    'startLine' => 20,
    'endLine' => 23,
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
        'name' => 'openssl_random_pseudo_bytes',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/openssl/openssl.stub',
        'extensionName' => 'openssl',
        'aliasName' => NULL,
      ),
    ),
  ),
));