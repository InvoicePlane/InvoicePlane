<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/application/modules/invoices/models/Mdl_templates.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Mdl_Templates
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-8709765efaaa844f3f3845824f52b2ac699e74510fddc0b16a58eba5d67aa17f',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Mdl_Templates',
        'filename' => '/var/www/projects/exprmt/application/modules/invoices/models/Mdl_templates.php',
      ),
    ),
    'namespace' => NULL,
    'name' => 'Mdl_Templates',
    'shortName' => 'Mdl_Templates',
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
    'endLine' => 276,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'CI_Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'ALLOWED_INVOICE_TEMPLATES' => 
      array (
        'declaringClassName' => 'Mdl_Templates',
        'implementingClassName' => 'Mdl_Templates',
        'name' => 'ALLOWED_INVOICE_TEMPLATES',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'pdf\' => [\'InvoicePlane\', \'InvoicePlane - paid\', \'InvoicePlane - overdue\'], \'public\' => [\'InvoicePlane_Web\']]',
          'attributes' => 
          array (
            'startLine' => 33,
            'endLine' => 42,
            'startTokenPos' => 50,
            'startFilePos' => 971,
            'endTokenPos' => 82,
            'endFilePos' => 1173,
          ),
        ),
        'docComment' => '/**
 * Static whitelist of allowed invoice templates.
 *
 * Security: This is a hardcoded list to prevent RCE vulnerability.
 * Templates are NEVER loaded dynamically from the filesystem.
 * Only templates in this list can be used, even if other PHP files exist in the templates directory.
 *
 * To add a new template:
 * 1. Add the template file to the appropriate directory
 * 2. Add the template name (without .php extension) to this array
 * 3. Deploy both changes together
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 33,
        'endLine' => 42,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
      'ALLOWED_QUOTE_TEMPLATES' => 
      array (
        'declaringClassName' => 'Mdl_Templates',
        'implementingClassName' => 'Mdl_Templates',
        'name' => 'ALLOWED_QUOTE_TEMPLATES',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'pdf\' => [\'InvoicePlane\'], \'public\' => [\'InvoicePlane_Web\']]',
          'attributes' => 
          array (
            'startLine' => 52,
            'endLine' => 59,
            'startTokenPos' => 95,
            'startFilePos' => 1460,
            'endTokenPos' => 121,
            'endFilePos' => 1589,
          ),
        ),
        'docComment' => '/**
 * Static whitelist of allowed quote templates.
 *
 * Security: This is a hardcoded list to prevent RCE vulnerability.
 * Templates are NEVER loaded dynamically from the filesystem.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 52,
        'endLine' => 59,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'get_invoice_templates' => 
      array (
        'name' => 'get_invoice_templates',
        'parameters' => 
        array (
          'type' => 
          array (
            'name' => 'type',
            'default' => 
            array (
              'code' => '\'pdf\'',
              'attributes' => 
              array (
                'startLine' => 74,
                'endLine' => 74,
                'startTokenPos' => 136,
                'startFilePos' => 2302,
                'endTokenPos' => 136,
                'endFilePos' => 2306,
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
            'startColumn' => 43,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the list of allowed invoice templates.
 *
 * Security: The selector is built from the static built-in whitelist plus the names
 * explicitly listed in the CUSTOM_INVOICE_TEMPLATES_PDF / CUSTOM_INVOICE_TEMPLATES_PUBLIC
 * allowlist constants. No directory, neither the application\'s own nor
 * CUSTOM_TEMPLATES_FOLDER, is ever scanned (prevents RCE). CUSTOM_TEMPLATES_FOLDER only
 * supplies the file\'s location at render time; on its own it lists nothing.
 *
 * @param string $type Template type (\'pdf\' or \'public\')
 *
 * @return array List of allowed template names (without .php extension)
 */',
        'startLine' => 74,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Templates',
        'implementingClassName' => 'Mdl_Templates',
        'currentClassName' => 'Mdl_Templates',
        'aliasName' => NULL,
      ),
      'get_quote_templates' => 
      array (
        'name' => 'get_quote_templates',
        'parameters' => 
        array (
          'type' => 
          array (
            'name' => 'type',
            'default' => 
            array (
              'code' => '\'pdf\'',
              'attributes' => 
              array (
                'startLine' => 100,
                'endLine' => 100,
                'startTokenPos' => 236,
                'startFilePos' => 3354,
                'endTokenPos' => 236,
                'endFilePos' => 3358,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 100,
            'endLine' => 100,
            'startColumn' => 41,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the list of allowed quote templates.
 *
 * Security: The selector is built from the static built-in whitelist plus the names
 * explicitly listed in the CUSTOM_QUOTE_TEMPLATES_PDF / CUSTOM_QUOTE_TEMPLATES_PUBLIC
 * allowlist constants. No directory, neither the application\'s own nor
 * CUSTOM_TEMPLATES_FOLDER, is ever scanned (prevents RCE). CUSTOM_TEMPLATES_FOLDER only
 * supplies the file\'s location at render time; on its own it lists nothing.
 *
 * @param string $type Template type (\'pdf\' or \'public\')
 *
 * @return array List of allowed template names (without .php extension)
 */',
        'startLine' => 100,
        'endLine' => 111,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Templates',
        'implementingClassName' => 'Mdl_Templates',
        'currentClassName' => 'Mdl_Templates',
        'aliasName' => NULL,
      ),
      'check_template_directory_permissions' => 
      array (
        'name' => 'check_template_directory_permissions',
        'parameters' => 
        array (
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
 * Check if template directories have insecure permissions.
 *
 * Security: Warns administrators if template directories are writable by the web server.
 * This is a defense-in-depth measure - the static whitelist already prevents exploitation,
 * but writable template directories are still a security misconfiguration that should be fixed.
 *
 * @return array Array of warnings (empty if no issues found)
 */',
        'startLine' => 122,
        'endLine' => 145,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Templates',
        'implementingClassName' => 'Mdl_Templates',
        'currentClassName' => 'Mdl_Templates',
        'aliasName' => NULL,
      ),
      'get_missing_allowlisted_template_settings' => 
      array (
        'name' => 'get_missing_allowlisted_template_settings',
        'parameters' => 
        array (
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
 * Find selected template settings that are not available in the current allowlists.
 *
 * This helps administrators upgrading from versions that discovered template files
 * automatically. If a saved template name is not built in and not listed in
 * ipconfig.php, it will not appear in the UI until it is added to the matching
 * CUSTOM_*_TEMPLATES setting.
 *
 * @return array<string, array<int, string>>
 */',
        'startLine' => 157,
        'endLine' => 207,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Templates',
        'implementingClassName' => 'Mdl_Templates',
        'currentClassName' => 'Mdl_Templates',
        'aliasName' => NULL,
      ),
      '_merge_custom' => 
      array (
        'name' => '_merge_custom',
        'parameters' => 
        array (
          'subpath' => 
          array (
            'name' => 'subpath',
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
            'startLine' => 231,
            'endLine' => 231,
            'startColumn' => 36,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'built_in' => 
          array (
            'name' => 'built_in',
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
            'startLine' => 231,
            'endLine' => 231,
            'startColumn' => 53,
            'endColumn' => 67,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Merge built-in templates with any custom templates explicitly allowlisted in the
 * CUSTOM_INVOICE_TEMPLATES_PDF / CUSTOM_INVOICE_TEMPLATES_PUBLIC /
 * CUSTOM_QUOTE_TEMPLATES_PDF / CUSTOM_QUOTE_TEMPLATES_PUBLIC constants.
 *
 * Security:
 * - The filesystem is NEVER scanned to discover templates (prevents RCE).
 * - Only names present in the explicit config constants are added to the list.
 * - Each name is validated against a strict allowlist regex before use.
 *   Any name that does not match is skipped and logged.
 * - Custom templates are prepended so admins can shadow built-in names;
 *   array_unique() deduplicates the merged list.
 *
 * To expose a custom template, add its name (without .php) to the appropriate
 * constant in ipconfig.php, e.g.:
 *   CUSTOM_INVOICE_TEMPLATES_PDF=MyTemplate,AnotherTemplate
 *
 * @param string $subpath  Relative sub-path key, e.g. \'invoice_templates/pdf\'
 * @param array  $built_in Hardcoded whitelist entries from the class constants
 *
 * @return array
 */',
        'startLine' => 231,
        'endLine' => 275,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Templates',
        'implementingClassName' => 'Mdl_Templates',
        'currentClassName' => 'Mdl_Templates',
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