@extends('layouts.master')

@section('javascript')
    <script type="text/javascript">
      $(function () {
        $('.btn-bill-expense').click(function () {
          $('#modal-placeholder').load("{{ route('expenseBill.create') }}", {
            id: $(this).data('expense-id'),
            redirectTo: '{{ request()->fullUrl() }}'
          });
        });

        $('.expense_filter_options').change(function () {
          $('form#filter').submit();
        });

        $('#btn-bulk-delete').click(function () {

          var ids = [];

          $('.bulk-record:checked').each(function () {
            ids.push($(this).data('id'));
          });

          if (ids.length > 0) {
            if (!confirm('{!! trans('ip.bulk_delete_record_warning') !!}')) return false;
            $.post("{{ route('expenses.bulk.delete') }}", {
              ids: ids
            }).done(function () {
              window.location = decodeURIComponent("{{ urlencode(request()->fullUrl()) }}");
            });
          }
        });
      });
    </script>
@stop

@section('content')

    <section class="content-header">
        <h1 class="pull-left">
            {{ trans('ip.expenses') }}
        </h1>

        <div class="pull-right">

            <a href="javascript:void(0)" class="btn btn-default bulk-actions" id="btn-bulk-delete"><i
                        class="fa fa-trash"></i> {{ trans('ip.delete') }}</a>

            <div class="btn-group">
                {!! Form::open(['method' => 'GET', 'id' => 'filter']) !!}
                {!! Form::select('company_profile', $companyProfiles, request('company_profile'), ['class' => 'expense_filter_options form-control inline']) !!}
                {!! Form::select('status', $statuses, request('status'), ['class' => 'expense_filter_options form-control inline']) !!}
                {!! Form::select('category', $categories, request('category'), ['class' => 'expense_filter_options form-control inline']) !!}
                {!! Form::select('vendor', $vendors, request('vendor'), ['class' => 'expense_filter_options form-control inline']) !!}
                {!! Form::close() !!}
            </div>
            <a href="{{ route('expenses.create') }}" class="btn btn-primary"><i
                        class="fa fa-plus"></i> {{ trans('ip.new') }}</a>
        </div>

        <div class="clearfix"></div>
    </section>

    <section class="content">

        @include('layouts._alerts')

        <div class="row">

            <div class="col-xs-12">

                <div class="box box-primary">

                    <div class="box-body no-padding">
                        <table class="table table-hover">

                            <thead>
                            <tr>
                                <th>
                                    <div class="btn-group"><input type="checkbox" id="bulk-select-all"></div>
                                </th>
                                <th class="col-md-2">{!! Sortable::link('expense_date', trans('ip.date')) !!}</th>
                                <th class="col-md-2">{!! Sortable::link('expense_categories.name', trans('ip.category')) !!}</th>
                                <th class="col-md-3">{!! Sortable::link('description', trans('ip.description')) !!}</th>
                                <th class="col-md-2">{!! Sortable::link('amount', trans('ip.amount')) !!}</th>
                                <th class="col-md-2">{{ trans('ip.attachments') }}</th>
                                <th class="col-md-1">{{ trans('ip.options') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($expenses as $expense)
                                <tr>
                                    <td><input type="checkbox" class="bulk-record" data-id="{{ $expense->id }}"></td>
                                    <td>{{ $expense->formatted_expense_date  }}</td>
                                    <td>
                                        {{ $expense->category_name }}
                                        @if ($expense->vendor_name)
                                            <br><span class="text-muted">{{ $expense->vendor_name }}</span>
                                        @endif
                                    </td>
                                    <td>{!! $expense->formatted_description !!}</td>
                                    <td>
                                        {{ $expense->formatted_amount }}
                                        @if ($expense->is_billable)
                                            @if ($expense->has_been_billed)
                                                <br><a href="{{ route('invoices.edit', [$expense->invoice_id]) }}"><span
                                                            class="label label-success">{{ trans('ip.billed') }}</span></a>
                                            @else
                                                <br><span class="label label-danger">{{ trans('ip.not_billed') }}</span>
                                            @endif
                                        @else
                                            <br><span class="label label-default">{{ trans('ip.not_billable') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach ($expense->attachments as $attachment)
                                            <a href="{{ $attachment->download_url }}"><i
                                                        class="fa fa-file-o"></i> {{ $attachment->filename }}</a><br>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                    data-toggle="dropdown">
                                                {{ trans('ip.options') }} <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                @if ($expense->is_billable and !$expense->has_been_billed)
                                                    <li><a href="javascript:void(0)" class="btn-bill-expense"
                                                           data-expense-id="{{ $expense->id }}"><i
                                                                    class="fa fa-money"></i> {{ trans('ip.bill_this_expense') }}
                                                        </a></li>
                                                @endif
                                                <li><a href="{{ route('expenses.edit', [$expense->id]) }}"><i
                                                                class="fa fa-edit"></i> {{ trans('ip.edit') }}</a></li>
                                                <li><a href="{{ route('expenses.delete', [$expense->id]) }}"
                                                       onclick="return confirm('{{ trans('ip.delete_record_warning') }}');"><i
                                                                class="fa fa-trash-o"></i> {{ trans('ip.delete') }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>

                </div>

                <div class="pull-right">
                    {!! $expenses->appends(request()->except('page'))->render() !!}
                </div>

            </div>

        </div>

    </section>

@stop