<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/vendor/pocketarc/codeigniter/system/core/compat/mbstring.php-PHPStan\BetterReflection\Reflection\ReflectionFunction-mb_substr
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-aa0b8d652e4dfd2166196184a778f07f4bef7cc23c61bc3c865e4d2e05ec6451',
   'data' => 
  array (
    'name' => 'mb_substr',
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
        'startLine' => 132,
        'endLine' => 132,
        'startColumn' => 21,
        'endColumn' => 24,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'start' => 
      array (
        'name' => 'start',
        'default' => NULL,
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 132,
        'endLine' => 132,
        'startColumn' => 27,
        'endColumn' => 32,
        'parameterIndex' => 1,
        'isOptional' => false,
      ),
      'length' => 
      array (
        'name' => 'length',
        'default' => 
        array (
          'code' => '\\NULL',
          'attributes' => 
          array (
            'startLine' => 132,
            'endLine' => 132,
            'startTokenPos' => 279,
            'startFilePos' => 4052,
            'endTokenPos' => 279,
            'endFilePos' => 4055,
          ),
        ),
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 132,
        'endLine' => 132,
        'startColumn' => 35,
        'endColumn' => 48,
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
            'startLine' => 132,
            'endLine' => 132,
            'startTokenPos' => 286,
            'startFilePos' => 4070,
            'endTokenPos' => 286,
            'endFilePos' => 4073,
          ),
        ),
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 132,
        'endLine' => 132,
        'startColumn' => 51,
        'endColumn' => 66,
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
 * mb_substr()
 *
 * WARNING: This function WILL fall-back to substr()
 * if iconv is not available.
 *
 * @link	https://secure.php.net/mb_substr
 * @param	string	$str
 * @param	int	$start
 * @param	int 	$length
 * @param	string	$encoding
 * @return	string
 */',
    'startLine' => 132,
    'endLine' => 149,
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
        'name' => 'mb_substr',
        'filename' => '/var/www/projects/exprmt/vendor/pocketarc/codeigniter/system/core/compat/mbstring.php',
      ),
    ),
  ),
));