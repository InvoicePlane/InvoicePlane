<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-json_last_error
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'name' => 'json_last_error',
    'parameters' => 
    array (
    ),
    'returnsReference' => false,
    'returnType' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
      'data' => 
      array (
        'name' => 'int',
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
              'startLine' => 95,
              'endLine' => 95,
              'startTokenPos' => 11,
              'startFilePos' => 2839,
              'endTokenPos' => 11,
              'endFilePos' => 2842,
            ),
          ),
        ),
      ),
    ),
    'docComment' => '/**
 * Returns the last error occurred
 * @link https://php.net/manual/en/function.json-last-error.php
 * @return int an integer, the value can be one of the following
 * constants:
 * <table class=\'doctable table\'>
 * <thead>
 * <tr>
 * <th>Constant</th>
 * <th>Meaning</th>
 * <th>Availability</th>
 * </tr>
 *
 * </thead>
 *
 * <tbody class=\'tbody\'>
 * <tr>
 * <td><strong><code>JSON_ERROR_NONE</code></strong></td>
 * <td>No error has occurred</td>
 * <td class=\'empty\'>&nbsp;</td>
 * </tr>
 *
 * <tr>
 * <td><strong><code>JSON_ERROR_DEPTH</code></strong></td>
 * <td>The maximum stack depth has been exceeded</td>
 * <td class=\'empty\'>&nbsp;</td>
 * </tr>
 *
 * <tr>
 * <td><strong><code>JSON_ERROR_STATE_MISMATCH</code></strong></td>
 * <td>Invalid or malformed JSON</td>
 * <td class=\'empty\'>&nbsp;</td>
 * </tr>
 *
 * <tr>
 * <td><strong><code>JSON_ERROR_CTRL_CHAR</code></strong></td>
 * <td>Control character error, possibly incorrectly encoded</td>
 * <td class=\'empty\'>&nbsp;</td>
 * </tr>
 *
 * <tr>
 * <td><strong><code>JSON_ERROR_SYNTAX</code></strong></td>
 * <td>Syntax error</td>
 * <td class=\'empty\'>&nbsp;</td>
 * </tr>
 *
 * <tr>
 * <td><strong><code>JSON_ERROR_UTF8</code></strong></td>
 * <td>Malformed UTF-8 characters, possibly incorrectly encoded</td>
 * <td>PHP 5.3.3</td>
 * </tr>
 *
 * <tr>
 * <td><strong><code>JSON_ERROR_RECURSION</code></strong></td>
 * <td>One or more recursive references in the value to be encoded</td>
 * <td>PHP 5.5.0</td>
 * </tr>
 *
 * <tr>
 * <td><strong><code>JSON_ERROR_INF_OR_NAN</code></strong></td>
 * <td>
 * One or more
 * <a href=\'language.types.float.php#language.types.float.nan\' class=\'link\'><strong><code>NAN</code></strong></a>
 * or <a href=\'function.is-infinite.php\' class=\'link\'><strong><code>INF</code></strong></a>
 * values in the value to be encoded
 * </td>
 * <td>PHP 5.5.0</td>
 * </tr>
 *
 * <tr>
 * <td><strong><code>JSON_ERROR_UNSUPPORTED_TYPE</code></strong></td>
 * <td>A value of a type that cannot be encoded was given</td>
 * <td>PHP 5.5.0</td>
 * </tr>
 *
 * <tr>
 * <td><strong><code>JSON_ERROR_INVALID_PROPERTY_NAME</code></strong></td>
 * <td>A property name that cannot be encoded was given</td>
 * <td>PHP 7.0.0</td>
 * </tr>
 *
 * <tr>
 * <td><strong><code>JSON_ERROR_UTF16</code></strong></td>
 * <td>Malformed UTF-16 characters, possibly incorrectly encoded</td>
 * <td>PHP 7.0.0</td>
 * </tr>
 *
 * </tbody>
 *
 * </table>
 */',
    'startLine' => 95,
    'endLine' => 98,
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
        'name' => 'json_last_error',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/json/json.stub',
        'extensionName' => 'json',
        'aliasName' => NULL,
      ),
    ),
  ),
));