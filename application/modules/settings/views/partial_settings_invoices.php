<div class="tab-info">

    <div class="form-group">
        <label for="settings[default_invoice_group]" class="control-label">
            <?php echo lang('default_invoice_group'); ?>
        </label>
        <select name="settings[default_invoice_group]" class="input-sm form-control">
            <option value=""></option>
            <?php foreach ($invoice_groups as $invoice_group) { ?>
                <option value="<?php echo $invoice_group->invoice_group_id; ?>"
                        <?php if ($this->mdl_settings->setting('default_invoice_group') == $invoice_group->invoice_group_id) { ?>selected="selected"<?php } ?>><?php echo $invoice_group->invoice_group_name; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[read_only_toggle]" class="control-label">
            <?php echo lang('set_to_read_only'); ?>
        </label>
        <select name="settings[read_only_toggle]" class="input-sm form-control">
            <option value="sent"
                <?php echo($this->mdl_settings->setting('read_only_toggle') == 'sent' ? 'selected="selected"' : ''); ?>>
                <?php echo lang('sent'); ?>
            </option>
            <option value="viewed"
                <?php echo($this->mdl_settings->setting('read_only_toggle') == 'viewed' ? 'selected="selected"' : ''); ?>>
                <?php echo lang('viewed'); ?>
            </option>
            <option value="paid"
                <?php echo($this->mdl_settings->setting('read_only_toggle') == 'paid' ? 'selected="selected"' : ''); ?>>
                <?php echo lang('paid'); ?>
            </option>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[invoices_due_after]" class="control-label">
            <?php echo lang('invoices_due_after'); ?>
        </label>
        <input type="text" name="settings[invoices_due_after]" class="input-sm form-control"
               value="<?php echo $this->mdl_settings->setting('invoices_due_after'); ?>">
    </div>

    <div class="form-group">
        <label for="settings[default_invoice_terms]">
            <?php echo lang('default_terms'); ?>
        </label>
		<textarea name="settings[default_invoice_terms]" rows="3"
                  class="input-sm form-control"><?php echo $this->mdl_settings->setting('default_invoice_terms'); ?></textarea>
    </div>

    <div class="form-group">
        <label for="settings[invoice_default_payment_method]" class="control-label">
            <?php echo lang('default_payment_method'); ?>
        </label>
        <select name="settings[invoice_default_payment_method]" class="input-sm form-control">
            <option value=""></option>
            <?php
            $setting = $this->mdl_settings->setting('invoice_default_payment_method');
            foreach ($payment_methods as $payment_method) {
                echo '<option value="' . $payment_method->payment_method_id . '"';
                if ($payment_method->payment_method_id == $setting) {
                    echo 'selected="selected"';
                }
                echo '>' . $payment_method->payment_method_name;
                echo '</option>';
            }
            ?>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[automatic_email_on_recur]" class="control-label">
            <?php echo lang('automatic_email_on_recur'); ?>
        </label>
        <select name="settings[automatic_email_on_recur]" class="input-sm form-control">
            <option value="0"
                    <?php if (!$this->mdl_settings->setting('automatic_email_on_recur')) { ?>selected="selected"<?php } ?>><?php echo lang('no'); ?></option>
            <option value="1"
                    <?php if ($this->mdl_settings->setting('automatic_email_on_recur')) { ?>selected="selected"<?php } ?>><?php echo lang('yes'); ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[mark_invoices_sent_pdf]" class="control-label">
            <?php echo lang('mark_invoices_sent_pdf'); ?>
        </label>
        <select name="settings[mark_invoices_sent_pdf]" class="input-sm form-control">
            <option value="0"
                    <?php if (!$this->mdl_settings->setting('mark_invoices_sent_pdf')) { ?>selected="selected"<?php } ?>><?php echo lang('no'); ?></option>
            <option value="1"
                    <?php if ($this->mdl_settings->setting('mark_invoices_sent_pdf')) { ?>selected="selected"<?php } ?>><?php echo lang('yes'); ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[invoice_pre_password]" class="control-label">
            <?php echo lang('invoice_pre_password'); ?>
        </label>
        <input type="text" name="settings[invoice_pre_password]" class="input-sm form-control"
               value="<?php echo $this->mdl_settings->setting('invoice_pre_password'); ?>">
    </div>

    <div class="form-group">
        <label class="control-label">
            <?php echo lang('invoice_logo'); ?>
        </label>
        <?php if ($this->mdl_settings->setting('invoice_logo')) { ?>
            <img src="<?php echo base_url(); ?>uploads/<?php echo $this->mdl_settings->setting('invoice_logo'); ?>"><br>
            <?php echo anchor('settings/remove_logo/invoice', 'Remove Logo'); ?><br>
        <?php } ?>
        <input type="file" name="invoice_logo" size="40" class="input-sm form-control"/>
    </div>

    <div class="form-group">
        <hr/>
        <h4><?php echo lang('invoice_template'); ?></h4>
    </div>

    <div class="form-group">
        <label for="settings[pdf_invoice_template]" class="control-label">
            <?php echo lang('default_pdf_template'); ?>
        </label>
        <select name="settings[pdf_invoice_template]" class="input-sm form-control">
            <option value=""></option>
            <?php foreach ($pdf_invoice_templates as $invoice_template) { ?>
                <option value="<?php echo $invoice_template; ?>"
                        <?php if ($this->mdl_settings->setting('pdf_invoice_template') == $invoice_template) { ?>selected="selected"<?php } ?>><?php echo $invoice_template; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[pdf_invoice_template_paid]" class="control-label">
            <?php echo lang('pdf_template_paid'); ?>
        </label>
        <select name="settings[pdf_invoice_template_paid]" class="input-sm form-control">
            <option value=""></option>
            <?php foreach ($pdf_invoice_templates as $invoice_template) { ?>
                <option value="<?php echo $invoice_template; ?>"
                        <?php if ($this->mdl_settings->setting('pdf_invoice_template_paid') == $invoice_template) { ?>selected="selected"<?php } ?>><?php echo $invoice_template; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[pdf_invoice_template_overdue]" class="control-label">
            <?php echo lang('pdf_template_overdue'); ?>
        </label>
        <select name="settings[pdf_invoice_template_overdue]" class="input-sm form-control">
            <option value=""></option>
            <?php foreach ($pdf_invoice_templates as $invoice_template) { ?>
                <option value="<?php echo $invoice_template; ?>"
                        <?php if ($this->mdl_settings->setting('pdf_invoice_template_overdue') == $invoice_template) { ?>selected="selected"<?php } ?>><?php echo $invoice_template; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[public_invoice_template]" class="control-label">
            <?php echo lang('default_public_template'); ?>
        </label>
        <select name="settings[public_invoice_template]" class="input-sm form-control">
            <option value=""></option>
            <?php foreach ($public_invoice_templates as $invoice_template) { ?>
                <option value="<?php echo $invoice_template; ?>"
                        <?php if ($this->mdl_settings->setting('public_invoice_template') == $invoice_template) { ?>selected="selected"<?php } ?>><?php echo $invoice_template; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[email_invoice_template]" class="control-label">
            <?php echo lang('default_email_template'); ?>
        </label>
        <select name="settings[email_invoice_template]" class="input-sm form-control">
            <option value=""></option>
            <?php foreach ($email_templates_invoice as $email_template) { ?>
                <option value="<?php echo $email_template->email_template_id; ?>"
                        <?php if ($this->mdl_settings->setting('email_invoice_template') == $email_template->email_template_id) { ?>selected="selected"<?php } ?>><?php echo $email_template->email_template_title; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[email_invoice_template_paid]" class="control-label">
            <?php echo lang('email_template_paid'); ?>
        </label>
        <select name="settings[email_invoice_template_paid]" class="input-sm form-control">
            <option value=""></option>
            <?php foreach ($email_templates_invoice as $email_template) { ?>
                <option value="<?php echo $email_template->email_template_id; ?>"
                        <?php if ($this->mdl_settings->setting('email_invoice_template_paid') == $email_template->email_template_id) { ?>selected="selected"<?php } ?>><?php echo $email_template->email_template_title; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[email_invoice_template_overdue]" class="control-label">
            <?php echo lang('email_template_overdue'); ?>
        </label>
        <select name="settings[email_invoice_template_overdue]" class="input-sm form-control">
            <option value=""></option>
            <?php foreach ($email_templates_invoice as $email_template) { ?>
                <option value="<?php echo $email_template->email_template_id; ?>"
                        <?php if ($this->mdl_settings->setting('email_invoice_template_overdue') == $email_template->email_template_id) { ?>selected="selected"<?php } ?>><?php echo $email_template->email_template_title; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[public_invoice_template]" class="control-label">
            <?php echo lang('pdf_invoice_footer'); ?>
        </label>
        <textarea name="settings[pdf_invoice_footer]"
                  class="input-sm form-control no-margin"><?php echo $this->mdl_settings->setting('pdf_invoice_footer'); ?></textarea>

        <p class="help-block"><?php echo lang('pdf_invoice_footer_hint'); ?></p>
    </div>

</div>