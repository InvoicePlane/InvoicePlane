<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/application/core/XSS_Protection_Trait.php-PHPStan\BetterReflection\Reflection\ReflectionClass-XSS_Protection_Trait
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-fa51624dbad863d77bfa90c2036fe149eb636bba16e899c32e3637a939f54737',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'XSS_Protection_Trait',
        'filename' => '/var/www/projects/exprmt/application/core/XSS_Protection_Trait.php',
      ),
    ),
    'namespace' => NULL,
    'name' => 'XSS_Protection_Trait',
    'shortName' => 'XSS_Protection_Trait',
    'isInterface' => false,
    'isTrait' => true,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * XSS Protection Trait.
 *
 * Provides XSS filtering methods for controllers to prevent cross-site scripting attacks.
 * Used by Admin_Controller and Guest_Controller to ensure consistent input sanitization.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 22,
    'endLine' => 205,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'filter_input' => 
      array (
        'name' => 'filter_input',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Filter and sanitize POST input to prevent XSS attacks.
 *
 * This method processes all POST data and applies appropriate sanitization:
 * - HTML fields (email_template_body, body): Sanitized with HTML Purifier
 * - Bypass fields (passwords): No sanitization to allow special characters
 * - All other fields: XSS cleaned and tags stripped
 *
 * @return void
 */',
        'startLine' => 34,
        'endLine' => 152,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => NULL,
        'declaringClassName' => 'XSS_Protection_Trait',
        'implementingClassName' => 'XSS_Protection_Trait',
        'currentClassName' => 'XSS_Protection_Trait',
        'aliasName' => NULL,
      ),
      'sanitize_array' => 
      array (
        'name' => 'sanitize_array',
        'parameters' => 
        array (
          'data' => 
          array (
            'name' => 'data',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 166,
            'endLine' => 166,
            'startColumn' => 9,
            'endColumn' => 19,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'bypass_keys' => 
          array (
            'name' => 'bypass_keys',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 167,
                'endLine' => 167,
                'startTokenPos' => 733,
                'startFilePos' => 6315,
                'endTokenPos' => 734,
                'endFilePos' => 6316,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 167,
            'endLine' => 167,
            'startColumn' => 9,
            'endColumn' => 31,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'path_prefix' => 
          array (
            'name' => 'path_prefix',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 168,
                'endLine' => 168,
                'startTokenPos' => 743,
                'startFilePos' => 6349,
                'endTokenPos' => 743,
                'endFilePos' => 6350,
              ),
            ),
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
            'startLine' => 168,
            'endLine' => 168,
            'startColumn' => 9,
            'endColumn' => 32,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'xss_detected' => 
          array (
            'name' => 'xss_detected',
            'default' => 
            array (
              'code' => '\\false',
              'attributes' => 
              array (
                'startLine' => 169,
                'endLine' => 169,
                'startTokenPos' => 753,
                'startFilePos' => 6383,
                'endTokenPos' => 753,
                'endFilePos' => 6387,
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
            'byRef' => true,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 169,
            'endLine' => 169,
            'startColumn' => 9,
            'endColumn' => 35,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
          'xss_log_entries' => 
          array (
            'name' => 'xss_log_entries',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 170,
                'endLine' => 170,
                'startTokenPos' => 763,
                'startFilePos' => 6424,
                'endTokenPos' => 764,
                'endFilePos' => 6425,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => true,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 170,
            'endLine' => 170,
            'startColumn' => 9,
            'endColumn' => 36,
            'parameterIndex' => 4,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Recursively sanitize array values.
 *
 * @param array  $data            The array to sanitize
 * @param array  $bypass_keys     Keys that should bypass sanitization
 * @param string $path_prefix     Prefix for tracking nested field paths
 * @param bool   $xss_detected    Reference to XSS detection flag
 * @param array  $xss_log_entries Reference to XSS log entries array
 *
 * @return array Sanitized array
 */',
        'startLine' => 165,
        'endLine' => 204,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'XSS_Protection_Trait',
        'implementingClassName' => 'XSS_Protection_Trait',
        'currentClassName' => 'XSS_Protection_Trait',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));