<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/application/modules/sessions/controllers/Sessions.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Sessions
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-7d54aa69c14f9e1b2b65316beca729676a76ff22607287962075c9f3f1e8d5e9',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Sessions',
        'filename' => '/var/www/projects/exprmt/application/modules/sessions/controllers/Sessions.php',
      ),
    ),
    'namespace' => NULL,
    'name' => 'Sessions',
    'shortName' => 'Sessions',
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
    'endLine' => 691,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Base_Controller',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'MAX_PASSWORD_RESET_EXPIRY_MINUTES' => 
      array (
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'name' => 'MAX_PASSWORD_RESET_EXPIRY_MINUTES',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '1440',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 23,
            'startTokenPos' => 50,
            'startFilePos' => 601,
            'endTokenPos' => 50,
            'endFilePos' => 604,
          ),
        ),
        'docComment' => '/**
 * Maximum allowed password reset token expiry time in minutes (24 hours)
 * This enforces a security upper limit on how long tokens can remain valid.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 59,
      ),
    ),
    'immediateProperties' => 
    array (
      'utc_timezone' => 
      array (
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'name' => 'utc_timezone',
        'modifiers' => 20,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * UTC timezone instance for consistent timestamp handling
 * Reused across password reset operations to avoid repeated instantiation.
 *
 * @var DateTimeZone
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
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
        'startLine' => 33,
        'endLine' => 36,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'currentClassName' => 'Sessions',
        'aliasName' => NULL,
      ),
      'login' => 
      array (
        'name' => 'login',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 38,
        'endLine' => 59,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'currentClassName' => 'Sessions',
        'aliasName' => NULL,
      ),
      'authenticate' => 
      array (
        'name' => 'authenticate',
        'parameters' => 
        array (
          'email_address' => 
          array (
            'name' => 'email_address',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 65,
            'endLine' => 65,
            'startColumn' => 34,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'password' => 
          array (
            'name' => 'password',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 65,
            'endLine' => 65,
            'startColumn' => 50,
            'endColumn' => 58,
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
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param $email_address
 * @param $password
 */',
        'startLine' => 65,
        'endLine' => 92,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'currentClassName' => 'Sessions',
        'aliasName' => NULL,
      ),
      'logout' => 
      array (
        'name' => 'logout',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 94,
        'endLine' => 99,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'currentClassName' => 'Sessions',
        'aliasName' => NULL,
      ),
      'passwordreset' => 
      array (
        'name' => 'passwordreset',
        'parameters' => 
        array (
          'token' => 
          array (
            'name' => 'token',
            'default' => 
            array (
              'code' => '\\null',
              'attributes' => 
              array (
                'startLine' => 104,
                'endLine' => 104,
                'startTokenPos' => 477,
                'startFilePos' => 3035,
                'endTokenPos' => 477,
                'endFilePos' => 3038,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 104,
            'endLine' => 104,
            'startColumn' => 35,
            'endColumn' => 47,
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
 * @return mixed
 */',
        'startLine' => 104,
        'endLine' => 359,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'currentClassName' => 'Sessions',
        'aliasName' => NULL,
      ),
      '_is_ip_rate_limited_login' => 
      array (
        'name' => '_is_ip_rate_limited_login',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Returns true when the current IP has exceeded the login attempt threshold.
 */',
        'startLine' => 364,
        'endLine' => 374,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'currentClassName' => 'Sessions',
        'aliasName' => NULL,
      ),
      '_record_ip_login_attempt' => 
      array (
        'name' => '_record_ip_login_attempt',
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
 * Records one failed login attempt for the current IP.
 */',
        'startLine' => 379,
        'endLine' => 385,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'currentClassName' => 'Sessions',
        'aliasName' => NULL,
      ),
      '_reset_ip_login_attempts' => 
      array (
        'name' => '_reset_ip_login_attempts',
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
 * Clears IP-based login attempt counter on successful authentication.
 */',
        'startLine' => 390,
        'endLine' => 394,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'currentClassName' => 'Sessions',
        'aliasName' => NULL,
      ),
      '_login_log_check' => 
      array (
        'name' => '_login_log_check',
        'parameters' => 
        array (
          'username' => 
          array (
            'name' => 'username',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 404,
            'endLine' => 404,
            'startColumn' => 39,
            'endColumn' => 47,
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
 * Checks if the login_log table has records for the
 * given.
 *
 * @param string $username
 *
 * @return object
 */',
        'startLine' => 404,
        'endLine' => 422,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'currentClassName' => 'Sessions',
        'aliasName' => NULL,
      ),
      '_is_ip_rate_limited_password_reset' => 
      array (
        'name' => '_is_ip_rate_limited_password_reset',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Check if IP address has exceeded rate limit for password resets.
 *
 * @return bool True if rate limited, false otherwise
 */',
        'startLine' => 429,
        'endLine' => 444,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'currentClassName' => 'Sessions',
        'aliasName' => NULL,
      ),
      '_record_password_reset_attempt' => 
      array (
        'name' => '_record_password_reset_attempt',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Record a password reset attempt for the current IP.
 */',
        'startLine' => 449,
        'endLine' => 457,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'currentClassName' => 'Sessions',
        'aliasName' => NULL,
      ),
      '_is_email_rate_limited_password_reset' => 
      array (
        'name' => '_is_email_rate_limited_password_reset',
        'parameters' => 
        array (
          'email' => 
          array (
            'name' => 'email',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 466,
            'endLine' => 466,
            'startColumn' => 60,
            'endColumn' => 65,
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
 * Check if email-based rate limit exceeded for password resets.
 *
 * @param string $email Email address to check
 *
 * @return bool True if rate limited, false otherwise
 */',
        'startLine' => 466,
        'endLine' => 479,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'currentClassName' => 'Sessions',
        'aliasName' => NULL,
      ),
      '_record_email_password_reset_attempt' => 
      array (
        'name' => '_record_email_password_reset_attempt',
        'parameters' => 
        array (
          'email' => 
          array (
            'name' => 'email',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 486,
            'endLine' => 486,
            'startColumn' => 59,
            'endColumn' => 64,
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
 * Record a password reset attempt for a specific email.
 *
 * @param string $email Email address
 */',
        'startLine' => 486,
        'endLine' => 494,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'currentClassName' => 'Sessions',
        'aliasName' => NULL,
      ),
      '_password_reset_ip_log_key' => 
      array (
        'name' => '_password_reset_ip_log_key',
        'parameters' => 
        array (
          'ip_address' => 
          array (
            'name' => 'ip_address',
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
            'startLine' => 496,
            'endLine' => 496,
            'startColumn' => 49,
            'endColumn' => 66,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 496,
        'endLine' => 499,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'currentClassName' => 'Sessions',
        'aliasName' => NULL,
      ),
      '_password_reset_email_log_key' => 
      array (
        'name' => '_password_reset_email_log_key',
        'parameters' => 
        array (
          'email' => 
          array (
            'name' => 'email',
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
            'startLine' => 501,
            'endLine' => 501,
            'startColumn' => 52,
            'endColumn' => 64,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 501,
        'endLine' => 504,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'currentClassName' => 'Sessions',
        'aliasName' => NULL,
      ),
      '_record_password_reset_log_attempt' => 
      array (
        'name' => '_record_password_reset_log_attempt',
        'parameters' => 
        array (
          'login_name' => 
          array (
            'name' => 'login_name',
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
            'startLine' => 506,
            'endLine' => 506,
            'startColumn' => 57,
            'endColumn' => 74,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'window_seconds' => 
          array (
            'name' => 'window_seconds',
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
            'startLine' => 506,
            'endLine' => 506,
            'startColumn' => 77,
            'endColumn' => 95,
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
        'docComment' => NULL,
        'startLine' => 506,
        'endLine' => 516,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'currentClassName' => 'Sessions',
        'aliasName' => NULL,
      ),
      '_login_log_is_within_window' => 
      array (
        'name' => '_login_log_is_within_window',
        'parameters' => 
        array (
          'login_log' => 
          array (
            'name' => 'login_log',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'object',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 518,
            'endLine' => 518,
            'startColumn' => 50,
            'endColumn' => 66,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'window_seconds' => 
          array (
            'name' => 'window_seconds',
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
            'startLine' => 518,
            'endLine' => 518,
            'startColumn' => 69,
            'endColumn' => 87,
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
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 518,
        'endLine' => 527,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'currentClassName' => 'Sessions',
        'aliasName' => NULL,
      ),
      '_is_bot_request' => 
      array (
        'name' => '_is_bot_request',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Check if the current request is from an automated tool or bot.
 *
 * @return bool True if bot/automated tool detected, false otherwise
 */',
        'startLine' => 534,
        'endLine' => 571,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'currentClassName' => 'Sessions',
        'aliasName' => NULL,
      ),
      '_login_log_addfailure' => 
      array (
        'name' => '_login_log_addfailure',
        'parameters' => 
        array (
          'username' => 
          array (
            'name' => 'username',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 580,
            'endLine' => 580,
            'startColumn' => 44,
            'endColumn' => 52,
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
 * If the username has a record in the login_log
 * table the count is incremented by 1, otherwise
 * a record for the given user is created.
 *
 * @param string $username
 */',
        'startLine' => 580,
        'endLine' => 598,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'currentClassName' => 'Sessions',
        'aliasName' => NULL,
      ),
      '_login_log_reset' => 
      array (
        'name' => '_login_log_reset',
        'parameters' => 
        array (
          'username' => 
          array (
            'name' => 'username',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 606,
            'endLine' => 606,
            'startColumn' => 39,
            'endColumn' => 47,
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
 * The record of the given user is deleted from the
 * login_log table.
 *
 * @param string $username
 */',
        'startLine' => 606,
        'endLine' => 609,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'currentClassName' => 'Sessions',
        'aliasName' => NULL,
      ),
      '_reject_expired_password_reset_token' => 
      array (
        'name' => '_reject_expired_password_reset_token',
        'parameters' => 
        array (
          'user' => 
          array (
            'name' => 'user',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 621,
            'endLine' => 621,
            'startColumn' => 59,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Rejects an expired (or malformed) password reset token.
 *
 * Shared by the token-link (GET) and password-change (POST) flows so both enforce the
 * same lifetime. When the token has expired or its stored expiry cannot be parsed, the
 * token is cleared and the request is redirected back to the reset page. When the token
 * is still valid this returns and execution continues.
 *
 * @param object $user The user row (must expose user_id and user_passwordreset_token_expiry)
 */',
        'startLine' => 621,
        'endLine' => 672,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'currentClassName' => 'Sessions',
        'aliasName' => NULL,
      ),
      '_clear_password_reset_token' => 
      array (
        'name' => '_clear_password_reset_token',
        'parameters' => 
        array (
          'user_id' => 
          array (
            'name' => 'user_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 680,
            'endLine' => 680,
            'startColumn' => 50,
            'endColumn' => 57,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Clears the password reset token and expiry for a user.
 * Helper method to avoid code duplication.
 *
 * @param int $user_id The user ID (will be type-cast to ensure it\'s an integer)
 */',
        'startLine' => 680,
        'endLine' => 690,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'Sessions',
        'implementingClassName' => 'Sessions',
        'currentClassName' => 'Sessions',
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