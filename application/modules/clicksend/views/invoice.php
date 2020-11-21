<script>
    $(function () {
        $('#clicksend_check_price_btn').click(function () {
            $.post("<?php echo site_url('clicksend/ajax/get_letter_price'); ?>",
            {
                invoice_id: <?php echo $invoice->invoice_id; ?>, address_name: $("#address_name").val(), address_line_1: $("#address_line_1").val(),
                address_line_2: $("#address_line_2").val(), address_city: $("#address_city").val(), address_postal_code: $("#address_postal_code").val(), 
                address_state: $("#address_state").val(), address_country: $("#address_country").val(), print_duplex: $("#print_duplex").val(), 
                print_color: $("#print_color").val()
            },
            function (data) {
                $(".fullpage-loader-close").click();
                alert("<?php _trans('price'); ?>: "+data.total_price_format)
            });
        });
    });
</script>
<form method="post" action="<?php echo site_url('clicksend/send_invoice/' . $invoice->invoice_id) ?>">

    <input type="hidden" name="<?php echo $this->config->item('csrf_token_name'); ?>"
           value="<?php echo $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('sendletter_invoice'); ?></h1>

        <div class="headerbar-item pull-right">
            <div class="btn-group btn-group-sm">
                <btn class="btn btn-info ajax-loader" id="clicksend_check_price_btn">
                    <i class="fa fa-money"></i>
                    <?php _trans('clicksend_check_price'); ?>
                </btn>
                <button class="btn btn-primary ajax-loader" name="btn_send" value="1">
                    <i class="fa fa-send"></i>
                    <?php _trans('send'); ?>
                </button>
                <button class="btn btn-danger" name="btn_cancel" value="1">
                    <i class="fa fa-times"></i>
                    <?php _trans('cancel'); ?>
                </button>
            </div>
        </div>
    </div>

    <div id="content">

        <div class="row">
            <div class="col-xs-12 col-md-8 col-md-offset-2">

                <?php $this->layout->load_view('layout/alerts');?>

                <div class="form-group">
                    <label for="address_name"><?php _trans('client_name'); ?></label>
                    <input type="text" name="address_name" id="address_name" class="form-control" required
                        value="<?php echo $invoice->client_name; ?>">
                </div>

                <div class="form-group">
                    <label for="address_line_1"><?php _trans('street_address'); ?></label>
                    <input type="text" name="address_line_1" id="address_line_1" class="form-control" required
                        value="<?php echo $invoice->client_address_1; ?>">
                </div>

                <div class="form-group">
                    <label for="address_line_2"><?php _trans('street_address_2'); ?></label>
                    <input type="text" name="address_line_2" id="address_line_2" class="form-control"
                        value="<?php echo $invoice->client_address_2; ?>">
                </div>

                <div class="form-group">
                    <label for="address_postal_code"><?php _trans('zip_code'); ?></label>
                    <input type="text" name="address_postal_code" id="address_postal_code" class="form-control" 
                        value="<?php echo $invoice->client_zip; ?>">
                </div>

                <div class="form-group">
                    <label for="address_city"><?php _trans('city'); ?></label>
                    <input type="text" name="address_city" id="address_city" class="form-control" 
                        value="<?php echo $invoice->client_city; ?>">
                </div>

                <div class="form-group">
                    <label for="address_state"><?php _trans('state'); ?></label>
                    <input type="text" name="address_state" id="address_state" class="form-control"
                        value="<?php echo $invoice->client_state; ?>">
                </div>

                <div class="form-group">
                    <label for="address_country"><?php _trans('country'); ?></label>

                    <div class="controls">
                        <select name="address_country" id="address_country" class="form-control simple-select">
                            <?php foreach ($countries as $cldr => $country) { ?>
                                <option value="<?php echo $cldr; ?>"
                                    <?php check_select($invoice->client_country, $cldr); ?>
                                ><?php echo $country ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="print_duplex"><?php _trans('print_duplex'); ?></label>
                    <select name="print_duplex" id="print_duplex" class="form-control simple-select">
                        <option value="0">
                            <?php echo _trans('no'); ?>
                        </option>
                        <option value="1" <?php check_select(get_setting('letter_standard_duplex', true), '1'); ?>>
                            <?php echo _trans('yes'); ?>
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="print_color"><?php _trans('print_color'); ?></label>
                    <select name="print_color" id="print_color" class="form-control simple-select">
                        <option value="0">
                            <?php echo _trans('no'); ?>
                        </option>
                        <option value="1" <?php check_select(get_setting('letter_standard_color'), '1'); ?>>
                            <?php echo _trans('yes'); ?>
                        </option>
                    </select>
                </div>

                <!--<div class="form-group">
                    <label for="pdf_template"><?php _trans('pdf_template'); ?></label>
                    <select name="pdf_template" id="pdf_template" class="form-control simple-select">
                        <option value=""><?php _trans('none'); ?></option>
                        <?php foreach ($pdf_templates as $pdf_template): ?>
                            <option value="<?php echo $pdf_template; ?>"
                                <?php check_select($selected_pdf_template, $pdf_template); ?>>
                                <?php echo $pdf_template; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>-->
            </div>
        </div>

    </div>

</form>

<?php $this->layout->load_view('upload/dropzone-invoice-scripts'); ?>
