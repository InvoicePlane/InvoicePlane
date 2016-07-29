<div class="tab-info">

    <div class="row">
        <div class="col-xs-12 col-md-6">

            <h4><?php echo trans('general_settings'); ?></h4>
            <br/>

            <div class="form-group">
                <label for="settings[quotes_expire_after]" class="control-label">
                    <?php echo trans('quotes_expire_after'); ?>
                </label>
                <input type="text" name="settings[quotes_expire_after]" class="input-sm form-control"
                       value="<?php echo $this->mdl_settings->setting('quotes_expire_after'); ?>">
            </div>

            <div class="form-group">
                <label for="settings[default_quote_group]" class="control-label">
                    <?php echo trans('default_quote_group'); ?>
                </label>
                <select name="settings[default_quote_group]" class="input-sm form-control">
                    <option value=""></option>
                    <?php foreach ($invoice_groups as $invoice_group) { ?>
                        <option value="<?php echo $invoice_group->invoice_group_id; ?>"
                            <?php if ($this->mdl_settings->setting('default_quote_group') == $invoice_group->invoice_group_id) {
                                echo 'selected="selected"';
                            } ?>>
                            <?php echo $invoice_group->invoice_group_name; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="settings[mark_quotes_sent_pdf]" class="control-label">
                    <?php echo trans('mark_quotes_sent_pdf'); ?>
                </label>
                <select name="settings[mark_quotes_sent_pdf]" class="input-sm form-control">
                    <option value="0"
                        <?php if (!$this->mdl_settings->setting('mark_quotes_sent_pdf')) {
                            echo 'selected="selected"';
                        } ?>>
                        <?php echo trans('no'); ?>
                    </option>
                    <option value="1"
                        <?php if ($this->mdl_settings->setting('mark_quotes_sent_pdf')) {
                            echo 'selected="selected"';
                        } ?>>
                        <?php echo trans('yes'); ?>
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="settings[quote_pre_password]" class="control-label">
                    <?php echo trans('quote_pre_password'); ?>
                </label>
                <input type="text" name="settings[quote_pre_password]" class="input-sm form-control"
                       value="<?php echo $this->mdl_settings->setting('quote_pre_password'); ?>">
            </div>

            <div class="form-group">
                <label for="settings[default_quote_notes]">
                    <?php echo trans('default_notes'); ?>
                </label>
                <textarea name="settings[default_quote_notes]" rows="3" class="input-sm form-control"
                ><?php echo $this->mdl_settings->setting('default_quote_notes'); ?></textarea>
            </div>

            <div class="form-group">
                <label for="settings[generate_quote_number_for_draft]" class="control-label">
                    <?php echo trans('generate_quote_number_for_draft'); ?>
                </label>
                <select name="settings[generate_quote_number_for_draft]" class="input-sm form-control">
                    <option value="0"
                        <?php if (!$this->mdl_settings->setting('generate_quote_number_for_draft')) {
                            echo 'selected="selected"';
                        } ?>>
                        <?php echo trans('no'); ?>
                    </option>
                    <option value="1"
                        <?php if ($this->mdl_settings->setting('generate_quote_number_for_draft')) {
                            echo 'selected="selected"';
                        } ?>>
                        <?php echo trans('yes'); ?>
                    </option>
                </select>
            </div>

        </div>
        <div class="col-xs-12 col-md-6">

            <h4><?php echo trans('quote_template'); ?></h4>
            <br/>

            <div class="form-group">
                <label for="settings[pdf_quote_template]" class="control-label">
                    <?php echo trans('default_pdf_template'); ?>
                </label>
                <select name="settings[pdf_quote_template]" class="input-sm form-control">
                    <option value=""></option>
                    <?php foreach ($pdf_quote_templates as $quote_template) { ?>
                        <option value="<?php echo $quote_template; ?>"
                            <?php if ($this->mdl_settings->setting('pdf_quote_template') == $quote_template) {
                                echo 'selected="selected"';
                            } ?>><?php echo $quote_template; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="settings[public_quote_template]" class="control-label">
                    <?php echo trans('default_public_template'); ?>
                </label>
                <select name="settings[public_quote_template]" class="input-sm form-control">
                    <option value=""></option>
                    <?php foreach ($public_quote_templates as $quote_template) { ?>
                        <option value="<?php echo $quote_template; ?>"
                            <?php if ($this->mdl_settings->setting('public_quote_template') == $quote_template) {
                                echo 'selected="selected"';
                            } ?>><?php echo $quote_template; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="settings[email_quote_template]" class="control-label">
                    <?php echo trans('default_email_template'); ?>
                </label>
                <select name="settings[email_quote_template]" class="input-sm form-control">
                    <option value=""></option>
                    <?php foreach ($email_templates_quote as $email_template) { ?>
                        <option value="<?php echo $email_template->email_template_id; ?>"
                            <?php if ($this->mdl_settings->setting('email_quote_template') == $email_template->email_template_id) {
                                echo 'selected="selected"';
                            } ?>><?php echo $email_template->email_template_title; ?></option>
                    <?php } ?>
                </select>
            </div>

        </div>
    </div>

</div>
