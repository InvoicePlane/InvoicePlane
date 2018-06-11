<div class="box box-primary">
    <div class="box-body">
        <span class="pull-left"><strong>{{ trans('ip.subtotal') }}</strong></span><span
                class="pull-right">{{ $invoice->amount->formatted_subtotal }}</span>

        <div class="clearfix"></div>

        @if ($invoice->discount > 0)
            <span class="pull-left"><strong>{{ trans('ip.discount') }}</strong></span><span
                    class="pull-right">{{ $invoice->amount->formatted_discount }}</span>

            <div class="clearfix"></div>
        @endif

        <span class="pull-left"><strong>{{ trans('ip.tax') }}</strong></span><span
                class="pull-right">{{ $invoice->amount->formatted_tax }}</span>

        <div class="clearfix"></div>
        <span class="pull-left"><strong>{{ trans('ip.total') }}</strong></span><span
                class="pull-right">{{ $invoice->amount->formatted_total }}</span>

        <div class="clearfix"></div>
        <span class="pull-left"><strong>{{ trans('ip.paid') }}</strong></span><span
                class="pull-right">{{ $invoice->amount->formatted_paid }}</span>

        <div class="clearfix"></div>
        <span class="pull-left"><strong>{{ trans('ip.balance') }}</strong></span><span
                class="pull-right">{{ $invoice->amount->formatted_balance }}</span>

        <div class="clearfix"></div>
    </div>
</div>