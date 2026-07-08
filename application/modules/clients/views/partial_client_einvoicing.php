<script type="text/javascript">
    $(function () {
        $('#btn_peppol_validate').on('click', function () {
            var participantId = $('#client_peppol_id').val().trim();
            var $result = $('#peppol_validate_result');

            if (!participantId) return;

            $result.html('<span class="text-muted"><?php _trans('peppol_validate'); ?>…</span>').show();

            $.post('<?php echo site_url('integrations/validate_participant'); ?>',
                {participant_id: participantId},
                function (data) {
                    if (data.reachable) {
                        $result.html('<span class="text-success"><i class="fa fa-check"></i> <?php _trans('peppol_reachable'); ?>' +
                            (data.name ? ' — ' + $('<span>').text(data.name).html() : '') + '</span>');
                    } else {
                        $result.html('<span class="text-danger"><i class="fa fa-times"></i> <?php _trans('peppol_not_reachable'); ?></span>');
                    }
                },
                'json'
            ).fail(function () {
                $result.html('<span class="text-danger"><i class="fa fa-times"></i> <?php _trans('peppol_not_reachable'); ?></span>');
            });
        });
    });

    $(function () {
        // Check if found on Peppol network (Post to integrations/validate_participant)
        $('#btn_peppol_validate').on('click', function () {
            var participantId = $('#client_peppol_id').val().trim();
            var $result = $('#peppol_validate_result');

            if (!participantId) return;

            $result.html('<span class="text-muted"><?php _trans('peppol_validate'); ?>…</span>').show();

            $.post('<?php echo site_url('integrations/validate_participant'); ?>',
                {participant_id: participantId},
                function (data) {
                    if (data.reachable) {
                        $result.html('<span class="text-success"><i class="fa fa-check"></i> <?php _trans('peppol_reachable'); ?>' +
                            (data.name ? ' — ' + $('<span>').text(data.name).html() : '') + '</span>');
                    } else {
                        $result.html('<span class="text-danger"><i class="fa fa-times"></i> <?php _trans('peppol_not_reachable'); ?></span>');
                    }
                },
                'json'
            ).fail(function () {
                $result.html('<span class="text-danger"><i class="fa fa-times"></i> <?php _trans('peppol_not_reachable'); ?></span>');
            });
        });

        // Cache jQuery selectors
        const $client_start_einvoicing = $('#client_start_einvoicing');
        const $toggle_einvoicing = $('.toggle_einvoicing');

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
                $toggle_einvoicing.show();
            } else {
                $toggle_einvoicing.hide();
            }
        }
    });
</script>

<div class="row<?php echo $xml_templates ? '' : ' hidden'; ?>">
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
$disabled                              = ''; // hint (And little tweak for .help-block)
            $client_einvoicing_version = $this->mdl_clients->form_value('client_einvoicing_version');
            // Check logged user e-invoice fields (show_table 0 = ok, 1 = no)
            if ($req_einvoicing->users[$_SESSION['user_id']]->show_table > 0) {
                $disabled = ' disabled="disabled"';
                ?>
            <p class="help-block"><?php _trans('einvoicing_start_hint'); ?></p>
<?php
            }
            ?>
        </div>

    </div>

    <div class="toggle_einvoicing">
        <div class="col-xs-12 col-md-6">

            <div class="form-group">
                <label for="client_einvoicing_version"><?php echo 'UBL / CII ' . trans('version'); ?></label>

                <select name="client_einvoicing_version" id="client_einvoicing_version" class="form-control simple-select"<?php echo $disabled; ?>>
                    <option value=""><?php _trans('none'); ?></option>
