<?php declare(strict_types = 1);

// osfsl-/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/PaymentLink.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Stripe\PaymentLink
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-92665d56fa269d79d56264cbd7f08b93caada2cd39f51ca1c2ed0cacf4996ea6-8.4.24-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Stripe\\PaymentLink',
        'filename' => '/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/PaymentLink.php',
      ),
    ),
    'namespace' => 'Stripe',
    'name' => 'Stripe\\PaymentLink',
    'shortName' => 'PaymentLink',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * A payment link is a shareable URL that will take your customers to a hosted payment page. A payment link can be shared and used multiple times.
 *
 * When a customer opens a payment link it will open a new <a href="https://stripe.com/docs/api/checkout/sessions">checkout session</a> to render the payment page. You can use <a href="https://stripe.com/docs/api/events/types#event_types-checkout.session.completed">checkout session events</a> to track payments through payment links.
 *
 * Related guide: <a href="https://stripe.com/docs/payment-links">Payment Links API</a>
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object\'s type. Objects of the same type share the same value.
 * @property bool $active Whether the payment link\'s <code>url</code> is active. If <code>false</code>, customers visiting the URL will be shown a page saying that the link has been deactivated.
 * @property \\Stripe\\StripeObject $after_completion
 * @property bool $allow_promotion_codes Whether user redeemable promotion codes are enabled.
 * @property null|string|\\Stripe\\Application $application The ID of the Connect application that created the Payment Link.
 * @property null|int $application_fee_amount The amount of the application fee (if any) that will be requested to be applied to the payment and transferred to the application owner\'s Stripe account.
 * @property null|float $application_fee_percent This represents the percentage of the subscription invoice total that will be transferred to the application owner\'s Stripe account.
 * @property \\Stripe\\StripeObject $automatic_tax
 * @property string $billing_address_collection Configuration for collecting the customer\'s billing address. Defaults to <code>auto</code>.
 * @property null|\\Stripe\\StripeObject $consent_collection When set, provides configuration to gather active consent from customers.
 * @property string $currency Three-letter <a href="https://www.iso.org/iso-4217-currency-codes.html">ISO currency code</a>, in lowercase. Must be a <a href="https://stripe.com/docs/currencies">supported currency</a>.
 * @property \\Stripe\\StripeObject[] $custom_fields Collect additional information from your customer using custom fields. Up to 3 fields are supported.
 * @property \\Stripe\\StripeObject $custom_text
 * @property string $customer_creation Configuration for Customer creation during checkout.
 * @property null|string $inactive_message The custom message to be displayed to a customer when a payment link is no longer active.
 * @property null|\\Stripe\\StripeObject $invoice_creation Configuration for creating invoice for payment mode payment links.
 * @property null|\\Stripe\\Collection<\\Stripe\\LineItem> $line_items The line items representing what is being sold.
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property \\Stripe\\StripeObject $metadata Set of <a href="https://stripe.com/docs/api/metadata">key-value pairs</a> that you can attach to an object. This can be useful for storing additional information about the object in a structured format.
 * @property null|string|\\Stripe\\Account $on_behalf_of The account on behalf of which to charge. See the <a href="https://support.stripe.com/questions/sending-invoices-on-behalf-of-connected-accounts">Connect documentation</a> for details.
 * @property null|\\Stripe\\StripeObject $payment_intent_data Indicates the parameters to be passed to PaymentIntent creation during checkout.
 * @property string $payment_method_collection Configuration for collecting a payment method during checkout. Defaults to <code>always</code>.
 * @property null|string[] $payment_method_types The list of payment method types that customers can use. When <code>null</code>, Stripe will dynamically show relevant payment methods you\'ve enabled in your <a href="https://dashboard.stripe.com/settings/payment_methods">payment method settings</a>.
 * @property \\Stripe\\StripeObject $phone_number_collection
 * @property null|\\Stripe\\StripeObject $restrictions Settings that restrict the usage of a payment link.
 * @property null|\\Stripe\\StripeObject $shipping_address_collection Configuration for collecting the customer\'s shipping address.
 * @property \\Stripe\\StripeObject[] $shipping_options The shipping rate options applied to the session.
 * @property string $submit_type Indicates the type of transaction being performed which customizes relevant text on the page, such as the submit button.
 * @property null|\\Stripe\\StripeObject $subscription_data When creating a subscription, the specified configuration data will be used. There must be at least one line item with a recurring price to use <code>subscription_data</code>.
 * @property \\Stripe\\StripeObject $tax_id_collection
 * @property null|\\Stripe\\StripeObject $transfer_data The account (if any) the payments will be attributed to for tax reporting, and where funds from each payment will be transferred to.
 * @property string $url The public URL that can be shared with customers.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 48,
    'endLine' => 167,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Stripe\\ApiResource',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Stripe\\ApiOperations\\Update',
    ),
    'immediateConstants' => 
    array (
      'OBJECT_NAME' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentLink',
        'implementingClassName' => 'Stripe\\PaymentLink',
        'name' => 'OBJECT_NAME',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'payment_link\'',
          'attributes' => 
          array (
            'startLine' => 50,
            'endLine' => 50,
            'startTokenPos' => 27,
            'startFilePos' => 5272,
            'endTokenPos' => 27,
            'endFilePos' => 5285,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 50,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'BILLING_ADDRESS_COLLECTION_AUTO' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentLink',
        'implementingClassName' => 'Stripe\\PaymentLink',
        'name' => 'BILLING_ADDRESS_COLLECTION_AUTO',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'auto\'',
          'attributes' => 
          array (
            'startLine' => 54,
            'endLine' => 54,
            'startTokenPos' => 41,
            'startFilePos' => 5364,
            'endTokenPos' => 41,
            'endFilePos' => 5369,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 54,
        'endLine' => 54,
        'startColumn' => 5,
        'endColumn' => 51,
      ),
      'BILLING_ADDRESS_COLLECTION_REQUIRED' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentLink',
        'implementingClassName' => 'Stripe\\PaymentLink',
        'name' => 'BILLING_ADDRESS_COLLECTION_REQUIRED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'required\'',
          'attributes' => 
          array (
            'startLine' => 55,
            'endLine' => 55,
            'startTokenPos' => 50,
            'startFilePos' => 5420,
            'endTokenPos' => 50,
            'endFilePos' => 5429,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 55,
        'endLine' => 55,
        'startColumn' => 5,
        'endColumn' => 59,
      ),
      'CUSTOMER_CREATION_ALWAYS' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentLink',
        'implementingClassName' => 'Stripe\\PaymentLink',
        'name' => 'CUSTOMER_CREATION_ALWAYS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'always\'',
          'attributes' => 
          array (
            'startLine' => 57,
            'endLine' => 57,
            'startTokenPos' => 59,
            'startFilePos' => 5470,
            'endTokenPos' => 59,
            'endFilePos' => 5477,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 57,
        'endLine' => 57,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'CUSTOMER_CREATION_IF_REQUIRED' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentLink',
        'implementingClassName' => 'Stripe\\PaymentLink',
        'name' => 'CUSTOMER_CREATION_IF_REQUIRED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'if_required\'',
          'attributes' => 
          array (
            'startLine' => 58,
            'endLine' => 58,
            'startTokenPos' => 68,
            'startFilePos' => 5522,
            'endTokenPos' => 68,
            'endFilePos' => 5534,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 58,
        'endLine' => 58,
        'startColumn' => 5,
        'endColumn' => 56,
      ),
      'PAYMENT_METHOD_COLLECTION_ALWAYS' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentLink',
        'implementingClassName' => 'Stripe\\PaymentLink',
        'name' => 'PAYMENT_METHOD_COLLECTION_ALWAYS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'always\'',
          'attributes' => 
          array (
            'startLine' => 60,
            'endLine' => 60,
            'startTokenPos' => 77,
            'startFilePos' => 5583,
            'endTokenPos' => 77,
            'endFilePos' => 5590,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 60,
        'endLine' => 60,
        'startColumn' => 5,
        'endColumn' => 54,
      ),
      'PAYMENT_METHOD_COLLECTION_IF_REQUIRED' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentLink',
        'implementingClassName' => 'Stripe\\PaymentLink',
        'name' => 'PAYMENT_METHOD_COLLECTION_IF_REQUIRED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'if_required\'',
          'attributes' => 
          array (
            'startLine' => 61,
            'endLine' => 61,
            'startTokenPos' => 86,
            'startFilePos' => 5643,
            'endTokenPos' => 86,
            'endFilePos' => 5655,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 61,
        'endLine' => 61,
        'startColumn' => 5,
        'endColumn' => 64,
      ),
      'SUBMIT_TYPE_AUTO' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentLink',
        'implementingClassName' => 'Stripe\\PaymentLink',
        'name' => 'SUBMIT_TYPE_AUTO',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'auto\'',
          'attributes' => 
          array (
            'startLine' => 63,
            'endLine' => 63,
            'startTokenPos' => 95,
            'startFilePos' => 5688,
            'endTokenPos' => 95,
            'endFilePos' => 5693,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 63,
        'endLine' => 63,
        'startColumn' => 5,
        'endColumn' => 36,
      ),
      'SUBMIT_TYPE_BOOK' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentLink',
        'implementingClassName' => 'Stripe\\PaymentLink',
        'name' => 'SUBMIT_TYPE_BOOK',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'book\'',
          'attributes' => 
          array (
            'startLine' => 64,
            'endLine' => 64,
            'startTokenPos' => 104,
            'startFilePos' => 5725,
            'endTokenPos' => 104,
            'endFilePos' => 5730,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 64,
        'endLine' => 64,
        'startColumn' => 5,
        'endColumn' => 36,
      ),
      'SUBMIT_TYPE_DONATE' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentLink',
        'implementingClassName' => 'Stripe\\PaymentLink',
        'name' => 'SUBMIT_TYPE_DONATE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'donate\'',
          'attributes' => 
          array (
            'startLine' => 65,
            'endLine' => 65,
            'startTokenPos' => 113,
            'startFilePos' => 5764,
            'endTokenPos' => 113,
            'endFilePos' => 5771,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 65,
        'endLine' => 65,
        'startColumn' => 5,
        'endColumn' => 40,
      ),
      'SUBMIT_TYPE_PAY' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentLink',
        'implementingClassName' => 'Stripe\\PaymentLink',
        'name' => 'SUBMIT_TYPE_PAY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'pay\'',
          'attributes' => 
          array (
            'startLine' => 66,
            'endLine' => 66,
            'startTokenPos' => 122,
            'startFilePos' => 5802,
            'endTokenPos' => 122,
            'endFilePos' => 5806,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 66,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 34,
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
                'startLine' => 78,
                'endLine' => 78,
                'startTokenPos' => 139,
                'startFilePos' => 6124,
                'endTokenPos' => 139,
                'endFilePos' => 6127,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 78,
            'endLine' => 78,
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
                'startLine' => 78,
                'endLine' => 78,
                'startTokenPos' => 146,
                'startFilePos' => 6141,
                'endTokenPos' => 146,
                'endFilePos' => 6144,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 78,
            'endLine' => 78,
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
 * Creates a payment link.
 *
 * @param null|array $params
 * @param null|array|string $options
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\PaymentLink the created resource
 */',
        'startLine' => 78,
        'endLine' => 88,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\PaymentLink',
        'implementingClassName' => 'Stripe\\PaymentLink',
        'currentClassName' => 'Stripe\\PaymentLink',
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
                'startLine' => 100,
                'endLine' => 100,
                'startTokenPos' => 243,
                'startFilePos' => 6808,
                'endTokenPos' => 243,
                'endFilePos' => 6811,
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
                'startLine' => 100,
                'endLine' => 100,
                'startTokenPos' => 250,
                'startFilePos' => 6822,
                'endTokenPos' => 250,
                'endFilePos' => 6825,
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
 * Returns a list of your payment links.
 *
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Collection<\\Stripe\\PaymentLink> of ApiResources
 */',
        'startLine' => 100,
        'endLine' => 105,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\PaymentLink',
        'implementingClassName' => 'Stripe\\PaymentLink',
        'currentClassName' => 'Stripe\\PaymentLink',
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
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 117,
            'endLine' => 117,
            'startColumn' => 37,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'opts' => 
          array (
            'name' => 'opts',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 117,
                'endLine' => 117,
                'startTokenPos' => 306,
                'startFilePos' => 7340,
                'endTokenPos' => 306,
                'endFilePos' => 7343,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 117,
            'endLine' => 117,
            'startColumn' => 42,
            'endColumn' => 53,
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
 * Retrieve a payment link.
 *
 * @param array|string $id the ID of the API resource to retrieve, or an options array containing an `id` key
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\PaymentLink
 */',
        'startLine' => 117,
        'endLine' => 124,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\PaymentLink',
        'implementingClassName' => 'Stripe\\PaymentLink',
        'currentClassName' => 'Stripe\\PaymentLink',
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
            'startLine' => 137,
            'endLine' => 137,
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
                'startLine' => 137,
                'endLine' => 137,
                'startTokenPos' => 369,
                'startFilePos' => 7893,
                'endTokenPos' => 369,
                'endFilePos' => 7896,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 137,
            'endLine' => 137,
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
                'startLine' => 137,
                'endLine' => 137,
                'startTokenPos' => 376,
                'startFilePos' => 7907,
                'endTokenPos' => 376,
                'endFilePos' => 7910,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 137,
            'endLine' => 137,
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
 * Updates a payment link.
 *
 * @param string $id the ID of the resource to update
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\PaymentLink the updated resource
 */',
        'startLine' => 137,
        'endLine' => 147,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\PaymentLink',
        'implementingClassName' => 'Stripe\\PaymentLink',
        'currentClassName' => 'Stripe\\PaymentLink',
        'aliasName' => NULL,
      ),
      'allLineItems' => 
      array (
        'name' => 'allLineItems',
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
            'startLine' => 158,
            'endLine' => 158,
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
                'startLine' => 158,
                'endLine' => 158,
                'startTokenPos' => 477,
                'startFilePos' => 8564,
                'endTokenPos' => 477,
                'endFilePos' => 8567,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 158,
            'endLine' => 158,
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
                'startLine' => 158,
                'endLine' => 158,
                'startTokenPos' => 484,
                'startFilePos' => 8578,
                'endTokenPos' => 484,
                'endFilePos' => 8581,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 158,
            'endLine' => 158,
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
 * @param string $id
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Collection<\\Stripe\\LineItem> list of line items
 */',
        'startLine' => 158,
        'endLine' => 166,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\PaymentLink',
        'implementingClassName' => 'Stripe\\PaymentLink',
        'currentClassName' => 'Stripe\\PaymentLink',
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