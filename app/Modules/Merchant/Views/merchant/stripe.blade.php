<script type="text/javascript">
    var handler = StripeCheckout.configure({
        key: '{{ $driver->getSetting('publishableKey') }}',
        image: @if ($invoice->companyProfile->logo_url) '{{ $invoice->companyProfile->logo_url }}', @else 'https://stripe.com/img/documentation/checkout/marketplace.png', @endif
        locale: 'auto',
        token: function(token) {
            window.location = '{{ route('merchant.returnUrl', [$driver->getName(), $urlKey]) }}?token=' + token.id;
        }
    });

    handler.open({
        name: '{!! $invoice->companyProfile->company !!}',
        description: '{{ trans('fi.invoice') }} #{{ $invoice->number }}',
        email: '{{ $invoice->client->email }}',
        billingAddress: true,
        zipCode: true,
        amount: {{ $invoice->amount->balance * 100 }},
        currency: '{{ $invoice->currency_code }}'
    });

    window.addEventListener('popstate', function() {
        handler.close();
    });

</script>