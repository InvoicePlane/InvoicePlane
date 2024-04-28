
<form id="payment-form">
    <div id="card-element"></div>
    <div id="card-errors" role="alert"></div>
    <br></br>
    <a id="getToken" class="btn btn-success btn-lg">Pay now</a>
</form>

<script>
    var stripe;
    $.getScript("https://js.stripe.com/v3/").done(()=>{
       stripe = Stripe('<?php echo($stripe_api_key); ?>');
       loadStripe();
    });

    function loadStripe() {
        var elements = stripe.elements();

        var style = {
            base: {
            color: "#32325d",
            fontFamily: 'Arial, sans-serif',
            fontSmoothing: "antialiased",
            fontSize: "16px",
            textDecoration:"underline",
            "::placeholder": {
                color: "#32325d"
            },
            },
            invalid: {
            fontFamily: 'Arial, sans-serif',
            color: "#fa755a",
            iconColor: "#fa755a"
            }
        };

    var card = elements.create("card",{style: style});

    card.mount("#card-element");

    card.on('ready',()=>{$("#fullpage-loader").fadeOut(200)});

    card.on('change', function(event) {

        var displayError = document.getElementById('card-errors');

        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    $('#getToken').click(()=>{
        $("#fullpage-loader").fadeIn(200)
        stripe.createToken(card).then((result)=>{
            if(result.error)
            {
                $("#fullpage-loader").fadeOut(200)
            }
            if(result.token)
            {
                $('input[name="creditcard_number"]').val(result.token.id)
                $("#payment-information-form").submit();
            }
        })
    });
}
</script>