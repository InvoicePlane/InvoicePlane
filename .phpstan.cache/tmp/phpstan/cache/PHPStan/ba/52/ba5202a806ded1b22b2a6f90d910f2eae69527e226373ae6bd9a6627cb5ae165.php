<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/vendor/composer/../paragonie/random_compat/lib/random.php-PHPStan\BetterReflection\Reflection\ReflectionFunction-random_bytes
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-ef4b72c4bab30fb5d210dac8104b42f058839f9d5177e7192a8d0d6b1852654e',
   'data' => 
  array (
    'name' => 'random_bytes',
    'parameters' => 
    array (
      'length' => 
      array (
        'name' => 'length',
        'default' => NULL,
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 211,
        'endLine' => 211,
        'startColumn' => 31,
        'endColumn' => 37,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
    ),
    'returnsReference' => false,
    'returnType' => NULL,
    'attributes' => 
    array (
    ),
    'docComment' => '/**
 * We don\'t have any more options, so let\'s throw an exception right now
 * and hope the developer won\'t let it fail silently.
 *
 * @param mixed $length
 * @psalm-suppress InvalidReturnType
 * @throws Exception
 * @return string
 */',
    'startLine' => 211,
    'endLine' => 218,
    'startColumn' => 9,
    'endColumn' => 9,
    'couldThrow' => false,
    'isClosure' => false,
    'isGenerator' => false,
    'isVariadic' => false,
    'isStatic' => false,
    'namespace' => NULL,
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'random_bytes',
        'filename' => '/var/www/projects/exprmt/vendor/composer/../paragonie/random_compat/lib/random.php',
      ),
    ),
  ),
));