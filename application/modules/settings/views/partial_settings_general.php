<script>
    $(function () {
        $('#btn_generate_cron_key').click(function () {
            $.post("<?php echo site_url('settings/ajax/get_cron_key'); ?>", function (data) {
                $('#cron_key').val(data);
            });
        });
    });
</script>

<div class="tab-info">

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="settings[default_language]" class="control-label">
                    <?php echo trans('language'); ?>
                </label>
                <select name="settings[default_language]" class=" form-control simple-select">
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
                <label for="settings[system_theme]" class="control-label">
                    <?php echo trans('theme'); ?>
                </label>
                <select name="settings[system_theme]" class=" form-control simple-select">
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
                <label for="settings[first_day_of_week]" class="control-label">
                    <?php echo trans('first_day_of_week'); ?>
                </label>
                <select name="settings[first_day_of_week]" class=" form-control simple-select">
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
                <label for="settings[date_format]" class="control-label">
                    <?php echo trans('date_format'); ?>
                </label>
                <select name="settings[date_format]" class=" form-control simple-select">
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
                <label for="settings[default_country]" class="control-label">
                    <?php echo trans('default_country'); ?>
                </label>
                <select name="settings[default_country]" class=" form-control simple-select">
                    <option value=""><?php echo trans('none'); ?></option>
                    <?php foreach ($countries as $cldr => $country) { ?>
                        <option value="<?php echo $cldr; ?>" <?php check_select(get_setting('default_country'), $cldr); ?>>
                            <?php echo $country ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>

    <hr/>
    <h4><?php echo trans('amount_settings'); ?></h4>
    <br/>

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label class="control-label">
                    <?php echo trans('currency_symbol'); ?>
                </label>
                <input type="text" name="settings[currency_symbol]" class=" form-control"
                       value="<?php echo get_setting('currency_symbol'); ?>">
            </div>
        </div>

        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="settings[currency_symbol_placement]" class="control-label">
                    <?php echo trans('currency_symbol_placement'); ?>
                </label>
                <select name="settings[currency_symbol_placement]" class=" form-control simple-select">
                    <option value="before" <?php check_select(get_setting('currency_symbol_placement'), 'before'); ?>>
                        <?php echo trans('before_amount'); ?>
                    </option>
                    <option value="after" <?php check_select(get_setting('currency_symbol_placement'), 'after'); ?>>
                        <?php echo trans('after_amount'); ?>
                    </option>
                    <option value="afterspace" <?php check_select(get_setting('currency_symbol_placement'), 'afterspace'); ?>>
                        <?php echo trans('after_amount_space'); ?>
                    </option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="settings[thousands_separator]" class="control-label">
                    <?php echo trans('thousands_separator'); ?>
                </label>
                <input type="text" name="settings[thousands_separator]" class=" form-control"
                       value="<?php echo get_setting('thousands_separator'); ?>">
            </div>
        </div>

        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="settings[decimal_point]" class="control-label">
                    <?php echo trans('decimal_point'); ?>
                </label>
                <input type="text" name="settings[decimal_point]" class=" form-control"
                       value="<?php echo get_setting('decimal_point'); ?>">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label class="control-label">
                    <?php echo trans('tax_rate_decimal_places'); ?>
                </label>
                <select name="settings[tax_rate_decimal_places]" class=" form-control simple-select"
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
                <label class="control-label">
                    <?php echo trans('default_list_limit'); ?>
                </label>
                <select name="settings[default_list_limit]" class=" form-control simple-select"
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

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label class="control-label">
                    <?php echo trans('currency_code'); ?>
                </label>
                <input type="text" name="settings[currency_code]" class=" form-control"
                       value="<?php echo get_setting('currency_code'); ?>">
            </div>
        </div>
    </div>

    <hr/>
    <h4><?php echo trans('dashboard'); ?></h4>
    <br/>

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="settings[quote_overview_period]" class="control-label">
                    <?php echo trans('quote_overview_period'); ?>
                </label>
                <select name="settings[quote_overview_period]" class=" form-control simple-select">
                    <option value="this-month" <?php check_select(get_setting('quote_overview_period'), 'this-month'); ?>>
                        <?php echo trans('this_month'); ?>
                    </option>
                    <option value="last-month" <?php check_select(get_setting('quote_overview_period'), 'last-month'); ?>>
                        <?php echo trans('last_month'); ?>
                    </option>
                    <option value="this-quarter" <?php check_select(get_setting('quote_overview_period'), 'this-quarter'); ?>>
                        <?php echo trans('this_quarter'); ?>
                    </option>
                    <option value="last-quarter" <?php check_select(get_setting('quote_overview_period'), 'last-quarter'); ?>>
                        <?php echo trans('last_quarter'); ?>
                    </option>
                    <option value="this-year" <?php check_select(get_setting('quote_overview_period'), 'this-year'); ?>>
                        <?php echo trans('this_year'); ?>
                    </option>
                    <option value="last-year" <?php check_select(get_setting('quote_overview_period'), 'last-year'); ?>>
                        <?php echo trans('last_year'); ?>
                    </option>
                </select>
            </div>
        </div>

        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="settings[invoice_overview_period]" class="control-label">
                    <?php echo trans('invoice_overview_period'); ?>
                </label>
                <select name="settings[invoice_overview_period]" class=" form-control simple-select">
                    <option value="this-month" <?php check_select(get_setting('invoice_overview_period'), 'this-month'); ?>>
                        <?php echo trans('this_month'); ?>
                    </option>
                    <option value="last-month" <?php check_select(get_setting('invoice_overview_period'), 'last-month'); ?>>
                        <?php echo trans('last_month'); ?>
                    </option>
                    <option value="this-quarter" <?php check_select(get_setting('invoice_overview_period'), 'this-quarter'); ?>>
                        <?php echo trans('this_quarter'); ?>
                    </option>
                    <option value="last-quarter" <?php check_select(get_setting('invoice_overview_period'), 'last-quarter'); ?>>
                        <?php echo trans('last_quarter'); ?>
                    </option>
                    <option value="this-year" <?php check_select(get_setting('invoice_overview_period'), 'this-year'); ?>>
                        <?php echo trans('this_year'); ?>
                    </option>
                    <option value="last-year" <?php check_select(get_setting('invoice_overview_period'), 'last-year'); ?>>
                        <?php echo trans('last_year'); ?>
                    </option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label class="control-label">
                    <?php echo trans('disable_quickactions'); ?>
                </label>
                <select name="settings[disable_quickactions]" class=" form-control simple-select"
                        id="disable_quickactions">
                    <option value="0">
                        <?php echo trans('no'); ?>
                    </option>
                    <option value="1" <?php check_select(get_setting('disable_quickactions')); ?>>
                        <?php echo trans('yes'); ?>
                    </option>
                </select>
            </div>
        </div>
    </div>

    <hr/>
    <h4><?php echo trans('interface'); ?></h4>
    <br/>

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label class="control-label">
                    <?php echo trans('disable_sidebar'); ?>
                </label>
                <select name="settings[disable_sidebar]" class=" form-control simple-select"
                        id="disable_sidebar">
                    <option value="0">
                        <?php echo trans('no'); ?>
                    </option>
                    <option value="1" <?php check_select(get_setting('disable_sidebar')); ?>>
                        <?php echo trans('yes'); ?>
                    </option>
                </select>
            </div>
        </div>

        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label class="control-label">
                    <?php echo trans('custom_title'); ?>
                </label>
                <input type="text" name="settings[custom_title]" class=" form-control"
                       value="<?php echo get_setting('custom_title'); ?>">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label">
            <?php echo trans('monospaced_font_for_amounts'); ?>
        </label>
        <select name="settings[monospace_amounts]" class=" form-control simple-select"
                id="monospace_amounts">
            <option value="0"><?php echo trans('no'); ?></option>
            <option value="1"
                    <?php check_select(get_setting('monospace_amounts')); ?>>
                <?php echo trans('yes'); ?>
            </option>
        </select>

        <p class="help-block">
            <?php echo trans('example'); ?>:
            <span style="font-family: Monaco, Lucida Console, monospace">
                <?php echo format_currency(123456.78); ?>
            </span>
        </p>
    </div>

    <div class="form-group">
        <label class="control-label">
            <?php echo trans('login_logo'); ?>
        </label>
        <?php if (get_setting('login_logo')) { ?>
            <img src="<?php echo base_url(); ?>uploads/<?php echo get_setting('login_logo'); ?>"><br>
            <?php echo anchor('settings/remove_logo/login', 'Remove Logo'); ?><br>
        <?php } ?>
        <input type="file" name="login_logo" size="40" class=" form-control"/>
    </div>

    <hr/>
    <h4><?php echo trans('system_settings'); ?></h4>
    <br/>

    <div class="form-group">
        <label for="settings[bcc_mails_to_admin]" class="control-label">
            <?php echo trans('bcc_mails_to_admin'); ?>
        </label>
        <select name="settings[bcc_mails_to_admin]" class=" form-control simple-select">
            <option value="0"><?php echo trans('no'); ?></option>
            <option value="1"
                    <?php check_select(get_setting('bcc_mails_to_admin')); ?>>
                <?php echo trans('yes'); ?>
            </option>
        </select>

        <p class="help-block"><?php echo trans('bcc_mails_to_admin_hint'); ?></p>
    </div>

    <div class="form-group">
        <label for="settings[cron_key]" class="control-label">
            <?php echo trans('cron_key'); ?>
        </label>

        <div class="row">
            <div class="col-xs-8 col-sm-9">
                <input type="text" name="settings[cron_key]" id="cron_key"
                       class=" form-control"
                       value="<?php echo get_setting('cron_key'); ?>">
            </div>
            <div class="col-xs-4 col-sm-3">
                <input id="btn_generate_cron_key" value="<?php echo trans('generate'); ?>"
                       type="button" class="btn btn-primary btn-sm btn-block">
            </div>
        </div>
    </div>

</div>
