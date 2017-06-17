<script>
    $(function () {
        $('#btn_generate_cron_key').click(function () {
            $.post("<?php echo site_url('settings/ajax/get_cron_key'); ?>", function (data) {
                $('#cron_key').val(data);
            });
        });
    });
</script>

<div class="col-xs-12 col-md-8 col-md-offset-2">

    <div class="panel panel-default">
        <div class="panel-heading">
            <?php _trans('general'); ?>
        </div>
        <div class="panel-body">

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[default_language]">
                            <?php _trans('language'); ?>
                        </label>
                        <select name="settings[default_language]" id="settings[default_language]"
                                class="form-control simple-select">
                            <?php foreach ($languages as $language) {
                                $sys_lang = get_setting('default_language');
                                ?>
                                <option value="<?php echo $language; ?>" <?php check_select($sys_lang, $language) ?>>
                                    <?php echo ucfirst($language); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[system_theme]">
                            <?php _trans('theme'); ?>
                        </label>
                        <select name="settings[system_theme]" id="settings[system_theme]"
                                class="form-control simple-select">
                            <?php foreach ($available_themes as $theme_key => $theme_name) { ?>
                                <option value="<?php echo $theme_key; ?>" <?php check_select(get_setting('system_theme'), $theme_key); ?>>
                                    <?php echo $theme_name; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[first_day_of_week]">
                            <?php _trans('first_day_of_week'); ?>
                        </label>
                        <select name="settings[first_day_of_week]" id="settings[first_day_of_week]"
                                class="form-control simple-select">
                            <?php foreach ($first_days_of_weeks as $first_day_of_week_id => $first_day_of_week_name) { ?>
                                <option value="<?php echo $first_day_of_week_id; ?>"
                                    <?php check_select(get_setting('first_day_of_week'), $first_day_of_week_id); ?>>
                                    <?php echo $first_day_of_week_name; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[date_format]">
                            <?php _trans('date_format'); ?>
                        </label>
                        <select name="settings[date_format]" id="settings[date_format]"
                                class="form-control simple-select">
                            <?php foreach ($date_formats as $date_format) { ?>
                                <option value="<?php echo $date_format['setting']; ?>"
                                    <?php check_select(get_setting('date_format'), $date_format['setting']); ?>>
                                    <?php echo $current_date->format($date_format['setting']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[default_country]">
                            <?php _trans('default_country'); ?>
                        </label>
                        <select name="settings[default_country]" id="settings[default_country]"
                                class="form-control simple-select">
                            <option value=""><?php _trans('none'); ?></option>
                            <?php foreach ($countries as $cldr => $country) { ?>
                                <option value="<?php echo $cldr; ?>" <?php check_select(get_setting('default_country'), $cldr); ?>>
                                    <?php echo $country ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading">
            <?php _trans('amount_settings'); ?>
        </div>
        <div class="panel-body">

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[currency_symbol]">
                            <?php _trans('currency_symbol'); ?>
                        </label>
                        <input type="text" name="settings[currency_symbol]" id="settings[currency_symbol]"
                               class="form-control"
                               value="<?php echo get_setting('currency_symbol', '', true); ?>">
                    </div>
                </div>

                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[currency_symbol_placement]">
                            <?php _trans('currency_symbol_placement'); ?>
                        </label>
                        <select name="settings[currency_symbol_placement]" id="settings[currency_symbol_placement]"
                                class="form-control simple-select">
                            <option value="before" <?php check_select(get_setting('currency_symbol_placement'), 'before'); ?>>
                                <?php _trans('before_amount'); ?>
                            </option>
                            <option value="after" <?php check_select(get_setting('currency_symbol_placement'), 'after'); ?>>
                                <?php _trans('after_amount'); ?>
                            </option>
                            <option value="afterspace" <?php check_select(get_setting('currency_symbol_placement'), 'afterspace'); ?>>
                                <?php _trans('after_amount_space'); ?>
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[thousands_separator]">
                            <?php _trans('thousands_separator'); ?>
                        </label>
                        <input type="text" name="settings[thousands_separator]" id="settings[thousands_separator]"
                               class="form-control"
                               value="<?php echo get_setting('thousands_separator', '', true); ?>">
                    </div>
                </div>

                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[decimal_point]">
                            <?php _trans('decimal_point'); ?>
                        </label>
                        <input type="text" name="settings[decimal_point]" id="settings[decimal_point]"
                               class="form-control"
                               value="<?php echo get_setting('decimal_point', '', true); ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="tax_rate_decimal_places">
                            <?php _trans('tax_rate_decimal_places'); ?>
                        </label>
                        <select name="settings[tax_rate_decimal_places]" class="form-control simple-select"
                                id="tax_rate_decimal_places">
                            <option value="2" <?php check_select(get_setting('tax_rate_decimal_places'), '2'); ?>>
                                2
                            </option>
                            <option value="3" <?php check_select(get_setting('tax_rate_decimal_places'), '3'); ?>>
                                3
                            </option>
                        </select>
                    </div>
                </div>

                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="default_list_limit">
                            <?php _trans('default_list_limit'); ?>
                        </label>
                        <select name="settings[default_list_limit]" class="form-control simple-select"
                                id="default_list_limit">
                            <option value="15" <?php check_select(get_setting('default_list_limit'), '15'); ?>>
                                15
                            </option>
                            <option value="25" <?php check_select(get_setting('default_list_limit'), '25'); ?>>
                                25
                            </option>
                            <option value="50" <?php check_select(get_setting('default_list_limit'), '50'); ?>>
                                50
                            </option>
                            <option value="100" <?php check_select(get_setting('default_list_limit'), '100'); ?>>
                                100
                            </option>
                        </select>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading">
            <?php _trans('dashboard'); ?>
        </div>
        <div class="panel-body">

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[quote_overview_period]">
                            <?php _trans('quote_overview_period'); ?>
                        </label>
                        <select name="settings[quote_overview_period]" id="settings[quote_overview_period]"
                                class="form-control simple-select">
                            <option value="this-month" <?php check_select(get_setting('quote_overview_period'), 'this-month'); ?>>
                                <?php _trans('this_month'); ?>
                            </option>
                            <option value="last-month" <?php check_select(get_setting('quote_overview_period'), 'last-month'); ?>>
                                <?php _trans('last_month'); ?>
                            </option>
                            <option value="this-quarter" <?php check_select(get_setting('quote_overview_period'), 'this-quarter'); ?>>
                                <?php _trans('this_quarter'); ?>
                            </option>
                            <option value="last-quarter" <?php check_select(get_setting('quote_overview_period'), 'last-quarter'); ?>>
                                <?php _trans('last_quarter'); ?>
                            </option>
                            <option value="this-year" <?php check_select(get_setting('quote_overview_period'), 'this-year'); ?>>
                                <?php _trans('this_year'); ?>
                            </option>
                            <option value="last-year" <?php check_select(get_setting('quote_overview_period'), 'last-year'); ?>>
                                <?php _trans('last_year'); ?>
                            </option>
                        </select>
                    </div>
                </div>

                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[invoice_overview_period]">
                            <?php _trans('invoice_overview_period'); ?>
                        </label>
                        <select name="settings[invoice_overview_period]" id="settings[invoice_overview_period]"
                                class="form-control simple-select">
                            <option value="this-month" <?php check_select(get_setting('invoice_overview_period'), 'this-month'); ?>>
                                <?php _trans('this_month'); ?>
                            </option>
                            <option value="last-month" <?php check_select(get_setting('invoice_overview_period'), 'last-month'); ?>>
                                <?php _trans('last_month'); ?>
                            </option>
                            <option value="this-quarter" <?php check_select(get_setting('invoice_overview_period'), 'this-quarter'); ?>>
                                <?php _trans('this_quarter'); ?>
                            </option>
                            <option value="last-quarter" <?php check_select(get_setting('invoice_overview_period'), 'last-quarter'); ?>>
                                <?php _trans('last_quarter'); ?>
                            </option>
                            <option value="this-year" <?php check_select(get_setting('invoice_overview_period'), 'this-year'); ?>>
                                <?php _trans('this_year'); ?>
                            </option>
                            <option value="last-year" <?php check_select(get_setting('invoice_overview_period'), 'last-year'); ?>>
                                <?php _trans('last_year'); ?>
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="disable_quickactions">
                            <?php _trans('disable_quickactions'); ?>
                        </label>
                        <select name="settings[disable_quickactions]" class="form-control simple-select"
                                id="disable_quickactions">
                            <option value="0">
                                <?php _trans('no'); ?>
                            </option>
                            <option value="1" <?php check_select(get_setting('disable_quickactions'), '1'); ?>>
                                <?php _trans('yes'); ?>
                            </option>
                        </select>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <?php _trans('interface'); ?>
        </div>
        <div class="panel-body">

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="disable_sidebar">
                            <?php _trans('disable_sidebar'); ?>
                        </label>
                        <select name="settings[disable_sidebar]" class="form-control simple-select"
                                id="disable_sidebar">
                            <option value="0">
                                <?php _trans('no'); ?>
                            </option>
                            <option value="1" <?php check_select(get_setting('disable_sidebar'), '1'); ?>>
                                <?php _trans('yes'); ?>
                            </option>
                        </select>
                    </div>
                </div>

                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[custom_title]">
                            <?php _trans('custom_title'); ?>
                        </label>
                        <input type="text" name="settings[custom_title]" id="settings[custom_title]"
                               class="form-control"
                               value="<?php echo get_setting('custom_title', '', true); ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-6">

                    <div class="form-group">
                        <label for="monospace_amounts">
                            <?php _trans('monospaced_font_for_amounts'); ?>
                        </label>
                        <select name="settings[monospace_amounts]" class="form-control simple-select"
                                id="monospace_amounts">
                            <option value="0"><?php _trans('no'); ?></option>
                            <option value="1" <?php check_select(get_setting('monospace_amounts'), '1'); ?>>
                                <?php _trans('yes'); ?>
                            </option>
                        </select>

                        <p class="help-block">
                            <?php _trans('example'); ?>:
                            <span style="font-family: Monaco, Lucida Console, monospace">
                        <?php echo format_currency(123456.78); ?>
                    </span>
                        </p>
                    </div>

                    <div class="form-group">
                        <label for="login_logo">
                            <?php _trans('login_logo'); ?>
                        </label>
                        <?php if (get_setting('login_logo')) { ?>
                            <img class="personal_logo"
                                 src="<?php echo base_url(); ?>uploads/<?php echo get_setting('login_logo'); ?>"><br>
                            <?php echo anchor('settings/remove_logo/login', 'Remove Logo'); ?><br>
                        <?php } ?>
                        <input type="file" name="login_logo" id="login_logo" class="form-control"/>
                    </div>

                </div>
                <div class="col-xs-12 col-md-6">

                    <div class="form-group">
                        <label for="settings[reports_in_new_tab]">
                            <?php _trans('open_reports_in_new_tab'); ?>
                        </label>
                        <select name="settings[reports_in_new_tab]" id="settings[reports_in_new_tab]"
                                class="form-control simple-select">
                            <option value="0"><?php _trans('no'); ?></option>
                            <option value="1" <?php check_select(get_setting('reports_in_new_tab'), '1'); ?>>
                                <?php _trans('yes'); ?>
                            </option>
                        </select>
                    </div>


                </div>
            </div>

        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <?php _trans('system_settings'); ?>
        </div>
        <div class="panel-body">

            <div class="row">
                <div class="col-xs-12 col-md-6">

                    <div class="form-group">
                        <label for="settings[bcc_mails_to_admin]">
                            <?php _trans('bcc_mails_to_admin'); ?>
                        </label>
                        <select name="settings[bcc_mails_to_admin]" id="settings[bcc_mails_to_admin]"
                                class="form-control simple-select">
                            <option value="0"><?php _trans('no'); ?></option>
                            <option value="1" <?php check_select(get_setting('bcc_mails_to_admin'), '1'); ?>>
                                <?php _trans('yes'); ?>
                            </option>
                        </select>

                        <p class="help-block"><?php _trans('bcc_mails_to_admin_hint'); ?></p>
                    </div>

                </div>
                <div class="col-xs-12 col-md-6">

                    <div class="form-group">
                        <label for="cron_key">
                            <?php _trans('cron_key'); ?>
                        </label>
                        <div class="input-group">
                            <input type="text" name="settings[cron_key]" id="cron_key" class="form-control" readonly
                                   value="<?php echo get_setting('cron_key'); ?>">
                            <div class="input-group-btn">
                                <button id="btn_generate_cron_key" type="button" class="btn btn-primary btn-block">
                                    <i class="fa fa-recycle fa-margin"></i> <?php _trans('generate'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>
