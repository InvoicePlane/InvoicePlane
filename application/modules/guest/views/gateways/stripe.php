<div id="stripe-checkout"></div>

<script>
    var stripe;
    $.getScript("https://js.stripe.com/v3/").done(() => {
        stripe = Stripe('<?php echo $stripe_api_key; ?>');

        loadStripe().then(() => {
            $("#fullpage-loader").fadeOut(200);
        });
    });

    async function loadStripe() {
        const fetchClientSecret = async () => {
            const formData = new URLSearchParams();
            formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');
            
            const response = await fetch('<?php echo site_url('guest/gateways/stripe/create_checkout_session/' . $invoice_url_key); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
                .then((response) => response.json())

            return await response.clientSecret;
        }

        const checkout = await stripe.initEmbeddedCheckout({
            fetchClientSecret
        });

        checkout.mount('#stripe-checkout');
    }
</script>
