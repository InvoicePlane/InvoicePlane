<link rel="stylesheet" href="<?php _core_asset('css/paypal.css'); ?>" type="text/css">
<div class="container">
    <div class="col-xs-12 col-md-6 col-md-offset-3">
        <div class="payment-button-container">
            <div id="paypal-buttons"></div>
        </div>
    </div>
</div>
<?php $adv_enabled = !empty($advanced_credit_cards); ?>
<?php $venmo_enabled = !empty($venmo); ?>
<?php if ($adv_enabled): ?>
<!-- OR Divider -->
<div class="col-xs-12 col-md-6 col-md-offset-3">
    <div class="payment-or-container">
        <div class="payment-or-divider">
            <span class="or-text">OR</span>
        </div>
    </div>
</div>

<div class="col-xs-12 col-md-6 col-md-offset-3">
    <div class="payment-form-container">
        <div id="card-fields">
            <h4>Pay with Credit / Debit Card</h4>

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

            <div class="card-submit-container">
                <button id="card-submit" type="button" class="btn btn-primary">Process Card Payment</button>
                <span id="card-spinner" role="status" aria-live="polite" aria-label="Processing…">
                    <span class="spinner-icon"></span>
                    <span class="spinner-text">Processing…</span>
                </span>
            </div>
        </div>
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
<script src="<?php _core_asset('js/paypal.js'); ?>"></script>
