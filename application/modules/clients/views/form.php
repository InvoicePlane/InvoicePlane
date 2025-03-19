<?php
$client_active = $this->mdl_clients->form_value('client_active');
$active        = ($client_active == 1 || ! is_numeric($client_active)) ? ' checked="checked"' : '';
// e-Invoicing panel
$nb_users      = count($req_einvoicing->users);
$me            = $req_einvoicing->users[$_SESSION['user_id']]->show_table;
$nb            = $req_einvoicing->show_table; // Of users in error
$ln            = 'user' . (($nb ?: $nb_users) > 1 ? 's' : ''); // tweak 1 on more nb_users no ok
$user_toggle   = ($req_einvoicing->show_table ? ($me ? 'danger' : 'warning') : 'default') . ' ' . ($me ? '" aria-expanded="true' : '" collapsed" aria-expanded="false');
?>
<script type="text/javascript">
    // e-Invoicing button panel helper user(s) icon toggle
    const switch_fa_toggle = function (id) {
        const f = $('#'+id);f.toggleClass('fa-user').toggleClass('fa-users');
    }

    $(function () {
        $("#client_country").select2({
            placeholder: "<?php _trans('country'); ?>",
            allowClear: true
        });

<?php $this->layout->load_view('clients/js/script_select_client_title.js'); ?>

    });

</script>

