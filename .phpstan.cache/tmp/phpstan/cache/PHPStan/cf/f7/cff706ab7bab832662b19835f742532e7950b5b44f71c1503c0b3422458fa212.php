<?php declare(strict_types = 1);

// osfsl-/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/Account.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Stripe\Account
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-0fad6c214b40138d8340ead61b66f720dc16fc994ba0f0855986191ca07cdfec-8.4.24-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Stripe\\Account',
        'filename' => '/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/Account.php',
      ),
    ),
    'namespace' => 'Stripe',
    'name' => 'Stripe\\Account',
    'shortName' => 'Account',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * This is an object representing a Stripe account. You can retrieve it to see
 * properties on the account like its current requirements or if the account is
 * enabled to make live charges or receive payouts.
 *
 * For accounts where <a href="/api/accounts/object#account_object-controller-requirement_collection">controller.requirement_collection</a>
 * is <code>application</code>, which includes Custom accounts, the properties below are always
 * returned.
 *
 * For accounts where <a href="/api/accounts/object#account_object-controller-requirement_collection">controller.requirement_collection</a>
 * is <code>stripe</code>, which includes Standard and Express accounts, some properties are only returned
 * until you create an <a href="/api/account_links">Account Link</a> or <a href="/api/account_sessions">Account Session</a>
 * to start Connect Onboarding. Learn about the <a href="/connect/accounts">differences between accounts</a>.
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object\'s type. Objects of the same type share the same value.
 * @property null|\\Stripe\\StripeObject $business_profile Business information about the account.
 * @property null|string $business_type The business type. After you create an <a href="/api/account_links">Account Link</a> or <a href="/api/account_sessions">Account Session</a>, this property is only returned for accounts where <a href="/api/accounts/object#account_object-controller-requirement_collection">controller.requirement_collection</a> is <code>application</code>, which includes Custom accounts.
 * @property null|\\Stripe\\StripeObject $capabilities
 * @property null|bool $charges_enabled Whether the account can create live charges.
 * @property null|\\Stripe\\StripeObject $company
 * @property null|\\Stripe\\StripeObject $controller
 * @property null|string $country The account\'s country.
 * @property null|int $created Time at which the account was connected. Measured in seconds since the Unix epoch.
 * @property null|string $default_currency Three-letter ISO currency code representing the default currency for the account. This must be a currency that <a href="https://stripe.com/docs/payouts">Stripe supports in the account\'s country</a>.
 * @property null|bool $details_submitted Whether account details have been submitted. Accounts with Stripe Dashboard access, which includes Standard accounts, cannot receive payouts before this is true. Accounts where this is false should be directed to <a href="/connect/onboarding">an onboarding flow</a> to finish submitting account details.
 * @property null|string $email An email address associated with the account. It\'s not used for authentication and Stripe doesn\'t market to this field without explicit approval from the platform.
 * @property null|\\Stripe\\Collection<\\Stripe\\BankAccount|\\Stripe\\Card> $external_accounts External accounts (bank accounts and debit cards) currently attached to this account. External accounts are only returned for requests where <code>controller[is_controller]</code> is true.
 * @property null|\\Stripe\\StripeObject $future_requirements
 * @property null|\\Stripe\\Person $individual <p>This is an object representing a person associated with a Stripe account.</p><p>A platform cannot access a person for an account where <a href="/api/accounts/object#account_object-controller-requirement_collection">account.controller.requirement_collection</a> is <code>stripe</code>, which includes Standard and Express accounts, after creating an Account Link or Account Session to start Connect onboarding.</p><p>See the <a href="/connect/standard-accounts">Standard onboarding</a> or <a href="/connect/express-accounts">Express onboarding</a> documentation for information about prefilling information and account onboarding steps. Learn more about <a href="/connect/handling-api-verification#person-information">handling identity verification with the API</a>.</p>
 * @property null|\\Stripe\\StripeObject $metadata Set of <a href="https://stripe.com/docs/api/metadata">key-value pairs</a> that you can attach to an object. This can be useful for storing additional information about the object in a structured format.
 * @property null|bool $payouts_enabled Whether Stripe can send payouts to this account.
 * @property null|\\Stripe\\StripeObject $requirements
 * @property null|\\Stripe\\StripeObject $settings Options for customizing how the account functions within Stripe.
 * @property null|\\Stripe\\StripeObject $tos_acceptance
 * @property null|string $type The Stripe account type. Can be <code>standard</code>, <code>express</code>, <code>custom</code>, or <code>none</code>.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 44,
    'endLine' => 523,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Stripe\\ApiResource',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Stripe\\ApiOperations\\NestedResource',
      1 => 'Stripe\\ApiOperations\\Update',
      2 => 'Stripe\\ApiOperations\\Retrieve',
    ),
    'immediateConstants' => 
    array (
      'OBJECT_NAME' => 
      array (
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'name' => 'OBJECT_NAME',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'account\'',
          'attributes' => 
          array (
            'startLine' => 46,
            'endLine' => 46,
            'startTokenPos' => 27,
            'startFilePos' => 4827,
            'endTokenPos' => 27,
            'endFilePos' => 4835,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 46,
        'endLine' => 46,
        'startColumn' => 5,
        'endColumn' => 34,
      ),
      'BUSINESS_TYPE_COMPANY' => 
      array (
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'name' => 'BUSINESS_TYPE_COMPANY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'company\'',
          'attributes' => 
          array (
            'startLine' => 51,
            'endLine' => 51,
            'startTokenPos' => 46,
            'startFilePos' => 4942,
            'endTokenPos' => 46,
            'endFilePos' => 4950,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 51,
        'endLine' => 51,
        'startColumn' => 5,
        'endColumn' => 44,
      ),
      'BUSINESS_TYPE_GOVERNMENT_ENTITY' => 
      array (
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'name' => 'BUSINESS_TYPE_GOVERNMENT_ENTITY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'government_entity\'',
          'attributes' => 
          array (
            'startLine' => 52,
            'endLine' => 52,
            'startTokenPos' => 55,
            'startFilePos' => 4997,
            'endTokenPos' => 55,
            'endFilePos' => 5015,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 52,
        'endLine' => 52,
        'startColumn' => 5,
        'endColumn' => 64,
      ),
      'BUSINESS_TYPE_INDIVIDUAL' => 
      array (
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'name' => 'BUSINESS_TYPE_INDIVIDUAL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'individual\'',
          'attributes' => 
          array (
            'startLine' => 53,
            'endLine' => 53,
            'startTokenPos' => 64,
            'startFilePos' => 5055,
            'endTokenPos' => 64,
            'endFilePos' => 5066,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 53,
        'endLine' => 53,
        'startColumn' => 5,
        'endColumn' => 50,
      ),
      'BUSINESS_TYPE_NON_PROFIT' => 
      array (
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'name' => 'BUSINESS_TYPE_NON_PROFIT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'non_profit\'',
          'attributes' => 
          array (
            'startLine' => 54,
            'endLine' => 54,
            'startTokenPos' => 73,
            'startFilePos' => 5106,
            'endTokenPos' => 73,
            'endFilePos' => 5117,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 54,
        'endLine' => 54,
        'startColumn' => 5,
        'endColumn' => 50,
      ),
      'TYPE_CUSTOM' => 
      array (
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'name' => 'TYPE_CUSTOM',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'custom\'',
          'attributes' => 
          array (
            'startLine' => 56,
            'endLine' => 56,
            'startTokenPos' => 82,
            'startFilePos' => 5145,
            'endTokenPos' => 82,
            'endFilePos' => 5152,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 56,
        'endLine' => 56,
        'startColumn' => 5,
        'endColumn' => 33,
      ),
      'TYPE_EXPRESS' => 
      array (
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'name' => 'TYPE_EXPRESS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'express\'',
          'attributes' => 
          array (
            'startLine' => 57,
            'endLine' => 57,
            'startTokenPos' => 91,
            'startFilePos' => 5180,
            'endTokenPos' => 91,
            'endFilePos' => 5188,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 57,
        'endLine' => 57,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'TYPE_NONE' => 
      array (
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'name' => 'TYPE_NONE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'none\'',
          'attributes' => 
          array (
            'startLine' => 58,
            'endLine' => 58,
            'startTokenPos' => 100,
            'startFilePos' => 5213,
            'endTokenPos' => 100,
            'endFilePos' => 5218,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 58,
        'endLine' => 58,
        'startColumn' => 5,
        'endColumn' => 29,
      ),
      'TYPE_STANDARD' => 
      array (
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'name' => 'TYPE_STANDARD',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'standard\'',
          'attributes' => 
          array (
            'startLine' => 59,
            'endLine' => 59,
            'startTokenPos' => 109,
            'startFilePos' => 5247,
            'endTokenPos' => 109,
            'endFilePos' => 5256,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 59,
        'endLine' => 59,
        'startColumn' => 5,
        'endColumn' => 37,
      ),
      'PATH_CAPABILITIES' => 
      array (
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'name' => 'PATH_CAPABILITIES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'/capabilities\'',
          'attributes' => 
          array (
            'startLine' => 315,
            'endLine' => 315,
            'startTokenPos' => 1281,
            'startFilePos' => 14419,
            'endTokenPos' => 1281,
            'endFilePos' => 14433,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 315,
        'endLine' => 315,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'PATH_EXTERNAL_ACCOUNTS' => 
      array (
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'name' => 'PATH_EXTERNAL_ACCOUNTS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'/external_accounts\'',
          'attributes' => 
          array (
            'startLine' => 360,
            'endLine' => 360,
            'startTokenPos' => 1458,
            'startFilePos' => 16135,
            'endTokenPos' => 1458,
            'endFilePos' => 16154,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 360,
        'endLine' => 360,
        'startColumn' => 5,
        'endColumn' => 56,
      ),
      'PATH_LOGIN_LINKS' => 
      array (
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'name' => 'PATH_LOGIN_LINKS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'/login_links\'',
          'attributes' => 
          array (
            'startLine' => 434,
            'endLine' => 434,
            'startTokenPos' => 1745,
            'startFilePos' => 19130,
            'endTokenPos' => 1745,
            'endFilePos' => 19143,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 434,
        'endLine' => 434,
        'startColumn' => 5,
        'endColumn' => 44,
      ),
      'PATH_PERSONS' => 
      array (
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'name' => 'PATH_PERSONS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'/persons\'',
          'attributes' => 
          array (
            'startLine' => 449,
            'endLine' => 449,
            'startTokenPos' => 1806,
            'startFilePos' => 19640,
            'endTokenPos' => 1806,
            'endFilePos' => 19649,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 449,
        'endLine' => 449,
        'startColumn' => 5,
        'endColumn' => 36,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'create' => 
      array (
        'name' => 'create',
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
                'startLine' => 80,
                'endLine' => 80,
                'startTokenPos' => 126,
                'startFilePos' => 6151,
                'endTokenPos' => 126,
                'endFilePos' => 6154,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 80,
            'endLine' => 80,
            'startColumn' => 35,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 80,
                'endLine' => 80,
                'startTokenPos' => 133,
                'startFilePos' => 6168,
                'endTokenPos' => 133,
                'endFilePos' => 6171,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 80,
            'endLine' => 80,
            'startColumn' => 51,
            'endColumn' => 65,
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
 * With <a href="/docs/connect">Connect</a>, you can create Stripe accounts for
 * your users. To do this, you’ll first need to <a
 * href="https://dashboard.stripe.com/account/applications/settings">register your
 * platform</a>.
 *
 * If you’ve already collected information for your connected accounts, you <a
 * href="/docs/connect/best-practices#onboarding">can prefill that information</a>
 * when creating the account. Connect Onboarding won’t ask for the prefilled
 * information during account onboarding. You can prefill any information on the
 * account.
 *
 * @param null|array $params
 * @param null|array|string $options
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Account the created resource
 */',
        'startLine' => 80,
        'endLine' => 90,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'delete' => 
      array (
        'name' => 'delete',
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
                'startLine' => 114,
                'endLine' => 114,
                'startTokenPos' => 228,
                'startFilePos' => 7484,
                'endTokenPos' => 228,
                'endFilePos' => 7487,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 114,
            'endLine' => 114,
            'startColumn' => 28,
            'endColumn' => 41,
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
                'startLine' => 114,
                'endLine' => 114,
                'startTokenPos' => 235,
                'startFilePos' => 7498,
                'endTokenPos' => 235,
                'endFilePos' => 7501,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 114,
            'endLine' => 114,
            'startColumn' => 44,
            'endColumn' => 55,
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
 * With <a href="/connect">Connect</a>, you can delete accounts you manage.
 *
 * Test-mode accounts can be deleted at any time.
 *
 * Live-mode accounts where Stripe is responsible for negative account balances
 * cannot be deleted, which includes Standard accounts. Live-mode accounts where
 * your platform is liable for negative account balances, which includes Custom and
 * Express accounts, can be deleted when all <a
 * href="/api/balance/balanace_object">balances</a> are zero.
 *
 * If you want to delete your own account, use the <a
 * href="https://dashboard.stripe.com/settings/account">account information tab in
 * your account settings</a> instead.
 *
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Account the deleted resource
 */',
        'startLine' => 114,
        'endLine' => 123,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'all' => 
      array (
        'name' => 'all',
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
                'startLine' => 136,
                'endLine' => 136,
                'startTokenPos' => 318,
                'startFilePos' => 8190,
                'endTokenPos' => 318,
                'endFilePos' => 8193,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 136,
            'endLine' => 136,
            'startColumn' => 32,
            'endColumn' => 45,
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
                'startLine' => 136,
                'endLine' => 136,
                'startTokenPos' => 325,
                'startFilePos' => 8204,
                'endTokenPos' => 325,
                'endFilePos' => 8207,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 136,
            'endLine' => 136,
            'startColumn' => 48,
            'endColumn' => 59,
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
 * Returns a list of accounts connected to your platform via <a
 * href="/docs/connect">Connect</a>. If you’re not a platform, the list is empty.
 *
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Collection<\\Stripe\\Account> of ApiResources
 */',
        'startLine' => 136,
        'endLine' => 141,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'update' => 
      array (
        'name' => 'update',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 172,
            'endLine' => 172,
            'startColumn' => 35,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 172,
                'endLine' => 172,
                'startTokenPos' => 381,
                'startFilePos' => 9887,
                'endTokenPos' => 381,
                'endFilePos' => 9890,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 172,
            'endLine' => 172,
            'startColumn' => 40,
            'endColumn' => 53,
            'parameterIndex' => 1,
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
                'startLine' => 172,
                'endLine' => 172,
                'startTokenPos' => 388,
                'startFilePos' => 9901,
                'endTokenPos' => 388,
                'endFilePos' => 9904,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 172,
            'endLine' => 172,
            'startColumn' => 56,
            'endColumn' => 67,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Updates a <a href="/connect/accounts">connected account</a> by setting the
 * values of the parameters passed. Any parameters not provided are left unchanged.
 *
 * For accounts where <a
 * href="/api/accounts/object#account_object-controller-requirement_collection">controller.requirement_collection</a>
 * is <code>application</code>, which includes Custom accounts, you can update any
 * information on the account.
 *
 * For accounts where <a
 * href="/api/accounts/object#account_object-controller-requirement_collection">controller.requirement_collection</a>
 * is <code>stripe</code>, which includes Standard and Express accounts, you can
 * update all information until you create an <a href="/api/account_links">Account
 * Link</a> or <a href="/api/account_sessions">Account Session</a> to start Connect
 * onboarding, after which some properties can no longer be updated.
 *
 * To update your own account, use the <a
 * href="https://dashboard.stripe.com/settings/account">Dashboard</a>. Refer to our
 * <a href="/docs/connect/updating-accounts">Connect</a> documentation to learn
 * more about updating accounts.
 *
 * @param string $id the ID of the resource to update
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Account the updated resource
 */',
        'startLine' => 172,
        'endLine' => 182,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'getSavedNestedResources' => 
      array (
        'name' => 'getSavedNestedResources',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 188,
        'endLine' => 199,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'instanceUrl' => 
      array (
        'name' => 'instanceUrl',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 201,
        'endLine' => 208,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'retrieve' => 
      array (
        'name' => 'retrieve',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 219,
                'endLine' => 219,
                'startTokenPos' => 608,
                'startFilePos' => 11160,
                'endTokenPos' => 608,
                'endFilePos' => 11163,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 219,
            'endLine' => 219,
            'startColumn' => 37,
            'endColumn' => 46,
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
                'startLine' => 219,
                'endLine' => 219,
                'startTokenPos' => 615,
                'startFilePos' => 11174,
                'endTokenPos' => 615,
                'endFilePos' => 11177,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 219,
            'endLine' => 219,
            'startColumn' => 49,
            'endColumn' => 60,
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
 * @param null|array|string $id the ID of the account to retrieve, or an
 *     options array containing an `id` key
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Account
 */',
        'startLine' => 219,
        'endLine' => 227,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'serializeParameters' => 
      array (
        'name' => 'serializeParameters',
        'parameters' => 
        array (
          'force' => 
          array (
            'name' => 'force',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 229,
                'endLine' => 229,
                'startTokenPos' => 694,
                'startFilePos' => 11419,
                'endTokenPos' => 694,
                'endFilePos' => 11423,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 229,
            'endLine' => 229,
            'startColumn' => 41,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 229,
        'endLine' => 249,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'serializeAdditionalOwners' => 
      array (
        'name' => 'serializeAdditionalOwners',
        'parameters' => 
        array (
          'legalEntity' => 
          array (
            'name' => 'legalEntity',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 251,
            'endLine' => 251,
            'startColumn' => 48,
            'endColumn' => 59,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'additionalOwners' => 
          array (
            'name' => 'additionalOwners',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 251,
            'endLine' => 251,
            'startColumn' => 62,
            'endColumn' => 78,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 251,
        'endLine' => 278,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'deauthorize' => 
      array (
        'name' => 'deauthorize',
        'parameters' => 
        array (
          'clientId' => 
          array (
            'name' => 'clientId',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 288,
                'endLine' => 288,
                'startTokenPos' => 1139,
                'startFilePos' => 13699,
                'endTokenPos' => 1139,
                'endFilePos' => 13702,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 288,
            'endLine' => 288,
            'startColumn' => 33,
            'endColumn' => 48,
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
                'startLine' => 288,
                'endLine' => 288,
                'startTokenPos' => 1146,
                'startFilePos' => 13713,
                'endTokenPos' => 1146,
                'endFilePos' => 13716,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 288,
            'endLine' => 288,
            'startColumn' => 51,
            'endColumn' => 62,
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
 * @param null|array $clientId
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\StripeObject object containing the response from the API
 */',
        'startLine' => 288,
        'endLine' => 296,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'reject' => 
      array (
        'name' => 'reject',
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
                'startLine' => 306,
                'endLine' => 306,
                'startTokenPos' => 1203,
                'startFilePos' => 14158,
                'endTokenPos' => 1203,
                'endFilePos' => 14161,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 306,
            'endLine' => 306,
            'startColumn' => 28,
            'endColumn' => 41,
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
                'startLine' => 306,
                'endLine' => 306,
                'startTokenPos' => 1210,
                'startFilePos' => 14172,
                'endTokenPos' => 1210,
                'endFilePos' => 14175,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 306,
            'endLine' => 306,
            'startColumn' => 44,
            'endColumn' => 55,
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
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Account the rejected account
 */',
        'startLine' => 306,
        'endLine' => 313,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'allCapabilities' => 
      array (
        'name' => 'allCapabilities',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 326,
            'endLine' => 326,
            'startColumn' => 44,
            'endColumn' => 46,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 326,
                'endLine' => 326,
                'startTokenPos' => 1301,
                'startFilePos' => 14832,
                'endTokenPos' => 1301,
                'endFilePos' => 14835,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 326,
            'endLine' => 326,
            'startColumn' => 49,
            'endColumn' => 62,
            'parameterIndex' => 1,
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
                'startLine' => 326,
                'endLine' => 326,
                'startTokenPos' => 1308,
                'startFilePos' => 14846,
                'endTokenPos' => 1308,
                'endFilePos' => 14849,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 326,
            'endLine' => 326,
            'startColumn' => 65,
            'endColumn' => 76,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param string $id the ID of the account on which to retrieve the capabilities
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Collection<\\Stripe\\Capability> the list of capabilities
 */',
        'startLine' => 326,
        'endLine' => 329,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'retrieveCapability' => 
      array (
        'name' => 'retrieveCapability',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 341,
            'endLine' => 341,
            'startColumn' => 47,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'capabilityId' => 
          array (
            'name' => 'capabilityId',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 341,
            'endLine' => 341,
            'startColumn' => 52,
            'endColumn' => 64,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 341,
                'endLine' => 341,
                'startTokenPos' => 1356,
                'startFilePos' => 15389,
                'endTokenPos' => 1356,
                'endFilePos' => 15392,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 341,
            'endLine' => 341,
            'startColumn' => 67,
            'endColumn' => 80,
            'parameterIndex' => 2,
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
                'startLine' => 341,
                'endLine' => 341,
                'startTokenPos' => 1363,
                'startFilePos' => 15403,
                'endTokenPos' => 1363,
                'endFilePos' => 15406,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 341,
            'endLine' => 341,
            'startColumn' => 83,
            'endColumn' => 94,
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
 * @param string $id the ID of the account to which the capability belongs
 * @param string $capabilityId the ID of the capability to retrieve
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Capability
 */',
        'startLine' => 341,
        'endLine' => 344,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'updateCapability' => 
      array (
        'name' => 'updateCapability',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 356,
            'endLine' => 356,
            'startColumn' => 45,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'capabilityId' => 
          array (
            'name' => 'capabilityId',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 356,
            'endLine' => 356,
            'startColumn' => 50,
            'endColumn' => 62,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 356,
                'endLine' => 356,
                'startTokenPos' => 1414,
                'startFilePos' => 15961,
                'endTokenPos' => 1414,
                'endFilePos' => 15964,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 356,
            'endLine' => 356,
            'startColumn' => 65,
            'endColumn' => 78,
            'parameterIndex' => 2,
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
                'startLine' => 356,
                'endLine' => 356,
                'startTokenPos' => 1421,
                'startFilePos' => 15975,
                'endTokenPos' => 1421,
                'endFilePos' => 15978,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 356,
            'endLine' => 356,
            'startColumn' => 81,
            'endColumn' => 92,
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
 * @param string $id the ID of the account to which the capability belongs
 * @param string $capabilityId the ID of the capability to update
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Capability
 */',
        'startLine' => 356,
        'endLine' => 359,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'allExternalAccounts' => 
      array (
        'name' => 'allExternalAccounts',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 371,
            'endLine' => 371,
            'startColumn' => 48,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 371,
                'endLine' => 371,
                'startTokenPos' => 1478,
                'startFilePos' => 16603,
                'endTokenPos' => 1478,
                'endFilePos' => 16606,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 371,
            'endLine' => 371,
            'startColumn' => 53,
            'endColumn' => 66,
            'parameterIndex' => 1,
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
                'startLine' => 371,
                'endLine' => 371,
                'startTokenPos' => 1485,
                'startFilePos' => 16617,
                'endTokenPos' => 1485,
                'endFilePos' => 16620,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 371,
            'endLine' => 371,
            'startColumn' => 69,
            'endColumn' => 80,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param string $id the ID of the account on which to retrieve the external accounts
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Collection<\\Stripe\\BankAccount|\\Stripe\\Card> the list of external accounts (BankAccount or Card)
 */',
        'startLine' => 371,
        'endLine' => 374,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'createExternalAccount' => 
      array (
        'name' => 'createExternalAccount',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 385,
            'endLine' => 385,
            'startColumn' => 50,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 385,
                'endLine' => 385,
                'startTokenPos' => 1530,
                'startFilePos' => 17103,
                'endTokenPos' => 1530,
                'endFilePos' => 17106,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 385,
            'endLine' => 385,
            'startColumn' => 55,
            'endColumn' => 68,
            'parameterIndex' => 1,
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
                'startLine' => 385,
                'endLine' => 385,
                'startTokenPos' => 1537,
                'startFilePos' => 17117,
                'endTokenPos' => 1537,
                'endFilePos' => 17120,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 385,
            'endLine' => 385,
            'startColumn' => 71,
            'endColumn' => 82,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param string $id the ID of the account on which to create the external account
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\BankAccount|\\Stripe\\Card
 */',
        'startLine' => 385,
        'endLine' => 388,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'deleteExternalAccount' => 
      array (
        'name' => 'deleteExternalAccount',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 400,
            'endLine' => 400,
            'startColumn' => 50,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'externalAccountId' => 
          array (
            'name' => 'externalAccountId',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 400,
            'endLine' => 400,
            'startColumn' => 55,
            'endColumn' => 72,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 400,
                'endLine' => 400,
                'startTokenPos' => 1585,
                'startFilePos' => 17704,
                'endTokenPos' => 1585,
                'endFilePos' => 17707,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 400,
            'endLine' => 400,
            'startColumn' => 75,
            'endColumn' => 88,
            'parameterIndex' => 2,
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
                'startLine' => 400,
                'endLine' => 400,
                'startTokenPos' => 1592,
                'startFilePos' => 17718,
                'endTokenPos' => 1592,
                'endFilePos' => 17721,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 400,
            'endLine' => 400,
            'startColumn' => 91,
            'endColumn' => 102,
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
 * @param string $id the ID of the account to which the external account belongs
 * @param string $externalAccountId the ID of the external account to delete
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\BankAccount|\\Stripe\\Card
 */',
        'startLine' => 400,
        'endLine' => 403,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'retrieveExternalAccount' => 
      array (
        'name' => 'retrieveExternalAccount',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 415,
            'endLine' => 415,
            'startColumn' => 52,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'externalAccountId' => 
          array (
            'name' => 'externalAccountId',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 415,
            'endLine' => 415,
            'startColumn' => 57,
            'endColumn' => 74,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 415,
                'endLine' => 415,
                'startTokenPos' => 1643,
                'startFilePos' => 18329,
                'endTokenPos' => 1643,
                'endFilePos' => 18332,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 415,
            'endLine' => 415,
            'startColumn' => 77,
            'endColumn' => 90,
            'parameterIndex' => 2,
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
                'startLine' => 415,
                'endLine' => 415,
                'startTokenPos' => 1650,
                'startFilePos' => 18343,
                'endTokenPos' => 1650,
                'endFilePos' => 18346,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 415,
            'endLine' => 415,
            'startColumn' => 93,
            'endColumn' => 104,
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
 * @param string $id the ID of the account to which the external account belongs
 * @param string $externalAccountId the ID of the external account to retrieve
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\BankAccount|\\Stripe\\Card
 */',
        'startLine' => 415,
        'endLine' => 418,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'updateExternalAccount' => 
      array (
        'name' => 'updateExternalAccount',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 430,
            'endLine' => 430,
            'startColumn' => 50,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'externalAccountId' => 
          array (
            'name' => 'externalAccountId',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 430,
            'endLine' => 430,
            'startColumn' => 55,
            'endColumn' => 72,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 430,
                'endLine' => 430,
                'startTokenPos' => 1701,
                'startFilePos' => 18952,
                'endTokenPos' => 1701,
                'endFilePos' => 18955,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 430,
            'endLine' => 430,
            'startColumn' => 75,
            'endColumn' => 88,
            'parameterIndex' => 2,
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
                'startLine' => 430,
                'endLine' => 430,
                'startTokenPos' => 1708,
                'startFilePos' => 18966,
                'endTokenPos' => 1708,
                'endFilePos' => 18969,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 430,
            'endLine' => 430,
            'startColumn' => 91,
            'endColumn' => 102,
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
 * @param string $id the ID of the account to which the external account belongs
 * @param string $externalAccountId the ID of the external account to update
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\BankAccount|\\Stripe\\Card
 */',
        'startLine' => 430,
        'endLine' => 433,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'createLoginLink' => 
      array (
        'name' => 'createLoginLink',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 445,
            'endLine' => 445,
            'startColumn' => 44,
            'endColumn' => 46,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 445,
                'endLine' => 445,
                'startTokenPos' => 1765,
                'startFilePos' => 19492,
                'endTokenPos' => 1765,
                'endFilePos' => 19495,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 445,
            'endLine' => 445,
            'startColumn' => 49,
            'endColumn' => 62,
            'parameterIndex' => 1,
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
                'startLine' => 445,
                'endLine' => 445,
                'startTokenPos' => 1772,
                'startFilePos' => 19506,
                'endTokenPos' => 1772,
                'endFilePos' => 19509,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 445,
            'endLine' => 445,
            'startColumn' => 65,
            'endColumn' => 76,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param string $id the ID of the account on which to create the login link
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\LoginLink
 */',
        'startLine' => 445,
        'endLine' => 448,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'allPersons' => 
      array (
        'name' => 'allPersons',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 460,
            'endLine' => 460,
            'startColumn' => 39,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 460,
                'endLine' => 460,
                'startTokenPos' => 1826,
                'startFilePos' => 20029,
                'endTokenPos' => 1826,
                'endFilePos' => 20032,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 460,
            'endLine' => 460,
            'startColumn' => 44,
            'endColumn' => 57,
            'parameterIndex' => 1,
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
                'startLine' => 460,
                'endLine' => 460,
                'startTokenPos' => 1833,
                'startFilePos' => 20043,
                'endTokenPos' => 1833,
                'endFilePos' => 20046,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 460,
            'endLine' => 460,
            'startColumn' => 60,
            'endColumn' => 71,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param string $id the ID of the account on which to retrieve the persons
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Collection<\\Stripe\\Person> the list of persons
 */',
        'startLine' => 460,
        'endLine' => 463,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'createPerson' => 
      array (
        'name' => 'createPerson',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 474,
            'endLine' => 474,
            'startColumn' => 41,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 474,
                'endLine' => 474,
                'startTokenPos' => 1878,
                'startFilePos' => 20482,
                'endTokenPos' => 1878,
                'endFilePos' => 20485,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 474,
            'endLine' => 474,
            'startColumn' => 46,
            'endColumn' => 59,
            'parameterIndex' => 1,
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
                'startLine' => 474,
                'endLine' => 474,
                'startTokenPos' => 1885,
                'startFilePos' => 20496,
                'endTokenPos' => 1885,
                'endFilePos' => 20499,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 474,
            'endLine' => 474,
            'startColumn' => 62,
            'endColumn' => 73,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param string $id the ID of the account on which to create the person
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Person
 */',
        'startLine' => 474,
        'endLine' => 477,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'deletePerson' => 
      array (
        'name' => 'deletePerson',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 489,
            'endLine' => 489,
            'startColumn' => 41,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'personId' => 
          array (
            'name' => 'personId',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 489,
            'endLine' => 489,
            'startColumn' => 46,
            'endColumn' => 54,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 489,
                'endLine' => 489,
                'startTokenPos' => 1933,
                'startFilePos' => 21008,
                'endTokenPos' => 1933,
                'endFilePos' => 21011,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 489,
            'endLine' => 489,
            'startColumn' => 57,
            'endColumn' => 70,
            'parameterIndex' => 2,
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
                'startLine' => 489,
                'endLine' => 489,
                'startTokenPos' => 1940,
                'startFilePos' => 21022,
                'endTokenPos' => 1940,
                'endFilePos' => 21025,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 489,
            'endLine' => 489,
            'startColumn' => 73,
            'endColumn' => 84,
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
 * @param string $id the ID of the account to which the person belongs
 * @param string $personId the ID of the person to delete
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Person
 */',
        'startLine' => 489,
        'endLine' => 492,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'retrievePerson' => 
      array (
        'name' => 'retrievePerson',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 504,
            'endLine' => 504,
            'startColumn' => 43,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'personId' => 
          array (
            'name' => 'personId',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 504,
            'endLine' => 504,
            'startColumn' => 48,
            'endColumn' => 56,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 504,
                'endLine' => 504,
                'startTokenPos' => 1991,
                'startFilePos' => 21549,
                'endTokenPos' => 1991,
                'endFilePos' => 21552,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 504,
            'endLine' => 504,
            'startColumn' => 59,
            'endColumn' => 72,
            'parameterIndex' => 2,
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
                'startLine' => 504,
                'endLine' => 504,
                'startTokenPos' => 1998,
                'startFilePos' => 21563,
                'endTokenPos' => 1998,
                'endFilePos' => 21566,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 504,
            'endLine' => 504,
            'startColumn' => 75,
            'endColumn' => 86,
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
 * @param string $id the ID of the account to which the person belongs
 * @param string $personId the ID of the person to retrieve
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Person
 */',
        'startLine' => 504,
        'endLine' => 507,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
      'updatePerson' => 
      array (
        'name' => 'updatePerson',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 519,
            'endLine' => 519,
            'startColumn' => 41,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'personId' => 
          array (
            'name' => 'personId',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 519,
            'endLine' => 519,
            'startColumn' => 46,
            'endColumn' => 54,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 519,
                'endLine' => 519,
                'startTokenPos' => 2049,
                'startFilePos' => 22088,
                'endTokenPos' => 2049,
                'endFilePos' => 22091,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 519,
            'endLine' => 519,
            'startColumn' => 57,
            'endColumn' => 70,
            'parameterIndex' => 2,
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
                'startLine' => 519,
                'endLine' => 519,
                'startTokenPos' => 2056,
                'startFilePos' => 22102,
                'endTokenPos' => 2056,
                'endFilePos' => 22105,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 519,
            'endLine' => 519,
            'startColumn' => 73,
            'endColumn' => 84,
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
 * @param string $id the ID of the account to which the person belongs
 * @param string $personId the ID of the person to update
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Person
 */',
        'startLine' => 519,
        'endLine' => 522,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Account',
        'implementingClassName' => 'Stripe\\Account',
        'currentClassName' => 'Stripe\\Account',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
        'Stripe\\ApiOperations\\Retrieve' => 
        array (
          0 => 
          array (
            'alias' => '_retrieve',
            'method' => 'retrieve',
            'hash' => 'stripe\\apioperations\\retrieve::retrieve',
          ),
        ),
      ),
      'modifiers' => 
      array (
        'stripe\\apioperations\\retrieve::retrieve' => 2,
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
        'stripe\\apioperations\\retrieve::retrieve' => 'Stripe\\ApiOperations\\Retrieve::retrieve',
      ),
    ),
  ),
));