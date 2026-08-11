<?php declare(strict_types = 1);

// osfsl-/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/StripeClient.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Stripe\StripeClient
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-d47eed40a3c5354e2d3d514b26f634b1e75d68abecc4e63c815f0d09464983a9-8.4.24-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Stripe\\StripeClient',
        'filename' => '/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/StripeClient.php',
      ),
    ),
    'namespace' => 'Stripe',
    'name' => 'Stripe\\StripeClient',
    'shortName' => 'StripeClient',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Client used to send requests to Stripe\'s API.
 *
 * @property \\Stripe\\Service\\OAuthService $oauth
 * // The beginning of the section generated from our OpenAPI spec
 * @property \\Stripe\\Service\\AccountLinkService $accountLinks
 * @property \\Stripe\\Service\\AccountService $accounts
 * @property \\Stripe\\Service\\AccountSessionService $accountSessions
 * @property \\Stripe\\Service\\ApplePayDomainService $applePayDomains
 * @property \\Stripe\\Service\\ApplicationFeeService $applicationFees
 * @property \\Stripe\\Service\\Apps\\AppsServiceFactory $apps
 * @property \\Stripe\\Service\\BalanceService $balance
 * @property \\Stripe\\Service\\BalanceTransactionService $balanceTransactions
 * @property \\Stripe\\Service\\Billing\\BillingServiceFactory $billing
 * @property \\Stripe\\Service\\BillingPortal\\BillingPortalServiceFactory $billingPortal
 * @property \\Stripe\\Service\\ChargeService $charges
 * @property \\Stripe\\Service\\Checkout\\CheckoutServiceFactory $checkout
 * @property \\Stripe\\Service\\Climate\\ClimateServiceFactory $climate
 * @property \\Stripe\\Service\\ConfirmationTokenService $confirmationTokens
 * @property \\Stripe\\Service\\CountrySpecService $countrySpecs
 * @property \\Stripe\\Service\\CouponService $coupons
 * @property \\Stripe\\Service\\CreditNoteService $creditNotes
 * @property \\Stripe\\Service\\CustomerService $customers
 * @property \\Stripe\\Service\\CustomerSessionService $customerSessions
 * @property \\Stripe\\Service\\DisputeService $disputes
 * @property \\Stripe\\Service\\Entitlements\\EntitlementsServiceFactory $entitlements
 * @property \\Stripe\\Service\\EphemeralKeyService $ephemeralKeys
 * @property \\Stripe\\Service\\EventService $events
 * @property \\Stripe\\Service\\ExchangeRateService $exchangeRates
 * @property \\Stripe\\Service\\FileLinkService $fileLinks
 * @property \\Stripe\\Service\\FileService $files
 * @property \\Stripe\\Service\\FinancialConnections\\FinancialConnectionsServiceFactory $financialConnections
 * @property \\Stripe\\Service\\Forwarding\\ForwardingServiceFactory $forwarding
 * @property \\Stripe\\Service\\Identity\\IdentityServiceFactory $identity
 * @property \\Stripe\\Service\\InvoiceItemService $invoiceItems
 * @property \\Stripe\\Service\\InvoiceService $invoices
 * @property \\Stripe\\Service\\Issuing\\IssuingServiceFactory $issuing
 * @property \\Stripe\\Service\\MandateService $mandates
 * @property \\Stripe\\Service\\PaymentIntentService $paymentIntents
 * @property \\Stripe\\Service\\PaymentLinkService $paymentLinks
 * @property \\Stripe\\Service\\PaymentMethodConfigurationService $paymentMethodConfigurations
 * @property \\Stripe\\Service\\PaymentMethodDomainService $paymentMethodDomains
 * @property \\Stripe\\Service\\PaymentMethodService $paymentMethods
 * @property \\Stripe\\Service\\PayoutService $payouts
 * @property \\Stripe\\Service\\PlanService $plans
 * @property \\Stripe\\Service\\PriceService $prices
 * @property \\Stripe\\Service\\ProductService $products
 * @property \\Stripe\\Service\\PromotionCodeService $promotionCodes
 * @property \\Stripe\\Service\\QuoteService $quotes
 * @property \\Stripe\\Service\\Radar\\RadarServiceFactory $radar
 * @property \\Stripe\\Service\\RefundService $refunds
 * @property \\Stripe\\Service\\Reporting\\ReportingServiceFactory $reporting
 * @property \\Stripe\\Service\\ReviewService $reviews
 * @property \\Stripe\\Service\\SetupAttemptService $setupAttempts
 * @property \\Stripe\\Service\\SetupIntentService $setupIntents
 * @property \\Stripe\\Service\\ShippingRateService $shippingRates
 * @property \\Stripe\\Service\\Sigma\\SigmaServiceFactory $sigma
 * @property \\Stripe\\Service\\SourceService $sources
 * @property \\Stripe\\Service\\SubscriptionItemService $subscriptionItems
 * @property \\Stripe\\Service\\SubscriptionService $subscriptions
 * @property \\Stripe\\Service\\SubscriptionScheduleService $subscriptionSchedules
 * @property \\Stripe\\Service\\Tax\\TaxServiceFactory $tax
 * @property \\Stripe\\Service\\TaxCodeService $taxCodes
 * @property \\Stripe\\Service\\TaxIdService $taxIds
 * @property \\Stripe\\Service\\TaxRateService $taxRates
 * @property \\Stripe\\Service\\Terminal\\TerminalServiceFactory $terminal
 * @property \\Stripe\\Service\\TestHelpers\\TestHelpersServiceFactory $testHelpers
 * @property \\Stripe\\Service\\TokenService $tokens
 * @property \\Stripe\\Service\\TopupService $topups
 * @property \\Stripe\\Service\\TransferService $transfers
 * @property \\Stripe\\Service\\Treasury\\TreasuryServiceFactory $treasury
 * @property \\Stripe\\Service\\WebhookEndpointService $webhookEndpoints
 * // The end of the section generated from our OpenAPI spec
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 79,
    'endLine' => 99,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Stripe\\BaseStripeClient',
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
      'coreServiceFactory' => 
      array (
        'declaringClassName' => 'Stripe\\StripeClient',
        'implementingClassName' => 'Stripe\\StripeClient',
        'name' => 'coreServiceFactory',
        'modifiers' => 4,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * @var \\Stripe\\Service\\CoreServiceFactory
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 84,
        'endLine' => 84,
        'startColumn' => 5,
        'endColumn' => 32,
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
      '__get' => 
      array (
        'name' => '__get',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 86,
            'endLine' => 86,
            'startColumn' => 27,
            'endColumn' => 31,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 86,
        'endLine' => 89,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\StripeClient',
        'implementingClassName' => 'Stripe\\StripeClient',
        'currentClassName' => 'Stripe\\StripeClient',
        'aliasName' => NULL,
      ),
      'getService' => 
      array (
        'name' => 'getService',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 91,
            'endLine' => 91,
            'startColumn' => 32,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 91,
        'endLine' => 98,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\StripeClient',
        'implementingClassName' => 'Stripe\\StripeClient',
        'currentClassName' => 'Stripe\\StripeClient',
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