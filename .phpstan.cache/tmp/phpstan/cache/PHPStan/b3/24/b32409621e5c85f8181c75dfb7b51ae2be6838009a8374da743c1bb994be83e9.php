<?php declare(strict_types = 1);

// osfsl-/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/Card.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Stripe\Card
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-7c13ae807baaa211c5f6b52404c65ed0c3ba490554474a9a2b476bc691ead9ed-8.4.24-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Stripe\\Card',
        'filename' => '/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/Card.php',
      ),
    ),
    'namespace' => 'Stripe',
    'name' => 'Stripe\\Card',
    'shortName' => 'Card',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * You can store multiple cards on a customer in order to charge the customer
 * later. You can also store multiple debit cards on a recipient in order to
 * transfer to those cards later.
 *
 * Related guide: <a href="https://stripe.com/docs/sources/cards">Card payments with Sources</a>
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object\'s type. Objects of the same type share the same value.
 * @property null|string|\\Stripe\\Account $account The account this card belongs to. This attribute will not be in the card object if the card belongs to a customer or recipient instead. This property is only available for accounts where <a href="/api/accounts/object#account_object-controller-requirement_collection">controller.requirement_collection</a> is <code>application</code>, which includes Custom accounts.
 * @property null|string $address_city City/District/Suburb/Town/Village.
 * @property null|string $address_country Billing address country, if provided when creating card.
 * @property null|string $address_line1 Address line 1 (Street address/PO Box/Company name).
 * @property null|string $address_line1_check If <code>address_line1</code> was provided, results of the check: <code>pass</code>, <code>fail</code>, <code>unavailable</code>, or <code>unchecked</code>.
 * @property null|string $address_line2 Address line 2 (Apartment/Suite/Unit/Building).
 * @property null|string $address_state State/County/Province/Region.
 * @property null|string $address_zip ZIP or postal code.
 * @property null|string $address_zip_check If <code>address_zip</code> was provided, results of the check: <code>pass</code>, <code>fail</code>, <code>unavailable</code>, or <code>unchecked</code>.
 * @property null|string[] $available_payout_methods A set of available payout methods for this card. Only values from this set should be passed as the <code>method</code> when creating a payout.
 * @property string $brand Card brand. Can be <code>American Express</code>, <code>Diners Club</code>, <code>Discover</code>, <code>Eftpos Australia</code>, <code>JCB</code>, <code>MasterCard</code>, <code>UnionPay</code>, <code>Visa</code>, or <code>Unknown</code>.
 * @property null|string $country Two-letter ISO code representing the country of the card. You could use this attribute to get a sense of the international breakdown of cards you\'ve collected.
 * @property null|string $currency Three-letter <a href="https://stripe.com/docs/payouts">ISO code for currency</a>. Only applicable on accounts (not customers or recipients). The card can be used as a transfer destination for funds in this currency. This property is only available for accounts where <a href="/api/accounts/object#account_object-controller-requirement_collection">controller.requirement_collection</a> is <code>application</code>, which includes Custom accounts.
 * @property null|string|\\Stripe\\Customer $customer The customer that this card belongs to. This attribute will not be in the card object if the card belongs to an account or recipient instead.
 * @property null|string $cvc_check If a CVC was provided, results of the check: <code>pass</code>, <code>fail</code>, <code>unavailable</code>, or <code>unchecked</code>. A result of unchecked indicates that CVC was provided but hasn\'t been checked yet. Checks are typically performed when attaching a card to a Customer object, or when creating a charge. For more details, see <a href="https://support.stripe.com/questions/check-if-a-card-is-valid-without-a-charge">Check if a card is valid without a charge</a>.
 * @property null|bool $default_for_currency Whether this card is the default external account for its currency. This property is only available for accounts where <a href="/api/accounts/object#account_object-controller-requirement_collection">controller.requirement_collection</a> is <code>application</code>, which includes Custom accounts.
 * @property null|string $dynamic_last4 (For tokenized numbers only.) The last four digits of the device account number.
 * @property int $exp_month Two-digit number representing the card\'s expiration month.
 * @property int $exp_year Four-digit number representing the card\'s expiration year.
 * @property null|string $fingerprint <p>Uniquely identifies this particular card number. You can use this attribute to check whether two customers who’ve signed up with you are using the same card number, for example. For payment methods that tokenize card information (Apple Pay, Google Pay), the tokenized number might be provided instead of the underlying card number.</p><p><em>As of May 1, 2021, card fingerprint in India for Connect changed to allow two fingerprints for the same card---one for India and one for the rest of the world.</em></p>
 * @property string $funding Card funding type. Can be <code>credit</code>, <code>debit</code>, <code>prepaid</code>, or <code>unknown</code>.
 * @property string $last4 The last four digits of the card.
 * @property null|\\Stripe\\StripeObject $metadata Set of <a href="https://stripe.com/docs/api/metadata">key-value pairs</a> that you can attach to an object. This can be useful for storing additional information about the object in a structured format.
 * @property null|string $name Cardholder name.
 * @property null|\\Stripe\\StripeObject $networks
 * @property null|string $status For external accounts that are cards, possible values are <code>new</code> and <code>errored</code>. If a payout fails, the status is set to <code>errored</code> and <a href="https://stripe.com/docs/payouts#payout-schedule">scheduled payouts</a> are stopped until account details are updated.
 * @property null|string $tokenization_method If the card number is tokenized, this is the method that was used. Can be <code>android_pay</code> (includes Google Pay), <code>apple_pay</code>, <code>masterpass</code>, <code>visa_checkout</code>, or null.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 44,
    'endLine' => 179,
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
        'declaringClassName' => 'Stripe\\Card',
        'implementingClassName' => 'Stripe\\Card',
        'name' => 'OBJECT_NAME',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'card\'',
          'attributes' => 
          array (
            'startLine' => 46,
            'endLine' => 46,
            'startTokenPos' => 27,
            'startFilePos' => 6067,
            'endTokenPos' => 27,
            'endFilePos' => 6072,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 46,
        'endLine' => 46,
        'startColumn' => 5,
        'endColumn' => 31,
      ),
      'CVC_CHECK_FAIL' => 
      array (
        'declaringClassName' => 'Stripe\\Card',
        'implementingClassName' => 'Stripe\\Card',
        'name' => 'CVC_CHECK_FAIL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'fail\'',
          'attributes' => 
          array (
            'startLine' => 74,
            'endLine' => 74,
            'startTokenPos' => 126,
            'startFilePos' => 6857,
            'endTokenPos' => 126,
            'endFilePos' => 6862,
          ),
        ),
        'docComment' => '/**
 * Possible string representations of the CVC check status.
 *
 * @see https://stripe.com/docs/api/cards/object#card_object-cvc_check
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 74,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 34,
      ),
      'CVC_CHECK_PASS' => 
      array (
        'declaringClassName' => 'Stripe\\Card',
        'implementingClassName' => 'Stripe\\Card',
        'name' => 'CVC_CHECK_PASS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'pass\'',
          'attributes' => 
          array (
            'startLine' => 75,
            'endLine' => 75,
            'startTokenPos' => 135,
            'startFilePos' => 6892,
            'endTokenPos' => 135,
            'endFilePos' => 6897,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 75,
        'endLine' => 75,
        'startColumn' => 5,
        'endColumn' => 34,
      ),
      'CVC_CHECK_UNAVAILABLE' => 
      array (
        'declaringClassName' => 'Stripe\\Card',
        'implementingClassName' => 'Stripe\\Card',
        'name' => 'CVC_CHECK_UNAVAILABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'unavailable\'',
          'attributes' => 
          array (
            'startLine' => 76,
            'endLine' => 76,
            'startTokenPos' => 144,
            'startFilePos' => 6934,
            'endTokenPos' => 144,
            'endFilePos' => 6946,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 76,
        'endLine' => 76,
        'startColumn' => 5,
        'endColumn' => 48,
      ),
      'CVC_CHECK_UNCHECKED' => 
      array (
        'declaringClassName' => 'Stripe\\Card',
        'implementingClassName' => 'Stripe\\Card',
        'name' => 'CVC_CHECK_UNCHECKED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'unchecked\'',
          'attributes' => 
          array (
            'startLine' => 77,
            'endLine' => 77,
            'startTokenPos' => 153,
            'startFilePos' => 6981,
            'endTokenPos' => 153,
            'endFilePos' => 6991,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 77,
        'endLine' => 77,
        'startColumn' => 5,
        'endColumn' => 44,
      ),
      'FUNDING_CREDIT' => 
      array (
        'declaringClassName' => 'Stripe\\Card',
        'implementingClassName' => 'Stripe\\Card',
        'name' => 'FUNDING_CREDIT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'credit\'',
          'attributes' => 
          array (
            'startLine' => 84,
            'endLine' => 84,
            'startTokenPos' => 164,
            'startFilePos' => 7185,
            'endTokenPos' => 164,
            'endFilePos' => 7192,
          ),
        ),
        'docComment' => '/**
 * Possible string representations of the funding of the card.
 *
 * @see https://stripe.com/docs/api/cards/object#card_object-funding
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 84,
        'endLine' => 84,
        'startColumn' => 5,
        'endColumn' => 36,
      ),
      'FUNDING_DEBIT' => 
      array (
        'declaringClassName' => 'Stripe\\Card',
        'implementingClassName' => 'Stripe\\Card',
        'name' => 'FUNDING_DEBIT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'debit\'',
          'attributes' => 
          array (
            'startLine' => 85,
            'endLine' => 85,
            'startTokenPos' => 173,
            'startFilePos' => 7221,
            'endTokenPos' => 173,
            'endFilePos' => 7227,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 85,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 34,
      ),
      'FUNDING_PREPAID' => 
      array (
        'declaringClassName' => 'Stripe\\Card',
        'implementingClassName' => 'Stripe\\Card',
        'name' => 'FUNDING_PREPAID',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'prepaid\'',
          'attributes' => 
          array (
            'startLine' => 86,
            'endLine' => 86,
            'startTokenPos' => 182,
            'startFilePos' => 7258,
            'endTokenPos' => 182,
            'endFilePos' => 7266,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 86,
        'endLine' => 86,
        'startColumn' => 5,
        'endColumn' => 38,
      ),
      'FUNDING_UNKNOWN' => 
      array (
        'declaringClassName' => 'Stripe\\Card',
        'implementingClassName' => 'Stripe\\Card',
        'name' => 'FUNDING_UNKNOWN',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'unknown\'',
          'attributes' => 
          array (
            'startLine' => 87,
            'endLine' => 87,
            'startTokenPos' => 191,
            'startFilePos' => 7297,
            'endTokenPos' => 191,
            'endFilePos' => 7305,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 87,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 38,
      ),
      'TOKENIZATION_METHOD_APPLE_PAY' => 
      array (
        'declaringClassName' => 'Stripe\\Card',
        'implementingClassName' => 'Stripe\\Card',
        'name' => 'TOKENIZATION_METHOD_APPLE_PAY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'apple_pay\'',
          'attributes' => 
          array (
            'startLine' => 94,
            'endLine' => 94,
            'startTokenPos' => 202,
            'startFilePos' => 7561,
            'endTokenPos' => 202,
            'endFilePos' => 7571,
          ),
        ),
        'docComment' => '/**
 * Possible string representations of the tokenization method when using Apple Pay or Google Pay.
 *
 * @see https://stripe.com/docs/api/cards/object#card_object-tokenization_method
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 94,
        'endLine' => 94,
        'startColumn' => 5,
        'endColumn' => 54,
      ),
      'TOKENIZATION_METHOD_GOOGLE_PAY' => 
      array (
        'declaringClassName' => 'Stripe\\Card',
        'implementingClassName' => 'Stripe\\Card',
        'name' => 'TOKENIZATION_METHOD_GOOGLE_PAY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'google_pay\'',
          'attributes' => 
          array (
            'startLine' => 95,
            'endLine' => 95,
            'startTokenPos' => 211,
            'startFilePos' => 7617,
            'endTokenPos' => 211,
            'endFilePos' => 7628,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 95,
        'endLine' => 95,
        'startColumn' => 5,
        'endColumn' => 56,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
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
                'startLine' => 58,
                'endLine' => 58,
                'startTokenPos' => 42,
                'startFilePos' => 6406,
                'endTokenPos' => 42,
                'endFilePos' => 6409,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 58,
            'endLine' => 58,
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
                'startLine' => 58,
                'endLine' => 58,
                'startTokenPos' => 49,
                'startFilePos' => 6420,
                'endTokenPos' => 49,
                'endFilePos' => 6423,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 58,
            'endLine' => 58,
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
 * Delete a specified external account for a given account.
 *
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Card the deleted resource
 */',
        'startLine' => 58,
        'endLine' => 67,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Card',
        'implementingClassName' => 'Stripe\\Card',
        'currentClassName' => 'Stripe\\Card',
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
        'docComment' => '/**
 * @return string The instance URL for this resource. It needs to be special
 *    cased because cards are nested resources that may belong to different
 *    top-level resources.
 */',
        'startLine' => 102,
        'endLine' => 121,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Card',
        'implementingClassName' => 'Stripe\\Card',
        'currentClassName' => 'Stripe\\Card',
        'aliasName' => NULL,
      ),
      'retrieve' => 
      array (
        'name' => 'retrieve',
        'parameters' => 
        array (
          '_id' => 
          array (
            'name' => '_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 129,
            'endLine' => 129,
            'startColumn' => 37,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          '_opts' => 
          array (
            'name' => '_opts',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 129,
                'endLine' => 129,
                'startTokenPos' => 404,
                'startFilePos' => 8747,
                'endTokenPos' => 404,
                'endFilePos' => 8750,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 129,
            'endLine' => 129,
            'startColumn' => 43,
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
 * @param array|string $_id
 * @param null|array|string $_opts
 *
 * @throws \\Stripe\\Exception\\BadMethodCallException
 */',
        'startLine' => 129,
        'endLine' => 137,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Card',
        'implementingClassName' => 'Stripe\\Card',
        'currentClassName' => 'Stripe\\Card',
        'aliasName' => NULL,
      ),
      'update' => 
      array (
        'name' => 'update',
        'parameters' => 
        array (
          '_id' => 
          array (
            'name' => '_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 146,
            'endLine' => 146,
            'startColumn' => 35,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          '_params' => 
          array (
            'name' => '_params',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 146,
                'endLine' => 146,
                'startTokenPos' => 457,
                'startFilePos' => 9340,
                'endTokenPos' => 457,
                'endFilePos' => 9343,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 146,
            'endLine' => 146,
            'startColumn' => 41,
            'endColumn' => 55,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          '_options' => 
          array (
            'name' => '_options',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 146,
                'endLine' => 146,
                'startTokenPos' => 464,
                'startFilePos' => 9358,
                'endTokenPos' => 464,
                'endFilePos' => 9361,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 146,
            'endLine' => 146,
            'startColumn' => 58,
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
 * @param string $_id
 * @param null|array $_params
 * @param null|array|string $_options
 *
 * @throws \\Stripe\\Exception\\BadMethodCallException
 */',
        'startLine' => 146,
        'endLine' => 155,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Card',
        'implementingClassName' => 'Stripe\\Card',
        'currentClassName' => 'Stripe\\Card',
        'aliasName' => NULL,
      ),
      'save' => 
      array (
        'name' => 'save',
        'parameters' => 
        array (
          'opts' => 
          array (
            'name' => 'opts',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 168,
                'endLine' => 168,
                'startTokenPos' => 516,
                'startFilePos' => 10174,
                'endTokenPos' => 516,
                'endFilePos' => 10177,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 168,
            'endLine' => 168,
            'startColumn' => 26,
            'endColumn' => 37,
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
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return static the saved resource
 *
 * @deprecated The `save` method is deprecated and will be removed in a
 *     future major version of the library. Use the static method `update`
 *     on the resource instead.
 */',
        'startLine' => 168,
        'endLine' => 178,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Card',
        'implementingClassName' => 'Stripe\\Card',
        'currentClassName' => 'Stripe\\Card',
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