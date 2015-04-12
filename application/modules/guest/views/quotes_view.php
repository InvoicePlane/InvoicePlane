<div id="headerbar">
    <h1><?php echo lang('quote'); ?> #<?php echo $quote->quote_number; ?></h1>

    <div class="pull-right btn-group">

        <?php if (in_array($quote->quote_status_id, array(2, 3))) { ?>
            <a href="<?php echo site_url('guest/quotes/approve/' . $quote->quote_id); ?>"
               class="btn btn-success btn-sm">
                <i class="glyphicon glyphicon-check"></i>
                <?php echo lang('approve_this_quote'); ?>
            </a>
            <a href="<?php echo site_url('guest/quotes/reject/' . $quote->quote_id); ?>"
               class="btn btn-danger btn-sm">
                <i class="glyphicon glyphicon-ban-circle"></i>
                <?php echo lang('reject_this_quote'); ?>
            </a>
        <?php } elseif ($quote->quote_status_id == 4) { ?>
            <a href="#" class="btn btn-success btn-sm disabled">
                <?php echo lang('quote_approved'); ?>
            </a>
        <?php } elseif ($quote->quote_status_id == 5) { ?>
            <a href="#" class="btn btn-danger btn-sm disabled">
                <?php echo lang('quote_rejected'); ?>
            </a>
        <?php } ?>

        <a href="<?php echo site_url('guest/quotes/generate_pdf/' . $quote_id); ?>"
           class="btn btn-default btn-sm" id="btn_generate_pdf">
            <i class="icon-print"></i>
            <?php echo lang('download_pdf'); ?>
        </a>
    </div>

</div>

<?php echo $this->layout->load_view('layout/alerts'); ?>

<div id="content" class="table-content">

    <div class="quote">

        <div class="cf">

            <div class="col-xs-12 col-md-9">

                <h2><?php echo $quote->client_name; ?></h2><br>
                <span>
                    <?php echo ($quote->client_address_1) ? $quote->client_address_1 . '<br>' : ''; ?>
                    <?php echo ($quote->client_address_2) ? $quote->client_address_2 . '<br>' : ''; ?>
                    <?php echo ($quote->client_city) ? $quote->client_city : ''; ?>
                    <?php echo ($quote->client_state) ? $quote->client_state : ''; ?>
                    <?php echo ($quote->client_zip) ? $quote->client_zip : ''; ?>
                    <?php echo ($quote->client_country) ? '<br>' . $quote->client_country : ''; ?>
                </span>
                <br><br>
                <?php if ($quote->client_phone) { ?>
                    <span><strong><?php echo lang('phone'); ?>:</strong> <?php echo $quote->client_phone; ?></span><br>
                <?php } ?>
                <?php if ($quote->client_email) { ?>
                    <span><strong><?php echo lang('email'); ?>:</strong> <?php echo $quote->client_email; ?></span>
                <?php } ?>

            </div>

            <div class="col-xs-12 col-md-3">
                <div class="panel panel-default panel-body text-right">
                    <p>
                        <b><?php echo lang('quote'); ?> #</b><br/>
                        <?php echo $quote->quote_number; ?>
                    </p>

                    <p>
                        <b><?php echo lang('date'); ?></b><br/>
                        <?php echo date_from_mysql($quote->quote_date_created); ?>
                    </p>

                    <p>
                        <b><?php echo lang('due_date'); ?></b><br/>
                        <?php echo date_from_mysql($quote->quote_date_expires); ?>
                    </p>

                </div>
            </div>

        </div>

        <div class="table-responsive">
            <table id="item_table" class="items table table-striped table-bordered">
                <thead>
                <tr>
                    <th><?php echo lang('item'); ?></th>
                    <th style="min-width: 300px;"><?php echo lang('description'); ?></th>
                    <th style="width: 100px;"><?php echo lang('quantity'); ?></th>
                    <th style="width: 100px;"><?php echo lang('price'); ?></th>
                    <th><?php echo lang('tax_rate'); ?></th>
                    <th><?php echo lang('subtotal'); ?></th>
                    <th><?php echo lang('tax'); ?></th>
                    <th><?php echo lang('total'); ?></th>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($items as $item) { ?>
                    <tr class="item">
                        <td style="vertical-align: top;">
                            <?php echo $item->item_name; ?></td>
                        <td><?php echo $item->item_description; ?></td>
                        <td style="vertical-align: top;">
                            <?php echo format_amount($item->item_quantity); ?></td>
                        <td style="vertical-align: top;">
                            <?php echo format_amount($item->item_price); ?></td>
                        <td style="vertical-align: top;">
                            <?php echo format_amount($item->item_tax_total); ?></td>
                        <td style="vertical-align: top;">
                            <span name="subtotal"><?php echo format_currency($item->item_subtotal); ?></span>
                        </td>
                        <td style="vertical-align: top;">
                            <span name="item_tax_total"><?php echo format_currency($item->item_tax_total); ?></span>
                        </td>
                        <td style="vertical-align: top;">
                            <span name="item_total"><?php echo format_currency($item->item_total); ?></span>
                        </td>
                    </tr>
                <?php } ?>

                </tbody>

            </table>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th><?php echo lang('subtotal'); ?></th>
                    <th><?php echo lang('item_tax'); ?></th>
                    <th><?php echo lang('quote_tax'); ?></th>
                    <th><?php echo lang('total'); ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?php echo format_currency($quote->quote_item_subtotal); ?></td>
                    <td><?php echo format_currency($quote->quote_item_tax_total); ?></td>
                    <td>
                        <?php if ($quote_tax_rates) {
                            foreach ($quote_tax_rates as $quote_tax_rate) { ?>
                                <?php echo $quote_tax_rate->quote_tax_rate_name . ' ' . $quote_tax_rate->quote_tax_rate_percent; ?>%:
                                <?php echo format_currency($quote_tax_rate->quote_tax_rate_amount); ?><br>
                            <?php }
                        } else {
                            echo format_currency('0');
                        } ?>
                    </td>
                    <td><b><?php echo format_currency($quote->quote_total); ?></b></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>