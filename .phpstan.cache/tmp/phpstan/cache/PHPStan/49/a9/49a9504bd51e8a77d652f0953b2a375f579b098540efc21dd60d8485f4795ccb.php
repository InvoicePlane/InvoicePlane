<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/vendor/pocketarc/codeigniter/system/core/compat/mbstring.php-PHPStan\BetterReflection\Reflection\ReflectionFunction-mb_strpos
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-aa0b8d652e4dfd2166196184a778f07f4bef7cc23c61bc3c865e4d2e05ec6451',
   'data' => 
  array (
    'name' => 'mb_strpos',
    'parameters' => 
    array (
      'haystack' => 
      array (
        'name' => 'haystack',
        'default' => NULL,
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 103,
        'endLine' => 103,
        'startColumn' => 21,
        'endColumn' => 29,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'needle' => 
      array (
        'name' => 'needle',
        'default' => NULL,
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 103,
        'endLine' => 103,
        'startColumn' => 32,
        'endColumn' => 38,
        'parameterIndex' => 1,
        'isOptional' => false,
      ),
      'offset' => 
      array (
        'name' => 'offset',
        'default' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 103,
            'endLine' => 103,
            'startTokenPos' => 163,
            'startFilePos' => 3279,
            'endTokenPos' => 163,
            'endFilePos' => 3279,
          ),
        ),
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 103,
        'endLine' => 103,
        'startColumn' => 41,
        'endColumn' => 51,
        'parameterIndex' => 2,
        'isOptional' => true,
      ),
      'encoding' => 
      array (
        'name' => 'encoding',
        'default' => 
        array (
          'code' => '\\NULL',
          'attributes' => 
          array (
            'startLine' => 103,
            'endLine' => 103,
            'startTokenPos' => 170,
            'startFilePos' => 3294,
            'endTokenPos' => 170,
            'endFilePos' => 3297,
          ),
        ),
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 103,
        'endLine' => 103,
        'startColumn' => 54,
        'endColumn' => 69,
        'parameterIndex' => 3,
        'isOptional' => true,
      ),
    ),
    'returnsReference' => false,
    'returnType' => NULL,
    'attributes' => 
    array (
    ),
    'docComment' => '/**
 * mb_strpos()
 *
 * WARNING: This function WILL fall-back to strpos()
 * if iconv is not available!
 *
 * @link	https://secure.php.net/mb_strpos
 * @param	string	$haystack
 * @param	string	$needle
 * @param	int	$offset
 * @param	string	$encoding
 * @return	mixed
 */',
    'startLine' => 103,
    'endLine' => 112,
    'startColumn' => 2,
    'endColumn' => 2,
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
        'name' => 'mb_strpos',
        'filename' => '/var/www/projects/exprmt/vendor/pocketarc/codeigniter/system/core/compat/mbstring.php',
      ),
    ),
  ),
));