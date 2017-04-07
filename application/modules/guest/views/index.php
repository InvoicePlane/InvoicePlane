<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('dashboard'); ?></h1>
</div>

<div id="content">

    <div class="panel panel-default">

        <div class="panel-heading">
            <?php _trans('quotes_requiring_approval'); ?>
        </div>

        <div class="panel-body no-padding">

            <?php if ($open_quotes) { ?>
                <?php echo $this->layout->load_view('guest/partial_quotes_table', array('quotes' => $open_quotes)); ?>
            <?php } else { ?>
                <div class="alert text-success no-margin"><?php _trans('no_quotes_requiring_approval'); ?></div>
            <?php } ?>

        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <?php _trans('overdue_invoices'); ?>
        </div>
        <div class="panel-body no-padding">

            <?php if ($overdue_invoices) { ?>
                <?php echo $this->layout->load_view('guest/partial_invoices_table', array('invoices' => $overdue_invoices)); ?>
            <?php } else { ?>
                <div class="alert text-success no-margin"><?php _trans('no_overdue_invoices'); ?></div>
            <?php } ?>

        </div>
    </div>

    <div class="panel panel-default">

        <div class="panel-heading">
            <?php _trans('open_invoices'); ?>
        </div>

        <div class="panel-body no-padding">
            <?php echo $this->layout->load_view('guest/partial_invoices_table', array('invoices' => $open_invoices)); ?>
        </div>

    </div>

</div>
