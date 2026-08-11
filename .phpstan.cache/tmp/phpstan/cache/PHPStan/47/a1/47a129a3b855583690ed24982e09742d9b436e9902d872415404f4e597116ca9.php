<?php declare(strict_types = 1);

// osfsl-/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/LineItem.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Stripe\LineItem
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-ca3d79e71325c00774a047b11059fc342bcfbdd609cb62c0dfc08431d52b2e61-8.4.24-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Stripe\\LineItem',
        'filename' => '/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/LineItem.php',
      ),
    ),
    'namespace' => 'Stripe',
    'name' => 'Stripe\\LineItem',
    'shortName' => 'LineItem',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * A line item.
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object\'s type. Objects of the same type share the same value.
 * @property int $amount_discount Total discount amount applied. If no discounts were applied, defaults to 0.
 * @property int $amount_subtotal Total before any discounts or taxes are applied.
 * @property int $amount_tax Total tax amount applied. If no tax was applied, defaults to 0.
 * @property int $amount_total Total after discounts and taxes.
 * @property string $currency Three-letter <a href="https://www.iso.org/iso-4217-currency-codes.html">ISO currency code</a>, in lowercase. Must be a <a href="https://stripe.com/docs/currencies">supported currency</a>.
 * @property string $description An arbitrary string attached to the object. Often useful for displaying to users. Defaults to product name.
 * @property null|\\Stripe\\StripeObject[] $discounts The discounts applied to the line item.
 * @property null|\\Stripe\\Price $price The price used to generate the line item.
 * @property null|int $quantity The quantity of products being purchased.
 * @property null|\\Stripe\\StripeObject[] $taxes The taxes applied to the line item.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 23,
    'endLine' => 26,
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
        'declaringClassName' => 'Stripe\\LineItem',
        'implementingClassName' => 'Stripe\\LineItem',
        'name' => 'OBJECT_NAME',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'item\'',
          'attributes' => 
          array (
            'startLine' => 25,
            'endLine' => 25,
            'startTokenPos' => 27,
            'startFilePos' => 1367,
            'endTokenPos' => 27,
            'endFilePos' => 1372,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 25,
        'endLine' => 25,
        'startColumn' => 5,
        'endColumn' => 31,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
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