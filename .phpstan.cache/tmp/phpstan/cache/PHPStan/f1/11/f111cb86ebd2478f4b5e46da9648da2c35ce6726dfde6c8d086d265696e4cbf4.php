<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/application/modules/setup/controllers/Cli.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Cli
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-24b5eef5b5d82bf93e81d1b364e8245f80fc270bc73097a2d86cc0b6d9ca04a1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Cli',
        'filename' => '/var/www/projects/exprmt/application/modules/setup/controllers/Cli.php',
      ),
    ),
    'namespace' => NULL,
    'name' => 'Cli',
    'shortName' => 'Cli',
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
    'endLine' => 137,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'MX_Controller',
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
        'docComment' => NULL,
        'startLine' => 19,
        'endLine' => 49,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Cli',
        'implementingClassName' => 'Cli',
        'currentClassName' => 'Cli',
        'aliasName' => NULL,
      ),
      'create_default_user' => 
      array (
        'name' => 'create_default_user',
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
 * Create a default admin user if no users exist.
 *
 * Reads DEFAULT_ADMIN_EMAIL, DEFAULT_ADMIN_PASSWORD, and DEFAULT_ADMIN_NAME
 * from the environment. Falls back to admin@localhost, a random password,
 * and "admin" respectively. The generated password is printed to stdout.
 * Skips creation when any user already exists in the database.
 *
 * Usage: php index.php setup/cli/create_default_user
 */',
        'startLine' => 61,
        'endLine' => 96,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Cli',
        'implementingClassName' => 'Cli',
        'currentClassName' => 'Cli',
        'aliasName' => NULL,
      ),
      'migrate' => 
      array (
        'name' => 'migrate',
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
 * Run pending database migrations.
 *
 * Usage: php index.php setup/cli/migrate
 */',
        'startLine' => 103,
        'endLine' => 136,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Cli',
        'implementingClassName' => 'Cli',
        'currentClassName' => 'Cli',
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