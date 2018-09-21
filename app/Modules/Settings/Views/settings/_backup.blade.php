<div class="row">
    <div class="col-md-12">
        @if (!config('app.demo'))
            <a href="{{ route('settings.backup.database') }}" target="_blank"
                class="btn btn-default">@lang('ip.download_database_backup')</a>
        @else
            <p>Database backup not available in demo.</p>
        @endif
    </div>
</div>
