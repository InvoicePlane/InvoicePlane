@include('quotes._js_edit')

<section class="content-header">
    <h1 class="pull-left">@lang('ip.quote') #{{ $quote->number }}</h1>

    @if ($quote->viewed)
        <span style="margin-left: 10px;" class="label label-success">@lang('ip.viewed')</span>
    @else
        <span style="margin-left: 10px;" class="label label-default">@lang('ip.not_viewed')</span>
    @endif

    @if ($quote->invoice)
        <span class="label label-info"><a href="{{ route('invoices.edit', [$quote->invoice_id]) }}"
                style="color: inherit;">@lang('ip.converted_to_invoice') {{ $quote->invoice->number }}</a></span>
    @endif

    <div class="pull-right">

        <a href="{{ route('quotes.pdf', [$quote->id]) }}" target="_blank" id="btn-pdf-quote"
            class="btn btn-default"><i class="fa fa-print"></i> @lang('ip.pdf')</a>
        @if (config('ip.mailConfigured'))
            <a href="javascript:void(0)" id="btn-email-quote" class="btn btn-default email-quote"
                data-quote-id="{{ $quote->id }}" data-redirect-to="{{ route('quotes.edit', [$quote->id]) }}"><i
                    class="fa fa-envelope"></i> @lang('ip.email')</a>
        @endif

        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                @lang('ip.other') <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                <li><a href="javascript:void(0)" id="btn-copy-quote"><i
                            class="fa fa-copy"></i> @lang('ip.copy')</a></li>
                <li><a href="javascript:void(0)" id="btn-quote-to-invoice"><i
                            class="fa fa-check"></i> @lang('ip.quote_to_invoice')</a></li>
                <li><a href="{{ route('clientCenter.public.quote.show', [$quote->url_key]) }}" target="_blank"><i
                            class="fa fa-globe"></i> @lang('ip.public')</a></li>
                <li class="divider"></li>
                <li><a href="{{ route('quotes.delete', [$quote->id]) }}"
                        onclick="return confirm('@lang('ip.delete_record_warning')');"><i
                            class="fa fa-trash-o"></i> @lang('ip.delete')</a></li>
            </ul>
        </div>

        <div class="btn-group">
            @if ($returnUrl)
                <a href="{{ $returnUrl }}" class="btn btn-default"><i
                        class="fa fa-backward"></i> @lang('ip.back')</a>
            @endif
        </div>

        <div class="btn-group">
            <button type="button" class="btn btn-primary btn-save-quote"><i
                    class="fa fa-save"></i> @lang('ip.save')</button>
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                <li><a href="#" class="btn-save-quote"
                        data-apply-exchange-rate="1">@lang('ip.save_and_apply_exchange_rate')</a></li>
            </ul>
        </div>

    </div>

    <div class="clearfix"></div>
</section>

