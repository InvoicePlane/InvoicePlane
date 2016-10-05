<script type="text/javascript">
    $(function () {
        $('#btn_generate_cron_key').click(function () {
            $.post("<?php echo site_url('settings/ajax/get_cron_key'); ?>", function (data) {
                $('#cron_key').val(data);
            });
        });
    });

    // Update check moved to partial_settings_updates.php!
</script>

<div class="tab-info">

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="settings[default_language]" class="control-label">
                    <?php echo trans('language'); ?>
                </label>
                <select name="settings[default_language]" class="input-sm form-control">
                    <?php foreach ($languages as $language) { ?>
                        <option value="<?php echo $language; ?>"
                                <?php if ($this->mdl_settings->setting('default_language') == $language) { ?>selected="selected"<?php } ?>><?php echo ucfirst($language); ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="settings[first_day_of_week]" class="control-label">
                    <?php echo trans('first_day_of_week'); ?>
                </label>
                <select name="settings[first_day_of_week]" class="input-sm form-control">
                    <?php foreach ($first_days_of_weeks as $first_day_of_week_id => $first_day_of_week_name) { ?>
                        <option value="<?php echo $first_day_of_week_id; ?>"
                                <?php if ($this->mdl_settings->setting('first_day_of_week') == $first_day_of_week_id) { ?>selected="selected"<?php } ?>><?php echo $first_day_of_week_name; ?></option>
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
                <select name="settings[default_country]" class="input-sm form-control">
                    <option></option>
                    <?php foreach ($countries as $cldr => $country) { ?>
                        <option value="<?php echo $cldr; ?>"
                                <?php if ($this->mdl_settings->setting('default_country') == $cldr) { ?>selected="selected"<?php } ?>><?php echo $country ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="settings[date_format]" class="control-label">
                    <?php echo trans('date_format'); ?>
                </label>
                <select name="settings[date_format]" class="input-sm form-control">
                    <?php foreach ($date_formats as $date_format) { ?>
                        <option value="<?php echo $date_format['setting']; ?>"
                                <?php if ($this->mdl_settings->setting('date_format') == $date_format['setting']) { ?>selected="selected"<?php } ?>><?php echo $current_date->format($date_format['setting']); ?></option>
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
                <input type="text" name="settings[currency_symbol]" class="input-sm form-control"
                       value="<?php echo $this->mdl_settings->setting('currency_symbol'); ?>">
            </div>
        </div>

        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="settings[currency_symbol_placement]" class="control-label">
                    <?php echo trans('currency_symbol_placement'); ?>
                </label>
                <select name="settings[currency_symbol_placement]" class="input-sm form-control">
                    <option value="before"
                            <?php if ($this->mdl_settings->setting('currency_symbol_placement') == 'before') { ?>selected="selected"<?php } ?>><?php echo trans('before_amount'); ?></option>
                    <option value="after"
                            <?php if ($this->mdl_settings->setting('currency_symbol_placement') == 'after') { ?>selected="selected"<?php } ?>><?php echo trans('after_amount'); ?></option>
                    <option value="afterspace"
                            <?php if ($this->mdl_settings->setting('currency_symbol_placement') == 'afterspace') { ?>selected="selected"<?php } ?>><?php echo trans('after_amount_space'); ?></option>
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
                <input type="text" name="settings[thousands_separator]" class="input-sm form-control"
                       value="<?php echo $this->mdl_settings->setting('thousands_separator'); ?>">
            </div>
        </div>

        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="settings[decimal_point]" class="control-label">
                    <?php echo trans('decimal_point'); ?>
                </label>
                <input type="text" name="settings[decimal_point]" class="input-sm form-control"
                       value="<?php echo $this->mdl_settings->setting('decimal_point'); ?>">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label class="control-label">
                    <?php echo trans('tax_rate_decimal_places'); ?>
                </label>
                <select name="settings[tax_rate_decimal_places]" class="input-sm form-control"
                        id="tax_rate_decimal_places">
                    <option value="2"
                            <?php if ($this->mdl_settings->setting('tax_rate_decimal_places') == '2') { ?>selected="selected"<?php } ?>>
                        2
                    </option>
                    <option value="3"
                            <?php if ($this->mdl_settings->setting('tax_rate_decimal_places') == '3') { ?>selected="selected"<?php } ?>>
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
                <select name="settings[default_list_limit]" class="input-sm form-control"
                        id="default_list_limit">
                    <option value="15"
                            <?php if ($this->mdl_settings->setting('default_list_limit') == '15') { ?>selected="selected"<?php } ?>>
                        15
                    </option>
                    <option value="25"
                            <?php if ($this->mdl_settings->setting('default_list_limit') == '25') { ?>selected="selected"<?php } ?>>
                        25
                    </option>
                    <option value="50"
                            <?php if ($this->mdl_settings->setting('default_list_limit') == '50') { ?>selected="selected"<?php } ?>>
                        50
                    </option>
                    <option value="100"
                            <?php if ($this->mdl_settings->setting('default_list_limit') == '100') { ?>selected="selected"<?php } ?>>
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
                <input type="text" name="settings[currency_code]" class="input-sm form-control"
                       value="<?php echo $this->mdl_settings->setting('currency_code'); ?>">
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
                <select name="settings[quote_overview_period]" class="input-sm form-control">
                    <option value="this-month"
                            <?php if ($this->mdl_settings->setting('quote_overview_period') == 'this-month') { ?>selected="selected"<?php } ?>><?php echo trans('this_month'); ?></option>
                    <option value="last-month"
                            <?php if ($this->mdl_settings->setting('quote_overview_period') == 'last-month') { ?>selected="selected"<?php } ?>><?php echo trans('last_month'); ?></option>
                    <option value="this-quarter"
                            <?php if ($this->mdl_settings->setting('quote_overview_period') == 'this-quarter') { ?>selected="selected"<?php } ?>><?php echo trans('this_quarter'); ?></option>
                    <option value="last-quarter"
                            <?php if ($this->mdl_settings->setting('quote_overview_period') == 'last-quarter') { ?>selected="selected"<?php } ?>><?php echo trans('last_quarter'); ?></option>
                    <option value="this-year"
                            <?php if ($this->mdl_settings->setting('quote_overview_period') == 'this-year') { ?>selected="selected"<?php } ?>><?php echo trans('this_year'); ?></option>
                    <option value="last-year"
                            <?php if ($this->mdl_settings->setting('quote_overview_period') == 'last-year') { ?>selected="selected"<?php } ?>><?php echo trans('last_year'); ?></option>
                </select>
            </div>
        </div>

        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="settings[invoice_overview_period]" class="control-label">
                    <?php echo trans('invoice_overview_period'); ?>
                </label>
                <select name="settings[invoice_overview_period]" class="input-sm form-control">
                    <option value="this-month"
                            <?php if ($this->mdl_settings->setting('invoice_overview_period') == 'this-month') { ?>selected="selected"<?php } ?>><?php echo trans('this_month'); ?></option>
                    <option value="last-month"
                            <?php if ($this->mdl_settings->setting('invoice_overview_period') == 'last-month') { ?>selected="selected"<?php } ?>><?php echo trans('last_month'); ?></option>
                    <option value="this-quarter"
                            <?php if ($this->mdl_settings->setting('invoice_overview_period') == 'this-quarter') { ?>selected="selected"<?php } ?>><?php echo trans('this_quarter'); ?></option>
                    <option value="last-quarter"
                            <?php if ($this->mdl_settings->setting('invoice_overview_period') == 'last-quarter') { ?>selected="selected"<?php } ?>><?php echo trans('last_quarter'); ?></option>
                    <option value="this-year"
                            <?php if ($this->mdl_settings->setting('invoice_overview_period') == 'this-year') { ?>selected="selected"<?php } ?>><?php echo trans('this_year'); ?></option>
                    <option value="last-year"
                            <?php if ($this->mdl_settings->setting('invoice_overview_period') == 'last-year') { ?>selected="selected"<?php } ?>><?php echo trans('last_year'); ?></option>
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
                <select name="settings[disable_quickactions]" class="input-sm form-control"
                        id="disable_quickactions">
                    <option value="0"
                            <?php if (!$this->mdl_settings->setting('disable_quickactions')) { ?>selected="selected"<?php } ?>><?php echo trans('no'); ?></option>
                    <option value="1"
                            <?php if ($this->mdl_settings->setting('disable_quickactions')) { ?>selected="selected"<?php } ?>><?php echo trans('yes'); ?></option>
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
                <select name="settings[disable_sidebar]" class="input-sm form-control"
                        id="disable_sidebar">
                    <option value="0"
                            <?php if (!$this->mdl_settings->setting('disable_sidebar')) { ?>selected="selected"<?php } ?>><?php echo trans('no'); ?></option>
                    <option value="1"
                            <?php if ($this->mdl_settings->setting('disable_sidebar')) { ?>selected="selected"<?php } ?>><?php echo trans('yes'); ?></option>
                </select>
            </div>
        </div>

        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label class="control-label">
                    <?php echo trans('custom_title'); ?>
                </label>
                <input type="text" name="settings[custom_title]" class="input-sm form-control"
                       value="<?php echo $this->mdl_settings->setting('custom_title'); ?>">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label">
            <?php echo trans('monospaced_font_for_amounts'); ?>
        </label>
        <select name="settings[monospace_amounts]" class="input-sm form-control"
                id="monospace_amounts">
            <option value="0"><?php echo trans('no'); ?></option>
            <option value="1"
                    <?php if ($this->mdl_settings->setting('monospace_amounts') == 1) { ?>selected="selected"<?php } ?>><?php echo trans('yes'); ?></option>
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
        <?php if ($this->mdl_settings->setting('login_logo')) { ?>
            <img src="<?php echo base_url(); ?>uploads/<?php echo $this->mdl_settings->setting('login_logo'); ?>"><br>
            <?php echo anchor('settings/remove_logo/login', 'Remove Logo'); ?><br>
        <?php } ?>
        <input type="file" name="login_logo" size="40" class="input-sm form-control"/>
    </div>

    <hr/>
    <h4><?php echo trans('system_settings'); ?></h4>
    <br/>

    <div class="form-group">
        <label for="settings[bcc_mails_to_admin]" class="control-label">
            <?php echo trans('bcc_mails_to_admin'); ?>
        </label>
        <select name="settings[bcc_mails_to_admin]" class="input-sm form-control">
            <option value="0"><?php echo trans('no'); ?></option>
            <option value="1"
                    <?php if ($this->mdl_settings->setting('bcc_mails_to_admin') == 1) { ?>selected="selected"<?php } ?>><?php echo trans('yes'); ?></option>
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
                       class="input-sm form-control"
                       value="<?php echo $this->mdl_settings->setting('cron_key'); ?>">
            </div>
            <div class="col-xs-4 col-sm-3">
                <input id="btn_generate_cron_key" value="<?php echo trans('generate'); ?>"
                       type="button" class="btn btn-primary btn-sm btn-block">
            </div>
        </div>
    </div>

</div>