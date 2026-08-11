<?php declare(strict_types = 1);

// osfsl-/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/Charge.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Stripe\Charge
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-656f12089632b1c610a76127646194d8f845176857e5065e6ba1a5e7408a3f02-8.4.24-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Stripe\\Charge',
        'filename' => '/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/Charge.php',
      ),
    ),
    'namespace' => 'Stripe',
    'name' => 'Stripe\\Charge',
    'shortName' => 'Charge',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * The <code>Charge</code> object represents a single attempt to move money into your Stripe account.
 * PaymentIntent confirmation is the most common way to create Charges, but transferring
 * money to a different Stripe account through Connect also creates Charges.
 * Some legacy payment flows create Charges directly, which is not recommended for new integrations.
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object\'s type. Objects of the same type share the same value.
 * @property int $amount Amount intended to be collected by this payment. A positive integer representing how much to charge in the <a href="https://stripe.com/docs/currencies#zero-decimal">smallest currency unit</a> (e.g., 100 cents to charge $1.00 or 100 to charge ¥100, a zero-decimal currency). The minimum amount is $0.50 US or <a href="https://stripe.com/docs/currencies#minimum-and-maximum-charge-amounts">equivalent in charge currency</a>. The amount value supports up to eight digits (e.g., a value of 99999999 for a USD charge of $999,999.99).
 * @property int $amount_captured Amount in cents (or local equivalent) captured (can be less than the amount attribute on the charge if a partial capture was made).
 * @property int $amount_refunded Amount in cents (or local equivalent) refunded (can be less than the amount attribute on the charge if a partial refund was issued).
 * @property null|string|\\Stripe\\Application $application ID of the Connect application that created the charge.
 * @property null|string|\\Stripe\\ApplicationFee $application_fee The application fee (if any) for the charge. <a href="https://stripe.com/docs/connect/direct-charges#collect-fees">See the Connect documentation</a> for details.
 * @property null|int $application_fee_amount The amount of the application fee (if any) requested for the charge. <a href="https://stripe.com/docs/connect/direct-charges#collect-fees">See the Connect documentation</a> for details.
 * @property null|string $authorization_code Authorization code on the charge.
 * @property null|string|\\Stripe\\BalanceTransaction $balance_transaction ID of the balance transaction that describes the impact of this charge on your account balance (not including refunds or disputes).
 * @property \\Stripe\\StripeObject $billing_details
 * @property null|string $calculated_statement_descriptor The full statement descriptor that is passed to card networks, and that is displayed on your customers\' credit card and bank statements. Allows you to see what the statement descriptor looks like after the static and dynamic portions are combined.
 * @property bool $captured If the charge was created without capturing, this Boolean represents whether it is still uncaptured or has since been captured.
 * @property int $created Time at which the object was created. Measured in seconds since the Unix epoch.
 * @property string $currency Three-letter <a href="https://www.iso.org/iso-4217-currency-codes.html">ISO currency code</a>, in lowercase. Must be a <a href="https://stripe.com/docs/currencies">supported currency</a>.
 * @property null|string|\\Stripe\\Customer $customer ID of the customer this charge is for if one exists.
 * @property null|string $description An arbitrary string attached to the object. Often useful for displaying to users.
 * @property bool $disputed Whether the charge has been disputed.
 * @property null|string|\\Stripe\\BalanceTransaction $failure_balance_transaction ID of the balance transaction that describes the reversal of the balance on your account due to payment failure.
 * @property null|string $failure_code Error code explaining reason for charge failure if available (see <a href="https://stripe.com/docs/error-codes">the errors section</a> for a list of codes).
 * @property null|string $failure_message Message to user further explaining reason for charge failure if available.
 * @property null|\\Stripe\\StripeObject $fraud_details Information on fraud assessments for the charge.
 * @property null|string|\\Stripe\\Invoice $invoice ID of the invoice this charge is for if one exists.
 * @property null|\\Stripe\\StripeObject $level3
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property \\Stripe\\StripeObject $metadata Set of <a href="https://stripe.com/docs/api/metadata">key-value pairs</a> that you can attach to an object. This can be useful for storing additional information about the object in a structured format.
 * @property null|string|\\Stripe\\Account $on_behalf_of The account (if any) the charge was made on behalf of without triggering an automatic transfer. See the <a href="https://stripe.com/docs/connect/separate-charges-and-transfers">Connect documentation</a> for details.
 * @property null|\\Stripe\\StripeObject $outcome Details about whether the payment was accepted, and why. See <a href="https://stripe.com/docs/declines">understanding declines</a> for details.
 * @property bool $paid <code>true</code> if the charge succeeded, or was successfully authorized for later capture.
 * @property null|string|\\Stripe\\PaymentIntent $payment_intent ID of the PaymentIntent associated with this charge, if one exists.
 * @property null|string $payment_method ID of the payment method used in this charge.
 * @property null|\\Stripe\\StripeObject $payment_method_details Details about the payment method at the time of the transaction.
 * @property null|\\Stripe\\StripeObject $radar_options Options to configure Radar. See <a href="https://stripe.com/docs/radar/radar-session">Radar Session</a> for more information.
 * @property null|string $receipt_email This is the email address that the receipt for this charge was sent to.
 * @property null|string $receipt_number This is the transaction number that appears on email receipts sent for this charge. This attribute will be <code>null</code> until a receipt has been sent.
 * @property null|string $receipt_url This is the URL to view the receipt for this charge. The receipt is kept up-to-date to the latest state of the charge, including any refunds. If the charge is for an Invoice, the receipt will be stylized as an Invoice receipt.
 * @property bool $refunded Whether the charge has been fully refunded. If the charge is only partially refunded, this attribute will still be false.
 * @property null|\\Stripe\\Collection<\\Stripe\\Refund> $refunds A list of refunds that have been applied to the charge.
 * @property null|string|\\Stripe\\Review $review ID of the review associated with this charge if one exists.
 * @property null|\\Stripe\\StripeObject $shipping Shipping information for the charge.
 * @property null|\\Stripe\\Account|\\Stripe\\BankAccount|\\Stripe\\Card|\\Stripe\\Source $source This is a legacy field that will be removed in the future. It contains the Source, Card, or BankAccount object used for the charge. For details about the payment method used for this charge, refer to <code>payment_method</code> or <code>payment_method_details</code> instead.
 * @property null|string|\\Stripe\\Transfer $source_transfer The transfer ID which created this charge. Only present if the charge came from another Stripe account. <a href="https://stripe.com/docs/connect/destination-charges">See the Connect documentation</a> for details.
 * @property null|string $statement_descriptor For card charges, use <code>statement_descriptor_suffix</code> instead. Otherwise, you can use this value as the complete description of a charge on your customers’ statements. Must contain at least one letter, maximum 22 characters.
 * @property null|string $statement_descriptor_suffix Provides information about the charge that customers see on their statements. Concatenated with the prefix (shortened descriptor) or statement descriptor that’s set on the account to form the complete statement descriptor. Maximum 22 characters for the concatenated descriptor.
 * @property string $status The status of the payment is either <code>succeeded</code>, <code>pending</code>, or <code>failed</code>.
 * @property null|string|\\Stripe\\Transfer $transfer ID of the transfer to the <code>destination</code> account (only applicable if the charge was created using the <code>destination</code> parameter).
 * @property null|\\Stripe\\StripeObject $transfer_data An optional dictionary including the account to automatically transfer to as part of a destination charge. <a href="https://stripe.com/docs/connect/destination-charges">See the Connect documentation</a> for details.
 * @property null|string $transfer_group A string that identifies this transaction as part of a group. See the <a href="https://stripe.com/docs/connect/separate-charges-and-transfers#transfer-options">Connect documentation</a> for details.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 62,
    'endLine' => 278,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Stripe\\ApiResource',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Stripe\\ApiOperations\\NestedResource',
      1 => 'Stripe\\ApiOperations\\Search',
      2 => 'Stripe\\ApiOperations\\Update',
    ),
    'immediateConstants' => 
    array (
      'OBJECT_NAME' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'OBJECT_NAME',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'charge\'',
          'attributes' => 
          array (
            'startLine' => 64,
            'endLine' => 64,
            'startTokenPos' => 27,
            'startFilePos' => 8959,
            'endTokenPos' => 27,
            'endFilePos' => 8966,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 64,
        'endLine' => 64,
        'startColumn' => 5,
        'endColumn' => 33,
      ),
      'STATUS_FAILED' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'STATUS_FAILED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'failed\'',
          'attributes' => 
          array (
            'startLine' => 70,
            'endLine' => 70,
            'startTokenPos' => 51,
            'startFilePos' => 9095,
            'endTokenPos' => 51,
            'endFilePos' => 9102,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 70,
        'endLine' => 70,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'STATUS_PENDING' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'STATUS_PENDING',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'pending\'',
          'attributes' => 
          array (
            'startLine' => 71,
            'endLine' => 71,
            'startTokenPos' => 60,
            'startFilePos' => 9132,
            'endTokenPos' => 60,
            'endFilePos' => 9140,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 71,
        'endLine' => 71,
        'startColumn' => 5,
        'endColumn' => 37,
      ),
      'STATUS_SUCCEEDED' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'STATUS_SUCCEEDED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'succeeded\'',
          'attributes' => 
          array (
            'startLine' => 72,
            'endLine' => 72,
            'startTokenPos' => 69,
            'startFilePos' => 9172,
            'endTokenPos' => 69,
            'endFilePos' => 9182,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 72,
        'endLine' => 72,
        'startColumn' => 5,
        'endColumn' => 41,
      ),
      'DECLINED_AUTHENTICATION_REQUIRED' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_AUTHENTICATION_REQUIRED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'authentication_required\'',
          'attributes' => 
          array (
            'startLine' => 169,
            'endLine' => 169,
            'startTokenPos' => 415,
            'startFilePos' => 12619,
            'endTokenPos' => 415,
            'endFilePos' => 12643,
          ),
        ),
        'docComment' => '/**
 * Possible string representations of decline codes.
 * These strings are applicable to the decline_code property of the \\Stripe\\Exception\\CardException exception.
 *
 * @see https://stripe.com/docs/declines/codes
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 169,
        'endLine' => 169,
        'startColumn' => 5,
        'endColumn' => 71,
      ),
      'DECLINED_APPROVE_WITH_ID' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_APPROVE_WITH_ID',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'approve_with_id\'',
          'attributes' => 
          array (
            'startLine' => 170,
            'endLine' => 170,
            'startTokenPos' => 424,
            'startFilePos' => 12683,
            'endTokenPos' => 424,
            'endFilePos' => 12699,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 170,
        'endLine' => 170,
        'startColumn' => 5,
        'endColumn' => 55,
      ),
      'DECLINED_CALL_ISSUER' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_CALL_ISSUER',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'call_issuer\'',
          'attributes' => 
          array (
            'startLine' => 171,
            'endLine' => 171,
            'startTokenPos' => 433,
            'startFilePos' => 12735,
            'endTokenPos' => 433,
            'endFilePos' => 12747,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 171,
        'endLine' => 171,
        'startColumn' => 5,
        'endColumn' => 47,
      ),
      'DECLINED_CARD_NOT_SUPPORTED' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_CARD_NOT_SUPPORTED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'card_not_supported\'',
          'attributes' => 
          array (
            'startLine' => 172,
            'endLine' => 172,
            'startTokenPos' => 442,
            'startFilePos' => 12790,
            'endTokenPos' => 442,
            'endFilePos' => 12809,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 172,
        'endLine' => 172,
        'startColumn' => 5,
        'endColumn' => 61,
      ),
      'DECLINED_CARD_VELOCITY_EXCEEDED' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_CARD_VELOCITY_EXCEEDED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'card_velocity_exceeded\'',
          'attributes' => 
          array (
            'startLine' => 173,
            'endLine' => 173,
            'startTokenPos' => 451,
            'startFilePos' => 12856,
            'endTokenPos' => 451,
            'endFilePos' => 12879,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 173,
        'endLine' => 173,
        'startColumn' => 5,
        'endColumn' => 69,
      ),
      'DECLINED_CURRENCY_NOT_SUPPORTED' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_CURRENCY_NOT_SUPPORTED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'currency_not_supported\'',
          'attributes' => 
          array (
            'startLine' => 174,
            'endLine' => 174,
            'startTokenPos' => 460,
            'startFilePos' => 12926,
            'endTokenPos' => 460,
            'endFilePos' => 12949,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 174,
        'endLine' => 174,
        'startColumn' => 5,
        'endColumn' => 69,
      ),
      'DECLINED_DO_NOT_HONOR' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_DO_NOT_HONOR',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'do_not_honor\'',
          'attributes' => 
          array (
            'startLine' => 175,
            'endLine' => 175,
            'startTokenPos' => 469,
            'startFilePos' => 12986,
            'endTokenPos' => 469,
            'endFilePos' => 12999,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 175,
        'endLine' => 175,
        'startColumn' => 5,
        'endColumn' => 49,
      ),
      'DECLINED_DO_NOT_TRY_AGAIN' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_DO_NOT_TRY_AGAIN',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'do_not_try_again\'',
          'attributes' => 
          array (
            'startLine' => 176,
            'endLine' => 176,
            'startTokenPos' => 478,
            'startFilePos' => 13040,
            'endTokenPos' => 478,
            'endFilePos' => 13057,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 176,
        'endLine' => 176,
        'startColumn' => 5,
        'endColumn' => 57,
      ),
      'DECLINED_DUPLICATED_TRANSACTION' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_DUPLICATED_TRANSACTION',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'duplicate_transaction\'',
          'attributes' => 
          array (
            'startLine' => 177,
            'endLine' => 177,
            'startTokenPos' => 487,
            'startFilePos' => 13104,
            'endTokenPos' => 487,
            'endFilePos' => 13126,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 177,
        'endLine' => 177,
        'startColumn' => 5,
        'endColumn' => 68,
      ),
      'DECLINED_EXPIRED_CARD' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_EXPIRED_CARD',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'expired_card\'',
          'attributes' => 
          array (
            'startLine' => 178,
            'endLine' => 178,
            'startTokenPos' => 496,
            'startFilePos' => 13163,
            'endTokenPos' => 496,
            'endFilePos' => 13176,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 178,
        'endLine' => 178,
        'startColumn' => 5,
        'endColumn' => 49,
      ),
      'DECLINED_FRAUDULENT' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_FRAUDULENT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'fraudulent\'',
          'attributes' => 
          array (
            'startLine' => 179,
            'endLine' => 179,
            'startTokenPos' => 505,
            'startFilePos' => 13211,
            'endTokenPos' => 505,
            'endFilePos' => 13222,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 179,
        'endLine' => 179,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'DECLINED_GENERIC_DECLINE' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_GENERIC_DECLINE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'generic_decline\'',
          'attributes' => 
          array (
            'startLine' => 180,
            'endLine' => 180,
            'startTokenPos' => 514,
            'startFilePos' => 13262,
            'endTokenPos' => 514,
            'endFilePos' => 13278,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 180,
        'endLine' => 180,
        'startColumn' => 5,
        'endColumn' => 55,
      ),
      'DECLINED_INCORRECT_NUMBER' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_INCORRECT_NUMBER',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'incorrect_number\'',
          'attributes' => 
          array (
            'startLine' => 181,
            'endLine' => 181,
            'startTokenPos' => 523,
            'startFilePos' => 13319,
            'endTokenPos' => 523,
            'endFilePos' => 13336,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 181,
        'endLine' => 181,
        'startColumn' => 5,
        'endColumn' => 57,
      ),
      'DECLINED_INCORRECT_CVC' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_INCORRECT_CVC',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'incorrect_cvc\'',
          'attributes' => 
          array (
            'startLine' => 182,
            'endLine' => 182,
            'startTokenPos' => 532,
            'startFilePos' => 13374,
            'endTokenPos' => 532,
            'endFilePos' => 13388,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 182,
        'endLine' => 182,
        'startColumn' => 5,
        'endColumn' => 51,
      ),
      'DECLINED_INCORRECT_PIN' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_INCORRECT_PIN',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'incorrect_pin\'',
          'attributes' => 
          array (
            'startLine' => 183,
            'endLine' => 183,
            'startTokenPos' => 541,
            'startFilePos' => 13426,
            'endTokenPos' => 541,
            'endFilePos' => 13440,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 183,
        'endLine' => 183,
        'startColumn' => 5,
        'endColumn' => 51,
      ),
      'DECLINED_INCORRECT_ZIP' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_INCORRECT_ZIP',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'incorrect_zip\'',
          'attributes' => 
          array (
            'startLine' => 184,
            'endLine' => 184,
            'startTokenPos' => 550,
            'startFilePos' => 13478,
            'endTokenPos' => 550,
            'endFilePos' => 13492,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 184,
        'endLine' => 184,
        'startColumn' => 5,
        'endColumn' => 51,
      ),
      'DECLINED_INSUFFICIENT_FUNDS' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_INSUFFICIENT_FUNDS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'insufficient_funds\'',
          'attributes' => 
          array (
            'startLine' => 185,
            'endLine' => 185,
            'startTokenPos' => 559,
            'startFilePos' => 13535,
            'endTokenPos' => 559,
            'endFilePos' => 13554,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 185,
        'endLine' => 185,
        'startColumn' => 5,
        'endColumn' => 61,
      ),
      'DECLINED_INVALID_ACCOUNT' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_INVALID_ACCOUNT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'invalid_account\'',
          'attributes' => 
          array (
            'startLine' => 186,
            'endLine' => 186,
            'startTokenPos' => 568,
            'startFilePos' => 13594,
            'endTokenPos' => 568,
            'endFilePos' => 13610,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 186,
        'endLine' => 186,
        'startColumn' => 5,
        'endColumn' => 55,
      ),
      'DECLINED_INVALID_AMOUNT' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_INVALID_AMOUNT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'invalid_amount\'',
          'attributes' => 
          array (
            'startLine' => 187,
            'endLine' => 187,
            'startTokenPos' => 577,
            'startFilePos' => 13649,
            'endTokenPos' => 577,
            'endFilePos' => 13664,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 187,
        'endLine' => 187,
        'startColumn' => 5,
        'endColumn' => 53,
      ),
      'DECLINED_INVALID_CVC' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_INVALID_CVC',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'invalid_cvc\'',
          'attributes' => 
          array (
            'startLine' => 188,
            'endLine' => 188,
            'startTokenPos' => 586,
            'startFilePos' => 13700,
            'endTokenPos' => 586,
            'endFilePos' => 13712,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 188,
        'endLine' => 188,
        'startColumn' => 5,
        'endColumn' => 47,
      ),
      'DECLINED_INVALID_EXPIRY_YEAR' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_INVALID_EXPIRY_YEAR',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'invalid_expiry_year\'',
          'attributes' => 
          array (
            'startLine' => 189,
            'endLine' => 189,
            'startTokenPos' => 595,
            'startFilePos' => 13756,
            'endTokenPos' => 595,
            'endFilePos' => 13776,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 189,
        'endLine' => 189,
        'startColumn' => 5,
        'endColumn' => 63,
      ),
      'DECLINED_INVALID_NUMBER' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_INVALID_NUMBER',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'invalid_number\'',
          'attributes' => 
          array (
            'startLine' => 190,
            'endLine' => 190,
            'startTokenPos' => 604,
            'startFilePos' => 13815,
            'endTokenPos' => 604,
            'endFilePos' => 13830,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 190,
        'endLine' => 190,
        'startColumn' => 5,
        'endColumn' => 53,
      ),
      'DECLINED_INVALID_PIN' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_INVALID_PIN',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'invalid_pin\'',
          'attributes' => 
          array (
            'startLine' => 191,
            'endLine' => 191,
            'startTokenPos' => 613,
            'startFilePos' => 13866,
            'endTokenPos' => 613,
            'endFilePos' => 13878,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 191,
        'endLine' => 191,
        'startColumn' => 5,
        'endColumn' => 47,
      ),
      'DECLINED_ISSUER_NOT_AVAILABLE' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_ISSUER_NOT_AVAILABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'issuer_not_available\'',
          'attributes' => 
          array (
            'startLine' => 192,
            'endLine' => 192,
            'startTokenPos' => 622,
            'startFilePos' => 13923,
            'endTokenPos' => 622,
            'endFilePos' => 13944,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 192,
        'endLine' => 192,
        'startColumn' => 5,
        'endColumn' => 65,
      ),
      'DECLINED_LOST_CARD' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_LOST_CARD',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'lost_card\'',
          'attributes' => 
          array (
            'startLine' => 193,
            'endLine' => 193,
            'startTokenPos' => 631,
            'startFilePos' => 13978,
            'endTokenPos' => 631,
            'endFilePos' => 13988,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 193,
        'endLine' => 193,
        'startColumn' => 5,
        'endColumn' => 43,
      ),
      'DECLINED_MERCHANT_BLACKLIST' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_MERCHANT_BLACKLIST',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'merchant_blacklist\'',
          'attributes' => 
          array (
            'startLine' => 194,
            'endLine' => 194,
            'startTokenPos' => 640,
            'startFilePos' => 14031,
            'endTokenPos' => 640,
            'endFilePos' => 14050,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 194,
        'endLine' => 194,
        'startColumn' => 5,
        'endColumn' => 61,
      ),
      'DECLINED_NEW_ACCOUNT_INFORMATION_AVAILABLE' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_NEW_ACCOUNT_INFORMATION_AVAILABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'new_account_information_available\'',
          'attributes' => 
          array (
            'startLine' => 195,
            'endLine' => 195,
            'startTokenPos' => 649,
            'startFilePos' => 14108,
            'endTokenPos' => 649,
            'endFilePos' => 14142,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 195,
        'endLine' => 195,
        'startColumn' => 5,
        'endColumn' => 91,
      ),
      'DECLINED_NO_ACTION_TAKEN' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_NO_ACTION_TAKEN',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'no_action_taken\'',
          'attributes' => 
          array (
            'startLine' => 196,
            'endLine' => 196,
            'startTokenPos' => 658,
            'startFilePos' => 14182,
            'endTokenPos' => 658,
            'endFilePos' => 14198,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 196,
        'endLine' => 196,
        'startColumn' => 5,
        'endColumn' => 55,
      ),
      'DECLINED_NOT_PERMITTED' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_NOT_PERMITTED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'not_permitted\'',
          'attributes' => 
          array (
            'startLine' => 197,
            'endLine' => 197,
            'startTokenPos' => 667,
            'startFilePos' => 14236,
            'endTokenPos' => 667,
            'endFilePos' => 14250,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 197,
        'endLine' => 197,
        'startColumn' => 5,
        'endColumn' => 51,
      ),
      'DECLINED_OFFLINE_PIN_REQUIRED' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_OFFLINE_PIN_REQUIRED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'offline_pin_required\'',
          'attributes' => 
          array (
            'startLine' => 198,
            'endLine' => 198,
            'startTokenPos' => 676,
            'startFilePos' => 14295,
            'endTokenPos' => 676,
            'endFilePos' => 14316,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 198,
        'endLine' => 198,
        'startColumn' => 5,
        'endColumn' => 65,
      ),
      'DECLINED_ONLINE_OR_OFFLINE_PIN_REQUIRED' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_ONLINE_OR_OFFLINE_PIN_REQUIRED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'online_or_offline_pin_required\'',
          'attributes' => 
          array (
            'startLine' => 199,
            'endLine' => 199,
            'startTokenPos' => 685,
            'startFilePos' => 14371,
            'endTokenPos' => 685,
            'endFilePos' => 14402,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 199,
        'endLine' => 199,
        'startColumn' => 5,
        'endColumn' => 85,
      ),
      'DECLINED_PICKUP_CARD' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_PICKUP_CARD',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'pickup_card\'',
          'attributes' => 
          array (
            'startLine' => 200,
            'endLine' => 200,
            'startTokenPos' => 694,
            'startFilePos' => 14438,
            'endTokenPos' => 694,
            'endFilePos' => 14450,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 200,
        'endLine' => 200,
        'startColumn' => 5,
        'endColumn' => 47,
      ),
      'DECLINED_PIN_TRY_EXCEEDED' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_PIN_TRY_EXCEEDED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'pin_try_exceeded\'',
          'attributes' => 
          array (
            'startLine' => 201,
            'endLine' => 201,
            'startTokenPos' => 703,
            'startFilePos' => 14491,
            'endTokenPos' => 703,
            'endFilePos' => 14508,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 201,
        'endLine' => 201,
        'startColumn' => 5,
        'endColumn' => 57,
      ),
      'DECLINED_PROCESSING_ERROR' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_PROCESSING_ERROR',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'processing_error\'',
          'attributes' => 
          array (
            'startLine' => 202,
            'endLine' => 202,
            'startTokenPos' => 712,
            'startFilePos' => 14549,
            'endTokenPos' => 712,
            'endFilePos' => 14566,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 202,
        'endLine' => 202,
        'startColumn' => 5,
        'endColumn' => 57,
      ),
      'DECLINED_REENTER_TRANSACTION' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_REENTER_TRANSACTION',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'reenter_transaction\'',
          'attributes' => 
          array (
            'startLine' => 203,
            'endLine' => 203,
            'startTokenPos' => 721,
            'startFilePos' => 14610,
            'endTokenPos' => 721,
            'endFilePos' => 14630,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 203,
        'endLine' => 203,
        'startColumn' => 5,
        'endColumn' => 63,
      ),
      'DECLINED_RESTRICTED_CARD' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_RESTRICTED_CARD',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'restricted_card\'',
          'attributes' => 
          array (
            'startLine' => 204,
            'endLine' => 204,
            'startTokenPos' => 730,
            'startFilePos' => 14670,
            'endTokenPos' => 730,
            'endFilePos' => 14686,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 204,
        'endLine' => 204,
        'startColumn' => 5,
        'endColumn' => 55,
      ),
      'DECLINED_REVOCATION_OF_ALL_AUTHORIZATIONS' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_REVOCATION_OF_ALL_AUTHORIZATIONS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'revocation_of_all_authorizations\'',
          'attributes' => 
          array (
            'startLine' => 205,
            'endLine' => 205,
            'startTokenPos' => 739,
            'startFilePos' => 14743,
            'endTokenPos' => 739,
            'endFilePos' => 14776,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 205,
        'endLine' => 205,
        'startColumn' => 5,
        'endColumn' => 89,
      ),
      'DECLINED_REVOCATION_OF_AUTHORIZATION' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_REVOCATION_OF_AUTHORIZATION',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'revocation_of_authorization\'',
          'attributes' => 
          array (
            'startLine' => 206,
            'endLine' => 206,
            'startTokenPos' => 748,
            'startFilePos' => 14828,
            'endTokenPos' => 748,
            'endFilePos' => 14856,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 206,
        'endLine' => 206,
        'startColumn' => 5,
        'endColumn' => 79,
      ),
      'DECLINED_SECURITY_VIOLATION' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_SECURITY_VIOLATION',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'security_violation\'',
          'attributes' => 
          array (
            'startLine' => 207,
            'endLine' => 207,
            'startTokenPos' => 757,
            'startFilePos' => 14899,
            'endTokenPos' => 757,
            'endFilePos' => 14918,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 207,
        'endLine' => 207,
        'startColumn' => 5,
        'endColumn' => 61,
      ),
      'DECLINED_SERVICE_NOT_ALLOWED' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_SERVICE_NOT_ALLOWED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'service_not_allowed\'',
          'attributes' => 
          array (
            'startLine' => 208,
            'endLine' => 208,
            'startTokenPos' => 766,
            'startFilePos' => 14962,
            'endTokenPos' => 766,
            'endFilePos' => 14982,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 208,
        'endLine' => 208,
        'startColumn' => 5,
        'endColumn' => 63,
      ),
      'DECLINED_STOLEN_CARD' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_STOLEN_CARD',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'stolen_card\'',
          'attributes' => 
          array (
            'startLine' => 209,
            'endLine' => 209,
            'startTokenPos' => 775,
            'startFilePos' => 15018,
            'endTokenPos' => 775,
            'endFilePos' => 15030,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 209,
        'endLine' => 209,
        'startColumn' => 5,
        'endColumn' => 47,
      ),
      'DECLINED_STOP_PAYMENT_ORDER' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_STOP_PAYMENT_ORDER',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'stop_payment_order\'',
          'attributes' => 
          array (
            'startLine' => 210,
            'endLine' => 210,
            'startTokenPos' => 784,
            'startFilePos' => 15073,
            'endTokenPos' => 784,
            'endFilePos' => 15092,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 210,
        'endLine' => 210,
        'startColumn' => 5,
        'endColumn' => 61,
      ),
      'DECLINED_TESTMODE_DECLINE' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_TESTMODE_DECLINE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'testmode_decline\'',
          'attributes' => 
          array (
            'startLine' => 211,
            'endLine' => 211,
            'startTokenPos' => 793,
            'startFilePos' => 15133,
            'endTokenPos' => 793,
            'endFilePos' => 15150,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 211,
        'endLine' => 211,
        'startColumn' => 5,
        'endColumn' => 57,
      ),
      'DECLINED_TRANSACTION_NOT_ALLOWED' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_TRANSACTION_NOT_ALLOWED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'transaction_not_allowed\'',
          'attributes' => 
          array (
            'startLine' => 212,
            'endLine' => 212,
            'startTokenPos' => 802,
            'startFilePos' => 15198,
            'endTokenPos' => 802,
            'endFilePos' => 15222,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 212,
        'endLine' => 212,
        'startColumn' => 5,
        'endColumn' => 71,
      ),
      'DECLINED_TRY_AGAIN_LATER' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_TRY_AGAIN_LATER',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'try_again_later\'',
          'attributes' => 
          array (
            'startLine' => 213,
            'endLine' => 213,
            'startTokenPos' => 811,
            'startFilePos' => 15262,
            'endTokenPos' => 811,
            'endFilePos' => 15278,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 213,
        'endLine' => 213,
        'startColumn' => 5,
        'endColumn' => 55,
      ),
      'DECLINED_WITHDRAWAL_COUNT_LIMIT_EXCEEDED' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'DECLINED_WITHDRAWAL_COUNT_LIMIT_EXCEEDED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'withdrawal_count_limit_exceeded\'',
          'attributes' => 
          array (
            'startLine' => 214,
            'endLine' => 214,
            'startTokenPos' => 820,
            'startFilePos' => 15334,
            'endTokenPos' => 820,
            'endFilePos' => 15366,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 214,
        'endLine' => 214,
        'startColumn' => 5,
        'endColumn' => 87,
      ),
      'PATH_REFUNDS' => 
      array (
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'name' => 'PATH_REFUNDS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'/refunds\'',
          'attributes' => 
          array (
            'startLine' => 248,
            'endLine' => 248,
            'startTokenPos' => 969,
            'startFilePos' => 16342,
            'endTokenPos' => 969,
            'endFilePos' => 16351,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 248,
        'endLine' => 248,
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
                'startLine' => 87,
                'endLine' => 87,
                'startTokenPos' => 86,
                'startFilePos' => 9732,
                'endTokenPos' => 86,
                'endFilePos' => 9735,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 87,
            'endLine' => 87,
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
                'startLine' => 87,
                'endLine' => 87,
                'startTokenPos' => 93,
                'startFilePos' => 9749,
                'endTokenPos' => 93,
                'endFilePos' => 9752,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 87,
            'endLine' => 87,
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
 * This method is no longer recommended—use the <a
 * href="/docs/api/payment_intents">Payment Intents API</a> to initiate a new
 * payment instead. Confirmation of the PaymentIntent creates the
 * <code>Charge</code> object used to request payment.
 *
 * @param null|array $params
 * @param null|array|string $options
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Charge the created resource
 */',
        'startLine' => 87,
        'endLine' => 97,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'currentClassName' => 'Stripe\\Charge',
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
                'startLine' => 110,
                'endLine' => 110,
                'startTokenPos' => 190,
                'startFilePos' => 10523,
                'endTokenPos' => 190,
                'endFilePos' => 10526,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 110,
            'endLine' => 110,
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
                'startLine' => 110,
                'endLine' => 110,
                'startTokenPos' => 197,
                'startFilePos' => 10537,
                'endTokenPos' => 197,
                'endFilePos' => 10540,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 110,
            'endLine' => 110,
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
 * Returns a list of charges you’ve previously created. The charges are returned in
 * sorted order, with the most recent charges appearing first.
 *
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Collection<\\Stripe\\Charge> of ApiResources
 */',
        'startLine' => 110,
        'endLine' => 115,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'currentClassName' => 'Stripe\\Charge',
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
            'startLine' => 130,
            'endLine' => 130,
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
                'startLine' => 130,
                'endLine' => 130,
                'startTokenPos' => 253,
                'startFilePos' => 11321,
                'endTokenPos' => 253,
                'endFilePos' => 11324,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 130,
            'endLine' => 130,
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
 * Retrieves the details of a charge that has previously been created. Supply the
 * unique charge ID that was returned from your previous request, and Stripe will
 * return the corresponding charge information. The same information is returned
 * when creating or refunding the charge.
 *
 * @param array|string $id the ID of the API resource to retrieve, or an options array containing an `id` key
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Charge
 */',
        'startLine' => 130,
        'endLine' => 137,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'currentClassName' => 'Stripe\\Charge',
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
            'startLine' => 151,
            'endLine' => 151,
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
                'startLine' => 151,
                'endLine' => 151,
                'startTokenPos' => 316,
                'startFilePos' => 11981,
                'endTokenPos' => 316,
                'endFilePos' => 11984,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 151,
            'endLine' => 151,
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
                'startLine' => 151,
                'endLine' => 151,
                'startTokenPos' => 323,
                'startFilePos' => 11995,
                'endTokenPos' => 323,
                'endFilePos' => 11998,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 151,
            'endLine' => 151,
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
 * Updates the specified charge by setting the values of the parameters passed. Any
 * parameters not provided will be left unchanged.
 *
 * @param string $id the ID of the resource to update
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Charge the updated resource
 */',
        'startLine' => 151,
        'endLine' => 161,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'currentClassName' => 'Stripe\\Charge',
        'aliasName' => NULL,
      ),
      'capture' => 
      array (
        'name' => 'capture',
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
                'startLine' => 224,
                'endLine' => 224,
                'startTokenPos' => 835,
                'startFilePos' => 15631,
                'endTokenPos' => 835,
                'endFilePos' => 15634,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 224,
            'endLine' => 224,
            'startColumn' => 29,
            'endColumn' => 42,
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
                'startLine' => 224,
                'endLine' => 224,
                'startTokenPos' => 842,
                'startFilePos' => 15645,
                'endTokenPos' => 842,
                'endFilePos' => 15648,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 224,
            'endLine' => 224,
            'startColumn' => 45,
            'endColumn' => 56,
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
 * @return \\Stripe\\Charge the captured charge
 */',
        'startLine' => 224,
        'endLine' => 231,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'currentClassName' => 'Stripe\\Charge',
        'aliasName' => NULL,
      ),
      'search' => 
      array (
        'name' => 'search',
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
                'startLine' => 241,
                'endLine' => 241,
                'startTokenPos' => 921,
                'startFilePos' => 16158,
                'endTokenPos' => 921,
                'endFilePos' => 16161,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 241,
            'endLine' => 241,
            'startColumn' => 35,
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
                'startLine' => 241,
                'endLine' => 241,
                'startTokenPos' => 928,
                'startFilePos' => 16172,
                'endTokenPos' => 928,
                'endFilePos' => 16175,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 241,
            'endLine' => 241,
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
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\SearchResult<\\Stripe\\Charge> the charge search results
 */',
        'startLine' => 241,
        'endLine' => 246,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'currentClassName' => 'Stripe\\Charge',
        'aliasName' => NULL,
      ),
      'allRefunds' => 
      array (
        'name' => 'allRefunds',
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
            'startLine' => 259,
            'endLine' => 259,
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
                'startLine' => 259,
                'endLine' => 259,
                'startTokenPos' => 989,
                'startFilePos' => 16730,
                'endTokenPos' => 989,
                'endFilePos' => 16733,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 259,
            'endLine' => 259,
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
                'startLine' => 259,
                'endLine' => 259,
                'startTokenPos' => 996,
                'startFilePos' => 16744,
                'endTokenPos' => 996,
                'endFilePos' => 16747,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 259,
            'endLine' => 259,
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
 * @param string $id the ID of the charge on which to retrieve the refunds
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Collection<\\Stripe\\Refund> the list of refunds
 */',
        'startLine' => 259,
        'endLine' => 262,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'currentClassName' => 'Stripe\\Charge',
        'aliasName' => NULL,
      ),
      'retrieveRefund' => 
      array (
        'name' => 'retrieveRefund',
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
            'startLine' => 274,
            'endLine' => 274,
            'startColumn' => 43,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'refundId' => 
          array (
            'name' => 'refundId',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 274,
            'endLine' => 274,
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
                'startLine' => 274,
                'endLine' => 274,
                'startTokenPos' => 1044,
                'startFilePos' => 17257,
                'endTokenPos' => 1044,
                'endFilePos' => 17260,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 274,
            'endLine' => 274,
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
                'startLine' => 274,
                'endLine' => 274,
                'startTokenPos' => 1051,
                'startFilePos' => 17271,
                'endTokenPos' => 1051,
                'endFilePos' => 17274,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 274,
            'endLine' => 274,
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
 * @param string $id the ID of the charge to which the refund belongs
 * @param string $refundId the ID of the refund to retrieve
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Refund
 */',
        'startLine' => 274,
        'endLine' => 277,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Charge',
        'implementingClassName' => 'Stripe\\Charge',
        'currentClassName' => 'Stripe\\Charge',
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