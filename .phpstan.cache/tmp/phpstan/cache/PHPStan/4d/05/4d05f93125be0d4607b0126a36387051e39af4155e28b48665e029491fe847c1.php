<?php declare(strict_types = 1);

// osfsl-/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/Source.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Stripe\Source
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-110cd3398528232c633cf73d2b228e7ab54fe26740afbf12acb7e95f0ee70c87-8.4.24-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Stripe\\Source',
        'filename' => '/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/Source.php',
      ),
    ),
    'namespace' => 'Stripe',
    'name' => 'Stripe\\Source',
    'shortName' => 'Source',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * <code>Source</code> objects allow you to accept a variety of payment methods. They
 * represent a customer\'s payment instrument, and can be used with the Stripe API
 * just like a <code>Card</code> object: once chargeable, they can be charged, or can be
 * attached to customers.
 *
 * Stripe doesn\'t recommend using the deprecated <a href="https://stripe.com/docs/api/sources">Sources API</a>.
 * We recommend that you adopt the <a href="https://stripe.com/docs/api/payment_methods">PaymentMethods API</a>.
 * This newer API provides access to our latest features and payment method types.
 *
 * Related guides: <a href="https://stripe.com/docs/sources">Sources API</a> and <a href="https://stripe.com/docs/sources/customers">Sources &amp; Customers</a>.
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object\'s type. Objects of the same type share the same value.
 * @property null|\\Stripe\\StripeObject $ach_credit_transfer
 * @property null|\\Stripe\\StripeObject $ach_debit
 * @property null|\\Stripe\\StripeObject $acss_debit
 * @property null|\\Stripe\\StripeObject $alipay
 * @property null|int $amount A positive integer in the smallest currency unit (that is, 100 cents for $1.00, or 1 for ¥1, Japanese Yen being a zero-decimal currency) representing the total amount associated with the source. This is the amount for which the source will be chargeable once ready. Required for <code>single_use</code> sources.
 * @property null|\\Stripe\\StripeObject $au_becs_debit
 * @property null|\\Stripe\\StripeObject $bancontact
 * @property null|\\Stripe\\StripeObject $card
 * @property null|\\Stripe\\StripeObject $card_present
 * @property string $client_secret The client secret of the source. Used for client-side retrieval using a publishable key.
 * @property null|\\Stripe\\StripeObject $code_verification
 * @property int $created Time at which the object was created. Measured in seconds since the Unix epoch.
 * @property null|string $currency Three-letter <a href="https://stripe.com/docs/currencies">ISO code for the currency</a> associated with the source. This is the currency for which the source will be chargeable once ready. Required for <code>single_use</code> sources.
 * @property null|string $customer The ID of the customer to which this source is attached. This will not be present when the source has not been attached to a customer.
 * @property null|\\Stripe\\StripeObject $eps
 * @property string $flow The authentication <code>flow</code> of the source. <code>flow</code> is one of <code>redirect</code>, <code>receiver</code>, <code>code_verification</code>, <code>none</code>.
 * @property null|\\Stripe\\StripeObject $giropay
 * @property null|\\Stripe\\StripeObject $ideal
 * @property null|\\Stripe\\StripeObject $klarna
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property null|\\Stripe\\StripeObject $metadata Set of <a href="https://stripe.com/docs/api/metadata">key-value pairs</a> that you can attach to an object. This can be useful for storing additional information about the object in a structured format.
 * @property null|\\Stripe\\StripeObject $multibanco
 * @property null|\\Stripe\\StripeObject $owner Information about the owner of the payment instrument that may be used or required by particular source types.
 * @property null|\\Stripe\\StripeObject $p24
 * @property null|\\Stripe\\StripeObject $receiver
 * @property null|\\Stripe\\StripeObject $redirect
 * @property null|\\Stripe\\StripeObject $sepa_credit_transfer
 * @property null|\\Stripe\\StripeObject $sepa_debit
 * @property null|\\Stripe\\StripeObject $sofort
 * @property null|\\Stripe\\StripeObject $source_order
 * @property null|string $statement_descriptor Extra information about a source. This will appear on your customer\'s statement every time you charge the source.
 * @property string $status The status of the source, one of <code>canceled</code>, <code>chargeable</code>, <code>consumed</code>, <code>failed</code>, or <code>pending</code>. Only <code>chargeable</code> sources can be used to create a charge.
 * @property null|\\Stripe\\StripeObject $three_d_secure
 * @property string $type The <code>type</code> of the source. The <code>type</code> is a payment method, one of <code>ach_credit_transfer</code>, <code>ach_debit</code>, <code>alipay</code>, <code>bancontact</code>, <code>card</code>, <code>card_present</code>, <code>eps</code>, <code>giropay</code>, <code>ideal</code>, <code>multibanco</code>, <code>klarna</code>, <code>p24</code>, <code>sepa_debit</code>, <code>sofort</code>, <code>three_d_secure</code>, or <code>wechat</code>. An additional hash is included on the source with a name matching this value. It contains additional information specific to the <a href="https://stripe.com/docs/sources">payment method</a> used.
 * @property null|string $usage Either <code>reusable</code> or <code>single_use</code>. Whether this source should be reusable or not. Some source types may or may not be reusable by construction, while others may leave the option at creation. If an incompatible value is passed, an error will be returned.
 * @property null|\\Stripe\\StripeObject $wechat
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 58,
    'endLine' => 246,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Stripe\\ApiResource',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Stripe\\ApiOperations\\Update',
      1 => 'Stripe\\ApiOperations\\NestedResource',
    ),
    'immediateConstants' => 
    array (
      'OBJECT_NAME' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'OBJECT_NAME',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'source\'',
          'attributes' => 
          array (
            'startLine' => 60,
            'endLine' => 60,
            'startTokenPos' => 27,
            'startFilePos' => 5413,
            'endTokenPos' => 27,
            'endFilePos' => 5420,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 60,
        'endLine' => 60,
        'startColumn' => 5,
        'endColumn' => 33,
      ),
      'FLOW_CODE_VERIFICATION' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'FLOW_CODE_VERIFICATION',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'code_verification\'',
          'attributes' => 
          array (
            'startLine' => 64,
            'endLine' => 64,
            'startTokenPos' => 41,
            'startFilePos' => 5490,
            'endTokenPos' => 41,
            'endFilePos' => 5508,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 64,
        'endLine' => 64,
        'startColumn' => 5,
        'endColumn' => 55,
      ),
      'FLOW_NONE' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'FLOW_NONE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'none\'',
          'attributes' => 
          array (
            'startLine' => 65,
            'endLine' => 65,
            'startTokenPos' => 50,
            'startFilePos' => 5533,
            'endTokenPos' => 50,
            'endFilePos' => 5538,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 65,
        'endLine' => 65,
        'startColumn' => 5,
        'endColumn' => 29,
      ),
      'FLOW_RECEIVER' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'FLOW_RECEIVER',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'receiver\'',
          'attributes' => 
          array (
            'startLine' => 66,
            'endLine' => 66,
            'startTokenPos' => 59,
            'startFilePos' => 5567,
            'endTokenPos' => 59,
            'endFilePos' => 5576,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 66,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 37,
      ),
      'FLOW_REDIRECT' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'FLOW_REDIRECT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'redirect\'',
          'attributes' => 
          array (
            'startLine' => 67,
            'endLine' => 67,
            'startTokenPos' => 68,
            'startFilePos' => 5605,
            'endTokenPos' => 68,
            'endFilePos' => 5614,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 67,
        'endLine' => 67,
        'startColumn' => 5,
        'endColumn' => 37,
      ),
      'STATUS_CANCELED' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'STATUS_CANCELED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'canceled\'',
          'attributes' => 
          array (
            'startLine' => 69,
            'endLine' => 69,
            'startTokenPos' => 77,
            'startFilePos' => 5646,
            'endTokenPos' => 77,
            'endFilePos' => 5655,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 69,
        'endLine' => 69,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATUS_CHARGEABLE' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'STATUS_CHARGEABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'chargeable\'',
          'attributes' => 
          array (
            'startLine' => 70,
            'endLine' => 70,
            'startTokenPos' => 86,
            'startFilePos' => 5688,
            'endTokenPos' => 86,
            'endFilePos' => 5699,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 70,
        'endLine' => 70,
        'startColumn' => 5,
        'endColumn' => 43,
      ),
      'STATUS_CONSUMED' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'STATUS_CONSUMED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'consumed\'',
          'attributes' => 
          array (
            'startLine' => 71,
            'endLine' => 71,
            'startTokenPos' => 95,
            'startFilePos' => 5730,
            'endTokenPos' => 95,
            'endFilePos' => 5739,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 71,
        'endLine' => 71,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATUS_FAILED' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'STATUS_FAILED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'failed\'',
          'attributes' => 
          array (
            'startLine' => 72,
            'endLine' => 72,
            'startTokenPos' => 104,
            'startFilePos' => 5768,
            'endTokenPos' => 104,
            'endFilePos' => 5775,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 72,
        'endLine' => 72,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'STATUS_PENDING' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'STATUS_PENDING',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'pending\'',
          'attributes' => 
          array (
            'startLine' => 73,
            'endLine' => 73,
            'startTokenPos' => 113,
            'startFilePos' => 5805,
            'endTokenPos' => 113,
            'endFilePos' => 5813,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 73,
        'endLine' => 73,
        'startColumn' => 5,
        'endColumn' => 37,
      ),
      'TYPE_ACH_CREDIT_TRANSFER' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'TYPE_ACH_CREDIT_TRANSFER',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'ach_credit_transfer\'',
          'attributes' => 
          array (
            'startLine' => 75,
            'endLine' => 75,
            'startTokenPos' => 122,
            'startFilePos' => 5854,
            'endTokenPos' => 122,
            'endFilePos' => 5874,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 75,
        'endLine' => 75,
        'startColumn' => 5,
        'endColumn' => 59,
      ),
      'TYPE_ACH_DEBIT' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'TYPE_ACH_DEBIT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'ach_debit\'',
          'attributes' => 
          array (
            'startLine' => 76,
            'endLine' => 76,
            'startTokenPos' => 131,
            'startFilePos' => 5904,
            'endTokenPos' => 131,
            'endFilePos' => 5914,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 76,
        'endLine' => 76,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'TYPE_ACSS_DEBIT' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'TYPE_ACSS_DEBIT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'acss_debit\'',
          'attributes' => 
          array (
            'startLine' => 77,
            'endLine' => 77,
            'startTokenPos' => 140,
            'startFilePos' => 5945,
            'endTokenPos' => 140,
            'endFilePos' => 5956,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 77,
        'endLine' => 77,
        'startColumn' => 5,
        'endColumn' => 41,
      ),
      'TYPE_ALIPAY' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'TYPE_ALIPAY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'alipay\'',
          'attributes' => 
          array (
            'startLine' => 78,
            'endLine' => 78,
            'startTokenPos' => 149,
            'startFilePos' => 5983,
            'endTokenPos' => 149,
            'endFilePos' => 5990,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 78,
        'endLine' => 78,
        'startColumn' => 5,
        'endColumn' => 33,
      ),
      'TYPE_AU_BECS_DEBIT' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'TYPE_AU_BECS_DEBIT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'au_becs_debit\'',
          'attributes' => 
          array (
            'startLine' => 79,
            'endLine' => 79,
            'startTokenPos' => 158,
            'startFilePos' => 6024,
            'endTokenPos' => 158,
            'endFilePos' => 6038,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 79,
        'endLine' => 79,
        'startColumn' => 5,
        'endColumn' => 47,
      ),
      'TYPE_BANCONTACT' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'TYPE_BANCONTACT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'bancontact\'',
          'attributes' => 
          array (
            'startLine' => 80,
            'endLine' => 80,
            'startTokenPos' => 167,
            'startFilePos' => 6069,
            'endTokenPos' => 167,
            'endFilePos' => 6080,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 80,
        'endLine' => 80,
        'startColumn' => 5,
        'endColumn' => 41,
      ),
      'TYPE_CARD' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'TYPE_CARD',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'card\'',
          'attributes' => 
          array (
            'startLine' => 81,
            'endLine' => 81,
            'startTokenPos' => 176,
            'startFilePos' => 6105,
            'endTokenPos' => 176,
            'endFilePos' => 6110,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 81,
        'endLine' => 81,
        'startColumn' => 5,
        'endColumn' => 29,
      ),
      'TYPE_CARD_PRESENT' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'TYPE_CARD_PRESENT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'card_present\'',
          'attributes' => 
          array (
            'startLine' => 82,
            'endLine' => 82,
            'startTokenPos' => 185,
            'startFilePos' => 6143,
            'endTokenPos' => 185,
            'endFilePos' => 6156,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 82,
        'endLine' => 82,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'TYPE_EPS' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'TYPE_EPS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'eps\'',
          'attributes' => 
          array (
            'startLine' => 83,
            'endLine' => 83,
            'startTokenPos' => 194,
            'startFilePos' => 6180,
            'endTokenPos' => 194,
            'endFilePos' => 6184,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 83,
        'endLine' => 83,
        'startColumn' => 5,
        'endColumn' => 27,
      ),
      'TYPE_GIROPAY' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'TYPE_GIROPAY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'giropay\'',
          'attributes' => 
          array (
            'startLine' => 84,
            'endLine' => 84,
            'startTokenPos' => 203,
            'startFilePos' => 6212,
            'endTokenPos' => 203,
            'endFilePos' => 6220,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 84,
        'endLine' => 84,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'TYPE_IDEAL' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'TYPE_IDEAL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'ideal\'',
          'attributes' => 
          array (
            'startLine' => 85,
            'endLine' => 85,
            'startTokenPos' => 212,
            'startFilePos' => 6246,
            'endTokenPos' => 212,
            'endFilePos' => 6252,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 85,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 31,
      ),
      'TYPE_KLARNA' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'TYPE_KLARNA',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'klarna\'',
          'attributes' => 
          array (
            'startLine' => 86,
            'endLine' => 86,
            'startTokenPos' => 221,
            'startFilePos' => 6279,
            'endTokenPos' => 221,
            'endFilePos' => 6286,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 86,
        'endLine' => 86,
        'startColumn' => 5,
        'endColumn' => 33,
      ),
      'TYPE_MULTIBANCO' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'TYPE_MULTIBANCO',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'multibanco\'',
          'attributes' => 
          array (
            'startLine' => 87,
            'endLine' => 87,
            'startTokenPos' => 230,
            'startFilePos' => 6317,
            'endTokenPos' => 230,
            'endFilePos' => 6328,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 87,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 41,
      ),
      'TYPE_P24' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'TYPE_P24',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'p24\'',
          'attributes' => 
          array (
            'startLine' => 88,
            'endLine' => 88,
            'startTokenPos' => 239,
            'startFilePos' => 6352,
            'endTokenPos' => 239,
            'endFilePos' => 6356,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 88,
        'endLine' => 88,
        'startColumn' => 5,
        'endColumn' => 27,
      ),
      'TYPE_SEPA_CREDIT_TRANSFER' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'TYPE_SEPA_CREDIT_TRANSFER',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'sepa_credit_transfer\'',
          'attributes' => 
          array (
            'startLine' => 89,
            'endLine' => 89,
            'startTokenPos' => 248,
            'startFilePos' => 6397,
            'endTokenPos' => 248,
            'endFilePos' => 6418,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 89,
        'endLine' => 89,
        'startColumn' => 5,
        'endColumn' => 61,
      ),
      'TYPE_SEPA_DEBIT' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'TYPE_SEPA_DEBIT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'sepa_debit\'',
          'attributes' => 
          array (
            'startLine' => 90,
            'endLine' => 90,
            'startTokenPos' => 257,
            'startFilePos' => 6449,
            'endTokenPos' => 257,
            'endFilePos' => 6460,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 90,
        'endLine' => 90,
        'startColumn' => 5,
        'endColumn' => 41,
      ),
      'TYPE_SOFORT' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'TYPE_SOFORT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'sofort\'',
          'attributes' => 
          array (
            'startLine' => 91,
            'endLine' => 91,
            'startTokenPos' => 266,
            'startFilePos' => 6487,
            'endTokenPos' => 266,
            'endFilePos' => 6494,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 91,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 33,
      ),
      'TYPE_THREE_D_SECURE' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'TYPE_THREE_D_SECURE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'three_d_secure\'',
          'attributes' => 
          array (
            'startLine' => 92,
            'endLine' => 92,
            'startTokenPos' => 275,
            'startFilePos' => 6529,
            'endTokenPos' => 275,
            'endFilePos' => 6544,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 92,
        'endLine' => 92,
        'startColumn' => 5,
        'endColumn' => 49,
      ),
      'TYPE_WECHAT' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'TYPE_WECHAT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'wechat\'',
          'attributes' => 
          array (
            'startLine' => 93,
            'endLine' => 93,
            'startTokenPos' => 284,
            'startFilePos' => 6571,
            'endTokenPos' => 284,
            'endFilePos' => 6578,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 93,
        'endLine' => 93,
        'startColumn' => 5,
        'endColumn' => 33,
      ),
      'USAGE_REUSABLE' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'USAGE_REUSABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'reusable\'',
          'attributes' => 
          array (
            'startLine' => 95,
            'endLine' => 95,
            'startTokenPos' => 293,
            'startFilePos' => 6609,
            'endTokenPos' => 293,
            'endFilePos' => 6618,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 95,
        'endLine' => 95,
        'startColumn' => 5,
        'endColumn' => 38,
      ),
      'USAGE_SINGLE_USE' => 
      array (
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'name' => 'USAGE_SINGLE_USE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'single_use\'',
          'attributes' => 
          array (
            'startLine' => 96,
            'endLine' => 96,
            'startTokenPos' => 302,
            'startFilePos' => 6650,
            'endTokenPos' => 302,
            'endFilePos' => 6661,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 96,
        'endLine' => 96,
        'startColumn' => 5,
        'endColumn' => 42,
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
                'startLine' => 108,
                'endLine' => 108,
                'startTokenPos' => 319,
                'startFilePos' => 6979,
                'endTokenPos' => 319,
                'endFilePos' => 6982,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 108,
            'endLine' => 108,
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
                'startLine' => 108,
                'endLine' => 108,
                'startTokenPos' => 326,
                'startFilePos' => 6996,
                'endTokenPos' => 326,
                'endFilePos' => 6999,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 108,
            'endLine' => 108,
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
 * Creates a new source object.
 *
 * @param null|array $params
 * @param null|array|string $options
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Source the created resource
 */',
        'startLine' => 108,
        'endLine' => 118,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'currentClassName' => 'Stripe\\Source',
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
            'startLine' => 132,
            'endLine' => 132,
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
                'startLine' => 132,
                'endLine' => 132,
                'startTokenPos' => 426,
                'startFilePos' => 7862,
                'endTokenPos' => 426,
                'endFilePos' => 7865,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 132,
            'endLine' => 132,
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
 * Retrieves an existing source object. Supply the unique source ID from a source
 * creation request and Stripe will return the corresponding up-to-date source
 * object information.
 *
 * @param array|string $id the ID of the API resource to retrieve, or an options array containing an `id` key
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Source
 */',
        'startLine' => 132,
        'endLine' => 139,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'currentClassName' => 'Stripe\\Source',
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
            'startLine' => 158,
            'endLine' => 158,
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
                'startLine' => 158,
                'endLine' => 158,
                'startTokenPos' => 489,
                'startFilePos' => 8814,
                'endTokenPos' => 489,
                'endFilePos' => 8817,
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
                'startLine' => 158,
                'endLine' => 158,
                'startTokenPos' => 496,
                'startFilePos' => 8828,
                'endTokenPos' => 496,
                'endFilePos' => 8831,
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
 * Updates the specified source by setting the values of the parameters passed. Any
 * parameters not provided will be left unchanged.
 *
 * This request accepts the <code>metadata</code> and <code>owner</code> as
 * arguments. It is also possible to update type specific information for selected
 * payment methods. Please refer to our <a href="/docs/sources">payment method
 * guides</a> for more detail.
 *
 * @param string $id the ID of the resource to update
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Source the updated resource
 */',
        'startLine' => 158,
        'endLine' => 168,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'currentClassName' => 'Stripe\\Source',
        'aliasName' => NULL,
      ),
      'detach' => 
      array (
        'name' => 'detach',
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
                'startLine' => 181,
                'endLine' => 181,
                'startTokenPos' => 597,
                'startFilePos' => 9562,
                'endTokenPos' => 597,
                'endFilePos' => 9565,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 181,
            'endLine' => 181,
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
                'startLine' => 181,
                'endLine' => 181,
                'startTokenPos' => 604,
                'startFilePos' => 9576,
                'endTokenPos' => 604,
                'endFilePos' => 9579,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 181,
            'endLine' => 181,
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
 * @throws \\Stripe\\Exception\\UnexpectedValueException if the source is not attached to a customer
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Source the detached source
 */',
        'startLine' => 181,
        'endLine' => 209,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'currentClassName' => 'Stripe\\Source',
        'aliasName' => NULL,
      ),
      'allSourceTransactions' => 
      array (
        'name' => 'allSourceTransactions',
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
            'startLine' => 220,
            'endLine' => 220,
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
                'startLine' => 220,
                'endLine' => 220,
                'startTokenPos' => 841,
                'startFilePos' => 10900,
                'endTokenPos' => 841,
                'endFilePos' => 10903,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 220,
            'endLine' => 220,
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
                'startLine' => 220,
                'endLine' => 220,
                'startTokenPos' => 848,
                'startFilePos' => 10914,
                'endTokenPos' => 848,
                'endFilePos' => 10917,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 220,
            'endLine' => 220,
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
 * @param string $id
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Collection<\\Stripe\\SourceTransaction> list of source transactions
 */',
        'startLine' => 220,
        'endLine' => 228,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'currentClassName' => 'Stripe\\Source',
        'aliasName' => NULL,
      ),
      'verify' => 
      array (
        'name' => 'verify',
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
                'startLine' => 238,
                'endLine' => 238,
                'startTokenPos' => 940,
                'startFilePos' => 11490,
                'endTokenPos' => 940,
                'endFilePos' => 11493,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 238,
            'endLine' => 238,
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
                'startLine' => 238,
                'endLine' => 238,
                'startTokenPos' => 947,
                'startFilePos' => 11504,
                'endTokenPos' => 947,
                'endFilePos' => 11507,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 238,
            'endLine' => 238,
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
 * @return \\Stripe\\Source the verified source
 */',
        'startLine' => 238,
        'endLine' => 245,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Source',
        'implementingClassName' => 'Stripe\\Source',
        'currentClassName' => 'Stripe\\Source',
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