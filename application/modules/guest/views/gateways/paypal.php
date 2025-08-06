<div class="container">
    <div id="paypal-buttons" class="col-xs-12 col-md-8 col-md-offset-2"></div>

    <?php $adv_enabled = !empty($advanced_credit_cards); ?>
    <?php if ($adv_enabled): ?>
        <div id="card-fields" class="col-xs-12 col-md-8 col-md-offset-2" style="margin-top:20px;">
            <h4 style="margin-bottom:10px;">Pay with credit or debit card</h4>

            <div id="card-name-field-container" class="form-group" style="margin-bottom:10px;"></div>
            <div id="card-number-field-container" class="form-group" style="margin-bottom:10px;"></div>
            <div id="card-expiry-field-container" class="form-group" style="margin-bottom:10px;"></div>
            <div id="card-cvv-field-container" class="form-group" style="margin-bottom:10px;"></div>

            <button id="card-submit" type="button" class="btn btn-primary">Pay with Card</button>
            <span id="card-spinner" style="display:none; margin-left:10px;" role="status" aria-live="polite" aria-label="Processing…">
          <span style="
            display:inline-block; width:16px; height:16px; border:2px solid #ccc; border-top-color:#333; border-radius:50%;
            animation: ip-spin 0.7s linear infinite; vertical-align:middle;">
          </span>
          <span style="margin-left:6px; vertical-align:middle;">Processing…</span>
        </span>
            <div id="card-errors" class="text-danger" style="margin-top:10px;"></div>
        </div>
    <?php endif; ?>
</div>
<style>
    @keyframes ip-spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
<script>
    // Setting flags from PHP
    const ADV_ENABLED = <?php echo $adv_enabled ? 'true' : 'false'; ?>;

    // Decide which SDK components to request
    const SDK_COMPONENTS = "<?php echo $adv_enabled ? 'buttons,card-fields' : 'buttons'; ?>";

    $.ajax({
        url: "https://www.paypal.com/sdk/js?client-id=<?php echo $paypal_client_id; ?>&enable-funding=venmo&currency=<?php echo $currency; ?>&intent=capture&components=" + SDK_COMPONENTS,
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

        // --- Advanced Card Fields: only if enabled and available in SDK ---
        if (ADV_ENABLED) {
            const cardSection = document.getElementById('card-fields');

            if (typeof paypal.CardFields !== 'function') {
                // SDK didn’t expose CardFields (merchant/app not approved) – hide the section
                if (cardSection) cardSection.style.display = 'none';
                return;
            }

            const cardFields = paypal.CardFields({
                createOrder() {
                    return fetch('<?php echo site_url('guest/gateways/paypal/paypal_create_order/' . $invoice_url_key); ?>', { method: 'GET' })
                        .then((res) => res.json())
                        .then((order) => order.id);
                },
                onApprove(data) {
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

            // Render secure fields
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

            // Click handler
            if (submitBtn) {
                submitBtn.addEventListener('click', function () {
                    setCardProcessing(true);
                    Promise.resolve(cardFields.submit())
                        .catch((err) => {
                            setCardProcessing(false);
                            console.error('Submit error:', err);
                            var el = document.getElementById('card-errors');
                            if (el) el.textContent = (err && (err.message || err)) || 'Could not submit card. Please check the fields and try again.';
                        });
                });
            }
        }
    }
</script>
