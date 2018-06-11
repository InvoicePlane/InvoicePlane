@extends('client_center.layouts.public')

@section('javascript')
    <script type="text/javascript">
      $(function () {
        $('#view-notes').hide();
        $('.btn-notes').click(function () {
          $('#view-doc').toggle();
          $('#view-notes').toggle();
          $('#' + $(this).data('button-toggle')).show();
          $(this).hide();
        });
      });
    </script>
@stop

@section('content')

    <section class="content">

        <div class="public-wrapper">

            @include('layouts._alerts')

            <div style="margin-bottom: 15px;">
                <a href="{{ route('clientCenter.public.quote.pdf', [$quote->url_key]) }}" target="_blank"
                   class="btn btn-primary"><i class="fa fa-print"></i> <span>@lang('ip.pdf')</span>
                </a>
                @if (auth()->check())
                    <a href="javascript:void(0)" id="btn-notes" data-button-toggle="btn-notes-back"
                       class="btn btn-primary btn-notes">
                        <i class="fa fa-comments"></i> @lang('ip.notes')
                    </a>
                    <a href="javascript:void(0)" id="btn-notes-back" data-button-toggle="btn-notes"
                       class="btn btn-primary btn-notes" style="display: none;">
                        <i class="fa fa-backward"></i> @lang('ip.back_to_quote')
                    </a>
                @endif
                @if (count($attachments))
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-files-o"></i> @lang('ip.attachments') <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            @foreach ($attachments as $attachment)
                                <li><a href="{{ $attachment->download_url }}">{{ $attachment->filename }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (in_array($quote->status_text, ['draft', 'sent']))
                    <a href="{{ route('clientCenter.public.quote.approve', [$quote->url_key]) }}"
                       class="btn btn-primary"
                       onclick="return confirm('@lang('ip.confirm_approve_quote')');">
                        <i class="fa fa-thumbs-up"></i> @lang('ip.approve')
                    </a>
                    <a href="{{ route('clientCenter.public.quote.reject', [$quote->url_key]) }}" class="btn btn-primary"
                       onclick="return confirm('@lang('ip.confirm_reject_quote')');">
                        <i class="fa fa-thumbs-down"></i> @lang('ip.reject')
                    </a>
                @endif
            </div>

            <div class="public-doc-wrapper">

                <div id="view-doc">
                    <iframe src="{{ route('clientCenter.public.quote.html', [$urlKey]) }}" frameborder="0"
                            style="width: 100%;" scrolling="no" onload="resizeIframe(this, 800);"></iframe>
                </div>

                @if (auth()->check())
                    <div id="view-notes">
                        @include('notes._notes', ['object' => $quote, 'model' => 'FI\Modules\Quotes\Models\Quote'])
                    </div>
                @endif

            </div>

        </div>

    </section>

@stop