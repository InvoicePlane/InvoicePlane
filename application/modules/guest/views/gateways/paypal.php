<link rel="stylesheet" href="<?php _core_asset('css/payment-forms.css'); ?>" type="text/css">
<div class="container">
    <div id="paypal-buttons" class="col-xs-12 col-md-8 col-md-offset-2"></div>

    <?php $adv_enabled = !empty($advanced_credit_cards); ?>
    <?php $venmo_enabled = !empty($venmo); ?>
    <?php if ($adv_enabled): ?>
        <div id="card-fields" class="col-xs-12 col-md-8 col-md-offset-2">
            <h4>Pay with credit or debit card</h4>

            <!-- Validation Error Display -->
            <div id="card-error-container" class="alert alert-danger">
                <div class="error-header">
                    <i class="fa fa-exclamation-circle"></i>
                    <strong>Please correct the following errors:</strong>
                </div>
                <ul id="card-error-list"></ul>
                <div id="card-error-message"></div>
            </div>

            <div id="card-name-field-container" class="form-group"></div>
            <div id="card-number-field-container" class="form-group"></div>
            <div id="card-expiry-field-container" class="form-group"></div>
            <div id="card-cvv-field-container" class="form-group"></div>

            <button id="card-submit" type="button" class="btn btn-primary">Pay with Card</button>
            <span id="card-spinner" role="status" aria-live="polite" aria-label="Processing…">
                <span class="spinner-icon"></span>
                <span class="spinner-text">Processing…</span>
            </span>
        </div>
    <?php endif; ?>
</div>
<script>
    // Prep PHP vars for use in JS
    window.PayPalConfig = {
        advEnabled: <?php echo $adv_enabled ? 'true' : 'false'; ?>,
        venmoEnabled: <?php echo $venmo_enabled ? 'true' : 'false'; ?>,
        clientId: '<?php echo $paypal_client_id; ?>',
        currency: '<?php echo $currency; ?>',
        invoiceUrlKey: '<?php echo $invoice_url_key; ?>',
        createOrderUrl: '<?php echo site_url('guest/gateways/paypal/paypal_create_order/' . $invoice_url_key); ?>',
        capturePaymentUrl: '<?php echo site_url('guest/gateways/paypal/paypal_capture_payment/'); ?>',
        successUrl: '<?php echo site_url('guest/view/invoice/' . $invoice_url_key); ?>',
        errorUrl: '<?php echo site_url('guest/payment_information/form/' . $invoice_url_key . '/paypal'); ?>'
    };
</script>
<script src="<?php _core_asset('js/payment-forms.js'); ?>"></script>
