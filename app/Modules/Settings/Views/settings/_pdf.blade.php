@section('javascript')
    @parent
    <script>
        $(function () {

            updatePDFOptions();

            $('#pdfDriver').change(function () {
                updatePDFOptions();
            });

            function updatePDFOptions () {

                $('.wkhtmltopdf-option').hide();

                pdfDriver = $('#pdfDriver').val();

                if (pdfDriver == 'wkhtmltopdf') {
                    $('.wkhtmltopdf-option').show();
                }
            }

        });
    </script>
@stop

<div class="row">

    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('ip.paper_size'): </label>
            {!! Form::select('setting[paperSize]', $paperSizes, config('ip.paperSize'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('ip.paper_orientation'): </label>
            {!! Form::select('setting[paperOrientation]', $paperOrientations, config('ip.paperOrientation'), ['class' => 'form-control']) !!}
        </div>
    </div>

</div>

<div class="form-group">
    <label>@lang('ip.pdf_driver'): </label>
    {!! Form::select('setting[pdfDriver]', $pdfDrivers, config('ip.pdfDriver'), ['id' => 'pdfDriver', 'class' => 'form-control']) !!}
</div>

<div class="form-group wkhtmltopdf-option">
    <label>@lang('ip.binary_path'): </label>
    {!! Form::text('setting[pdfBinaryPath]', config('ip.pdfBinaryPath'), ['class' => 'form-control']) !!}
</div>
