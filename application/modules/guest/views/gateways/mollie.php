<div class="container">
    <div class="col-xs-12 col-md-6 col-md-offset-3">
        <div class="text-center">
            <p>Redirecting to payment provider...</p>
            <i class="fa fa-spinner fa-spin fa-3x"></i>
        </div>
    </div>
</div>

<script>
    // Redirect to Mollie payment creation endpoint
    window.location.href = '<?php echo site_url('guest/gateways/mollie/mollie_create_payment/' . $invoice_url_key); ?>';
</script>
