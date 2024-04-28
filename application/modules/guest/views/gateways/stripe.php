
<div id="stripe-checkout"></div>

<script>
    var stripe;
    $.getScript("https://js.stripe.com/v3/").done(()=>{
       stripe = Stripe('<?php echo($stripe_api_key); ?>');

       loadStripe().then(()=>{
        $("#fullpage-loader").fadeOut(200);
       });
    });

    async function loadStripe() {
        const fetchClientSecret = async () => {
            const response = await fetch('<?php echo site_url('guest/gateways/stripe/create_checkout_session/'.$invoice_url_key); ?>',{
                method: 'GET'
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