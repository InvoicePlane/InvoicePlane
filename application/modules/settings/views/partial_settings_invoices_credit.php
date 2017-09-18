<div class="col-xs-12 col-md-8 col-md-offset-2">

    <div class="panel panel-default">
        <div class="panel-heading">
            <?php _trans('credit_invoice'); ?>
        </div>
        <div class="panel-body">

            <div class="row">
                <div class="col-xs-12 col-md-6">

                    <div class="form-group">
                        <label for="settings[default_invoice_credit_group]">
                            <?php _trans('default_invoice_group'); ?>
                        </label>
                        <select name="settings[default_invoice_credit_group]" id="settings[default_invoice_credit_group]"
                                class="form-control simple-select">
                            <option value=""><?php _trans('none'); ?></option>
                            <?php foreach ($invoice_groups as $invoice_group) { ?>
                                <option value="<?php echo $invoice_group->invoice_group_id; ?>"
                                    <?php check_select(get_setting('default_invoice_credit_group'), $invoice_group->invoice_group_id); ?>>
                                    <?php echo $invoice_group->invoice_group_name; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="settings[default_invoice_credit_terms]">
                            <?php _trans('default_terms'); ?>
                        </label>
                        <textarea name="settings[default_invoice_credit_terms]" id="settings[default_invoice_credit_terms]"
                                  class="form-control"
                                  rows="3"><?php echo get_setting('default_invoice_credit_terms', '', true); ?></textarea>
                    </div>

                </div>
                <div class="col-xs-12 col-md-6">

                    <div class="form-group">
                        <label for="settings[invoice_credit_default_payment_method]">
                            <?php _trans('default_payment_method'); ?>
                        </label>
                        <select name="settings[invoice_credit_default_payment_method]" class="form-control simple-select"
                                id="settings[invoice_credit_default_payment_method]">
                            <option value=""><?php _trans('none'); ?></option>
                            <?php
                            foreach ($payment_methods as $payment_method) { ?>
                                <option value="<?php echo $payment_method->payment_method_id; ?>"
                                    <?php check_select($payment_method->payment_method_id, get_setting('invoice_credit_default_payment_method')) ?>>
                                    <?php echo $payment_method->payment_method_name; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="settings[invoices_credit_due_after]">
                            <?php _trans('invoices_due_after'); ?>
                        </label>
                        <input type="number" name="settings[invoices_credit_due_after]" id="settings[invoices_credit_due_after]"
                               class="form-control" value="<?php echo get_setting('invoices_credit_due_after'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="settings[generate_invoice_credit_number_for_draft]">
                            <?php _trans('generate_invoice_number_for_draft'); ?>
                        </label>
                        <select name="settings[generate_invoice_credit_number_for_draft]" class="form-control simple-select"
                                id="settings[generate_invoice_credit_number_for_draft]">
                            <option value="0">
                                <?php _trans('no'); ?>
                            </option>
                            <option value="1" <?php check_select(get_setting('generate_invoice_credit_number_for_draft'), '1'); ?>>
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
            <?php _trans('invoice_template'); ?>
        </div>
        <div class="panel-body">

            <div class="row">
                <div class="col-xs-12 col-md-6">

                    <div class="form-group">
                        <label for="settings[pdf_invoice_credit_template]">
                            <?php _trans('default_pdf_template'); ?>
                        </label>
                        
                        <select name="settings[pdf_invoice_credit_template]" id="settings[pdf_invoice_credit_template]"
                                class="form-control simple-select">
                            <option value=""><?php _trans('none'); ?></option>
                            <?php foreach ($pdf_invoice_credit_templates as $invoice_credit_template) { ?>
                                <option value="<?php echo $invoice_credit_template; ?>"
                                    <?php check_select(get_setting('pdf_invoice_credit_template'), $invoice_credit_template); ?>>
                                    <?php echo $invoice_credit_template; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="settings[pdf_invoice_credit_template_paid]">
                            <?php _trans('pdf_template_paid'); ?>
                        </label>
                        <select name="settings[pdf_invoice_credit_template_paid]" id="settings[pdf_invoice_credit_template_paid]"
                                class="form-control simple-select">
                            <option value=""><?php _trans('none'); ?></option>
                            <?php foreach ($pdf_invoice_credit_templates as $invoice_credit_template) { ?>
                                <option value="<?php echo $invoice_credit_template; ?>"
                                    <?php check_select(get_setting('pdf_invoice_credit_template_paid'), $invoice_credit_template); ?>>
                                    <?php echo $invoice_credit_template; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="settings[pdf_invoice_credit_template_overdue]">
                            <?php _trans('pdf_template_overdue'); ?>
                        </label>
                        <select name="settings[pdf_invoice_credit_template_overdue]" class="form-control simple-select"
                                id="settings[pdf_invoice_credit_template_overdue]">
                            <option value=""><?php _trans('none'); ?></option>
                            <?php foreach ($pdf_invoice_credit_templates as $invoice_credit_template) { ?>
                                <option value="<?php echo $invoice_credit_template; ?>"
                                    <?php check_select(get_setting('pdf_invoice_credit_template_overdue'), $invoice_credit_template); ?>>
                                    <?php echo $invoice_credit_template; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="settings[public_invoice_credit_template]">
                            <?php _trans('default_public_template'); ?>
                        </label>
                        <select name="settings[public_invoice_credit_template]" id="settings[public_invoice_credit_template]"
                                class="form-control simple-select">
                            <option value=""><?php _trans('none'); ?></option>
                            <?php foreach ($public_invoice_credit_templates as $invoice_credit_template) { ?>
                                <option value="<?php echo $invoice_credit_template; ?>"
                                    <?php check_select(get_setting('public_invoice_credit_template'), $invoice_credit_template); ?>>
                                    <?php echo $invoice_credit_template; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                </div>
                <div class="col-xs-12 col-md-6">

                    <div class="form-group">
                        <label for="settings[email_invoice_credit_template]">
                            <?php _trans('default_email_template'); ?>
                        </label>
                        
                        <select name="settings[email_invoice_credit_template]" id="settings[email_invoice_credit_template]"
                                class="form-control simple-select">
                            <option value=""><?php _trans('none'); ?></option>
                            <?php foreach ($email_templates_invoice_credit as $email_template) { ?>
                                <option value="<?php echo $email_template->email_template_id; ?>"
                                    <?php check_select(get_setting('email_invoice_credit_template'), $email_template->email_template_id); ?>>
                                    <?php echo $email_template->email_template_title; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="settings[email_invoice_credit_template_paid]">
                            <?php _trans('email_template_paid'); ?>
                        </label>
                        <select name="settings[email_invoice_credit_template_paid]" id="settings[email_invoice_credit_template_paid]"
                                class="form-control simple-select">
                            <option value=""><?php _trans('none'); ?></option>
                            <?php foreach ($email_templates_invoice_credit as $email_template) { ?>
                                <option value="<?php echo $email_template->email_template_id; ?>"
                                    <?php check_select(get_setting('email_invoice_credit_template_paid'), $email_template->email_template_id); ?>>
                                    <?php echo $email_template->email_template_title; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="settings[email_invoice_credit_template_overdue]">
                            <?php _trans('email_template_overdue'); ?>
                        </label>
                        <select name="settings[email_invoice_credit_template_overdue]" class="form-control simple-select"
                                id="settings[email_invoice_credit_template_overdue]">
                            <option value=""><?php _trans('none'); ?></option>
                            <?php foreach ($email_templates_invoice_credit as $email_template) { ?>
                                <option value="<?php echo $email_template->email_template_id; ?>"
                                    <?php check_select(get_setting('email_invoice_credit_template_overdue'), $email_template->email_template_id); ?>>
                                    <?php echo $email_template->email_template_title; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-6">

                    <div class="form-group">
                        <label for="settings[pdf_invoice_credit_footer]">
                            <?php _trans('pdf_invoice_footer'); ?>
                        </label>
                        <textarea name="settings[pdf_invoice_credit_footer]" id="settings[pdf_invoice_credit_footer]"
                                  class="form-control no-margin"><?php echo get_setting('pdf_invoice_credit_footer', '', true); ?></textarea>
                        <p class="help-block"><?php _trans('pdf_invoice_footer_hint'); ?></p>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <?php _trans('other_settings'); ?>
        </div>
        <div class="panel-body">

            <div class="row">
                <div class="col-xs-12 col-md-6">

                    <div class="form-group">
                        <label for="settings[read_only_toggle_credit]">
                            <?php _trans('set_to_read_only'); ?>
                        </label>
                        <select name="settings[read_only_toggle_credit]" id="settings[read_only_toggle_credit]"
                                class="form-control simple-select">
                            <option value="2" <?php check_select(get_setting('read_only_toggle_credit'), '2'); ?>>
                                <?php _trans('sent'); ?>
                            </option>
                            <option value="3" <?php check_select(get_setting('read_only_toggle_credit'), '3'); ?>>
                                <?php _trans('viewed'); ?>
                            </option>
                            <option value="4" <?php check_select(get_setting('read_only_toggle_credit'), '4'); ?>>
                                <?php _trans('paid'); ?>
                            </option>
                        </select>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>
