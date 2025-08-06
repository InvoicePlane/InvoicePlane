<div class="container">
    <div id="paypal-buttons" class="col-xs-12 col-md-8 col-md-offset-2"></div>
    <div id="card-fields" class="col-xs-12 col-md-8 col-md-offset-2" style="margin-top:20px;">
        <h4 style="margin-bottom:10px;">Pay with Debit or Credit Card</h4>

        <div id="card-name-field-container" class="form-group" style="margin-bottom:10px;"></div>
        <div id="card-number-field-container" class="form-group" style="margin-bottom:10px;"></div>
        <div id="card-expiry-field-container" class="form-group" style="margin-bottom:10px;"></div>
        <div id="card-cvv-field-container" class="form-group" style="margin-bottom:10px;"></div>

        <button id="card-submit" type="button" class="btn btn-primary">Process Card Payment</button>
        <span id="card-spinner" style="display:none; margin-left:10px;" role="status" aria-live="polite" aria-label="Processing…">
          <span style="
            display:inline-block; width:16px; height:16px; border:2px solid #ccc; border-top-color:#333; border-radius:50%;
            animation: ip-spin 0.7s linear infinite; vertical-align:middle;">
          </span>
          <span style="margin-left:6px; vertical-align:middle;">Processing…</span>
        </span>
        <div id="card-errors" class="text-danger" style="margin-top:10px;"></div>
    </div>
</div>
<style>
    @keyframes ip-spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
<script>
    $.ajax({
        url: "https://www.paypal.com/sdk/js?client-id=<?php echo $paypal_client_id; ?>&currency=<?php echo $currency; ?>&intent=capture&components=buttons,card-fields",
        dataType: "script",
        cache: true,
        success: () => { initPayPal(); }
    });

    function initPayPal() {
        // --- Standard PayPal buttons (PayPal / Venmo / Pay Later) ---
        paypal.Buttons({
            createOrder() {
                return fetch('<?php echo site_url('guest/gateways/paypal/paypal_create_order/' . $invoice_url_key); ?>', { method: "GET" })
                    .then((response) => response.json())
                    .then((order) => order.id);
            },
            onApprove: function (data) {
                // NOTE: fixed dot access (data.orderID)
                return fetch('<?php echo site_url('guest/gateways/paypal/paypal_capture_payment/'); ?>' + data.orderID, { method: 'GET' })
                    .then((response) => {
                        if (!response.ok) throw new Error('Capture failed with HTTP ' + response.status);
                        window.location.replace('<?php echo site_url('guest/view/invoice/' . $invoice_url_key); ?>');
                    })
                    .catch((err) => {
                        console.error('Buttons capture error:', err);
                        window.location.replace('<?php echo site_url('guest/payment_information/form/' . $invoice_url_key . '/paypal'); ?>');
                    });
            },
            onError: function (error) {
                console.log('error on initPayPal', error);
                window.location.replace('<?php echo site_url('guest/payment_information/form/' . $invoice_url_key . '/paypal'); ?>');
            }
        }).render('#paypal-buttons');

        // --- Advanced Credit/Debit Card Fields ---
        const cardFields = paypal.CardFields({
            createOrder() {
                return fetch('<?php echo site_url('guest/gateways/paypal/paypal_create_order/' . $invoice_url_key); ?>', { method: 'GET' })
                    .then((res) => res.json())
                    .then((order) => order.id);
            },
            onApprove(data) {
                // keep spinner on during capture; re-enable only on error
                return fetch('<?php echo site_url('guest/gateways/paypal/paypal_capture_payment/'); ?>' + data.orderID, { method: 'GET' })
                    .then((res) => {
                        if (!res.ok) throw new Error('Capture failed with HTTP ' + res.status);
                        window.location.replace('<?php echo site_url('guest/view/invoice/' . $invoice_url_key); ?>');
                    })
                    .catch((err) => {
                        console.error('Capture error:', err);
                        setCardProcessing(false);
                        var el = document.getElementById('card-errors');
                        if (el) el.textContent = 'Payment capture failed. Please try again or use a different method.';
                    });
            },
            onError(err) {
                setCardProcessing(false);
                console.error('Card Fields error:', err);
                var el = document.getElementById('card-errors');
                if (el) el.textContent = (err && (err.message || err)) || 'Card processing error. Please try again.';
            }
        });

        // Render the secure fields into the containers (shows immediately on page load)
        cardFields.NameField().render('#card-name-field-container');
        cardFields.NumberField().render('#card-number-field-container');
        cardFields.ExpiryField().render('#card-expiry-field-container');
        cardFields.CVVField().render('#card-cvv-field-container');

        // Spinner + disable handling
        const submitBtn = document.getElementById('card-submit');
        const spinnerEl = document.getElementById('card-spinner');
        function setCardProcessing(isProcessing) {
            if (submitBtn) submitBtn.disabled = isProcessing;
            if (spinnerEl) spinnerEl.style.display = isProcessing ? 'inline-block' : 'none';
        }

        // Click-to-pay handler for the visible Card Pay button
        submitBtn.addEventListener('click', function () {
            setCardProcessing(true);
            // If submit() rejects before onError (e.g., validation), make sure we re-enable
            Promise.resolve(cardFields.submit())
                .catch((err) => {
                    setCardProcessing(false);
                    console.error('Submit error:', err);
                    var el = document.getElementById('card-errors');
                    if (el) el.textContent = (err && (err.message || err)) || 'Could not submit card. Please check the fields and try again.';
                });
        });
    }
</script>
