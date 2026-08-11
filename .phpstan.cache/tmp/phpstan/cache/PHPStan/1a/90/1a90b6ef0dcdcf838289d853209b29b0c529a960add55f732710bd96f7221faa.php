<?php declare(strict_types = 1);

// osfsl-/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/Checkout/Session.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Stripe\Checkout\Session
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-b5f73afd624d7561e042ada058737ebbbd0b5180dac9581e74ea62b4e8a06dfb-8.4.24-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Stripe\\Checkout\\Session',
        'filename' => '/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/Checkout/Session.php',
      ),
    ),
    'namespace' => 'Stripe\\Checkout',
    'name' => 'Stripe\\Checkout\\Session',
    'shortName' => 'Session',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * A Checkout Session represents your customer\'s session as they pay for
 * one-time purchases or subscriptions through <a href="https://stripe.com/docs/payments/checkout">Checkout</a>
 * or <a href="https://stripe.com/docs/payments/payment-links">Payment Links</a>. We recommend creating a
 * new Session each time your customer attempts to pay.
 *
 * Once payment is successful, the Checkout Session will contain a reference
 * to the <a href="https://stripe.com/docs/api/customers">Customer</a>, and either the successful
 * <a href="https://stripe.com/docs/api/payment_intents">PaymentIntent</a> or an active
 * <a href="https://stripe.com/docs/api/subscriptions">Subscription</a>.
 *
 * You can create a Checkout Session on your server and redirect to its URL
 * to begin Checkout.
 *
 * Related guide: <a href="https://stripe.com/docs/checkout/quickstart">Checkout quickstart</a>
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object\'s type. Objects of the same type share the same value.
 * @property null|\\Stripe\\StripeObject $after_expiration When set, provides configuration for actions to take if this Checkout Session expires.
 * @property null|bool $allow_promotion_codes Enables user redeemable promotion codes.
 * @property null|int $amount_subtotal Total of all items before discounts or taxes are applied.
 * @property null|int $amount_total Total of all items after discounts and taxes are applied.
 * @property \\Stripe\\StripeObject $automatic_tax
 * @property null|string $billing_address_collection Describes whether Checkout should collect the customer\'s billing address. Defaults to <code>auto</code>.
 * @property null|string $cancel_url If set, Checkout displays a back button and customers will be directed to this URL if they decide to cancel payment and return to your website.
 * @property null|string $client_reference_id A unique string to reference the Checkout Session. This can be a customer ID, a cart ID, or similar, and can be used to reconcile the Session with your internal systems.
 * @property null|string $client_secret Client secret to be used when initializing Stripe.js embedded checkout.
 * @property null|\\Stripe\\StripeObject $consent Results of <code>consent_collection</code> for this session.
 * @property null|\\Stripe\\StripeObject $consent_collection When set, provides configuration for the Checkout Session to gather active consent from customers.
 * @property int $created Time at which the object was created. Measured in seconds since the Unix epoch.
 * @property null|string $currency Three-letter <a href="https://www.iso.org/iso-4217-currency-codes.html">ISO currency code</a>, in lowercase. Must be a <a href="https://stripe.com/docs/currencies">supported currency</a>.
 * @property null|\\Stripe\\StripeObject $currency_conversion Currency conversion details for automatic currency conversion sessions
 * @property \\Stripe\\StripeObject[] $custom_fields Collect additional information from your customer using custom fields. Up to 3 fields are supported.
 * @property \\Stripe\\StripeObject $custom_text
 * @property null|string|\\Stripe\\Customer $customer The ID of the customer for this Session. For Checkout Sessions in <code>subscription</code> mode or Checkout Sessions with <code>customer_creation</code> set as <code>always</code> in <code>payment</code> mode, Checkout will create a new customer object based on information provided during the payment flow unless an existing customer was provided when the Session was created.
 * @property null|string $customer_creation Configure whether a Checkout Session creates a Customer when the Checkout Session completes.
 * @property null|\\Stripe\\StripeObject $customer_details The customer details including the customer\'s tax exempt status and the customer\'s tax IDs. Customer\'s address details are not present on Sessions in <code>setup</code> mode.
 * @property null|string $customer_email If provided, this value will be used when the Customer object is created. If not provided, customers will be asked to enter their email address. Use this parameter to prefill customer data if you already have an email on file. To access information about the customer once the payment flow is complete, use the <code>customer</code> attribute.
 * @property int $expires_at The timestamp at which the Checkout Session will expire.
 * @property null|string|\\Stripe\\Invoice $invoice ID of the invoice created by the Checkout Session, if it exists.
 * @property null|\\Stripe\\StripeObject $invoice_creation Details on the state of invoice creation for the Checkout Session.
 * @property null|\\Stripe\\Collection<\\Stripe\\LineItem> $line_items The line items purchased by the customer.
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property null|string $locale The IETF language tag of the locale Checkout is displayed in. If blank or <code>auto</code>, the browser\'s locale is used.
 * @property null|\\Stripe\\StripeObject $metadata Set of <a href="https://stripe.com/docs/api/metadata">key-value pairs</a> that you can attach to an object. This can be useful for storing additional information about the object in a structured format.
 * @property string $mode The mode of the Checkout Session.
 * @property null|string|\\Stripe\\PaymentIntent $payment_intent The ID of the PaymentIntent for Checkout Sessions in <code>payment</code> mode. You can\'t confirm or cancel the PaymentIntent for a Checkout Session. To cancel, <a href="https://stripe.com/docs/api/checkout/sessions/expire">expire the Checkout Session</a> instead.
 * @property null|string|\\Stripe\\PaymentLink $payment_link The ID of the Payment Link that created this Session.
 * @property null|string $payment_method_collection Configure whether a Checkout Session should collect a payment method. Defaults to <code>always</code>.
 * @property null|\\Stripe\\StripeObject $payment_method_configuration_details Information about the payment method configuration used for this Checkout session if using dynamic payment methods.
 * @property null|\\Stripe\\StripeObject $payment_method_options Payment-method-specific configuration for the PaymentIntent or SetupIntent of this CheckoutSession.
 * @property string[] $payment_method_types A list of the types of payment methods (e.g. card) this Checkout Session is allowed to accept.
 * @property string $payment_status The payment status of the Checkout Session, one of <code>paid</code>, <code>unpaid</code>, or <code>no_payment_required</code>. You can use this value to decide when to fulfill your customer\'s order.
 * @property null|\\Stripe\\StripeObject $phone_number_collection
 * @property null|string $recovered_from The ID of the original expired Checkout Session that triggered the recovery flow.
 * @property null|string $redirect_on_completion This parameter applies to <code>ui_mode: embedded</code>. Learn more about the <a href="https://stripe.com/docs/payments/checkout/custom-redirect-behavior">redirect behavior</a> of embedded sessions. Defaults to <code>always</code>.
 * @property null|string $return_url Applies to Checkout Sessions with <code>ui_mode: embedded</code>. The URL to redirect your customer back to after they authenticate or cancel their payment on the payment method\'s app or site.
 * @property null|\\Stripe\\StripeObject $saved_payment_method_options Controls saved payment method settings for the session. Only available in <code>payment</code> and <code>subscription</code> mode.
 * @property null|string|\\Stripe\\SetupIntent $setup_intent The ID of the SetupIntent for Checkout Sessions in <code>setup</code> mode. You can\'t confirm or cancel the SetupIntent for a Checkout Session. To cancel, <a href="https://stripe.com/docs/api/checkout/sessions/expire">expire the Checkout Session</a> instead.
 * @property null|\\Stripe\\StripeObject $shipping_address_collection When set, provides configuration for Checkout to collect a shipping address from a customer.
 * @property null|\\Stripe\\StripeObject $shipping_cost The details of the customer cost of shipping, including the customer chosen ShippingRate.
 * @property null|\\Stripe\\StripeObject $shipping_details Shipping information for this Checkout Session.
 * @property \\Stripe\\StripeObject[] $shipping_options The shipping rate options applied to this Session.
 * @property null|string $status The status of the Checkout Session, one of <code>open</code>, <code>complete</code>, or <code>expired</code>.
 * @property null|string $submit_type Describes the type of transaction being performed by Checkout in order to customize relevant text on the page, such as the submit button. <code>submit_type</code> can only be specified on Checkout Sessions in <code>payment</code> mode. If blank or <code>auto</code>, <code>pay</code> is used.
 * @property null|string|\\Stripe\\Subscription $subscription The ID of the subscription for Checkout Sessions in <code>subscription</code> mode.
 * @property null|string $success_url The URL the customer will be directed to after the payment or subscription creation is successful.
 * @property null|\\Stripe\\StripeObject $tax_id_collection
 * @property null|\\Stripe\\StripeObject $total_details Tax and discount details for the computed total amount.
 * @property null|string $ui_mode The UI mode of the Session. Defaults to <code>hosted</code>.
 * @property null|string $url The URL to the Checkout Session. Redirect customers to this URL to take them to Checkout. If you’re using <a href="https://stripe.com/docs/payments/checkout/custom-domains">Custom Domains</a>, the URL will use your subdomain. Otherwise, it’ll use <code>checkout.stripe.com.</code> This value is only present when the session is active.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 79,
    'endLine' => 209,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Stripe\\ApiResource',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'OBJECT_NAME' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'OBJECT_NAME',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'checkout.session\'',
          'attributes' => 
          array (
            'startLine' => 81,
            'endLine' => 81,
            'startTokenPos' => 27,
            'startFilePos' => 9972,
            'endTokenPos' => 27,
            'endFilePos' => 9989,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 81,
        'endLine' => 81,
        'startColumn' => 5,
        'endColumn' => 43,
      ),
      'BILLING_ADDRESS_COLLECTION_AUTO' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'BILLING_ADDRESS_COLLECTION_AUTO',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'auto\'',
          'attributes' => 
          array (
            'startLine' => 83,
            'endLine' => 83,
            'startTokenPos' => 36,
            'startFilePos' => 10037,
            'endTokenPos' => 36,
            'endFilePos' => 10042,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 83,
        'endLine' => 83,
        'startColumn' => 5,
        'endColumn' => 51,
      ),
      'BILLING_ADDRESS_COLLECTION_REQUIRED' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'BILLING_ADDRESS_COLLECTION_REQUIRED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'required\'',
          'attributes' => 
          array (
            'startLine' => 84,
            'endLine' => 84,
            'startTokenPos' => 45,
            'startFilePos' => 10093,
            'endTokenPos' => 45,
            'endFilePos' => 10102,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 84,
        'endLine' => 84,
        'startColumn' => 5,
        'endColumn' => 59,
      ),
      'CUSTOMER_CREATION_ALWAYS' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'CUSTOMER_CREATION_ALWAYS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'always\'',
          'attributes' => 
          array (
            'startLine' => 86,
            'endLine' => 86,
            'startTokenPos' => 54,
            'startFilePos' => 10143,
            'endTokenPos' => 54,
            'endFilePos' => 10150,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 86,
        'endLine' => 86,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'CUSTOMER_CREATION_IF_REQUIRED' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'CUSTOMER_CREATION_IF_REQUIRED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'if_required\'',
          'attributes' => 
          array (
            'startLine' => 87,
            'endLine' => 87,
            'startTokenPos' => 63,
            'startFilePos' => 10195,
            'endTokenPos' => 63,
            'endFilePos' => 10207,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 87,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 56,
      ),
      'MODE_PAYMENT' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'MODE_PAYMENT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'payment\'',
          'attributes' => 
          array (
            'startLine' => 89,
            'endLine' => 89,
            'startTokenPos' => 72,
            'startFilePos' => 10236,
            'endTokenPos' => 72,
            'endFilePos' => 10244,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 89,
        'endLine' => 89,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'MODE_SETUP' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'MODE_SETUP',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'setup\'',
          'attributes' => 
          array (
            'startLine' => 90,
            'endLine' => 90,
            'startTokenPos' => 81,
            'startFilePos' => 10270,
            'endTokenPos' => 81,
            'endFilePos' => 10276,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 90,
        'endLine' => 90,
        'startColumn' => 5,
        'endColumn' => 31,
      ),
      'MODE_SUBSCRIPTION' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'MODE_SUBSCRIPTION',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'subscription\'',
          'attributes' => 
          array (
            'startLine' => 91,
            'endLine' => 91,
            'startTokenPos' => 90,
            'startFilePos' => 10309,
            'endTokenPos' => 90,
            'endFilePos' => 10322,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 91,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'PAYMENT_METHOD_COLLECTION_ALWAYS' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'PAYMENT_METHOD_COLLECTION_ALWAYS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'always\'',
          'attributes' => 
          array (
            'startLine' => 93,
            'endLine' => 93,
            'startTokenPos' => 99,
            'startFilePos' => 10371,
            'endTokenPos' => 99,
            'endFilePos' => 10378,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 93,
        'endLine' => 93,
        'startColumn' => 5,
        'endColumn' => 54,
      ),
      'PAYMENT_METHOD_COLLECTION_IF_REQUIRED' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'PAYMENT_METHOD_COLLECTION_IF_REQUIRED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'if_required\'',
          'attributes' => 
          array (
            'startLine' => 94,
            'endLine' => 94,
            'startTokenPos' => 108,
            'startFilePos' => 10431,
            'endTokenPos' => 108,
            'endFilePos' => 10443,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 94,
        'endLine' => 94,
        'startColumn' => 5,
        'endColumn' => 64,
      ),
      'PAYMENT_STATUS_NO_PAYMENT_REQUIRED' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'PAYMENT_STATUS_NO_PAYMENT_REQUIRED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'no_payment_required\'',
          'attributes' => 
          array (
            'startLine' => 96,
            'endLine' => 96,
            'startTokenPos' => 117,
            'startFilePos' => 10494,
            'endTokenPos' => 117,
            'endFilePos' => 10514,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 96,
        'endLine' => 96,
        'startColumn' => 5,
        'endColumn' => 69,
      ),
      'PAYMENT_STATUS_PAID' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'PAYMENT_STATUS_PAID',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'paid\'',
          'attributes' => 
          array (
            'startLine' => 97,
            'endLine' => 97,
            'startTokenPos' => 126,
            'startFilePos' => 10549,
            'endTokenPos' => 126,
            'endFilePos' => 10554,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 97,
        'endLine' => 97,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'PAYMENT_STATUS_UNPAID' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'PAYMENT_STATUS_UNPAID',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'unpaid\'',
          'attributes' => 
          array (
            'startLine' => 98,
            'endLine' => 98,
            'startTokenPos' => 135,
            'startFilePos' => 10591,
            'endTokenPos' => 135,
            'endFilePos' => 10598,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 98,
        'endLine' => 98,
        'startColumn' => 5,
        'endColumn' => 43,
      ),
      'REDIRECT_ON_COMPLETION_ALWAYS' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'REDIRECT_ON_COMPLETION_ALWAYS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'always\'',
          'attributes' => 
          array (
            'startLine' => 100,
            'endLine' => 100,
            'startTokenPos' => 144,
            'startFilePos' => 10644,
            'endTokenPos' => 144,
            'endFilePos' => 10651,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 100,
        'endLine' => 100,
        'startColumn' => 5,
        'endColumn' => 51,
      ),
      'REDIRECT_ON_COMPLETION_IF_REQUIRED' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'REDIRECT_ON_COMPLETION_IF_REQUIRED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'if_required\'',
          'attributes' => 
          array (
            'startLine' => 101,
            'endLine' => 101,
            'startTokenPos' => 153,
            'startFilePos' => 10701,
            'endTokenPos' => 153,
            'endFilePos' => 10713,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 101,
        'endLine' => 101,
        'startColumn' => 5,
        'endColumn' => 61,
      ),
      'REDIRECT_ON_COMPLETION_NEVER' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'REDIRECT_ON_COMPLETION_NEVER',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'never\'',
          'attributes' => 
          array (
            'startLine' => 102,
            'endLine' => 102,
            'startTokenPos' => 162,
            'startFilePos' => 10757,
            'endTokenPos' => 162,
            'endFilePos' => 10763,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 102,
        'endLine' => 102,
        'startColumn' => 5,
        'endColumn' => 49,
      ),
      'STATUS_COMPLETE' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'STATUS_COMPLETE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'complete\'',
          'attributes' => 
          array (
            'startLine' => 104,
            'endLine' => 104,
            'startTokenPos' => 171,
            'startFilePos' => 10795,
            'endTokenPos' => 171,
            'endFilePos' => 10804,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 104,
        'endLine' => 104,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATUS_EXPIRED' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'STATUS_EXPIRED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'expired\'',
          'attributes' => 
          array (
            'startLine' => 105,
            'endLine' => 105,
            'startTokenPos' => 180,
            'startFilePos' => 10834,
            'endTokenPos' => 180,
            'endFilePos' => 10842,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 105,
        'endLine' => 105,
        'startColumn' => 5,
        'endColumn' => 37,
      ),
      'STATUS_OPEN' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'STATUS_OPEN',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'open\'',
          'attributes' => 
          array (
            'startLine' => 106,
            'endLine' => 106,
            'startTokenPos' => 189,
            'startFilePos' => 10869,
            'endTokenPos' => 189,
            'endFilePos' => 10874,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 106,
        'endLine' => 106,
        'startColumn' => 5,
        'endColumn' => 31,
      ),
      'SUBMIT_TYPE_AUTO' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'SUBMIT_TYPE_AUTO',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'auto\'',
          'attributes' => 
          array (
            'startLine' => 108,
            'endLine' => 108,
            'startTokenPos' => 198,
            'startFilePos' => 10907,
            'endTokenPos' => 198,
            'endFilePos' => 10912,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 108,
        'endLine' => 108,
        'startColumn' => 5,
        'endColumn' => 36,
      ),
      'SUBMIT_TYPE_BOOK' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'SUBMIT_TYPE_BOOK',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'book\'',
          'attributes' => 
          array (
            'startLine' => 109,
            'endLine' => 109,
            'startTokenPos' => 207,
            'startFilePos' => 10944,
            'endTokenPos' => 207,
            'endFilePos' => 10949,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 109,
        'endLine' => 109,
        'startColumn' => 5,
        'endColumn' => 36,
      ),
      'SUBMIT_TYPE_DONATE' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'SUBMIT_TYPE_DONATE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'donate\'',
          'attributes' => 
          array (
            'startLine' => 110,
            'endLine' => 110,
            'startTokenPos' => 216,
            'startFilePos' => 10983,
            'endTokenPos' => 216,
            'endFilePos' => 10990,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 110,
        'endLine' => 110,
        'startColumn' => 5,
        'endColumn' => 40,
      ),
      'SUBMIT_TYPE_PAY' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'SUBMIT_TYPE_PAY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'pay\'',
          'attributes' => 
          array (
            'startLine' => 111,
            'endLine' => 111,
            'startTokenPos' => 225,
            'startFilePos' => 11021,
            'endTokenPos' => 225,
            'endFilePos' => 11025,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 111,
        'endLine' => 111,
        'startColumn' => 5,
        'endColumn' => 34,
      ),
      'UI_MODE_EMBEDDED' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'UI_MODE_EMBEDDED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'embedded\'',
          'attributes' => 
          array (
            'startLine' => 113,
            'endLine' => 113,
            'startTokenPos' => 234,
            'startFilePos' => 11058,
            'endTokenPos' => 234,
            'endFilePos' => 11067,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 113,
        'endLine' => 113,
        'startColumn' => 5,
        'endColumn' => 40,
      ),
      'UI_MODE_HOSTED' => 
      array (
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'name' => 'UI_MODE_HOSTED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'hosted\'',
          'attributes' => 
          array (
            'startLine' => 114,
            'endLine' => 114,
            'startTokenPos' => 243,
            'startFilePos' => 11097,
            'endTokenPos' => 243,
            'endFilePos' => 11104,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 114,
        'endLine' => 114,
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
                'startLine' => 126,
                'endLine' => 126,
                'startTokenPos' => 260,
                'startFilePos' => 11429,
                'endTokenPos' => 260,
                'endFilePos' => 11432,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 126,
            'endLine' => 126,
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
                'startLine' => 126,
                'endLine' => 126,
                'startTokenPos' => 267,
                'startFilePos' => 11446,
                'endTokenPos' => 267,
                'endFilePos' => 11449,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 126,
            'endLine' => 126,
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
 * Creates a Session object.
 *
 * @param null|array $params
 * @param null|array|string $options
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Checkout\\Session the created resource
 */',
        'startLine' => 126,
        'endLine' => 136,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe\\Checkout',
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'currentClassName' => 'Stripe\\Checkout\\Session',
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
                'startLine' => 148,
                'endLine' => 148,
                'startTokenPos' => 364,
                'startFilePos' => 12117,
                'endTokenPos' => 364,
                'endFilePos' => 12120,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 148,
            'endLine' => 148,
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
                'startLine' => 148,
                'endLine' => 148,
                'startTokenPos' => 371,
                'startFilePos' => 12131,
                'endTokenPos' => 371,
                'endFilePos' => 12134,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 148,
            'endLine' => 148,
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
 * Returns a list of Checkout Sessions.
 *
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Collection<\\Stripe\\Checkout\\Session> of ApiResources
 */',
        'startLine' => 148,
        'endLine' => 153,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe\\Checkout',
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'currentClassName' => 'Stripe\\Checkout\\Session',
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
            'startLine' => 165,
            'endLine' => 165,
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
                'startLine' => 165,
                'endLine' => 165,
                'startTokenPos' => 427,
                'startFilePos' => 12657,
                'endTokenPos' => 427,
                'endFilePos' => 12660,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 165,
            'endLine' => 165,
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
 * Retrieves a Session object.
 *
 * @param array|string $id the ID of the API resource to retrieve, or an options array containing an `id` key
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Checkout\\Session
 */',
        'startLine' => 165,
        'endLine' => 172,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe\\Checkout',
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'currentClassName' => 'Stripe\\Checkout\\Session',
        'aliasName' => NULL,
      ),
      'expire' => 
      array (
        'name' => 'expire',
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
                'startLine' => 182,
                'endLine' => 182,
                'startTokenPos' => 485,
                'startFilePos' => 13106,
                'endTokenPos' => 485,
                'endFilePos' => 13109,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 182,
            'endLine' => 182,
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
                'startLine' => 182,
                'endLine' => 182,
                'startTokenPos' => 492,
                'startFilePos' => 13120,
                'endTokenPos' => 492,
                'endFilePos' => 13123,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 182,
            'endLine' => 182,
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
 * @return \\Stripe\\Checkout\\Session the expired session
 */',
        'startLine' => 182,
        'endLine' => 189,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\Checkout',
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'currentClassName' => 'Stripe\\Checkout\\Session',
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
            'startLine' => 200,
            'endLine' => 200,
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
                'startLine' => 200,
                'endLine' => 200,
                'startTokenPos' => 574,
                'startFilePos' => 13661,
                'endTokenPos' => 574,
                'endFilePos' => 13664,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 200,
            'endLine' => 200,
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
                'startLine' => 200,
                'endLine' => 200,
                'startTokenPos' => 581,
                'startFilePos' => 13675,
                'endTokenPos' => 581,
                'endFilePos' => 13678,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 200,
            'endLine' => 200,
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
        'startLine' => 200,
        'endLine' => 208,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe\\Checkout',
        'declaringClassName' => 'Stripe\\Checkout\\Session',
        'implementingClassName' => 'Stripe\\Checkout\\Session',
        'currentClassName' => 'Stripe\\Checkout\\Session',
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