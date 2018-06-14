<?php

namespace IP\Widgets\Dashboard\ClientActivity\Composers;

use IP\Modules\Activity\Models\Activity;

class ClientActivityWidgetComposer
{
    public function compose($view)
    {
        $recentClientActivity = Activity::where('activity', 'like', 'public%')
            ->orderBy('created_at', 'DESC')
            ->take(5)
            ->get();

        $view->with('recentClientActivity', $recentClientActivity);
    }
}