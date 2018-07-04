<div id="client-activity-widget">
    <div class="card">
        <div class="card-header">
            <span class="card-title">
                <i class="fa fa-users mr-2"></i> @lang('ip.recent_client_activity')
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <tbody>
                    <tr>
                        <th>@lang('ip.date')</th>
                        <th>@lang('ip.activity')</th>
                    </tr>
                    @foreach ($recentClientActivity as $activity)
                        <tr>
                            <td>{{ $activity->formatted_created_at }}</td>
                            <td>{!! $activity->formatted_activity !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>