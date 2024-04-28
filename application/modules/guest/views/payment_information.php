<!DOCTYPE html>

<!--[if lt IE 7]>
<html class="no-js ie6 oldie" lang="<?php _trans('cldr'); ?>"> <![endif]-->
<!--[if IE 7]>
<html class="no-js ie7 oldie" lang="<?php _trans('cldr'); ?>"> <![endif]-->
<!--[if IE 8]>
<html class="no-js ie8 oldie" lang="<?php _trans('cldr'); ?>"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="<?php _trans('cldr'); ?>"> <!--<![endif]-->

<head>
    <title>
        <?php
        if (get_setting('custom_title') != '') {
            echo get_setting('custom_title', '', true);
        } else {
            echo 'InvoicePlane';
        } ?>
    </title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="NOINDEX,NOFOLLOW">
    <meta name="_csrf" content="<?php echo $this->security->get_csrf_hash() ?>">

    <link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/core/img/favicon.png">

    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/core/css/custom.css">

    <?php if (get_setting('monospace_amounts') == 1) { ?>
        <link rel="stylesheet"
              href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/monospace.css">
    <?php } ?>

    <!--[if lt IE 9]>
    <script src="<?php echo base_url(); ?>assets/core/js/legacy.min.js"></script>
    <![endif]-->

    <script src="<?php echo base_url(); ?>assets/core/js/dependencies.min.js"></script>

    <script>
        $('.simple-select').select2();
    </script>
</head>
<body>

<nav class="navbar navbar-default ">
    <div class="container">

        <div class="navbar-brand">
            <?php _trans('online_payment_for_invoice'); ?> #<?php echo $invoice->invoice_number; ?>
        </div>

        <ul class="nav navbar-nav navbar-right">
            <li>
                <a href="<?php echo site_url('guest/view/generate_invoice_pdf/' . $invoice->invoice_url_key); ?>">
                    <i class="fa fa-print"></i> <?php _trans('download_pdf'); ?>
                </a>
            </li>
        </ul>

    </div>
</nav>

<div class="container">

    <div class="row">
        <div class="col-xs-12 col-md-8 col-md-offset-2">

            <br>
            <?php
            $logo = invoice_logo();
            if ($logo) {
                echo $logo . '<br><br>';
            }
            ?>

            <div class="form-group">
                <?php echo $this->layout->load_view('layout/alerts', ['without_margin' => true]); ?>
            </div>

            <div class="panel panel-default">

                <div class="panel-body">

                    <div class="row">
                        <div class="col-xs-12 col-md-7">
                            <h4>
                                <?php _htmlsc(format_client($invoice)) ?>
                            </h4>
                            <div class="client-address">
                                <?php $this->layout->load_view('clients/partial_client_address', array('client' => $invoice)); ?>
                            </div>
                        </div>

                        <div class="col-xs-12 col-md-5">
                            <div class="hidden-md hidden-lg"><br></div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed no-margin">
                                    <tbody>
                                    <tr>
                                        <td><?php echo trans('invoice_date'); ?></td>
                                        <td class="text-right"><?php echo date_from_mysql($invoice->invoice_date_created); ?></td>
                                    </tr>
                                    <tr class="<?php echo($is_overdue ? 'overdue' : '') ?>">
                                        <td><?php echo trans('due_date'); ?></td>
                                        <td class="text-right">
                                            <?php echo date_from_mysql($invoice->invoice_date_due); ?>
                                        </td>
                                    </tr>
                                    <tr class="<?php echo($is_overdue ? 'overdue' : '') ?>">
                                        <td><?php echo trans('total'); ?></td>
                                        <td class="text-right"><?php echo format_currency($invoice->invoice_total); ?></td>
                                    </tr>
                                    <tr class="<?php echo($is_overdue ? 'overdue' : '') ?>">
                                        <td><?php echo trans('balance'); ?></td>
                                        <td class="text-right"><?php echo format_currency($invoice->invoice_balance); ?></td>
                                    </tr>
                                    <?php if ($payment_method): ?>
                                        <tr>
                                            <td><?php echo trans('payment_method') . ': '; ?></td>
                                            <td class="text-right"><?php _htmlsc($payment_method->payment_method_name); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php if (!empty($invoice->invoice_terms)) : ?>
                            <div class="col-xs-12 text-muted">
                                <br>
                                <h4><?php echo trans('terms'); ?></h4>
                                <div><?php echo nl2br(htmlsc($invoice->invoice_terms)); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <?php if ($disable_form === false) { ?>
                <br>
                    <?php if (count ($gateways) > 1 )
                    //if more gateways are available
                    {?>
                    <div class="form-group">
                        <label for="gateway-select"><?php _trans('online_payment_method'); ?></label>
                        <select name="gateway" id="gateway-select" class="form-control simple-select">
                            <option value="none"><?php _trans('- Select -'); ?></option>
                            <?php
                            // Display all available gateways
                            foreach ($gateways as $gateway) { ?>
                                <option value="<?php echo $gateway; ?>">
                                    <?php echo ucwords(str_replace('_', ' ', $gateway)); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <?php } ?>
                    <br>
                    <div id="ajax-card-form"></div>
                </form>
                
            <?php } ?>

        </div>
    </div>

</div>

<div id="modal-placeholder"></div>

<?php echo $this->layout->load_view('layout/includes/fullpage-loader'); ?>

<script defer src="<?php echo base_url(); ?>assets/core/js/scripts.min.js"></script>

<script type="text/javascript">
<?php if (count ($gateways) > 1 ) {?>
    $('#gateway-select').change(()=>{
        if($('#gateway-select').select2('data')[0].id === "Stripe")
        {
            $("#fullpage-loader").fadeIn(200);
            $('#ajax-card-form').show();
            $('#ajax-card-form').load('<?php echo site_url('guest/payment_information/stripe/'.$invoice_url_key);?>');
        }
        else if($('#gateway-select').select2('data')[0].id === "PayPal")
        {
            $("#fullpage-loader").fadeIn(200);
            $('#ajax-card-form').show();
            $('#ajax-card-form').load('<?php echo site_url('guest/payment_information/paypal/'.$invoice_url_key);?>');
        }
        else
        {
            $('#ajax-card-form').hide();
            $('#ajax-card-form').html('');
        }
    });
<?php } else {?>
    $(document).ready(()=>{
        $("#fullpage-loader").fadeIn(200);
        $('#ajax-card-form').show();
        $('#ajax-card-form').load('<?php echo site_url('guest/payment_information/'.strtolower($gateways[0]).'/'.$invoice_url_key);?>');
    });
<?php }?>
</script>



</body>
</html>
