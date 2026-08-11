<?php declare(strict_types = 1);

// osfsl-/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/Service/BalanceService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Stripe\Service\BalanceService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-c510c8ee363cb1495d2a29792d5cedf54e1fec8dce67e6fdb4e5587b9eb3ef04-8.4.24-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Stripe\\Service\\BalanceService',
        'filename' => '/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/Service/BalanceService.php',
      ),
    ),
    'namespace' => 'Stripe\\Service',
    'name' => 'Stripe\\Service\\BalanceService',
    'shortName' => 'BalanceService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @phpstan-import-type RequestOptionsArray from \\Stripe\\Util\\RequestOptions
 * @psalm-import-type RequestOptionsArray from \\Stripe\\Util\\RequestOptions
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 11,
    'endLine' => 30,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Stripe\\Service\\AbstractService',
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
      'retrieve' => 
      array (
        'name' => 'retrieve',
        'parameters' => 
        array (
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 26,
                'endLine' => 26,
                'startTokenPos' => 33,
                'startFilePos' => 852,
                'endTokenPos' => 33,
                'endFilePos' => 855,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 26,
            'endLine' => 26,
            'startColumn' => 30,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'opts' => 
          array (
            'name' => 'opts',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 26,
                'endLine' => 26,
                'startTokenPos' => 40,
                'startFilePos' => 866,
                'endTokenPos' => 40,
                'endFilePos' => 869,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 26,
            'endLine' => 26,
            'startColumn' => 46,
            'endColumn' => 57,
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
 * Retrieves the current account balance, based on the authentication that was used
 * to make the request.  For a sample request, see <a
 * href="/docs/connect/account-balances#accounting-for-negative-balances">Accounting
 * for negative balances</a>.
 *
 * @param null|array $params
 * @param null|RequestOptionsArray|\\Stripe\\Util\\RequestOptions $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Balance
 */',
        'startLine' => 26,
        'endLine' => 29,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\Service',
        'declaringClassName' => 'Stripe\\Service\\BalanceService',
        'implementingClassName' => 'Stripe\\Service\\BalanceService',
        'currentClassName' => 'Stripe\\Service\\BalanceService',
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