<?php
foreach ($xml_templates as $xml_key => $xml_template) {
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
            $class_checks = ['fa fa-lg fa-check-square-o text-success', 'fa fa-lg fa-edit text-warning']; // Checkboxe icons
            $base         = 'address_1 zip city country company tax_code vat_id'; // Field names
            $keys         = explode(' ', $base); // To array
            $lang         = explode(' ', strtr($base, ['_1' => ''])); // Translation vars name
            // Users loop
            foreach ($req_einvoicing->users as $user_id => $user) {
                if ($user->show_table) {
                    $title_tip = ' data-toggle="tooltip" data-placement="bottom" title="' . trans('edit'); // Tooltip helper ! Need add: . '"'
                    $user_link = anchor('/users/form/' . $user_id, trans('user'), $title_tip . ' ' . htmlsc($user->user_name) . '"'); // ! Need add: . '"'
                    $open      = $user_id == $_SESSION['user_id'] && $req_einvoicing->users[$_SESSION['user_id']]->show_table;
                    $me        = $user_id == $_SESSION['user_id'];
                    ?>
        <!-- Check if mandatory eInvoicing fields are empty -->
        <div class="col-xs-12 col-md-6 einvoice-user-check-lists collapse<?php echo $open ? ' in" aria-expanded="true' : '" aria-expanded="false'; ?>">
            <div class="form-group" data-toggle="tooltip" data-placement="top" title="<?php _htmlsc($user->user_name); ?>">
                <div class="table-responsive">
                    <table class="table table-hover table-condensed table-bordered no-margin">
                        <thead class="text-center">
                            <tr>
                                <th><?php _trans('required_fields'); ?></th>
                                <th class="text-center" style="min-width:20%;"><?php _trans('client'); ?></th>
                                <th class="text-center" style="min-width:20%;"><?php echo $user_link; ?></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-center alert-<?php echo $me ? 'danger' : 'warning'; ?>" title="<?php _trans('username'); ?>">
                                    <small class="te te-1 dib"><i class="fa fa-fw fa-user"></i><?php _htmlsc($user->user_name); ?></small>
                                </th>
                            </tr>
                        </tfoot>

                        <tbody>
<?php
                            // Loop on required keys
                            foreach ($keys as $l => $key) {
                                // tr_show_* (attr name)
                                $tr_show_key = 'tr_show_' . $key;
                                // Show it in Errors (1)
                                if ($user->{$tr_show_key}) {
                                    // Prepare some stuff
                                    $c_icon = '<i class="' . $class_checks[$req_einvoicing->clients[$client_id]->{$key}] . '"></i>';
                                    $u_icon = '<i class="' . $class_checks[$user->{$key}] . '"></i>';
                                    ?>
                            <tr>
                                <td><?php _trans($lang[$l]); ?></td>
                                <td class="text-center">
                                    <?php echo anchor('clients/form/' . $client_id . '#client_' . $key, $c_icon, $title_tip . ' #' . trans($lang[$l]) . ' (' . mb_trim(trans('field')) . ')"'); ?>
                                </td>
                                <td class="text-center">
                                    <?php echo anchor('users/form/' . $user_id . '#user_' . $key, $u_icon, $title_tip . ' ' . htmlsc($user->user_name) . ' #' . trans($lang[$l]) . ' (' . mb_trim(trans('field')) . ')"'); ?>
                                </td>
                            </tr>
<?php
                                } // tr show
                            } // End Foreach $keys
                    ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
<?php
                } // End if user->show_table
            } // End foreach einvoicing->users

            ?>
    </div>

    <div class="toggle_einvoicing">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="client_peppol_id"><?php _trans('peppol_participant_id'); ?></label>
                <div class="input-group">
                    <input type="text" id="client_peppol_id" name="client_peppol_id"
                           class="form-control"
                           placeholder="0130:27325502"
                           value="<?php echo htmlsc($this->mdl_clients->form_value('client_peppol_id')); ?>">
                    <span class="input-group-btn">
                        <button type="button" id="btn_peppol_validate" class="btn btn-sm btn-default"
                                <?php echo $has_peppol_provider ? '' : 'disabled="disabled"'; ?>>
                            <?php _trans('peppol_validate'); ?>
                        </button>
                    </span>
                </div>
                <p class="help-block"><?php _trans('peppol_participant_id_help'); ?></p>
                <div id="peppol_validate_result" style="display:none;margin-top:4px;"></div>
            </div>
        </div>
    </div>

</div>
