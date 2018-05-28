<?php

namespace FI\Widgets\Dashboard\InvoiceSummary\Composers;

class InvoiceSummarySettingComposer
{
    public function compose($view)
    {
        $view->with('dashboardTotalOptions', periods());
    }
}