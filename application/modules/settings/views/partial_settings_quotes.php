<div class="col-xs-12 col-md-8 col-md-offset-2">

    <div class="panel panel-default">
        <div class="panel-heading">
            <?php echo trans('general_settings'); ?>
        </div>
        <div class="panel-body">

            <div class="form-group">
                <label for="settings[quotes_expire_after]" class="control-label">
                    <?php echo trans('quotes_expire_after'); ?>
                </label>
                <input type="number" name="settings[quotes_expire_after]" class=" form-control"
                       value="<?php echo get_setting('quotes_expire_after'); ?>">
            </div>

            <div class="form-group">
                <label for="settings[default_quote_group]" class="control-label">
                    <?php echo trans('default_quote_group'); ?>
                </label>
                <select name="settings[default_quote_group]" class=" form-control simple-select">
                    <option value=""><?php echo trans('none'); ?></option>
                    <?php foreach ($invoice_groups as $invoice_group) { ?>
                        <option value="<?php echo $invoice_group->invoice_group_id; ?>"
                            <?php check_select(get_setting('default_quote_group'), $invoice_group->invoice_group_id); ?>>
                            <?php echo $invoice_group->invoice_group_name; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="settings[mark_quotes_sent_pdf]" class="control-label">
                    <?php echo trans('mark_quotes_sent_pdf'); ?>
                </label>
                <select name="settings[mark_quotes_sent_pdf]" class=" form-control simple-select">
                    <option value="0">
                        <?php echo trans('no'); ?>
                    </option>
                    <option value="1" <?php check_select(get_setting('mark_quotes_sent_pdf'), '1'); ?>>
                        <?php echo trans('yes'); ?>
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="settings[quote_pre_password]" class="control-label">
                    <?php echo trans('quote_pre_password'); ?>
                </label>
                <input type="text" name="settings[quote_pre_password]" class=" form-control"
                       value="<?php echo get_setting('quote_pre_password', '', true); ?>">
            </div>

            <div class="form-group">
                <label for="settings[default_quote_notes]">
                    <?php echo trans('default_notes'); ?>
                </label>
                <textarea name="settings[default_quote_notes]" rows="3"
                          class="form-control"><?php echo nl2br(get_setting('default_quote_notes', '', true)); ?></textarea>
            </div>

            <div class="form-group">
                <label for="settings[generate_quote_number_for_draft]" class="control-label">
                    <?php echo trans('generate_quote_number_for_draft'); ?>
                </label>
                <select name="settings[generate_quote_number_for_draft]" class=" form-control simple-select">
                    <option value="0">
                        <?php echo trans('no'); ?>
                    </option>
                    <option value="1" <?php check_select(get_setting('generate_quote_number_for_draft'), '1'); ?>>
                        <?php echo trans('yes'); ?>
                    </option>
                </select>
            </div>

        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <?php echo trans('quote_templates'); ?>
        </div>
        <div class="panel-body">

            <div class="form-group">
                <label for="settings[pdf_quote_template]" class="control-label">
                    <?php echo trans('default_pdf_template'); ?>
                </label>
                <select name="settings[pdf_quote_template]" class=" form-control simple-select">
                    <option value=""><?php echo trans('none'); ?></option>
                    <?php foreach ($pdf_quote_templates as $quote_template) { ?>
                        <option value="<?php echo $quote_template; ?>"
                            <?php check_select(get_setting('pdf_quote_template'), $quote_template); ?>>
                            <?php echo $quote_template; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="settings[public_quote_template]" class="control-label">
                    <?php echo trans('default_public_template'); ?>
                </label>
                <select name="settings[public_quote_template]" class=" form-control simple-select">
                    <option value=""><?php echo trans('none'); ?></option>
                    <?php foreach ($public_quote_templates as $quote_template) { ?>
                        <option value="<?php echo $quote_template; ?>"
                            <?php check_select(get_setting('public_quote_template'), $quote_template); ?>>
                            <?php echo $quote_template; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="settings[email_quote_template]" class="control-label">
                    <?php echo trans('default_email_template'); ?>
                </label>
                <select name="settings[email_quote_template]" class=" form-control simple-select">
                    <option value=""><?php echo trans('none'); ?></option>
                    <?php foreach ($email_templates_quote as $email_template) { ?>
                        <option value="<?php echo $email_template->email_template_id; ?>"
                            <?php check_select(get_setting('email_quote_template'), $email_template->email_template_id); ?>>
                            <?php echo $email_template->email_template_title; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

        </div>
    </div>

</div>
