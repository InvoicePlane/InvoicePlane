<div id="content">
    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <div class="row <?php if (get_setting('disable_quickactions') == 1) echo 'hidden'; ?>">
        <div class="col-xs-12">

            <div id="panel-quick-actions" class="panel panel-default quick-actions">

                <div class="panel-heading">
                    <b><?php _trans('quick_actions'); ?></b>
                </div>

                <div class="btn-group btn-group-justified no-margin">
                    <a href="<?php echo site_url('clients/form'); ?>" class="btn btn-default">
                        <i class="fa fa-user fa-margin"></i>
                        <span class="hidden-xs"><?php _trans('add_client'); ?></span>
                    </a>
                    <a href="javascript:void(0)" class="create-quote btn btn-default">
                        <i class="fa fa-file fa-margin"></i>
                        <span class="hidden-xs"><?php _trans('create_quote'); ?></span>
                    </a>
                    <a href="javascript:void(0)" class="create-invoice btn btn-default">
                        <i class="fa fa-file-text fa-margin"></i>
                        <span class="hidden-xs"><?php _trans('create_invoice'); ?></span>
                    </a>
                    <a href="<?php echo site_url('payments/form'); ?>" class="btn btn-default">
                        <i class="fa fa-credit-card fa-margin"></i>
                        <span class="hidden-xs"><?php _trans('enter_payment'); ?></span>
                    </a>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-6">

            <div id="panel-quote-overview" class="panel panel-default overview">

                <div class="panel-heading">
                    <b><i class="fa fa-bar-chart fa-margin"></i> <?php _trans('quote_overview'); ?></b>
                    <span class="pull-right text-muted"><?php echo lang($quote_status_period); ?></span>
                </div>

                <table class="table table-bordered table-condensed no-margin">
                    <?php foreach ($quote_status_totals as $total) { ?>
                        <tr>
                            <td>
                                <a href="<?php echo site_url($total['href']); ?>">
                                    <?php echo $total['label']; ?>
                                </a>
                            </td>
                            <td class="amount">
                        <span class="<?php echo $total['class']; ?>">
                            <?php echo format_currency($total['sum_total']); ?>
                        </span>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>

        </div>
        <div class="col-xs-12 col-md-6">

            <div id="panel-invoice-overview" class="panel panel-default overview">

                <div class="panel-heading">
                    <b><i class="fa fa-bar-chart fa-margin"></i> <?php _trans('invoice_overview'); ?></b>
                    <span class="pull-right text-muted"><?php echo lang($invoice_status_period); ?></span>
                </div>

                <table class="table table-bordered table-condensed no-margin">
                    <?php foreach ($invoice_status_totals as $total) { ?>
                        <tr>
                            <td>
                                <a href="<?php echo site_url($total['href']); ?>">
                                    <?php echo $total['label']; ?>
                                </a>
                            </td>
                            <td class="amount">
                        <span class="<?php echo $total['class']; ?>">
                            <?php echo format_currency($total['sum_total']); ?>
                        </span>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>


            <?php if (empty($overdue_invoices)) { ?>
                <div class="panel panel-default panel-heading">
                    <span class="text-muted"><?php _trans('no_overdue_invoices'); ?></span>
                </div>
            <?php } else {
                $overdue_invoices_total = 0;
                foreach ($overdue_invoices as $invoice) {
                    $overdue_invoices_total += $invoice->invoice_balance;
                }
                ?>
                <div class="panel panel-danger panel-heading">
                    <?php echo anchor('invoices/status/overdue', '<i class="fa fa-external-link"></i> ' . trans('overdue_invoices'), 'class="text-danger"'); ?>
                    <span class="pull-right text-danger">
                        <?php echo format_currency($overdue_invoices_total); ?>
                    </span>
                </div>
            <?php } ?>

        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-6">

            <div id="panel-recent-quotes" class="panel panel-default">

                <div class="panel-heading">
                    <b><i class="fa fa-history fa-margin"></i> <?php _trans('recent_quotes'); ?></b>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-condensed no-margin">
                        <thead>
                        <tr>
                            <th><?php _trans('status'); ?></th>
                            <th style="min-width: 15%;"><?php _trans('date'); ?></th>
                            <th style="min-width: 15%;"><?php _trans('quote'); ?></th>
                            <th style="min-width: 35%;"><?php _trans('client'); ?></th>
                            <th style="text-align: right;"><?php _trans('balance'); ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($quotes as $quote) { ?>
                            <tr>
                                <td>
                                <span class="label
                                <?php echo $quote_statuses[$quote->quote_status_id]['class']; ?>">
                                    <?php echo $quote_statuses[$quote->quote_status_id]['label']; ?>
                                </span>
                                </td>
                                <td>
                                    <?php echo date_from_mysql($quote->quote_date_created); ?>
                                </td>
                                <td>
                                    <?php echo anchor('quotes/view/' . $quote->quote_id, ($quote->quote_number ? $quote->quote_number : $quote->quote_id)); ?>
                                </td>
                                <td>
                                    <?php echo anchor('clients/view/' . $quote->client_id, htmlsc(format_client($quote))); ?>
                                </td>
                                <td class="amount">
                                    <?php echo format_currency($quote->quote_total); ?>
                                </td>
                                <td style="text-align: center;">
                                    <a href="<?php echo site_url('quotes/generate_pdf/' . $quote->quote_id); ?>"
                                       title="<?php _trans('download_pdf'); ?>" target="_blank">
                                        <i class="fa fa-file-pdf-o"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="6" class="text-right small">
                                <?php echo anchor('quotes/status/all', trans('view_all')); ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <div class="col-xs-12 col-md-6">

            <div id="panel-recent-invoices" class="panel panel-default">

                <div class="panel-heading">
                    <b><i class="fa fa-history fa-margin"></i> <?php _trans('recent_invoices'); ?></b>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-condensed no-margin">
                        <thead>
                        <tr>
                            <th><?php _trans('status'); ?></th>
                            <th style="min-width: 15%;"><?php _trans('due_date'); ?></th>
                            <th style="min-width: 15%;"><?php _trans('invoice'); ?></th>
                            <th style="min-width: 35%;"><?php _trans('client'); ?></th>
                            <th style="text-align: right;"><?php _trans('balance'); ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php foreach ($invoices as $invoice) {
                            if ($this->config->item('disable_read_only') == true) {
                                $invoice->is_read_only = 0;
                            } ?>
                            <tr>
                                <td>
                                    <span class="label <?php echo $invoice_statuses[$invoice->invoice_status_id]['class']; ?>">
                                        <?php echo $invoice_statuses[$invoice->invoice_status_id]['label'];
                                        if ($invoice->invoice_sign == '-1') { ?>
                                            &nbsp;<i class="fa fa-credit-invoice"
                                                     title="<?php _trans('credit_invoice') ?>"></i>
                                        <?php }
                                        if ($invoice->is_read_only == 1) { ?>
                                            &nbsp;<i class="fa fa-read-only"
                                                     title="<?php _trans('read_only') ?>"></i>
                                        <?php }; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="<?php if ($invoice->is_overdue) { ?>font-overdue<?php } ?>">
                                        <?php echo date_from_mysql($invoice->invoice_date_due); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo anchor('invoices/view/' . $invoice->invoice_id, ($invoice->invoice_number ? $invoice->invoice_number : $invoice->invoice_id)); ?>
                                </td>
                                <td>
                                    <?php echo anchor('clients/view/' . $invoice->client_id, htmlsc(format_client($invoice))); ?>
                                </td>
                                <td class="amount">
                                    <?php echo format_currency($invoice->invoice_balance * $invoice->invoice_sign); ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($invoice->sumex_id != null): ?>
                                        <a href="<?php echo site_url('invoices/generate_sumex_pdf/' . $invoice->invoice_id); ?>"
                                           title="<?php _trans('download_pdf'); ?>" target="_blank">
                                            <i class="fa fa-file-pdf-o"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo site_url('invoices/generate_pdf/' . $invoice->invoice_id); ?>"
                                           title="<?php _trans('download_pdf'); ?>" target="_blank">
                                            <i class="fa fa-file-pdf-o"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="6" class="text-right small">
                                <?php echo anchor('invoices/status/all', trans('view_all')); ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>

    <?php if (get_setting('projects_enabled') == 1) : ?>
        <div class="row">
            <div class="col-xs-12 col-md-6">

                <div id="panel-projects" class="panel panel-default">

                    <div class="panel-heading">
                        <b><i class="fa fa-list fa-margin"></i> <?php _trans('projects'); ?></b>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed no-margin">
                            <thead>
                            <tr>
                                <th><?php _trans('project_name'); ?></th>
                                <th><?php _trans('client_name'); ?></th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php foreach ($projects as $project) { ?>
                                <tr>
                                    <td>
                                        <?php echo anchor('projects/view/' . $project->project_id, htmlsc($project->project_name)); ?>
                                    </td>
                                    <td>
                                        <?php echo anchor('clients/view/' . $project->client_id, htmlsc(format_client($project))); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>

                        </table>
                    </div>
                </div>

            </div>
            <div class="col-xs-12 col-md-6">

                <div id="panel-recent-invoices" class="panel panel-default">

                    <div class="panel-heading">
                        <b><i class="fa fa-check-square-o fa-margin"></i> <?php _trans('tasks'); ?></b>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-condensed no-margin">

                            <thead>
                            <tr>
                                <th><?php _trans('status'); ?></th>
                                <th><?php _trans('task_name'); ?></th>
                                <th><?php _trans('task_finish_date'); ?></th>
                                <th><?php _trans('project'); ?></th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php foreach ($tasks as $task) { ?>
                                <tr>
                                    <td>
                                    <span class="label <?php echo $task_statuses["$task->task_status"]['class']; ?>">
                                        <?php echo $task_statuses["$task->task_status"]['label']; ?>
                                    </span>
                                    </td>
                                    <td>
                                        <?php echo anchor('tasks/form/' . $task->task_id, htmlsc($task->task_name)) ?>
                                    </td>
                                    <td>
                                    <span class="<?php if ($task->is_overdue) { ?>text-danger<?php } ?>">
                                        <?php echo date_from_mysql($task->task_finish_date); ?>
                                    </span>
                                    </td>
                                    <td>
                                        <?php echo !empty($task->project_id) ? anchor('projects/view/' . $task->project_id, htmlsc($task->project_name)) : ''; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>

                        </table>
                    </div>

                </div>

            </div>
        </div>
    <?php endif; ?>

</div>
