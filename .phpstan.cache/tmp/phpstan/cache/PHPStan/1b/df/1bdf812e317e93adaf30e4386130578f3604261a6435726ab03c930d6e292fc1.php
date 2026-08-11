<?php declare(strict_types = 1);

// osfsl-/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/Invoice.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Stripe\Invoice
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-50ce36d939cd1c47db11a41388c26ec70daca43633346aab29d7c395e197bb9e-8.4.24-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Stripe\\Invoice',
        'filename' => '/var/www/projects/exprmt/vendor/composer/../stripe/stripe-php/lib/Invoice.php',
      ),
    ),
    'namespace' => 'Stripe',
    'name' => 'Stripe\\Invoice',
    'shortName' => 'Invoice',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Invoices are statements of amounts owed by a customer, and are either
 * generated one-off, or generated periodically from a subscription.
 *
 * They contain <a href="https://stripe.com/docs/api#invoiceitems">invoice items</a>, and proration adjustments
 * that may be caused by subscription upgrades/downgrades (if necessary).
 *
 * If your invoice is configured to be billed through automatic charges,
 * Stripe automatically finalizes your invoice and attempts payment. Note
 * that finalizing the invoice,
 * <a href="https://stripe.com/docs/invoicing/integration/automatic-advancement-collection">when automatic</a>, does
 * not happen immediately as the invoice is created. Stripe waits
 * until one hour after the last webhook was successfully sent (or the last
 * webhook timed out after failing). If you (and the platforms you may have
 * connected to) have no webhooks configured, Stripe waits one hour after
 * creation to finalize the invoice.
 *
 * If your invoice is configured to be billed by sending an email, then based on your
 * <a href="https://dashboard.stripe.com/account/billing/automatic">email settings</a>,
 * Stripe will email the invoice to your customer and await payment. These
 * emails can contain a link to a hosted page to pay the invoice.
 *
 * Stripe applies any customer credit on the account before determining the
 * amount due for the invoice (i.e., the amount that will be actually
 * charged). If the amount due for the invoice is less than Stripe\'s <a href="/docs/currencies#minimum-and-maximum-charge-amounts">minimum allowed charge
 * per currency</a>, the
 * invoice is automatically marked paid, and we add the amount due to the
 * customer\'s credit balance which is applied to the next invoice.
 *
 * More details on the customer\'s credit balance are
 * <a href="https://stripe.com/docs/billing/customer/balance">here</a>.
 *
 * Related guide: <a href="https://stripe.com/docs/billing/invoices/sending">Send invoices to customers</a>
 *
 * @property null|string $id Unique identifier for the object. This property is always present unless the invoice is an upcoming invoice. See <a href="https://stripe.com/docs/api/invoices/upcoming">Retrieve an upcoming invoice</a> for more details.
 * @property string $object String representing the object\'s type. Objects of the same type share the same value.
 * @property null|string $account_country The country of the business associated with this invoice, most often the business creating the invoice.
 * @property null|string $account_name The public name of the business associated with this invoice, most often the business creating the invoice.
 * @property null|(string|\\Stripe\\TaxId)[] $account_tax_ids The account tax IDs associated with the invoice. Only editable when the invoice is a draft.
 * @property int $amount_due Final amount due at this time for this invoice. If the invoice\'s total is smaller than the minimum charge amount, for example, or if there is account credit that can be applied to the invoice, the <code>amount_due</code> may be 0. If there is a positive <code>starting_balance</code> for the invoice (the customer owes money), the <code>amount_due</code> will also take that into account. The charge that gets generated for the invoice will be for the amount specified in <code>amount_due</code>.
 * @property int $amount_paid The amount, in cents (or local equivalent), that was paid.
 * @property int $amount_remaining The difference between amount_due and amount_paid, in cents (or local equivalent).
 * @property int $amount_shipping This is the sum of all the shipping amounts.
 * @property null|string|\\Stripe\\Application $application ID of the Connect Application that created the invoice.
 * @property null|int $application_fee_amount The fee in cents (or local equivalent) that will be applied to the invoice and transferred to the application owner\'s Stripe account when the invoice is paid.
 * @property int $attempt_count Number of payment attempts made for this invoice, from the perspective of the payment retry schedule. Any payment attempt counts as the first attempt, and subsequently only automatic retries increment the attempt count. In other words, manual payment attempts after the first attempt do not affect the retry schedule. If a failure is returned with a non-retryable return code, the invoice can no longer be retried unless a new payment method is obtained. Retries will continue to be scheduled, and attempt_count will continue to increment, but retries will only be executed if a new payment method is obtained.
 * @property bool $attempted Whether an attempt has been made to pay the invoice. An invoice is not attempted until 1 hour after the <code>invoice.created</code> webhook, for example, so you might not want to display that invoice as unpaid to your users.
 * @property null|bool $auto_advance Controls whether Stripe performs <a href="https://stripe.com/docs/invoicing/integration/automatic-advancement-collection">automatic collection</a> of the invoice. If <code>false</code>, the invoice\'s state doesn\'t automatically advance without an explicit action.
 * @property \\Stripe\\StripeObject $automatic_tax
 * @property null|string $billing_reason <p>Indicates the reason why the invoice was created.</p><p>* <code>manual</code>: Unrelated to a subscription, for example, created via the invoice editor. * <code>subscription</code>: No longer in use. Applies to subscriptions from before May 2018 where no distinction was made between updates, cycles, and thresholds. * <code>subscription_create</code>: A new subscription was created. * <code>subscription_cycle</code>: A subscription advanced into a new period. * <code>subscription_threshold</code>: A subscription reached a billing threshold. * <code>subscription_update</code>: A subscription was updated. * <code>upcoming</code>: Reserved for simulated invoices, per the upcoming invoice endpoint.</p>
 * @property null|string|\\Stripe\\Charge $charge ID of the latest charge generated for this invoice, if any.
 * @property string $collection_method Either <code>charge_automatically</code>, or <code>send_invoice</code>. When charging automatically, Stripe will attempt to pay this invoice using the default source attached to the customer. When sending an invoice, Stripe will email this invoice to the customer with payment instructions.
 * @property int $created Time at which the object was created. Measured in seconds since the Unix epoch.
 * @property string $currency Three-letter <a href="https://www.iso.org/iso-4217-currency-codes.html">ISO currency code</a>, in lowercase. Must be a <a href="https://stripe.com/docs/currencies">supported currency</a>.
 * @property null|\\Stripe\\StripeObject[] $custom_fields Custom fields displayed on the invoice.
 * @property null|string|\\Stripe\\Customer $customer The ID of the customer who will be billed.
 * @property null|\\Stripe\\StripeObject $customer_address The customer\'s address. Until the invoice is finalized, this field will equal <code>customer.address</code>. Once the invoice is finalized, this field will no longer be updated.
 * @property null|string $customer_email The customer\'s email. Until the invoice is finalized, this field will equal <code>customer.email</code>. Once the invoice is finalized, this field will no longer be updated.
 * @property null|string $customer_name The customer\'s name. Until the invoice is finalized, this field will equal <code>customer.name</code>. Once the invoice is finalized, this field will no longer be updated.
 * @property null|string $customer_phone The customer\'s phone number. Until the invoice is finalized, this field will equal <code>customer.phone</code>. Once the invoice is finalized, this field will no longer be updated.
 * @property null|\\Stripe\\StripeObject $customer_shipping The customer\'s shipping information. Until the invoice is finalized, this field will equal <code>customer.shipping</code>. Once the invoice is finalized, this field will no longer be updated.
 * @property null|string $customer_tax_exempt The customer\'s tax exempt status. Until the invoice is finalized, this field will equal <code>customer.tax_exempt</code>. Once the invoice is finalized, this field will no longer be updated.
 * @property null|\\Stripe\\StripeObject[] $customer_tax_ids The customer\'s tax IDs. Until the invoice is finalized, this field will contain the same tax IDs as <code>customer.tax_ids</code>. Once the invoice is finalized, this field will no longer be updated.
 * @property null|string|\\Stripe\\PaymentMethod $default_payment_method ID of the default payment method for the invoice. It must belong to the customer associated with the invoice. If not set, defaults to the subscription\'s default payment method, if any, or to the default payment method in the customer\'s invoice settings.
 * @property null|string|\\Stripe\\Account|\\Stripe\\BankAccount|\\Stripe\\Card|\\Stripe\\Source $default_source ID of the default payment source for the invoice. It must belong to the customer associated with the invoice and be in a chargeable state. If not set, defaults to the subscription\'s default source, if any, or to the customer\'s default source.
 * @property \\Stripe\\TaxRate[] $default_tax_rates The tax rates applied to this invoice, if any.
 * @property null|string $description An arbitrary string attached to the object. Often useful for displaying to users. Referenced as \'memo\' in the Dashboard.
 * @property null|\\Stripe\\Discount $discount Describes the current discount applied to this invoice, if there is one. Not populated if there are multiple discounts.
 * @property (string|\\Stripe\\Discount)[] $discounts The discounts applied to the invoice. Line item discounts are applied before invoice discounts. Use <code>expand[]=discounts</code> to expand each discount.
 * @property null|int $due_date The date on which payment for this invoice is due. This value will be <code>null</code> for invoices where <code>collection_method=charge_automatically</code>.
 * @property null|int $effective_at The date when this invoice is in effect. Same as <code>finalized_at</code> unless overwritten. When defined, this value replaces the system-generated \'Date of issue\' printed on the invoice PDF and receipt.
 * @property null|int $ending_balance Ending customer balance after the invoice is finalized. Invoices are finalized approximately an hour after successful webhook delivery or when payment collection is attempted for the invoice. If the invoice has not been finalized yet, this will be null.
 * @property null|string $footer Footer displayed on the invoice.
 * @property null|\\Stripe\\StripeObject $from_invoice Details of the invoice that was cloned. See the <a href="https://stripe.com/docs/invoicing/invoice-revisions">revision documentation</a> for more details.
 * @property null|string $hosted_invoice_url The URL for the hosted invoice page, which allows customers to view and pay an invoice. If the invoice has not been finalized yet, this will be null.
 * @property null|string $invoice_pdf The link to download the PDF for the invoice. If the invoice has not been finalized yet, this will be null.
 * @property \\Stripe\\StripeObject $issuer
 * @property null|\\Stripe\\StripeObject $last_finalization_error The error encountered during the previous attempt to finalize the invoice. This field is cleared when the invoice is successfully finalized.
 * @property null|string|\\Stripe\\Invoice $latest_revision The ID of the most recent non-draft revision of this invoice
 * @property \\Stripe\\Collection<\\Stripe\\InvoiceLineItem> $lines The individual line items that make up the invoice. <code>lines</code> is sorted as follows: (1) pending invoice items (including prorations) in reverse chronological order, (2) subscription items in reverse chronological order, and (3) invoice items added after invoice creation in chronological order.
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property null|\\Stripe\\StripeObject $metadata Set of <a href="https://stripe.com/docs/api/metadata">key-value pairs</a> that you can attach to an object. This can be useful for storing additional information about the object in a structured format.
 * @property null|int $next_payment_attempt The time at which payment will next be attempted. This value will be <code>null</code> for invoices where <code>collection_method=send_invoice</code>.
 * @property null|string $number A unique, identifying string that appears on emails sent to the customer for this invoice. This starts with the customer\'s unique invoice_prefix if it is specified.
 * @property null|string|\\Stripe\\Account $on_behalf_of The account (if any) for which the funds of the invoice payment are intended. If set, the invoice will be presented with the branding and support information of the specified account. See the <a href="https://stripe.com/docs/billing/invoices/connect">Invoices with Connect</a> documentation for details.
 * @property bool $paid Whether payment was successfully collected for this invoice. An invoice can be paid (most commonly) with a charge or with credit from the customer\'s account balance.
 * @property bool $paid_out_of_band Returns true if the invoice was manually marked paid, returns false if the invoice hasn\'t been paid yet or was paid on Stripe.
 * @property null|string|\\Stripe\\PaymentIntent $payment_intent The PaymentIntent associated with this invoice. The PaymentIntent is generated when the invoice is finalized, and can then be used to pay the invoice. Note that voiding an invoice will cancel the PaymentIntent.
 * @property \\Stripe\\StripeObject $payment_settings
 * @property int $period_end End of the usage period during which invoice items were added to this invoice. This looks back one period for a subscription invoice. Use the <a href="/api/invoices/line_item#invoice_line_item_object-period">line item period</a> to get the service period for each price.
 * @property int $period_start Start of the usage period during which invoice items were added to this invoice. This looks back one period for a subscription invoice. Use the <a href="/api/invoices/line_item#invoice_line_item_object-period">line item period</a> to get the service period for each price.
 * @property int $post_payment_credit_notes_amount Total amount of all post-payment credit notes issued for this invoice.
 * @property int $pre_payment_credit_notes_amount Total amount of all pre-payment credit notes issued for this invoice.
 * @property null|string|\\Stripe\\Quote $quote The quote this invoice was generated from.
 * @property null|string $receipt_number This is the transaction number that appears on email receipts sent for this invoice.
 * @property null|\\Stripe\\StripeObject $rendering The rendering-related settings that control how the invoice is displayed on customer-facing surfaces such as PDF and Hosted Invoice Page.
 * @property null|\\Stripe\\StripeObject $shipping_cost The details of the cost of shipping, including the ShippingRate applied on the invoice.
 * @property null|\\Stripe\\StripeObject $shipping_details Shipping details for the invoice. The Invoice PDF will use the <code>shipping_details</code> value if it is set, otherwise the PDF will render the shipping address from the customer.
 * @property int $starting_balance Starting customer balance before the invoice is finalized. If the invoice has not been finalized yet, this will be the current customer balance. For revision invoices, this also includes any customer balance that was applied to the original invoice.
 * @property null|string $statement_descriptor Extra information about an invoice for the customer\'s credit card statement.
 * @property null|string $status The status of the invoice, one of <code>draft</code>, <code>open</code>, <code>paid</code>, <code>uncollectible</code>, or <code>void</code>. <a href="https://stripe.com/docs/billing/invoices/workflow#workflow-overview">Learn more</a>
 * @property \\Stripe\\StripeObject $status_transitions
 * @property null|string|\\Stripe\\Subscription $subscription The subscription that this invoice was prepared for, if any.
 * @property null|\\Stripe\\StripeObject $subscription_details Details about the subscription that created this invoice.
 * @property null|int $subscription_proration_date Only set for upcoming invoices that preview prorations. The time used to calculate prorations.
 * @property int $subtotal Total of all subscriptions, invoice items, and prorations on the invoice before any invoice level discount or exclusive tax is applied. Item discounts are already incorporated
 * @property null|int $subtotal_excluding_tax The integer amount in cents (or local equivalent) representing the subtotal of the invoice before any invoice level discount or tax is applied. Item discounts are already incorporated
 * @property null|int $tax The amount of tax on this invoice. This is the sum of all the tax amounts on this invoice.
 * @property null|string|\\Stripe\\TestHelpers\\TestClock $test_clock ID of the test clock this invoice belongs to.
 * @property null|\\Stripe\\StripeObject $threshold_reason
 * @property int $total Total after discounts and taxes.
 * @property null|\\Stripe\\StripeObject[] $total_discount_amounts The aggregate amounts calculated per discount across all line items.
 * @property null|int $total_excluding_tax The integer amount in cents (or local equivalent) representing the total amount of the invoice including all discounts but excluding all tax.
 * @property \\Stripe\\StripeObject[] $total_tax_amounts The aggregate amounts calculated per tax rate for all line items.
 * @property null|\\Stripe\\StripeObject $transfer_data The account (if any) the payment will be attributed to for tax reporting, and where funds from the payment will be transferred to for the invoice.
 * @property null|int $webhooks_delivered_at Invoices are automatically paid or sent 1 hour after webhooks are delivered, or until all webhook delivery attempts have <a href="https://stripe.com/docs/billing/webhooks#understand">been exhausted</a>. This field tracks the time when webhooks for this invoice were successfully delivered. If the invoice had no webhooks to deliver, this will be set while the invoice is being created.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 124,
    'endLine' => 444,
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
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'OBJECT_NAME',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'invoice\'',
          'attributes' => 
          array (
            'startLine' => 126,
            'endLine' => 126,
            'startTokenPos' => 27,
            'startFilePos' => 18464,
            'endTokenPos' => 27,
            'endFilePos' => 18472,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 126,
        'endLine' => 126,
        'startColumn' => 5,
        'endColumn' => 34,
      ),
      'BILLING_REASON_AUTOMATIC_PENDING_INVOICE_ITEM_INVOICE' => 
      array (
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'BILLING_REASON_AUTOMATIC_PENDING_INVOICE_ITEM_INVOICE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'automatic_pending_invoice_item_invoice\'',
          'attributes' => 
          array (
            'startLine' => 132,
            'endLine' => 132,
            'startTokenPos' => 51,
            'startFilePos' => 18641,
            'endTokenPos' => 51,
            'endFilePos' => 18680,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 132,
        'endLine' => 132,
        'startColumn' => 5,
        'endColumn' => 107,
      ),
      'BILLING_REASON_MANUAL' => 
      array (
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'BILLING_REASON_MANUAL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'manual\'',
          'attributes' => 
          array (
            'startLine' => 133,
            'endLine' => 133,
            'startTokenPos' => 60,
            'startFilePos' => 18717,
            'endTokenPos' => 60,
            'endFilePos' => 18724,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 133,
        'endLine' => 133,
        'startColumn' => 5,
        'endColumn' => 43,
      ),
      'BILLING_REASON_QUOTE_ACCEPT' => 
      array (
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'BILLING_REASON_QUOTE_ACCEPT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'quote_accept\'',
          'attributes' => 
          array (
            'startLine' => 134,
            'endLine' => 134,
            'startTokenPos' => 69,
            'startFilePos' => 18767,
            'endTokenPos' => 69,
            'endFilePos' => 18780,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 134,
        'endLine' => 134,
        'startColumn' => 5,
        'endColumn' => 55,
      ),
      'BILLING_REASON_SUBSCRIPTION' => 
      array (
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'BILLING_REASON_SUBSCRIPTION',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'subscription\'',
          'attributes' => 
          array (
            'startLine' => 135,
            'endLine' => 135,
            'startTokenPos' => 78,
            'startFilePos' => 18823,
            'endTokenPos' => 78,
            'endFilePos' => 18836,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 135,
        'endLine' => 135,
        'startColumn' => 5,
        'endColumn' => 55,
      ),
      'BILLING_REASON_SUBSCRIPTION_CREATE' => 
      array (
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'BILLING_REASON_SUBSCRIPTION_CREATE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'subscription_create\'',
          'attributes' => 
          array (
            'startLine' => 136,
            'endLine' => 136,
            'startTokenPos' => 87,
            'startFilePos' => 18886,
            'endTokenPos' => 87,
            'endFilePos' => 18906,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 136,
        'endLine' => 136,
        'startColumn' => 5,
        'endColumn' => 69,
      ),
      'BILLING_REASON_SUBSCRIPTION_CYCLE' => 
      array (
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'BILLING_REASON_SUBSCRIPTION_CYCLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'subscription_cycle\'',
          'attributes' => 
          array (
            'startLine' => 137,
            'endLine' => 137,
            'startTokenPos' => 96,
            'startFilePos' => 18955,
            'endTokenPos' => 96,
            'endFilePos' => 18974,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 137,
        'endLine' => 137,
        'startColumn' => 5,
        'endColumn' => 67,
      ),
      'BILLING_REASON_SUBSCRIPTION_THRESHOLD' => 
      array (
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'BILLING_REASON_SUBSCRIPTION_THRESHOLD',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'subscription_threshold\'',
          'attributes' => 
          array (
            'startLine' => 138,
            'endLine' => 138,
            'startTokenPos' => 105,
            'startFilePos' => 19027,
            'endTokenPos' => 105,
            'endFilePos' => 19050,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 138,
        'endLine' => 138,
        'startColumn' => 5,
        'endColumn' => 75,
      ),
      'BILLING_REASON_SUBSCRIPTION_UPDATE' => 
      array (
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'BILLING_REASON_SUBSCRIPTION_UPDATE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'subscription_update\'',
          'attributes' => 
          array (
            'startLine' => 139,
            'endLine' => 139,
            'startTokenPos' => 114,
            'startFilePos' => 19100,
            'endTokenPos' => 114,
            'endFilePos' => 19120,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 139,
        'endLine' => 139,
        'startColumn' => 5,
        'endColumn' => 69,
      ),
      'BILLING_REASON_UPCOMING' => 
      array (
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'BILLING_REASON_UPCOMING',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'upcoming\'',
          'attributes' => 
          array (
            'startLine' => 140,
            'endLine' => 140,
            'startTokenPos' => 123,
            'startFilePos' => 19159,
            'endTokenPos' => 123,
            'endFilePos' => 19168,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 140,
        'endLine' => 140,
        'startColumn' => 5,
        'endColumn' => 47,
      ),
      'COLLECTION_METHOD_CHARGE_AUTOMATICALLY' => 
      array (
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'COLLECTION_METHOD_CHARGE_AUTOMATICALLY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'charge_automatically\'',
          'attributes' => 
          array (
            'startLine' => 142,
            'endLine' => 142,
            'startTokenPos' => 132,
            'startFilePos' => 19223,
            'endTokenPos' => 132,
            'endFilePos' => 19244,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 142,
        'endLine' => 142,
        'startColumn' => 5,
        'endColumn' => 74,
      ),
      'COLLECTION_METHOD_SEND_INVOICE' => 
      array (
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'COLLECTION_METHOD_SEND_INVOICE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'send_invoice\'',
          'attributes' => 
          array (
            'startLine' => 143,
            'endLine' => 143,
            'startTokenPos' => 141,
            'startFilePos' => 19290,
            'endTokenPos' => 141,
            'endFilePos' => 19303,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 143,
        'endLine' => 143,
        'startColumn' => 5,
        'endColumn' => 58,
      ),
      'CUSTOMER_TAX_EXEMPT_EXEMPT' => 
      array (
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'CUSTOMER_TAX_EXEMPT_EXEMPT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'exempt\'',
          'attributes' => 
          array (
            'startLine' => 145,
            'endLine' => 145,
            'startTokenPos' => 150,
            'startFilePos' => 19346,
            'endTokenPos' => 150,
            'endFilePos' => 19353,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 145,
        'endLine' => 145,
        'startColumn' => 5,
        'endColumn' => 48,
      ),
      'CUSTOMER_TAX_EXEMPT_NONE' => 
      array (
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'CUSTOMER_TAX_EXEMPT_NONE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'none\'',
          'attributes' => 
          array (
            'startLine' => 146,
            'endLine' => 146,
            'startTokenPos' => 159,
            'startFilePos' => 19393,
            'endTokenPos' => 159,
            'endFilePos' => 19398,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 146,
        'endLine' => 146,
        'startColumn' => 5,
        'endColumn' => 44,
      ),
      'CUSTOMER_TAX_EXEMPT_REVERSE' => 
      array (
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'CUSTOMER_TAX_EXEMPT_REVERSE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'reverse\'',
          'attributes' => 
          array (
            'startLine' => 147,
            'endLine' => 147,
            'startTokenPos' => 168,
            'startFilePos' => 19441,
            'endTokenPos' => 168,
            'endFilePos' => 19449,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 147,
        'endLine' => 147,
        'startColumn' => 5,
        'endColumn' => 50,
      ),
      'STATUS_DRAFT' => 
      array (
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'STATUS_DRAFT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'draft\'',
          'attributes' => 
          array (
            'startLine' => 149,
            'endLine' => 149,
            'startTokenPos' => 177,
            'startFilePos' => 19478,
            'endTokenPos' => 177,
            'endFilePos' => 19484,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 149,
        'endLine' => 149,
        'startColumn' => 5,
        'endColumn' => 33,
      ),
      'STATUS_OPEN' => 
      array (
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'STATUS_OPEN',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'open\'',
          'attributes' => 
          array (
            'startLine' => 150,
            'endLine' => 150,
            'startTokenPos' => 186,
            'startFilePos' => 19511,
            'endTokenPos' => 186,
            'endFilePos' => 19516,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 150,
        'endLine' => 150,
        'startColumn' => 5,
        'endColumn' => 31,
      ),
      'STATUS_PAID' => 
      array (
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'STATUS_PAID',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'paid\'',
          'attributes' => 
          array (
            'startLine' => 151,
            'endLine' => 151,
            'startTokenPos' => 195,
            'startFilePos' => 19543,
            'endTokenPos' => 195,
            'endFilePos' => 19548,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 151,
        'endLine' => 151,
        'startColumn' => 5,
        'endColumn' => 31,
      ),
      'STATUS_UNCOLLECTIBLE' => 
      array (
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'STATUS_UNCOLLECTIBLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'uncollectible\'',
          'attributes' => 
          array (
            'startLine' => 152,
            'endLine' => 152,
            'startTokenPos' => 204,
            'startFilePos' => 19584,
            'endTokenPos' => 204,
            'endFilePos' => 19598,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 152,
        'endLine' => 152,
        'startColumn' => 5,
        'endColumn' => 49,
      ),
      'STATUS_VOID' => 
      array (
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'STATUS_VOID',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'void\'',
          'attributes' => 
          array (
            'startLine' => 153,
            'endLine' => 153,
            'startTokenPos' => 213,
            'startFilePos' => 19625,
            'endTokenPos' => 213,
            'endFilePos' => 19630,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 153,
        'endLine' => 153,
        'startColumn' => 5,
        'endColumn' => 31,
      ),
      'BILLING_CHARGE_AUTOMATICALLY' => 
      array (
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'BILLING_CHARGE_AUTOMATICALLY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'charge_automatically\'',
          'attributes' => 
          array (
            'startLine' => 272,
            'endLine' => 272,
            'startTokenPos' => 645,
            'startFilePos' => 23839,
            'endTokenPos' => 645,
            'endFilePos' => 23860,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 272,
        'endLine' => 272,
        'startColumn' => 5,
        'endColumn' => 64,
      ),
      'BILLING_SEND_INVOICE' => 
      array (
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'BILLING_SEND_INVOICE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'send_invoice\'',
          'attributes' => 
          array (
            'startLine' => 273,
            'endLine' => 273,
            'startTokenPos' => 654,
            'startFilePos' => 23896,
            'endTokenPos' => 654,
            'endFilePos' => 23909,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 273,
        'endLine' => 273,
        'startColumn' => 5,
        'endColumn' => 48,
      ),
      'PATH_LINES' => 
      array (
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'name' => 'PATH_LINES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'/lines\'',
          'attributes' => 
          array (
            'startLine' => 429,
            'endLine' => 429,
            'startTokenPos' => 1439,
            'startFilePos' => 28696,
            'endTokenPos' => 1439,
            'endFilePos' => 28703,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 429,
        'endLine' => 429,
        'startColumn' => 5,
        'endColumn' => 32,
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
                'startLine' => 168,
                'endLine' => 168,
                'startTokenPos' => 230,
                'startFilePos' => 20211,
                'endTokenPos' => 230,
                'endFilePos' => 20214,
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
                'startLine' => 168,
                'endLine' => 168,
                'startTokenPos' => 237,
                'startFilePos' => 20228,
                'endTokenPos' => 237,
                'endFilePos' => 20231,
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
 * This endpoint creates a draft invoice for a given customer. The invoice remains
 * a draft until you <a href="#finalize_invoice">finalize</a> the invoice, which
 * allows you to <a href="#pay_invoice">pay</a> or <a href="#send_invoice">send</a>
 * the invoice to your customers.
 *
 * @param null|array $params
 * @param null|array|string $options
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Invoice the created resource
 */',
        'startLine' => 168,
        'endLine' => 178,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'currentClassName' => 'Stripe\\Invoice',
        'aliasName' => NULL,
      ),
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
                'startLine' => 193,
                'endLine' => 193,
                'startTokenPos' => 332,
                'startFilePos' => 21120,
                'endTokenPos' => 332,
                'endFilePos' => 21123,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 193,
            'endLine' => 193,
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
                'startLine' => 193,
                'endLine' => 193,
                'startTokenPos' => 339,
                'startFilePos' => 21134,
                'endTokenPos' => 339,
                'endFilePos' => 21137,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 193,
            'endLine' => 193,
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
 * Permanently deletes a one-off invoice draft. This cannot be undone. Attempts to
 * delete invoices that are no longer in a draft state will fail; once an invoice
 * has been finalized or if an invoice is for a subscription, it must be <a
 * href="#void_invoice">voided</a>.
 *
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Invoice the deleted resource
 */',
        'startLine' => 193,
        'endLine' => 202,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'currentClassName' => 'Stripe\\Invoice',
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
                'startLine' => 216,
                'endLine' => 216,
                'startTokenPos' => 422,
                'startFilePos' => 21872,
                'endTokenPos' => 422,
                'endFilePos' => 21875,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 216,
            'endLine' => 216,
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
                'startLine' => 216,
                'endLine' => 216,
                'startTokenPos' => 429,
                'startFilePos' => 21886,
                'endTokenPos' => 429,
                'endFilePos' => 21889,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 216,
            'endLine' => 216,
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
 * You can list all invoices, or list the invoices for a specific customer. The
 * invoices are returned sorted by creation date, with the most recently created
 * invoices appearing first.
 *
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Collection<\\Stripe\\Invoice> of ApiResources
 */',
        'startLine' => 216,
        'endLine' => 221,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'currentClassName' => 'Stripe\\Invoice',
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
            'startLine' => 233,
            'endLine' => 233,
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
                'startLine' => 233,
                'endLine' => 233,
                'startTokenPos' => 485,
                'startFilePos' => 22416,
                'endTokenPos' => 485,
                'endFilePos' => 22419,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 233,
            'endLine' => 233,
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
 * Retrieves the invoice with the given ID.
 *
 * @param array|string $id the ID of the API resource to retrieve, or an options array containing an `id` key
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Invoice
 */',
        'startLine' => 233,
        'endLine' => 240,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'currentClassName' => 'Stripe\\Invoice',
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
            'startLine' => 260,
            'endLine' => 260,
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
                'startLine' => 260,
                'endLine' => 260,
                'startTokenPos' => 548,
                'startFilePos' => 23451,
                'endTokenPos' => 548,
                'endFilePos' => 23454,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 260,
            'endLine' => 260,
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
                'startLine' => 260,
                'endLine' => 260,
                'startTokenPos' => 555,
                'startFilePos' => 23465,
                'endTokenPos' => 555,
                'endFilePos' => 23468,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 260,
            'endLine' => 260,
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
 * Draft invoices are fully editable. Once an invoice is <a
 * href="/docs/billing/invoices/workflow#finalized">finalized</a>, monetary values,
 * as well as <code>collection_method</code>, become uneditable.
 *
 * If you would like to stop the Stripe Billing engine from automatically
 * finalizing, reattempting payments on, sending reminders for, or <a
 * href="/docs/billing/invoices/reconciliation">automatically reconciling</a>
 * invoices, pass <code>auto_advance=false</code>.
 *
 * @param string $id the ID of the resource to update
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Invoice the updated resource
 */',
        'startLine' => 260,
        'endLine' => 270,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'currentClassName' => 'Stripe\\Invoice',
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
                'startLine' => 283,
                'endLine' => 283,
                'startTokenPos' => 671,
                'startFilePos' => 24188,
                'endTokenPos' => 671,
                'endFilePos' => 24191,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 283,
            'endLine' => 283,
            'startColumn' => 42,
            'endColumn' => 55,
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
                'startLine' => 283,
                'endLine' => 283,
                'startTokenPos' => 678,
                'startFilePos' => 24202,
                'endTokenPos' => 678,
                'endFilePos' => 24205,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 283,
            'endLine' => 283,
            'startColumn' => 58,
            'endColumn' => 69,
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
 * @return \\Stripe\\Invoice the created invoice
 */',
        'startLine' => 283,
        'endLine' => 291,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'currentClassName' => 'Stripe\\Invoice',
        'aliasName' => NULL,
      ),
      'finalizeInvoice' => 
      array (
        'name' => 'finalizeInvoice',
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
                'startLine' => 301,
                'endLine' => 301,
                'startTokenPos' => 769,
                'startFilePos' => 24780,
                'endTokenPos' => 769,
                'endFilePos' => 24783,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 301,
            'endLine' => 301,
            'startColumn' => 37,
            'endColumn' => 50,
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
                'startLine' => 301,
                'endLine' => 301,
                'startTokenPos' => 776,
                'startFilePos' => 24794,
                'endTokenPos' => 776,
                'endFilePos' => 24797,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 301,
            'endLine' => 301,
            'startColumn' => 53,
            'endColumn' => 64,
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
 * @return \\Stripe\\Invoice the finalized invoice
 */',
        'startLine' => 301,
        'endLine' => 308,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'currentClassName' => 'Stripe\\Invoice',
        'aliasName' => NULL,
      ),
      'markUncollectible' => 
      array (
        'name' => 'markUncollectible',
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
                'startLine' => 318,
                'endLine' => 318,
                'startTokenPos' => 853,
                'startFilePos' => 25291,
                'endTokenPos' => 853,
                'endFilePos' => 25294,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 318,
            'endLine' => 318,
            'startColumn' => 39,
            'endColumn' => 52,
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
                'startLine' => 318,
                'endLine' => 318,
                'startTokenPos' => 860,
                'startFilePos' => 25305,
                'endTokenPos' => 860,
                'endFilePos' => 25308,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 318,
            'endLine' => 318,
            'startColumn' => 55,
            'endColumn' => 66,
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
 * @return \\Stripe\\Invoice the uncollectible invoice
 */',
        'startLine' => 318,
        'endLine' => 325,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'currentClassName' => 'Stripe\\Invoice',
        'aliasName' => NULL,
      ),
      'pay' => 
      array (
        'name' => 'pay',
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
                'startLine' => 335,
                'endLine' => 335,
                'startTokenPos' => 937,
                'startFilePos' => 25789,
                'endTokenPos' => 937,
                'endFilePos' => 25792,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 335,
            'endLine' => 335,
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
                'startLine' => 335,
                'endLine' => 335,
                'startTokenPos' => 944,
                'startFilePos' => 25803,
                'endTokenPos' => 944,
                'endFilePos' => 25806,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 335,
            'endLine' => 335,
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
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Invoice the paid invoice
 */',
        'startLine' => 335,
        'endLine' => 342,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'currentClassName' => 'Stripe\\Invoice',
        'aliasName' => NULL,
      ),
      'sendInvoice' => 
      array (
        'name' => 'sendInvoice',
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
                'startLine' => 352,
                'endLine' => 352,
                'startTokenPos' => 1021,
                'startFilePos' => 26280,
                'endTokenPos' => 1021,
                'endFilePos' => 26283,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 352,
            'endLine' => 352,
            'startColumn' => 33,
            'endColumn' => 46,
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
                'startLine' => 352,
                'endLine' => 352,
                'startTokenPos' => 1028,
                'startFilePos' => 26294,
                'endTokenPos' => 1028,
                'endFilePos' => 26297,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 352,
            'endLine' => 352,
            'startColumn' => 49,
            'endColumn' => 60,
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
 * @return \\Stripe\\Invoice the sent invoice
 */',
        'startLine' => 352,
        'endLine' => 359,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'currentClassName' => 'Stripe\\Invoice',
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
                'startLine' => 369,
                'endLine' => 369,
                'startTokenPos' => 1107,
                'startFilePos' => 26780,
                'endTokenPos' => 1107,
                'endFilePos' => 26783,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 369,
            'endLine' => 369,
            'startColumn' => 37,
            'endColumn' => 50,
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
                'startLine' => 369,
                'endLine' => 369,
                'startTokenPos' => 1114,
                'startFilePos' => 26794,
                'endTokenPos' => 1114,
                'endFilePos' => 26797,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 369,
            'endLine' => 369,
            'startColumn' => 53,
            'endColumn' => 64,
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
 * @return \\Stripe\\Invoice the upcoming invoice
 */',
        'startLine' => 369,
        'endLine' => 377,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'currentClassName' => 'Stripe\\Invoice',
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
                'startLine' => 387,
                'endLine' => 387,
                'startTokenPos' => 1207,
                'startFilePos' => 27403,
                'endTokenPos' => 1207,
                'endFilePos' => 27406,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 387,
            'endLine' => 387,
            'startColumn' => 42,
            'endColumn' => 55,
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
                'startLine' => 387,
                'endLine' => 387,
                'startTokenPos' => 1214,
                'startFilePos' => 27417,
                'endTokenPos' => 1214,
                'endFilePos' => 27420,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 387,
            'endLine' => 387,
            'startColumn' => 58,
            'endColumn' => 69,
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
 * @return \\Stripe\\Collection<\\Stripe\\InvoiceLineItem> list of invoice line items
 */',
        'startLine' => 387,
        'endLine' => 395,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'currentClassName' => 'Stripe\\Invoice',
        'aliasName' => NULL,
      ),
      'voidInvoice' => 
      array (
        'name' => 'voidInvoice',
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
                'startLine' => 405,
                'endLine' => 405,
                'startTokenPos' => 1305,
                'startFilePos' => 27987,
                'endTokenPos' => 1305,
                'endFilePos' => 27990,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 405,
            'endLine' => 405,
            'startColumn' => 33,
            'endColumn' => 46,
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
                'startLine' => 405,
                'endLine' => 405,
                'startTokenPos' => 1312,
                'startFilePos' => 28001,
                'endTokenPos' => 1312,
                'endFilePos' => 28004,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 405,
            'endLine' => 405,
            'startColumn' => 49,
            'endColumn' => 60,
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
 * @return \\Stripe\\Invoice the voided invoice
 */',
        'startLine' => 405,
        'endLine' => 412,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'currentClassName' => 'Stripe\\Invoice',
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
                'startLine' => 422,
                'endLine' => 422,
                'startTokenPos' => 1391,
                'startFilePos' => 28513,
                'endTokenPos' => 1391,
                'endFilePos' => 28516,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 422,
            'endLine' => 422,
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
                'startLine' => 422,
                'endLine' => 422,
                'startTokenPos' => 1398,
                'startFilePos' => 28527,
                'endTokenPos' => 1398,
                'endFilePos' => 28530,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 422,
            'endLine' => 422,
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
 * @return \\Stripe\\SearchResult<\\Stripe\\Invoice> the invoice search results
 */',
        'startLine' => 422,
        'endLine' => 427,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'currentClassName' => 'Stripe\\Invoice',
        'aliasName' => NULL,
      ),
      'allLines' => 
      array (
        'name' => 'allLines',
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
            'startLine' => 440,
            'endLine' => 440,
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
                'startLine' => 440,
                'endLine' => 440,
                'startTokenPos' => 1459,
                'startFilePos' => 29112,
                'endTokenPos' => 1459,
                'endFilePos' => 29115,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 440,
            'endLine' => 440,
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
                'startLine' => 440,
                'endLine' => 440,
                'startTokenPos' => 1466,
                'startFilePos' => 29126,
                'endTokenPos' => 1466,
                'endFilePos' => 29129,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 440,
            'endLine' => 440,
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
 * @param string $id the ID of the invoice on which to retrieve the invoice line items
 * @param null|array $params
 * @param null|array|string $opts
 *
 * @throws \\Stripe\\Exception\\ApiErrorException if the request fails
 *
 * @return \\Stripe\\Collection<\\Stripe\\InvoiceLineItem> the list of invoice line items
 */',
        'startLine' => 440,
        'endLine' => 443,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Stripe',
        'declaringClassName' => 'Stripe\\Invoice',
        'implementingClassName' => 'Stripe\\Invoice',
        'currentClassName' => 'Stripe\\Invoice',
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