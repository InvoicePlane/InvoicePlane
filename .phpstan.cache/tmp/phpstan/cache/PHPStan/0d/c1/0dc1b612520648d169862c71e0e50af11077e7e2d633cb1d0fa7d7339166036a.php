<?php declare(strict_types = 1);

// osfsl-/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/Review.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Stripe\Review
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-ec79904346b32d48c1ef15a72cfca273996cd61709bfcca911acbe94b0a70258-8.4.24-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Stripe\\Review',
        'filename' => '/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/Review.php',
      ),
    ),
    'namespace' => 'Stripe',
    'name' => 'Stripe\\Review',
    'shortName' => 'Review',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Reviews can be used to supplement automated fraud detection with human expertise.
 *
 * Learn more about <a href="/radar">Radar</a> and reviewing payments
 * <a href="https://stripe.com/docs/radar/reviews">here</a>.
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object\'s type. Objects of the same type share the same value.
 * @property null|string $billing_zip The ZIP or postal code of the card used, if applicable.
 * @property null|string|\\Stripe\\Charge $charge The charge associated with this review.
 * @property null|string $closed_reason The reason the review was closed, or null if it has not yet been closed. One of <code>approved</code>, <code>refunded</code>, <code>refunded_as_fraud</code>, <code>disputed</code>, or <code>redacted</code>.
 * @property int $created Time at which the object was created. Measured in seconds since the Unix epoch.
 * @property null|string $ip_address The IP address where the payment originated.
 * @property null|\\Stripe\\StripeObject $ip_address_location Information related to the location of the payment. Note that this information is an approximation and attempts to locate the nearest population center - it should not be used to determine a specific address.
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property bool $open If <code>true</code>, the review needs action.
 * @property string $opened_reason The reason the review was opened. One of <code>rule</code> or <code>manual</code>.
 * @property null|string|\\Stripe\\PaymentIntent $payment_intent The PaymentIntent ID associated with this review, if one exists.
 * @property string $reason The reason the review is currently open or closed. One of <code>rule</code>, <code>manual</code>, <code>approved</code>, <code>refunded</code>, <code>refunded_as_fraud</code>, <code>disputed</code>, or <code>redacted</code>.
 * @property null|\\Stripe\\StripeObject $session Information related to the browsing session of the user who initiated the payment.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 28,
    'endLine' => 109,
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
        'declaringClassName' => 'Stripe\\Review',
        'implementingClassName' => 'Stripe\\Review',
        'name' => 'OBJECT_NAME',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'review\'',
          'attributes' => 
          array (
            'startLine' => 30,
            'endLine' => 30,
            'startTokenPos' => 27,
            'startFilePos' => 2276,
            'endTokenPos' => 27,
            'endFilePos' => 2283,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 33,
      ),
      'CLOSED_REASON_APPROVED' => 
      array (
        'declaringClassName' => 'Stripe\\Review',
        'implementingClassName' => 'Stripe\\Review',
        'name' => 'CLOSED_REASON_APPROVED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'approved\'',
          'attributes' => 
          array (
            'startLine' => 32,
            'endLine' => 32,
            'startTokenPos' => 36,
            'startFilePos' => 2322,
            'endTokenPos' => 36,
            'endFilePos' => 2331,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 32,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'CLOSED_REASON_DISPUTED' => 
      array (
        'declaringClassName' => 'Stripe\\Review',
        'implementingClassName' => 'Stripe\\Review',
        'name' => 'CLOSED_REASON_DISPUTED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'disputed\'',
          'attributes' => 
          array (
            'startLine' => 33,
            'endLine' => 33,
            'startTokenPos' => 45,
            'startFilePos' => 2369,
            'endTokenPos' => 45,
            'endFilePos' => 2378,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 33,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'CLOSED_REASON_REDACTED' => 
      array (
        'declaringClassName' => 'Stripe\\Review',
        'implementingClassName' => 'Stripe\\Review',
        'name' => 'CLOSED_REASON_REDACTED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'redacted\'',
          'attributes' => 
          array (
            'startLine' => 34,
            'endLine' => 34,
            'startTokenPos' => 54,
            'startFilePos' => 2416,
            'endTokenPos' => 54,
            'endFilePos' => 2425,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 34,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'CLOSED_REASON_REFUNDED' => 
      array (
        'declaringClassName' => 'Stripe\\Review',
        'implementingClassName' => 'Stripe\\Review',
        'name' => 'CLOSED_REASON_REFUNDED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'refunded\'',
          'attributes' => 
          array (
            'startLine' => 35,
            'endLine' => 35,
            'startTokenPos' => 63,
            'startFilePos' => 2463,
            'endTokenPos' => 63,
            'endFilePos' => 2472,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 35,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'CLOSED_REASON_REFUNDED_AS_FRAUD' => 
      array (
        'declaringClassName' => 'Stripe\\Review',
        'implementingClassName' => 'Stripe\\Review',
        'name' => 'CLOSED_REASON_REFUNDED_AS_FRAUD',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'refunded_as_fraud\'',
          'attributes' => 
          array (
            'startLine' => 36,
            'endLine' => 36,
            'startTokenPos' => 72,
            'startFilePos' => 2519,
            'endTokenPos' => 72,
            'endFilePos' => 2537,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 36,
        'endLine' => 36,
        'startColumn' => 5,
        'endColumn' => 64,
      ),
      'OPENED_REASON_MANUAL' => 
      array (
        'declaringClassName' => 'Stripe\\Review',
        'implementingClassName' => 'Stripe\\Review',
        'name' => 'OPENED_REASON_MANUAL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'manual\'',
          'attributes' => 
          array (
            'startLine' => 38,
            'endLine' => 38,
            'startTokenPos' => 81,
            'startFilePos' => 2574,
            'endTokenPos' => 81,
            'endFilePos' => 2581,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 38,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 42,
      ),
      'OPENED_REASON_RULE' => 
      array (
        'declaringClassName' => 'Stripe\\Review',
        'implementingClassName' => 'Stripe\\Review',
        'name' => 'OPENED_REASON_RULE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'rule\'',
          'attributes' => 
          array (
            'startLine' => 39,
            'endLine' => 39,
            'startTokenPos' => 90,
            'startFilePos' => 2615,
            'endTokenPos' => 90,
            'endFilePos' => 2620,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 39,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 38,
      ),
      'REASON_APPROVED' => 
      array (
        'declaringClassName' => 'Stripe\\Review',
        'implementingClassName' => 'Stripe\\Review',
        'name' => 'REASON_APPROVED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'approved\'',
          'attributes' => 
          array (
            'startLine' => 86,
            'endLine' => 86,
            'startTokenPos' => 224,
            'startFilePos' => 4236,
            'endTokenPos' => 224,
            'endFilePos' => 4245,
          ),
        ),
        'docComment' => '/**
 * Possible string representations of the current, the opening or the closure reason of the review.
 * Not all of these enumeration apply to all of the ´reason´ fields. Please consult the Review object to
 * determine where these are apply.
 *
 * @see https://stripe.com/docs/api/radar/reviews/object
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 86,
        'endLine' => 86,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'REASON_DISPUTED' => 
      array (
        'declaringClassName' => 'Stripe\\Review',
        'implementingClassName' => 'Stripe\\Review',
        'name' => 'REASON_DISPUTED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'disputed\'',
          'attributes' => 
          array (
            'startLine' => 87,
            'endLine' => 87,
            'startTokenPos' => 233,
            'startFilePos' => 4276,
            'endTokenPos' => 233,
            'endFilePos' => 4285,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 87,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'REASON_MANUAL' => 
      array (
        'declaringClassName' => 'Stripe\\Review',
        'implementingClassName' => 'Stripe\\Review',
        'name' => 'REASON_MANUAL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'manual\'',
          'attributes' => 
          array (
            'startLine' => 88,
            'endLine' => 88,
            'startTokenPos' => 242,
            'startFilePos' => 4314,
            'endTokenPos' => 242,
            'endFilePos' => 4321,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 88,
        'endLine' => 88,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'REASON_REFUNDED' => 
      array (
        'declaringClassName' => 'Stripe\\Review',
        'implementingClassName' => 'Stripe\\Review',
        'name' => 'REASON_REFUNDED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'refunded\'',
          'attributes' => 
          array (
            'startLine' => 89,
            'endLine' => 89,
            'startTokenPos' => 251,
            'startFilePos' => 4352,
            'endTokenPos' => 251,
            'endFilePos' => 4361,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 89,
        'endLine' => 89,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'REASON_REFUNDED_AS_FRAUD' => 
      array (
        'declaringClassName' => 'Stripe\\Review',
        'implementingClassName' => 'Stripe\\Review',
        'name' => 'REASON_REFUNDED_AS_FRAUD',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'refunded_as_fraud\'',
          'attributes' => 
          array (
            'startLine' => 90,
            'endLine' => 90,
            'startTokenPos' => 260,
            'startFilePos' => 4401,
            'endTokenPos' => 260,
            'endFilePos' => 4419,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 90,
        'endLine' => 90,
        'startColumn' => 5,
        'endColumn' => 57,
      ),
      'REASON_RULE' => 
      array (
        'declaringClassName' => 'Stripe\\Review',
        'implementingClassName' => 'Stripe\\Review',
        'name' => 'REASON_RULE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'rule\'',
          'attributes' => 
          array (
            'startLine' => 91,
            'endLine' => 91,
            'startTokenPos' => 269,
            'startFilePos' => 4446,
            'endTokenPos' => 269,
            'endFilePos' => 4451,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 91,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 31,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
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
                'startLine' => 53,
                'endLine' => 53,
                'startTokenPos' => 107,
                'startFilePos' => 3148,
                'endTokenPos' => 107,
                'endFilePos' => 3151,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 53,
            'endLine' => 53,
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
                'startLine' => 53,
                'endLine' => 53,
                'startTokenPos' => 114,
                'startFilePos' => 3162,
                'endTokenPos' => 114,
                'endFilePos' => 3165,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 53,
            'endLine' => 53,
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
 * Returns a list of <code>Review</code> objects that have <code>open</code> set to
 * <code>true</code>. The objects are sorted in descending order by creation date,
 * with the most recently created object appearing first.
 *
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Collection<\\Stripe\\Review> of ApiResources
 */',
        'startLine' => 53,
        'endLine' => 58,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Review',
        'implementingClassName' => 'Stripe\\Review',
        'currentClassName' => 'Stripe\\Review',
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
            'startLine' => 70,
            'endLine' => 70,
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
                'startLine' => 70,
                'endLine' => 70,
                'startTokenPos' => 170,
                'startFilePos' => 3690,
                'endTokenPos' => 170,
                'endFilePos' => 3693,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 70,
            'endLine' => 70,
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
 * Retrieves a <code>Review</code> object.
 *
 * @param array|string $id the ID of the API resource to retrieve, or an options array containing an `id` key
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Review
 */',
        'startLine' => 70,
        'endLine' => 77,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Review',
        'implementingClassName' => 'Stripe\\Review',
        'currentClassName' => 'Stripe\\Review',
        'aliasName' => NULL,
      ),
      'approve' => 
      array (
        'name' => 'approve',
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
                'startLine' => 101,
                'endLine' => 101,
                'startTokenPos' => 284,
                'startFilePos' => 4716,
                'endTokenPos' => 284,
                'endFilePos' => 4719,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 101,
            'endLine' => 101,
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
                'startLine' => 101,
                'endLine' => 101,
                'startTokenPos' => 291,
                'startFilePos' => 4730,
                'endTokenPos' => 291,
                'endFilePos' => 4733,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 101,
            'endLine' => 101,
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
 * @return \\Stripe\\Review the approved review
 */',
        'startLine' => 101,
        'endLine' => 108,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Review',
        'implementingClassName' => 'Stripe\\Review',
        'currentClassName' => 'Stripe\\Review',
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