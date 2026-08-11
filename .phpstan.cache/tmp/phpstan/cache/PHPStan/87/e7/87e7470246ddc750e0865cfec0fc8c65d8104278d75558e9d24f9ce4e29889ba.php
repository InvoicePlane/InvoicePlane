<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/application/modules/settings/controllers/Settings.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Settings
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-0d89d49c8d8378e72f96b7fb734b4391cf724eb9c911f75f6014a1f43c0c0899',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Settings',
        'filename' => '/var/www/projects/exprmt/application/modules/settings/controllers/Settings.php',
      ),
    ),
    'namespace' => NULL,
    'name' => 'Settings',
    'shortName' => 'Settings',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
      0 => 
      array (
        'name' => 'AllowDynamicProperties',
        'isRepeated' => false,
        'arguments' => 
        array (
        ),
      ),
    ),
    'startLine' => 16,
    'endLine' => 451,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Admin_Controller',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'MIN_TAX_RATE_DECIMALS' => 
      array (
        'declaringClassName' => 'Settings',
        'implementingClassName' => 'Settings',
        'name' => 'MIN_TAX_RATE_DECIMALS',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '2',
          'attributes' => 
          array (
            'startLine' => 19,
            'endLine' => 19,
            'startTokenPos' => 48,
            'startFilePos' => 430,
            'endTokenPos' => 48,
            'endFilePos' => 430,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 5,
        'endColumn' => 44,
      ),
      'MAX_TAX_RATE_DECIMALS' => 
      array (
        'declaringClassName' => 'Settings',
        'implementingClassName' => 'Settings',
        'name' => 'MAX_TAX_RATE_DECIMALS',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '3',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 21,
            'startTokenPos' => 59,
            'startFilePos' => 476,
            'endTokenPos' => 59,
            'endFilePos' => 476,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 44,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Settings constructor.
 */',
        'startLine' => 26,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Settings',
        'implementingClassName' => 'Settings',
        'currentClassName' => 'Settings',
        'aliasName' => NULL,
      ),
      'index' => 
      array (
        'name' => 'index',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 40,
        'endLine' => 214,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Settings',
        'implementingClassName' => 'Settings',
        'currentClassName' => 'Settings',
        'aliasName' => NULL,
      ),
      'remove_logo' => 
      array (
        'name' => 'remove_logo',
        'parameters' => 
        array (
          'type' => 
          array (
            'name' => 'type',
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
            'startLine' => 224,
            'endLine' => 224,
            'startColumn' => 33,
            'endColumn' => 44,
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
 * Remove a logo file with security validation.
 *
 * Security: Validates that the logo file path is safe and within the uploads directory
 * to prevent arbitrary file deletion attacks. Requires POST request and valid CSRF token.
 *
 * @param string $type Logo type (\'invoice\' or \'login\')
 */',
        'startLine' => 224,
        'endLine' => 314,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Settings',
        'implementingClassName' => 'Settings',
        'currentClassName' => 'Settings',
        'aliasName' => NULL,
      ),
      'check_svg_logos' => 
      array (
        'name' => 'check_svg_logos',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Check for SVG logos and display security warnings
 * This provides a soft migration path for existing SVG logos.
 */',
        'startLine' => 320,
        'endLine' => 337,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'Settings',
        'implementingClassName' => 'Settings',
        'currentClassName' => 'Settings',
        'aliasName' => NULL,
      ),
      'handleTaxRateDecimalPlaces' => 
      array (
        'name' => 'handleTaxRateDecimalPlaces',
        'parameters' => 
        array (
          'settings' => 
          array (
            'name' => 'settings',
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
            'startLine' => 346,
            'endLine' => 346,
            'startColumn' => 49,
            'endColumn' => 63,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Validate and persist tax rate decimal places setting, including schema changes.
 *
 * @param array $settings
 *
 * @return array settings array, with tax_rate_decimal_places removed if it was processed
 */',
        'startLine' => 346,
        'endLine' => 429,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'Settings',
        'implementingClassName' => 'Settings',
        'currentClassName' => 'Settings',
        'aliasName' => NULL,
      ),
      'strip_logo_metadata' => 
      array (
        'name' => 'strip_logo_metadata',
        'parameters' => 
        array (
          'filePath' => 
          array (
            'name' => 'filePath',
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
            'startLine' => 439,
            'endLine' => 439,
            'startColumn' => 42,
            'endColumn' => 57,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'logoType' => 
          array (
            'name' => 'logoType',
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
            'startLine' => 439,
            'endLine' => 439,
            'startColumn' => 60,
            'endColumn' => 75,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
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
 * Strip EXIF metadata from uploaded logo file.
 *
 * @param string $filePath The full path to the uploaded file
 * @param string $logoType The type of logo (invoice_logo or login_logo)
 *
 * @return void
 */',
        'startLine' => 439,
        'endLine' => 450,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'Settings',
        'implementingClassName' => 'Settings',
        'currentClassName' => 'Settings',
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