<section class="content">

    <div class="row">

        <div class="col-lg-10">

            @include('layouts._alerts')

            <div id="form-status-placeholder"></div>

            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">@lang('ip.summary')</h3>
                        </div>
                        <div class="box-body">
                            {!! Form::text('summary', $quote->summary, ['id' => 'summary', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-sm-6" id="col-from">

                    @include('quotes._edit_from')

                </div>

                <div class="col-sm-6" id="col-to">

                    @include('quotes._edit_to')

                </div>

            </div>

            <div class="row">

                <div class="col-sm-12 table-responsive" style="overflow-x: visible;">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">@lang('ip.items')</h3>

                            <div class="box-tools pull-right">
                                <button class="btn btn-primary btn-sm" id="btn-add-item"><i
                                        class="fa fa-plus"></i> @lang('ip.add_item')</button>
                            </div>
                        </div>

                        <div class="box-body">
                            <table id="item-table" class="table table-hover">
                                <thead>
                                <tr>
                                    <th style="width: 20%;">@lang('ip.product')</th>
                                    <th style="width: 25%;">@lang('ip.description')</th>
                                    <th style="width: 10%;">@lang('ip.qty')</th>
                                    <th style="width: 10%;">@lang('ip.price')</th>
                                    <th style="width: 10%;">{{ trans('ip.tax_1') }}</th>
                                    <th style="width: 10%;">{{ trans('ip.tax_2') }}</th>
                                    <th style="width: 10%; text-align: right; padding-right: 25px;">@lang('ip.total')</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr id="new-item" style="display: none;">
                                    <td>
                                        {!! Form::hidden('quote_id', $quote->id) !!}
                                        {!! Form::hidden('id', '') !!}
                                        {!! Form::text('name', null, ['class' => 'form-control']) !!}<br>
                                        <label><input type="checkbox" name="save_item_as_lookup"
                                                tabindex="999"> @lang('ip.save_item_as_lookup')</label>
                                    </td>
                                    <td>{!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 1]) !!}</td>
                                    <td>{!! Form::text('quantity', null, ['class' => 'form-control']) !!}</td>
                                    <td>{!! Form::text('price', null, ['class' => 'form-control']) !!}</td>
                                    <td>{!! Form::select('tax_rate_id', $taxRates, config('ip.itemTaxRate'), ['class' => 'form-control']) !!}</td>
                                    <td>{!! Form::select('tax_rate_2_id', $taxRates, config('ip.itemTax2Rate'), ['class' => 'form-control']) !!}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @foreach ($quote->items as $item)
                                    <tr class="item" id="tr-item-{{ $item->id }}">
                                        <td>
                                            {!! Form::hidden('quote_id', $quote->id) !!}
                                            {!! Form::hidden('id', $item->id) !!}
                                            {!! Form::text('name', $item->name, ['class' => 'form-control item-lookup']) !!}
                                        </td>
                                        <td>{!! Form::textarea('description', $item->description, ['class' => 'form-control', 'rows' => 1]) !!}</td>
                                        <td>{!! Form::text('quantity', $item->formatted_quantity, ['class' => 'form-control']) !!}</td>
                                        <td>{!! Form::text('price', $item->formatted_numeric_price, ['class' => 'form-control']) !!}</td>
                                        <td>{!! Form::select('tax_rate_id', $taxRates, $item->tax_rate_id, ['class' => 'form-control']) !!}</td>
                                        <td>{!! Form::select('tax_rate_2_id', $taxRates, $item->tax_rate_2_id, ['class' => 'form-control']) !!}</td>
                                        <td style="text-align: right; padding-right: 25px;">{{ $item->amount->formatted_subtotal }}</td>
                                        <td>
                                            <a class="btn btn-xs btn-default btn-delete-quote-item"
                                                href="javascript:void(0);"
                                                title="@lang('ip.delete')" data-item-id="{{ $item->id }}">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-lg-12">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab-additional"
                                    data-toggle="tab">@lang('ip.additional')</a></li>
                            <li><a href="#tab-notes" data-toggle="tab">@lang('ip.notes')</a></li>
                            <li><a href="#tab-attachments" data-toggle="tab">@lang('ip.attachments')</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab-additional">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>@lang('ip.terms_and_conditions')</label>
                                            {!! Form::textarea('terms', $quote->terms, ['id' => 'terms', 'class' => 'form-control', 'rows' => 5]) !!}
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>@lang('ip.footer')</label>
                                            {!! Form::textarea('footer', $quote->footer, ['id' => 'footer', 'class' => 'form-control', 'rows' => 5]) !!}
                                        </div>
                                    </div>
                                </div>

                                @if ($customFields->count())
                                    <div class="row">
                                        <div class="col-md-12">
                                            @include('custom_fields._custom_fields_unbound', ['object' => $quote])
                                        </div>
                                    </div>
                                @endif

                            </div>
                            <div class="tab-pane" id="tab-notes">
                                <div class="row">
                                    <div class="col-lg-12">
                                        @include('notes._notes', ['object' => $quote, 'model' => 'IP\Modules\Quotes\Models\Quote', 'showPrivateCheckbox' => true, 'hideHeader' => true])
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab-attachments">
                                <div class="row">
                                    <div class="col-lg-12">
                                        @include('attachments._table', ['object' => $quote, 'model' => 'IP\Modules\Quotes\Models\Quote'])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-2">

            <div id="div-totals">
                @include('quotes._edit_totals')
            </div>

            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title">@lang('ip.options')</h3>
                </div>

                <div class="box-body">

                    <div class="form-group">
                        <label>@lang('ip.quote') #</label>
                        {!! Form::text('number', $quote->number, ['id' => 'number', 'class' =>
                        'form-control
                        input-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>@lang('ip.date')</label>
                        {!! Form::text('quote_date', $quote->formatted_quote_date, ['id' =>
                        'quote_date', 'class' => 'form-control input-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>@lang('ip.expires')</label>
                        {!! Form::text('expires_at', $quote->formatted_expires_at, ['id' => 'expires_at', 'class'
                        => 'form-control input-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>@lang('ip.discount')</label>
                        <div class="input-group">
                            {!! Form::text('discount', $quote->formatted_numeric_discount, ['id' =>
                            'discount', 'class' => 'form-control input-sm']) !!}
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>@lang('ip.currency')</label>
                        {!! Form::select('currency_code', $currencies, $quote->currency_code, ['id' =>
                        'currency_code', 'class' => 'form-control input-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>@lang('ip.exchange_rate')</label>
                        <div class="input-group">
                            {!! Form::text('exchange_rate', $quote->exchange_rate, ['id' =>
                            'exchange_rate', 'class' => 'form-control input-sm']) !!}
                            <span class="input-group-btn">
                                <button class="btn btn-default btn-sm" id="btn-update-exchange-rate" type="button"
                                    data-toggle="tooltip" data-placement="left"
                                    title="@lang('ip.update_exchange_rate')"><i class="fa fa-refresh"></i>
                                </button>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>@lang('ip.status')</label>
                        {!! Form::select('quote_status_id', $statuses, $quote->quote_status_id,
                        ['id' => 'quote_status_id', 'class' => 'form-control input-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>@lang('ip.template')</label>
                        {!! Form::select('template', $templates, $quote->template,
                        ['id' => 'template', 'class' => 'form-control input-sm']) !!}
                    </div>

                </div>
            </div>
        </div>

    </div>

</section>
