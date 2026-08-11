<?php declare(strict_types = 1);

// osfsl-/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/Service/InvoiceService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Stripe\Service\InvoiceService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-68cfb00956179ffec306d05e18bec25717e982b35d051b3060680530c85b8c44-8.4.24-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Stripe\\Service\\InvoiceService',
        'filename' => '/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/Service/InvoiceService.php',
      ),
    ),
    'namespace' => 'Stripe\\Service',
    'name' => 'Stripe\\Service\\InvoiceService',
    'shortName' => 'InvoiceService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @phpstan-import-type RequestOptionsArray from \\Stripe\\Util\\RequestOptions
 * @psalm-import-type RequestOptionsArray from \\Stripe\\Util\\RequestOptions
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 11,
    'endLine' => 365,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Stripe\\Service\\AbstractService',
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
                'startLine' => 25,
                'endLine' => 25,
                'startTokenPos' => 33,
                'startFilePos' => 800,
                'endTokenPos' => 33,
                'endFilePos' => 803,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 25,
            'endLine' => 25,
            'startColumn' => 25,
            'endColumn' => 38,
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
                'startLine' => 25,
                'endLine' => 25,
                'startTokenPos' => 40,
                'startFilePos' => 814,
                'endTokenPos' => 40,
                'endFilePos' => 817,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 25,
            'endLine' => 25,
            'startColumn' => 41,
            'endColumn' => 52,
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
 * You can list all invoices, or list the invoices for a specific customer. The
 * invoices are returned sorted by creation date, with the most recently created
 * invoices appearing first.
 *
 * @param null|array $params
 * @param null|RequestOptionsArray|\\Stripe\\Util\\RequestOptions $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Collection<\\Stripe\\Invoice>
 */',
        'startLine' => 25,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\Service',
        'declaringClassName' => 'Stripe\\Service\\InvoiceService',
        'implementingClassName' => 'Stripe\\Service\\InvoiceService',
        'currentClassName' => 'Stripe\\Service\\InvoiceService',
        'aliasName' => NULL,
      ),
      'allLines' => 
      array (
        'name' => 'allLines',
        'parameters' => 
        array (
          'parentId' => 
          array (
            'name' => 'parentId',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 44,
            'endLine' => 44,
            'startColumn' => 30,
            'endColumn' => 38,
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
                'startLine' => 44,
                'endLine' => 44,
                'startTokenPos' => 81,
                'startFilePos' => 1534,
                'endTokenPos' => 81,
                'endFilePos' => 1537,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 44,
            'endLine' => 44,
            'startColumn' => 41,
            'endColumn' => 54,
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
                'startLine' => 44,
                'endLine' => 44,
                'startTokenPos' => 88,
                'startFilePos' => 1548,
                'endTokenPos' => 88,
                'endFilePos' => 1551,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 44,
            'endLine' => 44,
            'startColumn' => 57,
            'endColumn' => 68,
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
 * When retrieving an invoice, you’ll get a <strong>lines</strong> property
 * containing the total count of line items and the first handful of those items.
 * There is also a URL where you can retrieve the full (paginated) list of line
 * items.
 *
 * @param string $parentId
 * @param null|array $params
 * @param null|RequestOptionsArray|\\Stripe\\Util\\RequestOptions $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Collection<\\Stripe\\InvoiceLineItem>
 */',
        'startLine' => 44,
        'endLine' => 47,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\Service',
        'declaringClassName' => 'Stripe\\Service\\InvoiceService',
        'implementingClassName' => 'Stripe\\Service\\InvoiceService',
        'currentClassName' => 'Stripe\\Service\\InvoiceService',
        'aliasName' => NULL,
      ),
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
                'startLine' => 62,
                'endLine' => 62,
                'startTokenPos' => 134,
                'startFilePos' => 2266,
                'endTokenPos' => 134,
                'endFilePos' => 2269,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 62,
            'endLine' => 62,
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
                'startLine' => 62,
                'endLine' => 62,
                'startTokenPos' => 141,
                'startFilePos' => 2280,
                'endTokenPos' => 141,
                'endFilePos' => 2283,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 62,
            'endLine' => 62,
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
 * This endpoint creates a draft invoice for a given customer. The invoice remains
 * a draft until you <a href="#finalize_invoice">finalize</a> the invoice, which
 * allows you to <a href="#pay_invoice">pay</a> or <a href="#send_invoice">send</a>
 * the invoice to your customers.
 *
 * @param null|array $params
 * @param null|RequestOptionsArray|\\Stripe\\Util\\RequestOptions $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Invoice
 */',
        'startLine' => 62,
        'endLine' => 65,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\Service',
        'declaringClassName' => 'Stripe\\Service\\InvoiceService',
        'implementingClassName' => 'Stripe\\Service\\InvoiceService',
        'currentClassName' => 'Stripe\\Service\\InvoiceService',
        'aliasName' => NULL,
      ),
      'createPreview' => 
      array (
        'name' => 'createPreview',
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
                'startTokenPos' => 179,
                'startFilePos' => 4309,
                'endTokenPos' => 179,
                'endFilePos' => 4312,
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
                'startLine' => 101,
                'endLine' => 101,
                'startTokenPos' => 186,
                'startFilePos' => 4323,
                'endTokenPos' => 186,
                'endFilePos' => 4326,
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
 * At any time, you can preview the upcoming invoice for a customer. This will show
 * you all the charges that are pending, including subscription renewal charges,
 * invoice item charges, etc. It will also show you any discounts that are
 * applicable to the invoice.
 *
 * Note that when you are viewing an upcoming invoice, you are simply viewing a
 * preview – the invoice has not yet been created. As such, the upcoming invoice
 * will not show up in invoice listing calls, and you cannot use the API to pay or
 * edit the invoice. If you want to change the amount that your customer will be
 * billed, you can add, remove, or update pending invoice items, or update the
 * customer’s discount.
 *
 * You can preview the effects of updating a subscription, including a preview of
 * what proration will take place. To ensure that the actual proration is
 * calculated exactly the same as the previewed proration, you should pass the
 * <code>subscription_details.proration_date</code> parameter when doing the actual
 * subscription update. The recommended way to get only the prorations being
 * previewed is to consider only proration line items where
 * <code>period[start]</code> is equal to the
 * <code>subscription_details.proration_date</code> value passed in the request.
 *
 * Note: Currency conversion calculations use the latest exchange rates. Exchange
 * rates may vary between the time of the preview and the time of the actual
 * invoice creation. <a href="https://docs.stripe.com/currencies/conversions">Learn
 * more</a>
 *
 * @param null|array $params
 * @param null|RequestOptionsArray|\\Stripe\\Util\\RequestOptions $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Invoice
 */',
        'startLine' => 101,
        'endLine' => 104,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\Service',
        'declaringClassName' => 'Stripe\\Service\\InvoiceService',
        'implementingClassName' => 'Stripe\\Service\\InvoiceService',
        'currentClassName' => 'Stripe\\Service\\InvoiceService',
        'aliasName' => NULL,
      ),
      'delete' => 
      array (
        'name' => 'delete',
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
            'startLine' => 120,
            'endLine' => 120,
            'startColumn' => 28,
            'endColumn' => 30,
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
                'startLine' => 120,
                'endLine' => 120,
                'startTokenPos' => 227,
                'startFilePos' => 5034,
                'endTokenPos' => 227,
                'endFilePos' => 5037,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 120,
            'endLine' => 120,
            'startColumn' => 33,
            'endColumn' => 46,
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
                'startLine' => 120,
                'endLine' => 120,
                'startTokenPos' => 234,
                'startFilePos' => 5048,
                'endTokenPos' => 234,
                'endFilePos' => 5051,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 120,
            'endLine' => 120,
            'startColumn' => 49,
            'endColumn' => 60,
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
 * Permanently deletes a one-off invoice draft. This cannot be undone. Attempts to
 * delete invoices that are no longer in a draft state will fail; once an invoice
 * has been finalized or if an invoice is for a subscription, it must be <a
 * href="#void_invoice">voided</a>.
 *
 * @param string $id
 * @param null|array $params
 * @param null|RequestOptionsArray|\\Stripe\\Util\\RequestOptions $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Invoice
 */',
        'startLine' => 120,
        'endLine' => 123,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\Service',
        'declaringClassName' => 'Stripe\\Service\\InvoiceService',
        'implementingClassName' => 'Stripe\\Service\\InvoiceService',
        'currentClassName' => 'Stripe\\Service\\InvoiceService',
        'aliasName' => NULL,
      ),
      'finalizeInvoice' => 
      array (
        'name' => 'finalizeInvoice',
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
            'startLine' => 138,
            'endLine' => 138,
            'startColumn' => 37,
            'endColumn' => 39,
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
                'startLine' => 138,
                'endLine' => 138,
                'startTokenPos' => 283,
                'startFilePos' => 5694,
                'endTokenPos' => 283,
                'endFilePos' => 5697,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 138,
            'endLine' => 138,
            'startColumn' => 42,
            'endColumn' => 55,
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
                'startLine' => 138,
                'endLine' => 138,
                'startTokenPos' => 290,
                'startFilePos' => 5708,
                'endTokenPos' => 290,
                'endFilePos' => 5711,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 138,
            'endLine' => 138,
            'startColumn' => 58,
            'endColumn' => 69,
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
 * Stripe automatically finalizes drafts before sending and attempting payment on
 * invoices. However, if you’d like to finalize a draft invoice manually, you can
 * do so using this method.
 *
 * @param string $id
 * @param null|array $params
 * @param null|RequestOptionsArray|\\Stripe\\Util\\RequestOptions $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Invoice
 */',
        'startLine' => 138,
        'endLine' => 141,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\Service',
        'declaringClassName' => 'Stripe\\Service\\InvoiceService',
        'implementingClassName' => 'Stripe\\Service\\InvoiceService',
        'currentClassName' => 'Stripe\\Service\\InvoiceService',
        'aliasName' => NULL,
      ),
      'markUncollectible' => 
      array (
        'name' => 'markUncollectible',
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
            'startLine' => 155,
            'endLine' => 155,
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
                'startLine' => 155,
                'endLine' => 155,
                'startTokenPos' => 339,
                'startFilePos' => 6297,
                'endTokenPos' => 339,
                'endFilePos' => 6300,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 155,
            'endLine' => 155,
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
                'startLine' => 155,
                'endLine' => 155,
                'startTokenPos' => 346,
                'startFilePos' => 6311,
                'endTokenPos' => 346,
                'endFilePos' => 6314,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 155,
            'endLine' => 155,
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
 * Marking an invoice as uncollectible is useful for keeping track of bad debts
 * that can be written off for accounting purposes.
 *
 * @param string $id
 * @param null|array $params
 * @param null|RequestOptionsArray|\\Stripe\\Util\\RequestOptions $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Invoice
 */',
        'startLine' => 155,
        'endLine' => 158,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\Service',
        'declaringClassName' => 'Stripe\\Service\\InvoiceService',
        'implementingClassName' => 'Stripe\\Service\\InvoiceService',
        'currentClassName' => 'Stripe\\Service\\InvoiceService',
        'aliasName' => NULL,
      ),
      'pay' => 
      array (
        'name' => 'pay',
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
            'startLine' => 175,
            'endLine' => 175,
            'startColumn' => 25,
            'endColumn' => 27,
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
                'startLine' => 175,
                'endLine' => 175,
                'startTokenPos' => 395,
                'startFilePos' => 7148,
                'endTokenPos' => 395,
                'endFilePos' => 7151,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 175,
            'endLine' => 175,
            'startColumn' => 30,
            'endColumn' => 43,
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
                'startLine' => 175,
                'endLine' => 175,
                'startTokenPos' => 402,
                'startFilePos' => 7162,
                'endTokenPos' => 402,
                'endFilePos' => 7165,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 175,
            'endLine' => 175,
            'startColumn' => 46,
            'endColumn' => 57,
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
 * Stripe automatically creates and then attempts to collect payment on invoices
 * for customers on subscriptions according to your <a
 * href="https://dashboard.stripe.com/account/billing/automatic">subscriptions
 * settings</a>. However, if you’d like to attempt payment on an invoice out of the
 * normal collection schedule or for some other reason, you can do so.
 *
 * @param string $id
 * @param null|array $params
 * @param null|RequestOptionsArray|\\Stripe\\Util\\RequestOptions $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Invoice
 */',
        'startLine' => 175,
        'endLine' => 178,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\Service',
        'declaringClassName' => 'Stripe\\Service\\InvoiceService',
        'implementingClassName' => 'Stripe\\Service\\InvoiceService',
        'currentClassName' => 'Stripe\\Service\\InvoiceService',
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
            'startLine' => 191,
            'endLine' => 191,
            'startColumn' => 30,
            'endColumn' => 32,
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
                'startLine' => 191,
                'endLine' => 191,
                'startTokenPos' => 451,
                'startFilePos' => 7645,
                'endTokenPos' => 451,
                'endFilePos' => 7648,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 191,
            'endLine' => 191,
            'startColumn' => 35,
            'endColumn' => 48,
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
                'startLine' => 191,
                'endLine' => 191,
                'startTokenPos' => 458,
                'startFilePos' => 7659,
                'endTokenPos' => 458,
                'endFilePos' => 7662,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 191,
            'endLine' => 191,
            'startColumn' => 51,
            'endColumn' => 62,
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
 * Retrieves the invoice with the given ID.
 *
 * @param string $id
 * @param null|array $params
 * @param null|RequestOptionsArray|\\Stripe\\Util\\RequestOptions $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Invoice
 */',
        'startLine' => 191,
        'endLine' => 194,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\Service',
        'declaringClassName' => 'Stripe\\Service\\InvoiceService',
        'implementingClassName' => 'Stripe\\Service\\InvoiceService',
        'currentClassName' => 'Stripe\\Service\\InvoiceService',
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
                'startLine' => 211,
                'endLine' => 211,
                'startTokenPos' => 504,
                'startFilePos' => 8573,
                'endTokenPos' => 504,
                'endFilePos' => 8576,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 211,
            'endLine' => 211,
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
                'startLine' => 211,
                'endLine' => 211,
                'startTokenPos' => 511,
                'startFilePos' => 8587,
                'endTokenPos' => 511,
                'endFilePos' => 8590,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 211,
            'endLine' => 211,
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
 * Search for invoices you’ve previously created using Stripe’s <a
 * href="/docs/search#search-query-language">Search Query Language</a>. Don’t use
 * search in read-after-write flows where strict consistency is necessary. Under
 * normal operating conditions, data is searchable in less than a minute.
 * Occasionally, propagation of new or updated data can be up to an hour behind
 * during outages. Search functionality is not available to merchants in India.
 *
 * @param null|array $params
 * @param null|RequestOptionsArray|\\Stripe\\Util\\RequestOptions $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\SearchResult<\\Stripe\\Invoice>
 */',
        'startLine' => 211,
        'endLine' => 214,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\Service',
        'declaringClassName' => 'Stripe\\Service\\InvoiceService',
        'implementingClassName' => 'Stripe\\Service\\InvoiceService',
        'currentClassName' => 'Stripe\\Service\\InvoiceService',
        'aliasName' => NULL,
      ),
      'sendInvoice' => 
      array (
        'name' => 'sendInvoice',
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
            'startLine' => 234,
            'endLine' => 234,
            'startColumn' => 33,
            'endColumn' => 35,
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
                'startLine' => 234,
                'endLine' => 234,
                'startTokenPos' => 552,
                'startFilePos' => 9564,
                'endTokenPos' => 552,
                'endFilePos' => 9567,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 234,
            'endLine' => 234,
            'startColumn' => 38,
            'endColumn' => 51,
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
                'startLine' => 234,
                'endLine' => 234,
                'startTokenPos' => 559,
                'startFilePos' => 9578,
                'endTokenPos' => 559,
                'endFilePos' => 9581,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 234,
            'endLine' => 234,
            'startColumn' => 54,
            'endColumn' => 65,
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
 * Stripe will automatically send invoices to customers according to your <a
 * href="https://dashboard.stripe.com/account/billing/automatic">subscriptions
 * settings</a>. However, if you’d like to manually send an invoice to your
 * customer out of the normal schedule, you can do so. When sending invoices that
 * have already been paid, there will be no reference to the payment in the email.
 *
 * Requests made in test-mode result in no emails being sent, despite sending an
 * <code>invoice.sent</code> event.
 *
 * @param string $id
 * @param null|array $params
 * @param null|RequestOptionsArray|\\Stripe\\Util\\RequestOptions $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Invoice
 */',
        'startLine' => 234,
        'endLine' => 237,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\Service',
        'declaringClassName' => 'Stripe\\Service\\InvoiceService',
        'implementingClassName' => 'Stripe\\Service\\InvoiceService',
        'currentClassName' => 'Stripe\\Service\\InvoiceService',
        'aliasName' => NULL,
      ),
      'upcoming' => 
      array (
        'name' => 'upcoming',
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
                'startLine' => 273,
                'endLine' => 273,
                'startTokenPos' => 605,
                'startFilePos' => 11633,
                'endTokenPos' => 605,
                'endFilePos' => 11636,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 273,
            'endLine' => 273,
            'startColumn' => 30,
            'endColumn' => 43,
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
                'startLine' => 273,
                'endLine' => 273,
                'startTokenPos' => 612,
                'startFilePos' => 11647,
                'endTokenPos' => 612,
                'endFilePos' => 11650,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 273,
            'endLine' => 273,
            'startColumn' => 46,
            'endColumn' => 57,
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
 * At any time, you can preview the upcoming invoice for a customer. This will show
 * you all the charges that are pending, including subscription renewal charges,
 * invoice item charges, etc. It will also show you any discounts that are
 * applicable to the invoice.
 *
 * Note that when you are viewing an upcoming invoice, you are simply viewing a
 * preview – the invoice has not yet been created. As such, the upcoming invoice
 * will not show up in invoice listing calls, and you cannot use the API to pay or
 * edit the invoice. If you want to change the amount that your customer will be
 * billed, you can add, remove, or update pending invoice items, or update the
 * customer’s discount.
 *
 * You can preview the effects of updating a subscription, including a preview of
 * what proration will take place. To ensure that the actual proration is
 * calculated exactly the same as the previewed proration, you should pass the
 * <code>subscription_details.proration_date</code> parameter when doing the actual
 * subscription update. The recommended way to get only the prorations being
 * previewed is to consider only proration line items where
 * <code>period[start]</code> is equal to the
 * <code>subscription_details.proration_date</code> value passed in the request.
 *
 * Note: Currency conversion calculations use the latest exchange rates. Exchange
 * rates may vary between the time of the preview and the time of the actual
 * invoice creation. <a href="https://docs.stripe.com/currencies/conversions">Learn
 * more</a>
 *
 * @param null|array $params
 * @param null|RequestOptionsArray|\\Stripe\\Util\\RequestOptions $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Invoice
 */',
        'startLine' => 273,
        'endLine' => 276,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\Service',
        'declaringClassName' => 'Stripe\\Service\\InvoiceService',
        'implementingClassName' => 'Stripe\\Service\\InvoiceService',
        'currentClassName' => 'Stripe\\Service\\InvoiceService',
        'aliasName' => NULL,
      ),
      'upcomingLines' => 
      array (
        'name' => 'upcomingLines',
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
                'startLine' => 291,
                'endLine' => 291,
                'startTokenPos' => 650,
                'startFilePos' => 12338,
                'endTokenPos' => 650,
                'endFilePos' => 12341,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 291,
            'endLine' => 291,
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
                'startLine' => 291,
                'endLine' => 291,
                'startTokenPos' => 657,
                'startFilePos' => 12352,
                'endTokenPos' => 657,
                'endFilePos' => 12355,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 291,
            'endLine' => 291,
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
 * When retrieving an upcoming invoice, you’ll get a <strong>lines</strong>
 * property containing the total count of line items and the first handful of those
 * items. There is also a URL where you can retrieve the full (paginated) list of
 * line items.
 *
 * @param null|array $params
 * @param null|RequestOptionsArray|\\Stripe\\Util\\RequestOptions $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Collection<\\Stripe\\InvoiceLineItem>
 */',
        'startLine' => 291,
        'endLine' => 294,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\Service',
        'declaringClassName' => 'Stripe\\Service\\InvoiceService',
        'implementingClassName' => 'Stripe\\Service\\InvoiceService',
        'currentClassName' => 'Stripe\\Service\\InvoiceService',
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
            'startLine' => 314,
            'endLine' => 314,
            'startColumn' => 28,
            'endColumn' => 30,
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
                'startLine' => 314,
                'endLine' => 314,
                'startTokenPos' => 698,
                'startFilePos' => 13296,
                'endTokenPos' => 698,
                'endFilePos' => 13299,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 314,
            'endLine' => 314,
            'startColumn' => 33,
            'endColumn' => 46,
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
                'startLine' => 314,
                'endLine' => 314,
                'startTokenPos' => 705,
                'startFilePos' => 13310,
                'endTokenPos' => 705,
                'endFilePos' => 13313,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 314,
            'endLine' => 314,
            'startColumn' => 49,
            'endColumn' => 60,
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
 * Draft invoices are fully editable. Once an invoice is <a
 * href="/docs/billing/invoices/workflow#finalized">finalized</a>, monetary values,
 * as well as <code>collection_method</code>, become uneditable.
 *
 * If you would like to stop the Stripe Billing engine from automatically
 * finalizing, reattempting payments on, sending reminders for, or <a
 * href="/docs/billing/invoices/reconciliation">automatically reconciling</a>
 * invoices, pass <code>auto_advance=false</code>.
 *
 * @param string $id
 * @param null|array $params
 * @param null|RequestOptionsArray|\\Stripe\\Util\\RequestOptions $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Invoice
 */',
        'startLine' => 314,
        'endLine' => 317,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\Service',
        'declaringClassName' => 'Stripe\\Service\\InvoiceService',
        'implementingClassName' => 'Stripe\\Service\\InvoiceService',
        'currentClassName' => 'Stripe\\Service\\InvoiceService',
        'aliasName' => NULL,
      ),
      'updateLine' => 
      array (
        'name' => 'updateLine',
        'parameters' => 
        array (
          'parentId' => 
          array (
            'name' => 'parentId',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 336,
            'endLine' => 336,
            'startColumn' => 32,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 336,
            'endLine' => 336,
            'startColumn' => 43,
            'endColumn' => 45,
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
                'startLine' => 336,
                'endLine' => 336,
                'startTokenPos' => 757,
                'startFilePos' => 14259,
                'endTokenPos' => 757,
                'endFilePos' => 14262,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 336,
            'endLine' => 336,
            'startColumn' => 48,
            'endColumn' => 61,
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
                'startLine' => 336,
                'endLine' => 336,
                'startTokenPos' => 764,
                'startFilePos' => 14273,
                'endTokenPos' => 764,
                'endFilePos' => 14276,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 336,
            'endLine' => 336,
            'startColumn' => 64,
            'endColumn' => 75,
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
 * Updates an invoice’s line item. Some fields, such as <code>tax_amounts</code>,
 * only live on the invoice line item, so they can only be updated through this
 * endpoint. Other fields, such as <code>amount</code>, live on both the invoice
 * item and the invoice line item, so updates on this endpoint will propagate to
 * the invoice item as well. Updating an invoice’s line item is only possible
 * before the invoice is finalized.
 *
 * @param string $parentId
 * @param string $id
 * @param null|array $params
 * @param null|RequestOptionsArray|\\Stripe\\Util\\RequestOptions $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\InvoiceLineItem
 */',
        'startLine' => 336,
        'endLine' => 339,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\Service',
        'declaringClassName' => 'Stripe\\Service\\InvoiceService',
        'implementingClassName' => 'Stripe\\Service\\InvoiceService',
        'currentClassName' => 'Stripe\\Service\\InvoiceService',
        'aliasName' => NULL,
      ),
      'voidInvoice' => 
      array (
        'name' => 'voidInvoice',
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
            'startLine' => 361,
            'endLine' => 361,
            'startColumn' => 33,
            'endColumn' => 35,
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
                'startLine' => 361,
                'endLine' => 361,
                'startTokenPos' => 816,
                'startFilePos' => 15420,
                'endTokenPos' => 816,
                'endFilePos' => 15423,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 361,
            'endLine' => 361,
            'startColumn' => 38,
            'endColumn' => 51,
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
                'startLine' => 361,
                'endLine' => 361,
                'startTokenPos' => 823,
                'startFilePos' => 15434,
                'endTokenPos' => 823,
                'endFilePos' => 15437,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 361,
            'endLine' => 361,
            'startColumn' => 54,
            'endColumn' => 65,
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
 * Mark a finalized invoice as void. This cannot be undone. Voiding an invoice is
 * similar to <a href="#delete_invoice">deletion</a>, however it only applies to
 * finalized invoices and maintains a papertrail where the invoice can still be
 * found.
 *
 * Consult with local regulations to determine whether and how an invoice might be
 * amended, canceled, or voided in the jurisdiction you’re doing business in. You
 * might need to <a href="#create_invoice">issue another invoice</a> or <a
 * href="#create_credit_note">credit note</a> instead. Stripe recommends that you
 * consult with your legal counsel for advice specific to your business.
 *
 * @param string $id
 * @param null|array $params
 * @param null|RequestOptionsArray|\\Stripe\\Util\\RequestOptions $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Invoice
 */',
        'startLine' => 361,
        'endLine' => 364,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe\\Service',
        'declaringClassName' => 'Stripe\\Service\\InvoiceService',
        'implementingClassName' => 'Stripe\\Service\\InvoiceService',
        'currentClassName' => 'Stripe\\Service\\InvoiceService',
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