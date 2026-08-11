<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/vendor/pocketarc/codeigniter/system/core/compat/mbstring.php-PHPStan\BetterReflection\Reflection\ReflectionFunction-mb_strlen
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-aa0b8d652e4dfd2166196184a778f07f4bef7cc23c61bc3c865e4d2e05ec6451',
   'data' => 
  array (
    'name' => 'mb_strlen',
    'parameters' => 
    array (
      'str' => 
      array (
        'name' => 'str',
        'default' => NULL,
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 74,
        'endLine' => 74,
        'startColumn' => 21,
        'endColumn' => 24,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'encoding' => 
      array (
        'name' => 'encoding',
        'default' => 
        array (
          'code' => '\\NULL',
          'attributes' => 
          array (
            'startLine' => 74,
            'endLine' => 74,
            'startTokenPos' => 66,
            'startFilePos' => 2554,
            'endTokenPos' => 66,
            'endFilePos' => 2557,
          ),
        ),
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 74,
        'endLine' => 74,
        'startColumn' => 27,
        'endColumn' => 42,
        'parameterIndex' => 1,
        'isOptional' => true,
      ),
    ),
    'returnsReference' => false,
    'returnType' => NULL,
    'attributes' => 
    array (
    ),
    'docComment' => '/**
 * mb_strlen()
 *
 * WARNING: This function WILL fall-back to strlen()
 * if iconv is not available!
 *
 * @link	https://secure.php.net/mb_strlen
 * @param	string	$str
 * @param	string	$encoding
 * @return	int
 */',
    'startLine' => 74,
    'endLine' => 83,
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
        'name' => 'mb_strlen',
        'filename' => '/var/www/projects/exprmt/vendor/pocketarc/codeigniter/system/core/compat/mbstring.php',
      ),
    ),
  ),
));