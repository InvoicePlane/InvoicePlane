<div class="container">
    <div id="paypal-buttons" class="col-xs-12 col-md-8 col-md-offset-2"></div>
</div>
<div id="paypal-buttons"></div>

<script>
    $.ajax({
        url: "https://www.paypal.com/sdk/js?client-id=<?php echo $paypal_client_id; ?>&currency=<?php echo $currency; ?>",
        dataType: "script",
        cache: true,
        success: () => {
            initPayPal();
        }
    });

    function initPayPal() {
        paypal.Buttons({
            createOrder() {
                return fetch('<?php echo site_url('guest/gateways/paypal/paypal_create_order/' . $invoice_url_key); ?>',
                    {
                        method: "GET"
                    })
                    .then((response) => response.json())
                    .then((order) => order.id)
            },
            onApprove: function (data) {
                return fetch('<?php echo site_url('guest/gateways/paypal/paypal_capture_payment/'); ?>' + data . orderID, {
                    method: 'GET'
                })
                    .then((response) => window.location.replace('<?php echo site_url('guest/view/invoice/' . $invoice_url_key); ?>'));
            },
            onError: function (error) {
                console.log(error)
                window.location.replace('<?php echo site_url('guest/payment_information/form/' . $invoice_url_key . '/paypal'); ?>')
            }
        }).render('#paypal-buttons');
    }
function initPayPal() {
    paypal.Buttons({
        createOrder() {
            return fetch('<?php echo site_url('guest/gateways/paypal/paypal_create_order/'.$invoice_url_key); ?>',
            {
                method: "GET"
            })
            .then((response) => response.json())
            .then((order) => order.id)
        },
        onApprove: function (data) {
            return fetch('<?php echo site_url('guest/gateways/paypal/paypal_capture_payment/');?>'+data.orderID,{
                method: 'GET'
            })
            .then((response) => window.location.replace('<?php echo site_url('guest/view/invoice/'.$invoice_url_key); ?>'));
        },
        onError: function (error) {
            window.location.replace('<?php echo site_url('guest/payment_information/form/'.$invoice_url_key .'/paypal'); ?>')
        }
    }).render('#paypal-buttons');
    $("#fullpage-loader").fadeOut(200);
}
</script>
