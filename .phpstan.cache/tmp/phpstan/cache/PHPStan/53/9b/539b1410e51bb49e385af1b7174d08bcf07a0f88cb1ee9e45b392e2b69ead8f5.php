<?php declare(strict_types = 1);

// osfsl-/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/PaymentMethod.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Stripe\PaymentMethod
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-0c73ff9f49ed7afa9f122cda00a18cab1caec81bbbb6a5715bc4778636f48cf5-8.4.24-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Stripe\\PaymentMethod',
        'filename' => '/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/PaymentMethod.php',
      ),
    ),
    'namespace' => 'Stripe',
    'name' => 'Stripe\\PaymentMethod',
    'shortName' => 'PaymentMethod',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * PaymentMethod objects represent your customer\'s payment instruments.
 * You can use them with <a href="https://stripe.com/docs/payments/payment-intents">PaymentIntents</a> to collect payments or save them to
 * Customer objects to store instrument details for future payments.
 *
 * Related guides: <a href="https://stripe.com/docs/payments/payment-methods">Payment Methods</a> and <a href="https://stripe.com/docs/payments/more-payment-scenarios">More Payment Scenarios</a>.
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object\'s type. Objects of the same type share the same value.
 * @property null|\\Stripe\\StripeObject $acss_debit
 * @property null|\\Stripe\\StripeObject $affirm
 * @property null|\\Stripe\\StripeObject $afterpay_clearpay
 * @property null|\\Stripe\\StripeObject $alipay
 * @property null|string $allow_redisplay This field indicates whether this payment method can be shown again to its customer in a checkout flow. Stripe products such as Checkout and Elements use this field to determine whether a payment method can be shown as a saved payment method in a checkout flow. The field defaults to “unspecified”.
 * @property null|\\Stripe\\StripeObject $amazon_pay
 * @property null|\\Stripe\\StripeObject $au_becs_debit
 * @property null|\\Stripe\\StripeObject $bacs_debit
 * @property null|\\Stripe\\StripeObject $bancontact
 * @property \\Stripe\\StripeObject $billing_details
 * @property null|\\Stripe\\StripeObject $blik
 * @property null|\\Stripe\\StripeObject $boleto
 * @property null|\\Stripe\\StripeObject $card
 * @property null|\\Stripe\\StripeObject $card_present
 * @property null|\\Stripe\\StripeObject $cashapp
 * @property int $created Time at which the object was created. Measured in seconds since the Unix epoch.
 * @property null|string|\\Stripe\\Customer $customer The ID of the Customer to which this PaymentMethod is saved. This will not be set when the PaymentMethod has not been saved to a Customer.
 * @property null|\\Stripe\\StripeObject $customer_balance
 * @property null|\\Stripe\\StripeObject $eps
 * @property null|\\Stripe\\StripeObject $fpx
 * @property null|\\Stripe\\StripeObject $giropay
 * @property null|\\Stripe\\StripeObject $grabpay
 * @property null|\\Stripe\\StripeObject $ideal
 * @property null|\\Stripe\\StripeObject $interac_present
 * @property null|\\Stripe\\StripeObject $klarna
 * @property null|\\Stripe\\StripeObject $konbini
 * @property null|\\Stripe\\StripeObject $link
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property null|\\Stripe\\StripeObject $metadata Set of <a href="https://stripe.com/docs/api/metadata">key-value pairs</a> that you can attach to an object. This can be useful for storing additional information about the object in a structured format.
 * @property null|\\Stripe\\StripeObject $mobilepay
 * @property null|\\Stripe\\StripeObject $multibanco
 * @property null|\\Stripe\\StripeObject $oxxo
 * @property null|\\Stripe\\StripeObject $p24
 * @property null|\\Stripe\\StripeObject $paynow
 * @property null|\\Stripe\\StripeObject $paypal
 * @property null|\\Stripe\\StripeObject $pix
 * @property null|\\Stripe\\StripeObject $promptpay
 * @property null|\\Stripe\\StripeObject $radar_options Options to configure Radar. See <a href="https://stripe.com/docs/radar/radar-session">Radar Session</a> for more information.
 * @property null|\\Stripe\\StripeObject $revolut_pay
 * @property null|\\Stripe\\StripeObject $sepa_debit
 * @property null|\\Stripe\\StripeObject $sofort
 * @property null|\\Stripe\\StripeObject $swish
 * @property null|\\Stripe\\StripeObject $twint
 * @property string $type The type of the PaymentMethod. An additional hash is included on the PaymentMethod with a name matching this value. It contains additional information specific to the PaymentMethod type.
 * @property null|\\Stripe\\StripeObject $us_bank_account
 * @property null|\\Stripe\\StripeObject $wechat_pay
 * @property null|\\Stripe\\StripeObject $zip
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 64,
    'endLine' => 243,
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
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'OBJECT_NAME',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'payment_method\'',
          'attributes' => 
          array (
            'startLine' => 66,
            'endLine' => 66,
            'startTokenPos' => 27,
            'startFilePos' => 4196,
            'endTokenPos' => 27,
            'endFilePos' => 4211,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 66,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 41,
      ),
      'ALLOW_REDISPLAY_ALWAYS' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'ALLOW_REDISPLAY_ALWAYS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'always\'',
          'attributes' => 
          array (
            'startLine' => 70,
            'endLine' => 70,
            'startTokenPos' => 41,
            'startFilePos' => 4281,
            'endTokenPos' => 41,
            'endFilePos' => 4288,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 70,
        'endLine' => 70,
        'startColumn' => 5,
        'endColumn' => 44,
      ),
      'ALLOW_REDISPLAY_LIMITED' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'ALLOW_REDISPLAY_LIMITED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'limited\'',
          'attributes' => 
          array (
            'startLine' => 71,
            'endLine' => 71,
            'startTokenPos' => 50,
            'startFilePos' => 4327,
            'endTokenPos' => 50,
            'endFilePos' => 4335,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 71,
        'endLine' => 71,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'ALLOW_REDISPLAY_UNSPECIFIED' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'ALLOW_REDISPLAY_UNSPECIFIED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'unspecified\'',
          'attributes' => 
          array (
            'startLine' => 72,
            'endLine' => 72,
            'startTokenPos' => 59,
            'startFilePos' => 4378,
            'endTokenPos' => 59,
            'endFilePos' => 4390,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 72,
        'endLine' => 72,
        'startColumn' => 5,
        'endColumn' => 54,
      ),
      'TYPE_ACSS_DEBIT' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_ACSS_DEBIT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'acss_debit\'',
          'attributes' => 
          array (
            'startLine' => 74,
            'endLine' => 74,
            'startTokenPos' => 68,
            'startFilePos' => 4422,
            'endTokenPos' => 68,
            'endFilePos' => 4433,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 74,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 41,
      ),
      'TYPE_AFFIRM' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_AFFIRM',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'affirm\'',
          'attributes' => 
          array (
            'startLine' => 75,
            'endLine' => 75,
            'startTokenPos' => 77,
            'startFilePos' => 4460,
            'endTokenPos' => 77,
            'endFilePos' => 4467,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 75,
        'endLine' => 75,
        'startColumn' => 5,
        'endColumn' => 33,
      ),
      'TYPE_AFTERPAY_CLEARPAY' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_AFTERPAY_CLEARPAY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'afterpay_clearpay\'',
          'attributes' => 
          array (
            'startLine' => 76,
            'endLine' => 76,
            'startTokenPos' => 86,
            'startFilePos' => 4505,
            'endTokenPos' => 86,
            'endFilePos' => 4523,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 76,
        'endLine' => 76,
        'startColumn' => 5,
        'endColumn' => 55,
      ),
      'TYPE_ALIPAY' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_ALIPAY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'alipay\'',
          'attributes' => 
          array (
            'startLine' => 77,
            'endLine' => 77,
            'startTokenPos' => 95,
            'startFilePos' => 4550,
            'endTokenPos' => 95,
            'endFilePos' => 4557,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 77,
        'endLine' => 77,
        'startColumn' => 5,
        'endColumn' => 33,
      ),
      'TYPE_AMAZON_PAY' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_AMAZON_PAY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'amazon_pay\'',
          'attributes' => 
          array (
            'startLine' => 78,
            'endLine' => 78,
            'startTokenPos' => 104,
            'startFilePos' => 4588,
            'endTokenPos' => 104,
            'endFilePos' => 4599,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 78,
        'endLine' => 78,
        'startColumn' => 5,
        'endColumn' => 41,
      ),
      'TYPE_AU_BECS_DEBIT' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
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
            'startTokenPos' => 113,
            'startFilePos' => 4633,
            'endTokenPos' => 113,
            'endFilePos' => 4647,
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
      'TYPE_BACS_DEBIT' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_BACS_DEBIT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'bacs_debit\'',
          'attributes' => 
          array (
            'startLine' => 80,
            'endLine' => 80,
            'startTokenPos' => 122,
            'startFilePos' => 4678,
            'endTokenPos' => 122,
            'endFilePos' => 4689,
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
      'TYPE_BANCONTACT' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_BANCONTACT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'bancontact\'',
          'attributes' => 
          array (
            'startLine' => 81,
            'endLine' => 81,
            'startTokenPos' => 131,
            'startFilePos' => 4720,
            'endTokenPos' => 131,
            'endFilePos' => 4731,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 81,
        'endLine' => 81,
        'startColumn' => 5,
        'endColumn' => 41,
      ),
      'TYPE_BLIK' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_BLIK',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'blik\'',
          'attributes' => 
          array (
            'startLine' => 82,
            'endLine' => 82,
            'startTokenPos' => 140,
            'startFilePos' => 4756,
            'endTokenPos' => 140,
            'endFilePos' => 4761,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 82,
        'endLine' => 82,
        'startColumn' => 5,
        'endColumn' => 29,
      ),
      'TYPE_BOLETO' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_BOLETO',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'boleto\'',
          'attributes' => 
          array (
            'startLine' => 83,
            'endLine' => 83,
            'startTokenPos' => 149,
            'startFilePos' => 4788,
            'endTokenPos' => 149,
            'endFilePos' => 4795,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 83,
        'endLine' => 83,
        'startColumn' => 5,
        'endColumn' => 33,
      ),
      'TYPE_CARD' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_CARD',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'card\'',
          'attributes' => 
          array (
            'startLine' => 84,
            'endLine' => 84,
            'startTokenPos' => 158,
            'startFilePos' => 4820,
            'endTokenPos' => 158,
            'endFilePos' => 4825,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 84,
        'endLine' => 84,
        'startColumn' => 5,
        'endColumn' => 29,
      ),
      'TYPE_CARD_PRESENT' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_CARD_PRESENT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'card_present\'',
          'attributes' => 
          array (
            'startLine' => 85,
            'endLine' => 85,
            'startTokenPos' => 167,
            'startFilePos' => 4858,
            'endTokenPos' => 167,
            'endFilePos' => 4871,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 85,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'TYPE_CASHAPP' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_CASHAPP',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'cashapp\'',
          'attributes' => 
          array (
            'startLine' => 86,
            'endLine' => 86,
            'startTokenPos' => 176,
            'startFilePos' => 4899,
            'endTokenPos' => 176,
            'endFilePos' => 4907,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 86,
        'endLine' => 86,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'TYPE_CUSTOMER_BALANCE' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_CUSTOMER_BALANCE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'customer_balance\'',
          'attributes' => 
          array (
            'startLine' => 87,
            'endLine' => 87,
            'startTokenPos' => 185,
            'startFilePos' => 4944,
            'endTokenPos' => 185,
            'endFilePos' => 4961,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 87,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 53,
      ),
      'TYPE_EPS' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_EPS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'eps\'',
          'attributes' => 
          array (
            'startLine' => 88,
            'endLine' => 88,
            'startTokenPos' => 194,
            'startFilePos' => 4985,
            'endTokenPos' => 194,
            'endFilePos' => 4989,
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
      'TYPE_FPX' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_FPX',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'fpx\'',
          'attributes' => 
          array (
            'startLine' => 89,
            'endLine' => 89,
            'startTokenPos' => 203,
            'startFilePos' => 5013,
            'endTokenPos' => 203,
            'endFilePos' => 5017,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 89,
        'endLine' => 89,
        'startColumn' => 5,
        'endColumn' => 27,
      ),
      'TYPE_GIROPAY' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_GIROPAY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'giropay\'',
          'attributes' => 
          array (
            'startLine' => 90,
            'endLine' => 90,
            'startTokenPos' => 212,
            'startFilePos' => 5045,
            'endTokenPos' => 212,
            'endFilePos' => 5053,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 90,
        'endLine' => 90,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'TYPE_GRABPAY' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_GRABPAY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'grabpay\'',
          'attributes' => 
          array (
            'startLine' => 91,
            'endLine' => 91,
            'startTokenPos' => 221,
            'startFilePos' => 5081,
            'endTokenPos' => 221,
            'endFilePos' => 5089,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 91,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'TYPE_IDEAL' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_IDEAL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'ideal\'',
          'attributes' => 
          array (
            'startLine' => 92,
            'endLine' => 92,
            'startTokenPos' => 230,
            'startFilePos' => 5115,
            'endTokenPos' => 230,
            'endFilePos' => 5121,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 92,
        'endLine' => 92,
        'startColumn' => 5,
        'endColumn' => 31,
      ),
      'TYPE_INTERAC_PRESENT' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_INTERAC_PRESENT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'interac_present\'',
          'attributes' => 
          array (
            'startLine' => 93,
            'endLine' => 93,
            'startTokenPos' => 239,
            'startFilePos' => 5157,
            'endTokenPos' => 239,
            'endFilePos' => 5173,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 93,
        'endLine' => 93,
        'startColumn' => 5,
        'endColumn' => 51,
      ),
      'TYPE_KLARNA' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_KLARNA',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'klarna\'',
          'attributes' => 
          array (
            'startLine' => 94,
            'endLine' => 94,
            'startTokenPos' => 248,
            'startFilePos' => 5200,
            'endTokenPos' => 248,
            'endFilePos' => 5207,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 94,
        'endLine' => 94,
        'startColumn' => 5,
        'endColumn' => 33,
      ),
      'TYPE_KONBINI' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_KONBINI',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'konbini\'',
          'attributes' => 
          array (
            'startLine' => 95,
            'endLine' => 95,
            'startTokenPos' => 257,
            'startFilePos' => 5235,
            'endTokenPos' => 257,
            'endFilePos' => 5243,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 95,
        'endLine' => 95,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'TYPE_LINK' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_LINK',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'link\'',
          'attributes' => 
          array (
            'startLine' => 96,
            'endLine' => 96,
            'startTokenPos' => 266,
            'startFilePos' => 5268,
            'endTokenPos' => 266,
            'endFilePos' => 5273,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 96,
        'endLine' => 96,
        'startColumn' => 5,
        'endColumn' => 29,
      ),
      'TYPE_MOBILEPAY' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_MOBILEPAY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'mobilepay\'',
          'attributes' => 
          array (
            'startLine' => 97,
            'endLine' => 97,
            'startTokenPos' => 275,
            'startFilePos' => 5303,
            'endTokenPos' => 275,
            'endFilePos' => 5313,
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
      'TYPE_MULTIBANCO' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_MULTIBANCO',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'multibanco\'',
          'attributes' => 
          array (
            'startLine' => 98,
            'endLine' => 98,
            'startTokenPos' => 284,
            'startFilePos' => 5344,
            'endTokenPos' => 284,
            'endFilePos' => 5355,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 98,
        'endLine' => 98,
        'startColumn' => 5,
        'endColumn' => 41,
      ),
      'TYPE_OXXO' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_OXXO',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'oxxo\'',
          'attributes' => 
          array (
            'startLine' => 99,
            'endLine' => 99,
            'startTokenPos' => 293,
            'startFilePos' => 5380,
            'endTokenPos' => 293,
            'endFilePos' => 5385,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 99,
        'endLine' => 99,
        'startColumn' => 5,
        'endColumn' => 29,
      ),
      'TYPE_P24' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_P24',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'p24\'',
          'attributes' => 
          array (
            'startLine' => 100,
            'endLine' => 100,
            'startTokenPos' => 302,
            'startFilePos' => 5409,
            'endTokenPos' => 302,
            'endFilePos' => 5413,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 100,
        'endLine' => 100,
        'startColumn' => 5,
        'endColumn' => 27,
      ),
      'TYPE_PAYNOW' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_PAYNOW',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'paynow\'',
          'attributes' => 
          array (
            'startLine' => 101,
            'endLine' => 101,
            'startTokenPos' => 311,
            'startFilePos' => 5440,
            'endTokenPos' => 311,
            'endFilePos' => 5447,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 101,
        'endLine' => 101,
        'startColumn' => 5,
        'endColumn' => 33,
      ),
      'TYPE_PAYPAL' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_PAYPAL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'paypal\'',
          'attributes' => 
          array (
            'startLine' => 102,
            'endLine' => 102,
            'startTokenPos' => 320,
            'startFilePos' => 5474,
            'endTokenPos' => 320,
            'endFilePos' => 5481,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 102,
        'endLine' => 102,
        'startColumn' => 5,
        'endColumn' => 33,
      ),
      'TYPE_PIX' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_PIX',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'pix\'',
          'attributes' => 
          array (
            'startLine' => 103,
            'endLine' => 103,
            'startTokenPos' => 329,
            'startFilePos' => 5505,
            'endTokenPos' => 329,
            'endFilePos' => 5509,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 103,
        'endLine' => 103,
        'startColumn' => 5,
        'endColumn' => 27,
      ),
      'TYPE_PROMPTPAY' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_PROMPTPAY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'promptpay\'',
          'attributes' => 
          array (
            'startLine' => 104,
            'endLine' => 104,
            'startTokenPos' => 338,
            'startFilePos' => 5539,
            'endTokenPos' => 338,
            'endFilePos' => 5549,
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
      'TYPE_REVOLUT_PAY' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_REVOLUT_PAY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'revolut_pay\'',
          'attributes' => 
          array (
            'startLine' => 105,
            'endLine' => 105,
            'startTokenPos' => 347,
            'startFilePos' => 5581,
            'endTokenPos' => 347,
            'endFilePos' => 5593,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 105,
        'endLine' => 105,
        'startColumn' => 5,
        'endColumn' => 43,
      ),
      'TYPE_SEPA_DEBIT' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_SEPA_DEBIT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'sepa_debit\'',
          'attributes' => 
          array (
            'startLine' => 106,
            'endLine' => 106,
            'startTokenPos' => 356,
            'startFilePos' => 5624,
            'endTokenPos' => 356,
            'endFilePos' => 5635,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 106,
        'endLine' => 106,
        'startColumn' => 5,
        'endColumn' => 41,
      ),
      'TYPE_SOFORT' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_SOFORT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'sofort\'',
          'attributes' => 
          array (
            'startLine' => 107,
            'endLine' => 107,
            'startTokenPos' => 365,
            'startFilePos' => 5662,
            'endTokenPos' => 365,
            'endFilePos' => 5669,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 107,
        'endLine' => 107,
        'startColumn' => 5,
        'endColumn' => 33,
      ),
      'TYPE_SWISH' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_SWISH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'swish\'',
          'attributes' => 
          array (
            'startLine' => 108,
            'endLine' => 108,
            'startTokenPos' => 374,
            'startFilePos' => 5695,
            'endTokenPos' => 374,
            'endFilePos' => 5701,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 108,
        'endLine' => 108,
        'startColumn' => 5,
        'endColumn' => 31,
      ),
      'TYPE_TWINT' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_TWINT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'twint\'',
          'attributes' => 
          array (
            'startLine' => 109,
            'endLine' => 109,
            'startTokenPos' => 383,
            'startFilePos' => 5727,
            'endTokenPos' => 383,
            'endFilePos' => 5733,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 109,
        'endLine' => 109,
        'startColumn' => 5,
        'endColumn' => 31,
      ),
      'TYPE_US_BANK_ACCOUNT' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_US_BANK_ACCOUNT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'us_bank_account\'',
          'attributes' => 
          array (
            'startLine' => 110,
            'endLine' => 110,
            'startTokenPos' => 392,
            'startFilePos' => 5769,
            'endTokenPos' => 392,
            'endFilePos' => 5785,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 110,
        'endLine' => 110,
        'startColumn' => 5,
        'endColumn' => 51,
      ),
      'TYPE_WECHAT_PAY' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_WECHAT_PAY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'wechat_pay\'',
          'attributes' => 
          array (
            'startLine' => 111,
            'endLine' => 111,
            'startTokenPos' => 401,
            'startFilePos' => 5816,
            'endTokenPos' => 401,
            'endFilePos' => 5827,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 111,
        'endLine' => 111,
        'startColumn' => 5,
        'endColumn' => 41,
      ),
      'TYPE_ZIP' => 
      array (
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'name' => 'TYPE_ZIP',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'zip\'',
          'attributes' => 
          array (
            'startLine' => 112,
            'endLine' => 112,
            'startTokenPos' => 410,
            'startFilePos' => 5851,
            'endTokenPos' => 410,
            'endFilePos' => 5855,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 112,
        'endLine' => 112,
        'startColumn' => 5,
        'endColumn' => 27,
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
                'startLine' => 132,
                'endLine' => 132,
                'startTokenPos' => 427,
                'startFilePos' => 6684,
                'endTokenPos' => 427,
                'endFilePos' => 6687,
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
                'startLine' => 132,
                'endLine' => 132,
                'startTokenPos' => 434,
                'startFilePos' => 6701,
                'endTokenPos' => 434,
                'endFilePos' => 6704,
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
 * Creates a PaymentMethod object. Read the <a
 * href="/docs/stripe-js/reference#stripe-create-payment-method">Stripe.js
 * reference</a> to learn how to create PaymentMethods via Stripe.js.
 *
 * Instead of creating a PaymentMethod directly, we recommend using the <a
 * href="/docs/payments/accept-a-payment">PaymentIntents</a> API to accept a
 * payment immediately or the <a
 * href="/docs/payments/save-and-reuse">SetupIntent</a> API to collect payment
 * method details ahead of a future payment.
 *
 * @param null|array $params
 * @param null|array|string $options
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\PaymentMethod the created resource
 */',
        'startLine' => 132,
        'endLine' => 142,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'currentClassName' => 'Stripe\\PaymentMethod',
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
                'startLine' => 157,
                'endLine' => 157,
                'startTokenPos' => 531,
                'startFilePos' => 7603,
                'endTokenPos' => 531,
                'endFilePos' => 7606,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 157,
            'endLine' => 157,
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
                'startLine' => 157,
                'endLine' => 157,
                'startTokenPos' => 538,
                'startFilePos' => 7617,
                'endTokenPos' => 538,
                'endFilePos' => 7620,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 157,
            'endLine' => 157,
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
 * Returns a list of PaymentMethods for Treasury flows. If you want to list the
 * PaymentMethods attached to a Customer for payments, you should use the <a
 * href="/docs/api/payment_methods/customer_list">List a Customer’s
 * PaymentMethods</a> API instead.
 *
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Collection<\\Stripe\\PaymentMethod> of ApiResources
 */',
        'startLine' => 157,
        'endLine' => 162,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'currentClassName' => 'Stripe\\PaymentMethod',
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
            'startLine' => 177,
            'endLine' => 177,
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
                'startLine' => 177,
                'endLine' => 177,
                'startTokenPos' => 594,
                'startFilePos' => 8354,
                'endTokenPos' => 594,
                'endFilePos' => 8357,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 177,
            'endLine' => 177,
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
 * Retrieves a PaymentMethod object attached to the StripeAccount. To retrieve a
 * payment method attached to a Customer, you should use <a
 * href="/docs/api/payment_methods/customer">Retrieve a Customer’s
 * PaymentMethods</a>.
 *
 * @param array|string $id the ID of the API resource to retrieve, or an options array containing an `id` key
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\PaymentMethod
 */',
        'startLine' => 177,
        'endLine' => 184,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'currentClassName' => 'Stripe\\PaymentMethod',
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
            'startLine' => 198,
            'endLine' => 198,
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
                'startLine' => 198,
                'endLine' => 198,
                'startTokenPos' => 657,
                'startFilePos' => 8983,
                'endTokenPos' => 657,
                'endFilePos' => 8986,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 198,
            'endLine' => 198,
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
                'startLine' => 198,
                'endLine' => 198,
                'startTokenPos' => 664,
                'startFilePos' => 8997,
                'endTokenPos' => 664,
                'endFilePos' => 9000,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 198,
            'endLine' => 198,
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
 * Updates a PaymentMethod object. A PaymentMethod must be attached a customer to
 * be updated.
 *
 * @param string $id the ID of the resource to update
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\PaymentMethod the updated resource
 */',
        'startLine' => 198,
        'endLine' => 208,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'currentClassName' => 'Stripe\\PaymentMethod',
        'aliasName' => NULL,
      ),
      'attach' => 
      array (
        'name' => 'attach',
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
                'startLine' => 218,
                'endLine' => 218,
                'startTokenPos' => 760,
                'startFilePos' => 9605,
                'endTokenPos' => 760,
                'endFilePos' => 9608,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 218,
            'endLine' => 218,
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
                'startLine' => 218,
                'endLine' => 218,
                'startTokenPos' => 767,
                'startFilePos' => 9619,
                'endTokenPos' => 767,
                'endFilePos' => 9622,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 218,
            'endLine' => 218,
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
 * @return \\Stripe\\PaymentMethod the attached payment method
 */',
        'startLine' => 218,
        'endLine' => 225,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'currentClassName' => 'Stripe\\PaymentMethod',
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
                'startLine' => 235,
                'endLine' => 235,
                'startTokenPos' => 844,
                'startFilePos' => 10111,
                'endTokenPos' => 844,
                'endFilePos' => 10114,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 235,
            'endLine' => 235,
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
                'startLine' => 235,
                'endLine' => 235,
                'startTokenPos' => 851,
                'startFilePos' => 10125,
                'endTokenPos' => 851,
                'endFilePos' => 10128,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 235,
            'endLine' => 235,
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
 * @return \\Stripe\\PaymentMethod the detached payment method
 */',
        'startLine' => 235,
        'endLine' => 242,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\PaymentMethod',
        'implementingClassName' => 'Stripe\\PaymentMethod',
        'currentClassName' => 'Stripe\\PaymentMethod',
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