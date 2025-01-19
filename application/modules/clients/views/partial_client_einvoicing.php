<script type="text/javascript">
    $(function () {
        // Cache jQuery selectors
        const $client_start_einvoicing = $('#client_start_einvoicing');
        const $div_show_einvoicing = $('#div_show_einvoicing');

        // Initial toggle based on current value
        toggle_einvoicing();

        // Toggle on change event
        $client_start_einvoicing.change(function () {
            toggle_einvoicing();
        });

        // Function to toggle einvoicing visibility
        function toggle_einvoicing() {
            const start_einvoicing = $client_start_einvoicing.val();

            if (start_einvoicing === '1') {
                $div_show_einvoicing.show();
            } else {
                $div_show_einvoicing.hide();
            }
        }
    });
</script>

<div class="row" style="font-size: medium;">
    <div class="col-xs-12 col-md-6">

        <div class="form-group">
            <label for="client_start_einvoicing">
                <?php _trans('einvoicing_start'); ?>
            </label>
            <select name="client_start_einvoicing" class="form-control simple-select"
                id="client_start_einvoicing" data-minimum-results-for-search="Infinity">
                <?php $active = ($this->mdl_clients->form_value('client_einvoicing_version') == '') ? '0' : '1'; ?>
                <option value="0" <?php check_select($active, '0'); ?>>
                    <?php _trans('no'); ?>
                </option>
                <option value="1" <?php check_select($active, '1'); ?>>
                    <?php _trans('yes'); ?>
                </option>
            </select>
            <?php if ($req_einvoicing['show_table'] > 0) { ?>
                <p class="help-block"><?php echo trans('einvoicing_start_hint'); ?></p>
            <?php } ?>
        </div>

    </div>

    <div id="div_show_einvoicing">
        <div class="col-xs-12 col-md-6">

            <div class="form-group">
                <label for="client_einvoicing_version"><?php echo 'UBL / CII ' . trans('version'); ?></label>
                <?php if ($req_einvoicing['show_table'] == 0) { ?>
                    <select name="client_einvoicing_version" id="client_einvoicing_version" class="form-control">
                <?php } else { ?>
                    <select disabled name="client_einvoicing_version" id="client_einvoicing_version" class="form-control">
                <?php } ?>
                    <option value=""><?php echo trans('none'); ?></option>
                    <?php foreach ($xml_templates as $xml_key => $xml_template) { ?>
                        <option value="<?php echo $xml_key; ?>"
                            <?php check_select($xml_key, $this->mdl_clients->form_value('client_einvoicing_version')) ?>>
                            <?php echo $xml_template; ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if ($req_einvoicing['show_table'] == 1) { ?>
                    <p class="help-block"><?php echo trans('einvoicing_ubl_cii_required_help'); ?></p>
                <?php } else { ?>
                    <p class="help-block"><?php echo trans('einvoicing_ubl_cii_creation_help'); ?></p>
                <?php } ?>
            </div>
        </div>

        <!-- check if mandatory e-invoicing fields are empty -->
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <div class="table-responsive">
                    <?php if ($req_einvoicing['show_table'] == 1) { ?>
                        <table class="table table-hover table-condensed table-bordered no-margin">
                    <?php } else { ?>
                        <table style="display:none;">
                    <?php } ?>
                        <thead>
                            <tr>
                                <th><?php _trans('required_fields'); ?></th>
                                <th style="min-width: 20%; text-align: center;"><?php _trans('user'); ?></th>
                                <th style="min-width: 20%; text-align: center;"><?php _trans('client'); ?></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($req_einvoicing['tr_show_address_1'] == 1) { ?>
                                <tr>
                            <?php } else { ?>
                                <tr style="display:none;">
                            <?php } ?>
                                <td>
                                    <?php echo trans('street_address'); ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($req_einvoicing['user_address_1'] == 0) { ?>
                                        <i class="fa fa-check-square-o" style="font-size:16px; color:green"></i>
                                    <?php } else { ?>
                                        <i class="fa fa-edit" style="font-size:16px; color:red"></i>
                                    <?php } ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($req_einvoicing['client_address_1'] == 0) { ?>
                                        <i class="fa fa-check-square-o" style="font-size:16px; color:green"></i>
                                    <?php } else { ?>
                                        <i class="fa fa-edit" style="font-size:16px; color:red"></i>
                                    <?php } ?>
                                </td>
                            </tr>

                            <?php if ($req_einvoicing['tr_show_zip'] == 1) { ?>
                                <tr>
                            <?php } else { ?>
                                <tr style="display:none;">
                            <?php } ?>
                                <td>
                                    <?php _trans('zip_code'); ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($req_einvoicing['user_zip'] == 0) { ?>
                                        <i class="fa fa-check-square-o" style="font-size:16px; color:green"></i>
                                    <?php } else { ?>
                                        <i class="fa fa-edit" style="font-size:16px; color:red"></i>
                                    <?php } ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($req_einvoicing['client_zip'] == 0) { ?>
                                        <i class="fa fa-check-square-o" style="font-size:16px; color:green"></i>
                                    <?php } else { ?>
                                        <i class="fa fa-edit" style="font-size:16px; color:red"></i>
                                    <?php } ?>
                                </td>
                            </tr>

                            <?php if ($req_einvoicing['tr_show_city'] == 1) { ?>
                                <tr>
                            <?php } else { ?>
                                <tr style="display:none;">
                            <?php } ?>
                                <td>
                                    <?php _trans('city'); ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($req_einvoicing['user_city'] == 0) { ?>
                                        <i class="fa fa-check-square-o" style="font-size:16px; color:green"></i>
                                    <?php } else { ?>
                                        <i class="fa fa-edit" style="font-size:16px; color:red"></i>
                                    <?php } ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($req_einvoicing['client_city'] == 0) { ?>
                                        <i class="fa fa-check-square-o" style="font-size:16px; color:green"></i>
                                    <?php } else { ?>
                                        <i class="fa fa-edit" style="font-size:16px; color:red"></i>
                                    <?php } ?>
                                </td>
                            </tr>

                            <?php if ($req_einvoicing['tr_show_country'] == 1) { ?>
                                <tr>
                            <?php } else { ?>
                                <tr style="display:none;">
                            <?php } ?>
                                <td>
                                    <?php _trans('country'); ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($req_einvoicing['user_country'] == 0) { ?>
                                        <i class="fa fa-check-square-o" style="font-size:16px; color:green"></i>
                                    <?php } else { ?>
                                        <i class="fa fa-edit" style="font-size:16px; color:red"></i>
                                    <?php } ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($req_einvoicing['client_country'] == 0) { ?>
                                        <i class="fa fa-check-square-o" style="font-size:16px; color:green"></i>
                                    <?php } else { ?>
                                        <i class="fa fa-edit" style="font-size:16px; color:red"></i>
                                    <?php } ?>
                                </td>
                            </tr>

                            <?php if ($req_einvoicing['tr_show_company'] == 1) { ?>
                                <tr>
                            <?php } else { ?>
                                <tr style="display:none;">
                            <?php } ?>
                                <td>
                                    <?php echo trans('company') . ' ' . trans('name'); ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($req_einvoicing['user_company'] == 0) { ?>
                                        <i class="fa fa-check-square-o" style="font-size:16px; color:green"></i>
                                    <?php } else { ?>
                                        <i class="fa fa-edit" style="font-size:16px; color:red"></i>
                                    <?php } ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($req_einvoicing['client_company'] == 0) { ?>
                                        <i class="fa fa-check-square-o" style="font-size:16px; color:green"></i>
                                    <?php } else { ?>
                                        <i class="fa fa-edit" style="font-size:16px; color:red"></i>
                                    <?php } ?>
                                </td>
                            </tr>

                            <?php if ($req_einvoicing['tr_show_vat_id'] == 1) { ?>
                                <tr>
                            <?php } else { ?>
                                <tr style="display:none;">
                            <?php } ?>
                                <td>
                                    <?php _trans('vat_id'); ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($req_einvoicing['user_vat_id'] == 0) { ?>
                                        <i class="fa fa-check-square-o" style="font-size:16px; color:green"></i>
                                    <?php } else { ?>
                                        <i class="fa fa-edit" style="font-size:16px; color:red"></i>
                                    <?php } ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($req_einvoicing['client_vat_id'] == 0) { ?>
                                        <i class="fa fa-check-square-o" style="font-size:16px; color:green"></i>
                                    <?php } else { ?>
                                        <i class="fa fa-edit" style="font-size:16px; color:red"></i>
                                    <?php } ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
