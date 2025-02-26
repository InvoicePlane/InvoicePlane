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
<?php
$disabled = ''; // hint
$client_einvoicing_version = $this->mdl_clients->form_value('client_einvoicing_version');
if ($req_einvoicing->show_table > 0)
{
    $disabled = ' disabled="disabled"';
?>
            <p class="help-block"><?php _trans('einvoicing_start_hint'); ?></p>
<?php
}
?>
        </div>

    </div>

    <div id="div_show_einvoicing">
        <div class="col-xs-12 col-md-6">

            <div class="form-group">
                <label for="client_einvoicing_version"><?php echo 'UBL / CII ' . trans('version'); ?></label>

                <select name="client_einvoicing_version" id="client_einvoicing_version" class="form-control"<?php echo $disabled; ?>>
                    <option value=""><?php _trans('none'); ?></option>
<?php
foreach ($xml_templates as $xml_key => $xml_template)
{
?>
                    <option value="<?php echo $xml_key; ?>" <?php check_select($xml_key, $client_einvoicing_version) ?>>
                        <?php echo $xml_template; ?>
                    </option>
<?php
}
?>
                </select>

                <p class="help-block">
                    <?php $disabled ? _trans('einvoicing_ubl_cii_required_help') : _trans('einvoicing_ubl_cii_creation_help'); ?>
                </p>
            </div>
        </div>

<?php
$class_checks = ['fa fa-lg fa-check-square-o text-success', 'fa fa-lg fa-edit text-warning'];
$user_link = anchor('/users/form/' . $req_einvoicing->user_id, trans('user'));
?>
        <!-- check if mandatory e-invoicing fields are empty -->
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <div class="table-responsive">
                <table class="table table-hover table-condensed table-bordered no-margin<?php echo $disabled ? '' : ' hidden'; ?>">
                    <thead>
                        <tr>
                            <th><?php _trans('required_fields'); ?></th>
                            <th style="min-width: 20%; text-align: center;"><?php echo $user_link; ?></th>
                            <th style="min-width: 20%; text-align: center;"><?php _trans('client'); ?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr<?php echo $req_einvoicing->tr_show_address_1 ? '' : ' class="hidden"'; ?>>
                            <td><?php echo trans('street_address'); ?></td>
                            <td class="text-center">
                                <i class="<?php echo $class_checks[$req_einvoicing->user_address_1] ?>"></i>
                            </td>
                            <td class="text-center">
                                <i class="<?php echo $class_checks[$req_einvoicing->client_address_1] ?>"></i>
                            </td>
                        </tr>

                        <tr<?php echo $req_einvoicing->tr_show_zip ? '' : ' class="hidden"'; ?>>
                            <td><?php _trans('zip_code'); ?></td>
                            <td class="text-center">
                                <i class="<?php echo $class_checks[$req_einvoicing->user_zip] ?>"></i>
                            </td>
                            <td class="text-center">
                                <i class="<?php echo $class_checks[$req_einvoicing->client_zip] ?>"></i>
                            </td>
                        </tr>

                        <tr<?php echo $req_einvoicing->tr_show_city ? '' : ' class="hidden"'; ?>>
                            <td><?php _trans('city'); ?></td>
                            <td class="text-center">
                                <i class="<?php echo $class_checks[$req_einvoicing->user_city] ?>"></i>
                            </td>
                            <td class="text-center">
                                <i class="<?php echo $class_checks[$req_einvoicing->client_city] ?>"></i>
                            </td>
                        </tr>

                        <tr<?php echo $req_einvoicing->tr_show_country ? '' : ' class="hidden"'; ?>>
                            <td><?php _trans('country'); ?></td>
                            <td class="text-center">
                                <i class="<?php echo $class_checks[$req_einvoicing->user_country] ?>"></i>
                            </td>
                            <td class="text-center">
                                <i class="<?php echo $class_checks[$req_einvoicing->client_country] ?>"></i>
                            </td>
                        </tr>

                        <tr<?php echo $req_einvoicing->tr_show_company ? '' : ' class="hidden"'; ?>>
                            <td><?php echo trans('company') . ' ' . trans('name'); ?></td>
                            <td class="text-center">
                                <i class="<?php echo $class_checks[$req_einvoicing->user_company] ?>"></i>
                            </td>
                            <td class="text-center">
                                <i class="<?php echo $class_checks[$req_einvoicing->client_company] ?>"></i>
                            </td>
                        </tr>

                        <tr<?php echo $req_einvoicing->tr_show_vat_id ? '' : ' class="hidden"'; ?>>
                            <td><?php _trans('vat_id'); ?></td>
                            <td class="text-center">
                                <i class="<?php echo $class_checks[$req_einvoicing->user_vat_id] ?>"></i>
                            </td>
                            <td class="text-center">
                                <i class="<?php echo $class_checks[$req_einvoicing->client_vat_id] ?>"></i>
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