<form method="post">
    <?php _csrf_field(); ?>

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('client_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>
    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <input class="hidden" name="is_update" type="hidden" value="<?php echo $this->mdl_clients->form_value('is_update') ? '1' : '0'; ?>">

        <div class="row"><!-- personal + e-invoice -->
            <div class="col-xs-12 col-sm-6">

                <div class="panel panel-default">
                    <div class="panel-heading form-inline clearfix">
                        <?php _trans('personal_information'); ?>
                        <div class="pull-right">
                            <label for="client_active" class="control-label">
                                <?php _trans('active_client'); ?>
                                <input id="client_active" name="client_active" type="checkbox" value="1"<?php echo $active; ?>>
                            </label>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="client_name">
                                <?php _trans('client_name'); ?>
                            </label>
                            <input id="client_name" name="client_name" type="text" class="form-control"
                                   autofocus
                                   value="<?php echo $this->mdl_clients->form_value('client_name', true); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="client_surname">
                                <?php _trans('client_surname_optional'); ?>
                            </label>
                            <input id="client_surname" name="client_surname" type="text" class="form-control"
                                   value="<?php echo $this->mdl_clients->form_value('client_surname', true); ?>">
                        </div>
                        <div class="form-group" data-toggle="tooltip" data-placement="bottom" title="e-<?php _trans('invoicing'); ?> (B2B <?php _trans('required_field'); ?>)">
                            <label for="client_company"><?php _trans('client_company'); ?></label>

                            <div class="controls input-group">
                                <input id="client_company" name="client_company" type="text" class="form-control"
                                       value="<?php echo $this->mdl_clients->form_value('client_company', true); ?>">
                                <span class="input-group-addon">e-<?php _trans('invoicing'); ?></span>
                            </div>
                        </div>
                        <div class="form-group no-margin">
                            <label for="client_language">
                                <?php _trans('language'); ?>
                            </label>
                            <select name="client_language" id="client_language" class="form-control simple-select">
                                <option value="system">
                                    <?php _trans('use_system_language') ?>
                                </option>
<?php
foreach ($languages as $language)
{
    $client_lang = $this->mdl_clients->form_value('client_language');
?>
                                <option value="<?php echo $language; ?>"
                                    <?php check_select($client_lang, $language) ?>>
                                    <?php echo ucfirst($language); ?>
                                </option>
<?php
}
?>
                            </select>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="panel panel-default"><!-- eInvoicing panel -->

                    <div class="panel-heading">
                        e-<?php _trans('invoicing'); ?>
                        <span class="<?php echo $client_id ? 'pull-right' : 'hidden'; ?> toggle_einvoicing<?php
                              echo ! $req_einvoicing->show_table
                                   ? ''
                                   : ' btn btn-xs btn-default cursor-pointer alert-' . $user_toggle . '"
                              data-toggle="collapse" data-target=".einvoice-user-check-lists"
                              onclick="switch_fa_toggle(\'einvoice_users_check_fa_toggle\')';
                        ?>">
                            <i class="fa fa-<?php echo $nb ? ($me ? 'ban' : 'warning') : 'check-square-o text-success'; ?>"></i>
                            <span data-toggle="tooltip" data-placement="bottom" title="<?php echo '🗸 ' . ($nb_users - $nb) . '/' . $nb_users . ' ' . trans('user' . ($nb_users > 1 ? 's' : '')); ?>">
                                <?php echo ($nb ?: $nb_users) . ' ' . trans($ln); ?>
                            </span>
                            <i id="einvoice_users_check_fa_toggle" class="fa fa-<?php echo $nb ? 'user' . ($me ? '' : 's') : 'file-code-o'; ?> fa-margin"></i>
                        </span>


                    </div>


                    <div class="panel-body">
<?php
if ($this->mdl_clients->form_value('client_id'))
{
    $this->layout->load_view('clients/partial_client_einvoicing');
}
else
{
?>
                        <div class="alert alert-warning small" style="font-size:medium;">
                            <i class="fa fa-exclamation-triangle fa-2x"></i>&nbsp;
                            <?php _trans('einvoicing_no_enabled_hint'); ?>
                        </div>
<?php
} // End if
?>
                    </div>
                </div>

            </div>
        </div>

        <div class="row"><!-- Address + contact -->
            <div class="col-xs-12 col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php _trans('address'); ?>
                    </div>

                    <div class="panel-body">
                        <div class="form-group" data-toggle="tooltip" data-placement="bottom" title="e-<?php _trans('invoicing'); ?> (<?php _trans('required_field'); ?>)">
                            <label for="client_address_1"><?php _trans('street_address'); ?></label>

                            <div class="controls input-group">
                                <input type="text" name="client_address_1" id="client_address_1" class="form-control"
                                       value="<?php echo $this->mdl_clients->form_value('client_address_1', true); ?>">
                                <span class="input-group-addon">e-<?php _trans('invoicing'); ?></span>
                            </div>
                        </div>

                        <div class="form-group" data-toggle="tooltip" data-placement="bottom" title="e-<?php _trans('invoicing'); ?> (<?php _trans('optional'); ?>)">
                            <label for="client_address_2"><?php _trans('street_address_2'); ?></label>

                            <div class="controls input-group">
                                <input type="text" name="client_address_2" id="client_address_2" class="form-control"
                                       value="<?php echo $this->mdl_clients->form_value('client_address_2', true); ?>">
                                <span class="input-group-addon">e-<?php _trans('invoicing'); ?></span>
                            </div>
                        </div>

                        <div class="form-group" data-toggle="tooltip" data-placement="bottom" title="e-<?php _trans('invoicing'); ?> (<?php _trans('required_field'); ?>)">
                            <label for="client_city"><?php _trans('city'); ?></label>

                            <div class="controls input-group">
                                <input type="text" name="client_city" id="client_city" class="form-control"
                                       value="<?php echo $this->mdl_clients->form_value('client_city', true); ?>">
                                <span class="input-group-addon">e-<?php _trans('invoicing'); ?></span>
                            </div>
                        </div>

                        <div class="form-group" data-toggle="tooltip" data-placement="bottom" title="e-<?php _trans('invoicing'); ?> (<?php _trans('optional'); ?>)">
                            <label for="client_state"><?php _trans('state'); ?></label>

                            <div class="controls input-group">
                                <input type="text" name="client_state" id="client_state" class="form-control"
                                       value="<?php echo $this->mdl_clients->form_value('client_state', true); ?>">
                                <span class="input-group-addon">e-<?php _trans('invoicing'); ?></span>
                            </div>
                        </div>

                        <div class="form-group" data-toggle="tooltip" data-placement="bottom" title="e-<?php _trans('invoicing'); ?> (<?php _trans('required_field'); ?>)">
                            <label for="client_zip"><?php _trans('zip_code'); ?></label>

                            <div class="controls input-group">
                                <input type="text" name="client_zip" id="client_zip" class="form-control"
                                       value="<?php echo $this->mdl_clients->form_value('client_zip', true); ?>">
                                <span class="input-group-addon">e-<?php _trans('invoicing'); ?></span>
                            </div>
                        </div>

                        <div class="form-group" data-toggle="tooltip" data-placement="bottom" title="e-<?php _trans('invoicing'); ?> (<?php _trans('required_field'); ?>)">
                            <label for="client_country"><?php _trans('country'); ?></label>

                            <div class="controls input-group">
                                <select name="client_country" id="client_country" class="form-control">
                                    <option value=""><?php _trans('none'); ?></option>
                                    <?php foreach ($countries as $cldr => $country) { ?>
                                        <option value="<?php echo $cldr; ?>"
                                            <?php check_select($selected_country, $cldr); ?>
                                        ><?php echo $country ?></option>
                                    <?php } ?>
                                </select>
                                <span class="input-group-addon">e-<?php _trans('invoicing'); ?></span>
                            </div>
                        </div>

<?php
foreach ($custom_fields as $custom_field)
{
    if ($custom_field->custom_field_location == 1)
    {
        print_field($this->mdl_clients, $custom_field, $custom_values);
    }
}
?>
                    </div>

                </div>

            </div>
            <div class="col-xs-12 col-sm-6">

                <div class="panel panel-default">

                    <div class="panel-heading">
                        <?php _trans('contact_information'); ?>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="client_invoicing_contact"><?php _trans('contact'); ?> (<?php _trans('invoicing'); ?>)</label>

                            <div class="controls">
                                <input type="text" name="client_invoicing_contact" id="client_invoicing_contact" class="form-control"
                                    value="<?php echo htmlsc($this->mdl_clients->form_value('client_invoicing_contact')); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="client_phone"><?php _trans('phone_number'); ?></label>

                            <div class="controls">
                                <input type="text" name="client_phone" id="client_phone" class="form-control"
                                       value="<?php echo $this->mdl_clients->form_value('client_phone', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="client_fax"><?php _trans('fax_number'); ?></label>

                            <div class="controls">
                                <input type="text" name="client_fax" id="client_fax" class="form-control"
                                       value="<?php echo $this->mdl_clients->form_value('client_fax', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="client_mobile"><?php _trans('mobile_number'); ?></label>

                            <div class="controls">
                                <input type="text" name="client_mobile" id="client_mobile" class="form-control"
                                       value="<?php echo $this->mdl_clients->form_value('client_mobile', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="client_email"><?php _trans('email_address'); ?></label>

                            <div class="controls">
                                <input type="text" name="client_email" id="client_email" class="form-control"
                                       value="<?php echo $this->mdl_clients->form_value('client_email', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="client_web"><?php _trans('web_address'); ?></label>

                            <div class="controls">
                                <input type="text" name="client_web" id="client_web" class="form-control"
                                       value="<?php echo $this->mdl_clients->form_value('client_web', true); ?>">
                            </div>
                        </div>

<?php
foreach ($custom_fields as $custom_field)
{
    if ($custom_field->custom_field_location == 2)
    {
        print_field($this->mdl_clients, $custom_field, $custom_values);
    }
}
?>
                    </div>

                </div>

            </div>
        </div>

        <div class="row"><!-- Tax + Persona -->
            <div class="col-xs-12 col-sm-6">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php _trans('tax_information'); ?>
                    </div>

                    <div class="panel-body">
                        <div class="form-group" data-toggle="tooltip" data-placement="bottom" title="e-<?php _trans('invoicing'); ?> (B2B <?php _trans('required_field'); ?>)">
                            <label for="client_vat_id"><?php _trans('vat_id'); ?></label>

                            <div class="controls input-group">
                                <input type="text" name="client_vat_id" id="client_vat_id" class="form-control"
                                       value="<?php echo $this->mdl_clients->form_value('client_vat_id', true); ?>">
                                <span class="input-group-addon">e-<?php _trans('invoicing'); ?></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="client_tax_code"><?php _trans('tax_code'); ?></label>

                            <div class="controls">
                                <input type="text" name="client_tax_code" id="client_tax_code" class="form-control"
                                       value="<?php echo $this->mdl_clients->form_value('client_tax_code', true); ?>">
                            </div>
                        </div>

<?php
foreach ($custom_fields as $custom_field)
{
    if ($custom_field->custom_field_location == 4)
    {
        print_field($this->mdl_clients, $custom_field, $custom_values);
    }
}
?>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="panel panel-default">

                    <div class="panel-heading">
                        <?php _trans('personal_information'); ?>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="client_gender"><?php _trans('gender'); ?></label>
                            <div class="controls">
                                <select name="client_gender" id="client_gender"
                                        class="form-control simple-select" data-minimum-results-for-search="Infinity">
<?php
$genders = [
    trans('gender_male'),
    trans('gender_female'),
    trans('gender_other'),
];
$client_gender = $this->mdl_clients->form_value('client_gender');
foreach ($genders as $key => $val) {
?>
                                    <option value=" <?php echo $key; ?>" <?php check_select($key, $client_gender) ?>>
                                        <?php echo $val; ?>
                                    </option>
<?php
}
?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
<?php
$client_title    = $this->mdl_clients->form_value('client_title');
$is_custom_title = null === ClientTitleEnum::tryFrom($client_title);
?>
                            <label for="client_title"><?php _trans('client_title'); ?></label>
                            <select name="client_title" id="client_title" class="form-control simple-select">
<?php
foreach ($client_title_choices as $client_title_choice)
{
?>
                                <option
                                    value="<?php echo $client_title_choice; ?>"
                                    <?php echo $client_title === $client_title_choice ? 'selected' : ''; ?>
                                    <?php echo $is_custom_title && $client_title_choice === ClientTitleEnum::CUSTOM ? 'selected' : ''; ?>
                                >
                                    <?php echo ucfirst(trans($client_title_choice)); ?>
                                </option>
<?php
}
?>
                            </select>
                        </div>
                        <div class="form-group">
                            <input
                                id="client_title_custom"
                                name="client_title_custom"
                                type="text"
                                class="form-control <?php echo $client_title === ClientTitleEnum::CUSTOM || $is_custom_title ? '' : 'hidden' ?>"
                                placeholder=<?php echo trans('custom_title') ?>
                                value="<?php echo $this->mdl_clients->form_value('client_title', true); ?>"
                            >
                        </div>
                        <div class="form-group has-feedback">
                            <label for="client_birthdate"><?php _trans('birthdate'); ?></label>
<?php
$bdate = $this->mdl_clients->form_value('client_birthdate');
$bdate = ($bdate && $bdate != '0000-00-00') ? date_from_mysql($bdate) : '';
?>
                            <div class="input-group">
                                <input type="text" name="client_birthdate" id="client_birthdate"
                                    class="form-control datepicker"
                                    value="<?php _htmlsc($bdate); ?>">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar fa-fw"></i>
                                </span>
                            </div>
                        </div>

<?php
if ($this->mdl_settings->setting('sumex') == '1')
{
    $avs = format_avs($this->mdl_clients->form_value('client_avs'));
    $insuredNumber = $this->mdl_clients->form_value('client_insurednumber');
    $veka = $this->mdl_clients->form_value('client_veka');
?>

                        <div class="form-group">
                            <label for="client_avs"><?php _trans('sumex_ssn'); ?></label>
                            <div class="controls">
                                <input type="text" name="client_avs" id="client_avs" class="form-control"
                                       value="<?php _htmlsc($avs); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="client_insurednumber"><?php _trans('sumex_insurednumber'); ?></label>
                            <div class="controls">
                                <input type="text" name="client_insurednumber" id="client_insurednumber" class="form-control"
                                       value="<?php _htmle($insuredNumber); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="client_veka"><?php _trans('sumex_veka'); ?></label>
                            <div class="controls">
                                <input type="text" name="client_veka" id="client_veka" class="form-control"
                                       value="<?php _htmle($veka); ?>">
                            </div>
                        </div>

<?php
} // End if sumex
?>

<?php
$default_custom = false;
foreach ($custom_fields as $custom_field)
{
    if( ! $default_custom && ! $custom_field->custom_field_location) $default_custom = true;

    if ($custom_field->custom_field_location == 3)
    {
        print_field($this->mdl_clients, $custom_field, $custom_values);
    }
}
?>
                    </div>

                </div>

            </div>
        </div>

<?php
if ($default_custom)
{
?>
            <div class="row">
                <div class="col-xs-12">

                    <hr>

                    <div class="panel panel-default">
                        <div class="panel-heading"><?php _trans('custom_fields'); ?></div>
                        <div class="panel-body">
                            <div class="row">
<?php
    $classes = ['control-label', 'controls', '', 'form-group col-xs-12 col-sm-6'];
    foreach ($custom_fields as $custom_field)
    {
        if (! $custom_field->custom_field_location) // == 0
        {
            print_field($this->mdl_clients, $custom_field, $custom_values, $classes[0], $classes[1], $classes[2], $classes[3]);
        }
    }
?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
<?php
} // End if custom_fields
?>
    </div>
</form>
