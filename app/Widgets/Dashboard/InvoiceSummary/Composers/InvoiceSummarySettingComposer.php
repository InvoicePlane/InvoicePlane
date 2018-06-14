<?php

namespace IP\Widgets\Dashboard\InvoiceSummary\Composers;

class InvoiceSummarySettingComposer
{
    public function compose($view)
    {
        $view->with('dashboardTotalOptions', periods());
    }
}