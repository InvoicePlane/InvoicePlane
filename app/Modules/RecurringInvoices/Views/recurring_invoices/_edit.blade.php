@include('recurring_invoices._js_edit')

<section class="content-header">
    <h1 class="pull-left">@lang('ip.recurring_invoice') #{{ $recurringInvoice->id }}</h1>

    <div class="pull-right">

        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                @lang('ip.other') <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                <li><a href="javascript:void(0)" id="btn-copy-recurring-invoice"><i
                                class="fa fa-copy"></i> @lang('ip.copy')</a></li>
                <li class="divider"></li>
                <li><a href="{{ route('recurringInvoices.delete', [$recurringInvoice->id]) }}"
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
            <button type="button" class="btn btn-primary btn-save-recurring-invoice"><i
                        class="fa fa-save"></i> @lang('ip.save')</button>
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                <li><a href="#" class="btn-save-recurring-invoice"
                       data-apply-exchange-rate="1">@lang('ip.save_and_apply_exchange_rate')</a></li>
            </ul>
        </div>

    </div>

    <div class="clearfix"></div>
</section>

<section class="content">

    <div class="row">

        <div class="col-lg-10">

            <div id="form-status-placeholder"></div>

            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">@lang('ip.summary')</h3>
                        </div>
                        <div class="box-body">
                            {!! Form::text('summary', $recurringInvoice->summary, ['id' => 'summary', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-sm-6" id="col-from">

                    @include('recurring_invoices._edit_from')

                </div>

                <div class="col-sm-6" id="col-to">

                    @include('recurring_invoices._edit_to')

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
                                        {!! Form::hidden('recurring_invoice_id', $recurringInvoice->id) !!}
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
                                @foreach ($recurringInvoice->items as $item)
                                    <tr class="item" id="tr-item-{{ $item->id }}">
                                        <td>
                                            {!! Form::hidden('recurring_invoice_id', $recurringInvoice->id) !!}
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
                                            <a class="btn btn-xs btn-default btn-delete-recurring-invoice-item"
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
                        </ul>
                        <div class="tab-content">

                            <div class="tab-pane active" id="tab-additional">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>@lang('ip.terms_and_conditions')</label>
                                            {!! Form::textarea('terms', $recurringInvoice->terms, ['id' => 'terms', 'class' => 'form-control', 'rows' => 5]) !!}
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>@lang('ip.footer')</label>
                                            {!! Form::textarea('footer', $recurringInvoice->footer, ['id' => 'footer', 'class' => 'form-control', 'rows' => 5]) !!}
                                        </div>
                                    </div>
                                </div>

                                @if ($customFields->count())
                                    <div class="row">
                                        <div class="col-md-12">
                                            @include('custom_fields._custom_fields_unbound', ['object' => $recurringInvoice])
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2">

            <div id="div-totals">
                @include('recurring_invoices._edit_totals')
            </div>

            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title">@lang('ip.options')</h3>
                </div>

                <div class="box-body">

                    <div class="form-group">
                        <label>@lang('ip.next_date')</label>
                        {!! Form::text('next_date', $recurringInvoice->formatted_next_date, ['id' => 'next_date', 'class' => 'form-control input-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>@lang('ip.every')</label>
                        <div class="row">
                            <div class="col-md-4">
                                {!! Form::select('recurring_frequency', array_combine(range(1, 90), range(1, 90)), $recurringInvoice->recurring_frequency, ['id' => 'recurring_frequency', 'class' => 'form-control']) !!}
                            </div>
                            <div class="col-md-8">
                                {!! Form::select('recurring_period', $frequencies, $recurringInvoice->recurring_period, ['id' => 'recurring_period', 'class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>@lang('ip.stop_date')</label>
                        {!! Form::text('stop_date', $recurringInvoice->formatted_stop_date, ['id' => 'stop_date', 'class' => 'form-control input-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>@lang('ip.discount')</label>
                        <div class="input-group">
                            {!! Form::text('discount', $recurringInvoice->formatted_numeric_discount, ['id' =>
                            'discount', 'class' => 'form-control input-sm']) !!}
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>@lang('ip.currency')</label>
                        {!! Form::select('currency_code', $currencies, $recurringInvoice->currency_code, ['id' =>
                        'currency_code', 'class' => 'form-control input-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>@lang('ip.exchange_rate')</label>
                        <div class="input-group">
                            {!! Form::text('exchange_rate', $recurringInvoice->exchange_rate, ['id' => 'exchange_rate', 'class' => 'form-control input-sm']) !!}
                            <span class="input-group-btn">
                                <button class="btn btn-default btn-sm" id="btn-update-exchange-rate" type="button"
                                        data-toggle="tooltip" data-placement="left"
                                        title="@lang('ip.update_exchange_rate')">
                                    <i class="fa fa-refresh"></i>
                                </button>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>@lang('ip.group')</label>
                        {!! Form::select('group_id', $groups, $recurringInvoice->group_id, ['id' => 'group_id', 'class' => 'form-control input-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>@lang('ip.template')</label>
                        {!! Form::select('template', $templates, $recurringInvoice->template, ['id' => 'template', 'class' => 'form-control input-sm']) !!}
                    </div>

                </div>
            </div>
        </div>

    </div>

</section>