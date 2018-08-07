<div class="row">

    <div class="col-md-12">
        <div class="form-group">
            <label>@lang('ip.display_profile_image'): </label>
            {!! Form::select('setting[displayProfileImage]', $yesNoArray, config('ip.displayProfileImage'), ['class' => 'form-control']) !!}
        </div>
    </div>

</div>

@foreach ($dashboardWidgets as $widget)

    <h4 style="font-weight: bold; clear: both;">{{ $widget }}</h4>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>@lang('ip.enabled'): </label>
                {!! Form::select('setting[widgetEnabled' . $widget . ']', $yesNoArray, config('ip.widgetEnabled' .
                $widget), ['id' => 'widgetEnabled' . $widget, 'class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>@lang('ip.display_order'): </label>
                {!! Form::select('setting[widgetDisplayOrder' . $widget . ']', $displayOrderArray,
                config('ip.widgetDisplayOrder' . $widget),
                ['id' => 'widgetDisplayOrder' . $widget, 'class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>@lang('ip.column_width'): </label>
                {!! Form::select('setting[widgetColumnWidth' . $widget . ']', $colWidthArray,
                config('ip.widgetColumnWidth' . $widget), ['id' => 'widgetColumnWidth' . $widget, 'class' =>
                'form-control']) !!}
            </div>
        </div>
    </div>

    @if (view()->exists($widget . 'WidgetSettings'))
        @include($widget . 'WidgetSettings')
    @endif

@endforeach