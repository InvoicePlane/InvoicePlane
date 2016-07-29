<form method="post" class="form-horizontal">

    <div id="headerbar">
        <h1><?php echo trans('email_template_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <input class="hidden" name="is_update" type="hidden"
            <?php if ($this->mdl_email_templates->form_value('is_update')) {
                echo 'value="1"';
            } else {
                echo 'value="0"';
            } ?>
        >

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="email_template_title" class="control-label"><?php echo trans('title'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="email_template_title" id="email_template_title"
                       value="<?php echo $this->mdl_email_templates->form_value('email_template_title'); ?>"
                       class="form-control">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="email_template_type" class="control-label"><?php echo trans('type'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <label class="radio-inline">
                    <input type="radio" name="email_template_type" id="email_template_type_invoice"
                           value="invoice"> <?php echo trans('invoice'); ?>
                </label>
                <label class="radio-inline">
                    <input type="radio" name="email_template_type" id="email_template_type_quote"
                           value="quote"> <?php echo trans('quote'); ?>
                </label>
            </div>
        </div>

        <hr>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="email_template_from_name" class="control-label"><?php echo trans('from_name'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="email_template_from_name" id="email_template_from_name"
                       class="form-control taggable"
                       value="<?php echo $this->mdl_email_templates->form_value('email_template_from_name'); ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="email_template_from_email" class="control-label"><?php echo trans('from_email'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="email_template_from_email" id="email_template_from_email"
                       class="form-control taggable"
                       value="<?php echo $this->mdl_email_templates->form_value('email_template_from_email'); ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="email_template_cc" class="control-label"><?php echo trans('cc'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="email_template_cc" id="email_template_cc" class="form-control taggable"
                       value="<?php echo $this->mdl_email_templates->form_value('email_template_cc'); ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="email_template_bcc" class="control-label"><?php echo trans('bcc'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="email_template_bcc" id="email_template_bcc" class="form-control taggable"
                       value="<?php echo $this->mdl_email_templates->form_value('email_template_bcc'); ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="email_template_subject" class="control-label"><?php echo trans('subject'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="email_template_subject" id="email_template_subject"
                       class="form-control taggable"
                       value="<?php echo html_escape($this->mdl_email_templates->form_value('email_template_subject')); ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="email_template_pdf_template" class="control-label">
                    <?php echo trans('pdf_template'); ?>:
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <select name="email_template_pdf_template" id="email_template_pdf_template" class="form-control">
                    <option value="" class="empty-option"></option>
                    <?php foreach ($invoice_templates as $template): ?>
                        <option class="hidden-invoice" value="<?php echo $template; ?>"
                                <?php if ($selected_pdf_template == $template) { ?>selected="selected"<?php } ?>><?php echo $template; ?></option>
                    <?php endforeach; ?>
                    <?php foreach ($quote_templates as $template): ?>
                        <option class="hidden-quote" value="<?php echo $template; ?>"
                                <?php if ($selected_pdf_template == $template) { ?>selected="selected"<?php } ?>><?php echo $template; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="email_template_body">
                    <?php echo trans('body'); ?>:
                </label>
            </div>

            <div class="col-xs-12 col-sm-6">
                <div class="html-tags btn-group btn-group-sm">
                    <span class="html-tag btn btn-default" data-tag-type="text-paragraph">
                        <i class="fa fa-paragraph"></i>
                    </span>
                    <span class="html-tag btn btn-default" data-tag-type="text-bold">
                        <i class="fa fa-bold"></i>
                    </span>
                    <span class="html-tag btn btn-default" data-tag-type="text-italic">
                        <i class="fa fa-italic"></i>
                    </span>
                </div>
                <div class="html-tags btn-group btn-group-sm">
                    <span class="html-tag btn btn-default" data-tag-type="text-h1">H1</span>
                    <span class="html-tag btn btn-default" data-tag-type="text-h2">H2</span>
                    <span class="html-tag btn btn-default" data-tag-type="text-h3">H3</span>
                    <span class="html-tag btn btn-default" data-tag-type="text-h4">H4</span>
                </div>
                <div class="html-tags btn-group btn-group-sm">
                    <span class="html-tag btn btn-default" data-tag-type="text-code">
                        <i class="fa fa-code"></i>
                    </span>
                    <span class="html-tag btn btn-default" data-tag-type="text-hr">
                        &lt;hr/&gt;
                    </span>
                    <span class="html-tag btn btn-default" data-tag-type="text-css">
                        CSS
                    </span>
                </div>

                <textarea name="email_template_body" id="email_template_body" style="height: 200px;"
                          class="email-template-body form-control taggable"><?php echo $this->mdl_email_templates->form_value('email_template_body'); ?></textarea>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php echo trans('preview'); ?>
                        <span id="email-template-preview-reload" class="pull-right cursor-pointer">
                            <i class="fa fa-refresh"></i>
                        </span>
                    </div>
                    <div class="panel-body">
                        <iframe id="email-template-preview"></iframe>
                    </div>
                </div>
            </div>
        </div>

        <div class="row form-group">
            <div class="col-xs-12">
                <h4><?php echo trans('email_template_tags'); ?></h4>

                <p><?php echo trans('email_template_tags_instructions'); ?></p><br/>
            </div>

            <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
                <strong><?php echo trans('client'); ?></strong><br>
                <a href="#" class="text-tag" data-tag="{{{client_name}}}"><?php echo trans('client_name'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{client_address_1}}}"><?php echo trans('client'); ?> <?php echo lang('address'); ?>
                    1</a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{client_address_2}}}"><?php echo trans('client'); ?> <?php echo lang('address'); ?>
                    2</a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{client_city}}}"><?php echo trans('client'); ?><?php echo lang('city'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{client_state}}}"><?php echo trans('client'); ?><?php echo lang('state'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{client_zip}}}"><?php echo trans('client'); ?><?php echo lang('zip_code'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{client_country}}}"><?php echo trans('client'); ?><?php echo lang('country'); ?></a><br>
                <?php foreach ($custom_fields['ip_client_custom'] as $custom) { ?>
                    <a href="#" class="text-tag"
                       data-tag="{{{<?php echo $custom->custom_field_column; ?>}}}"><?php echo $custom->custom_field_label; ?></a>
                    <br>
                <?php } ?>
            </div>

            <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
                <strong><?php echo trans('user'); ?></strong><br>
                <a href="#" class="text-tag"
                   data-tag="{{{user_name}}}"><?php echo trans('user'); ?><?php echo lang('name'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{user_company}}}"><?php echo trans('user'); ?><?php echo lang('company'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{user_address_1}}}"><?php echo trans('user'); ?> <?php echo lang('address'); ?> 1</a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{user_address_2}}}"><?php echo trans('user'); ?> <?php echo lang('address'); ?> 2</a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{user_city}}}"><?php echo trans('user'); ?><?php echo lang('city'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{user_state}}}"><?php echo trans('user'); ?><?php echo lang('state'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{user_zip}}}"><?php echo trans('user'); ?><?php echo lang('zip_code'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{user_country}}}"><?php echo trans('user'); ?><?php echo lang('country'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{user_phone}}}"><?php echo trans('user'); ?><?php echo lang('phone'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{user_fax}}}"><?php echo trans('user'); ?><?php echo lang('fax'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{user_mobile}}}"><?php echo trans('user'); ?><?php echo lang('mobile'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{user_email}}}"><?php echo trans('user'); ?><?php echo lang('email'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{user_web}}}"><?php echo trans('user'); ?><?php echo lang('web_address'); ?></a><br>
                <?php foreach ($custom_fields['ip_user_custom'] as $custom) { ?>
                    <a href="#" class="text-tag"
                       data-tag="{{{<?php echo $custom->custom_field_column; ?>}}}"><?php echo $custom->custom_field_label; ?></a>
                    <br>
                <?php } ?>
            </div>

            <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 hidden-invoice">
                <strong><?php echo trans('invoices'); ?></strong><br>
                <a href="#" class="text-tag"
                   data-tag="{{{invoice_guest_url}}}"><?php echo trans('invoice'); ?><?php echo lang('guest_url'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{invoice_number}}}"><?php echo trans('invoice'); ?><?php echo lang('id'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{invoice_date_due}}}"><?php echo trans('invoice'); ?><?php echo lang('due_date'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{invoice_date_created}}}"><?php echo trans('invoice'); ?><?php echo lang('created'); ?></a><br>
                <a href="#" class="text-tag" data-tag="{{{invoice_terms}}}"><?php echo trans('invoice_terms'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{invoice_total}}}"><?php echo trans('invoice'); ?><?php echo lang('total'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{invoice_paid}}}"><?php echo trans('invoice'); ?><?php echo lang('total_paid'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{invoice_balance}}}"><?php echo trans('invoice'); ?><?php echo lang('balance'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{invoice_status}}}"><?php echo trans('invoice'); ?><?php echo lang('status'); ?></a><br>
                <?php foreach ($custom_fields['ip_invoice_custom'] as $custom) { ?>
                    <a href="#" class="text-tag"
                       data-tag="{{{<?php echo $custom->custom_field_column; ?>}}}"><?php echo $custom->custom_field_label; ?></a>
                    <br>
                <?php } ?>
            </div>

            <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 hidden-quote">
                <strong><?php echo trans('quotes'); ?></strong><br>
                <a href="#" class="text-tag"
                   data-tag="{{{quote_total}}}"><?php echo trans('quote'); ?><?php echo lang('total'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{quote_date_created}}}"><?php echo trans('quote_date'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{quote_date_expires}}}"><?php echo trans('quote'); ?><?php echo lang('expires'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{quote_number}}}"><?php echo trans('quote'); ?><?php echo lang('id'); ?></a><br>
                <a href="#" class="text-tag"
                   data-tag="{{{quote_guest_url}}}"><?php echo trans('quote'); ?><?php echo lang('guest_url'); ?></a><br>
                <?php foreach ($custom_fields['ip_quote_custom'] as $custom) { ?>
                    <a href="#" class="text-tag"
                       data-tag="{{{<?php echo $custom->custom_field_column; ?>}}}"><?php echo $custom->custom_field_label; ?></a>
                    <br>
                <?php } ?>
            </div>

        </div>

    </div>

</form>

<script type="text/javascript">
    $(function () {
        var email_template_type = "<?php echo $this->mdl_email_templates->form_value('email_template_type'); ?>";
        var $email_template_type_options = $("[name=email_template_type]");

        $email_template_type_options.click(function () {
            // remove class "show" and deselect any selected elements.
            $(".show").removeClass("show").parent("select").each(function () {
                this.options.selectedIndex = 0;
            });
            // add show class to corresponding class
            $(".hidden-" + $(this).val()).addClass("show");
        });
        if (email_template_type === "") {
            $email_template_type_options.first().click();
        } else {
            $email_template_type_options.each(function () {
                if ($(this).val() === email_template_type) {
                    $(this).click();
                }
            });
        }
    });
</script>