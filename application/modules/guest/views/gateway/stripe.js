    var stripe = Stripe("pk_test_ruttiYWbopHL0a3ttNAf0aUf");
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
    card.on('change', function(event) {
    var displayError = document.getElementById('card-errors');
    if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = '';
    }
    });

    function process_payment()
    {
        stripe.createToken(card).then((result)=>{
            console.log(result);
        })
    }