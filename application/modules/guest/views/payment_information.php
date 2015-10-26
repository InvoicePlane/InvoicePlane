<div id="headerbar">
    <h1><?php echo lang('online_payment_for'); ?> #<?php echo $invoice->invoice_number; ?></h1>

    <div class="pull-right">
        <a href="<?php echo site_url('guest/invoices/generate_pdf/'); ?>"
            class="btn btn-sm btn-default">
            <i class="fa fa-print"></i> <?php echo lang('download_pdf'); ?>
        </a>
    </div>

</div>

<?php echo $this->layout->load_view('layout/alerts'); ?>

<div id="content">

    <form action="<?php echo site_url('guest/payment_handler/make_payment/'); ?>"
          method="post" id="payment-information-form" class="form-horizontal">

        <div class="form-group">

            <input type="hidden" name="invoice_url_key" value="<?php echo $invoice->invoice_url_key; ?>">

            <label for="gateway"><?php echo lang('gateway'); ?></label>
            <select name="gateway" id="gateway-select" class="form-control">
                <?php
                // Display all available gateways
                foreach ($gateways as $gateway) { ?>
                    <option value="<?php echo $gateway; ?>"><?php echo ucwords(str_replace('_', ' ', $gateway)); ?></option>
                <?php } ?>
            </select>

            <?php
            /*
             * @todo implementation of the credit card form data is missing at the moment
             */
            ?>

        </div>

        <div class="form-group">
            <button class="btn btn-success" type="submit"><?php echo lang('pay_now'); ?></button>
        </div>

    </form>

</div>