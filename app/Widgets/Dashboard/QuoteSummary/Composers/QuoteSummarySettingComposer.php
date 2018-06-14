<?php

namespace IP\Widgets\Dashboard\QuoteSummary\Composers;

class QuoteSummarySettingComposer
{
    public function compose($view)
    {
        $view->with('dashboardTotalOptions', periods());
    }
}