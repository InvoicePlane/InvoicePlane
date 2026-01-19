<div class="container">
    <div class="col-xs-12 col-md-6 col-md-offset-3">
        <div class="payment-button-container">
            <p><?php _trans('online_payment_redirecting'); ?></p>
            <div class="text-center">
                <i class="fa fa-spinner fa-spin fa-3x"></i>
            </div>
        </div>
    </div>
</div>

<script>
    // Redirect to Mollie payment creation endpoint
    window.location.href = '<?php echo site_url('guest/gateways/mollie/mollie_create_payment/' . $invoice_url_key); ?>';
</script>
