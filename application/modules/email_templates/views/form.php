<form method="post">

    <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('email_template_form'); ?></h1>
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

        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">

                <div class="form-group">
                    <label for="email_template_title" class="control-label"><?php _trans('title'); ?></label>
                    <input type="text" name="email_template_title" id="email_template_title"
                           value="<?php echo $this->mdl_email_templates->form_value('email_template_title', true); ?>"
                           class="form-control">
                </div>

                <div class="form-group">
                    <label for="email_template_type" class="control-label"><?php _trans('type'); ?></label>
                    <div class="radio">
                        <label>
                            <input type="radio" name="email_template_type" id="email_template_type_invoice"
                                   value="invoice" checked>
                            <?php _trans('invoice'); ?>
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="email_template_type" id="email_template_type_invoice"
                                   value="quote">
                            <?php _trans('quote'); ?>
                        </label>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <label for="email_template_from_name" class="control-label">
                        <?php _trans('from_name'); ?>
                    </label>
                    <input type="text" name="email_template_from_name" id="email_template_from_name"
                           class="form-control taggable"
                           value="<?php echo $this->mdl_email_templates->form_value('email_template_from_name', true); ?>">
                </div>

                <div class="form-group">
                    <label for="email_template_from_email" class="control-label">
                        <?php _trans('from_email'); ?>
                    </label>
                    <input type="text" name="email_template_from_email" id="email_template_from_email"
                           class="form-control taggable"
                           value="<?php echo $this->mdl_email_templates->form_value('email_template_from_email', true); ?>">
                </div>

                <div class="form-group">
                    <label for="email_template_cc" class="control-label"><?php _trans('cc'); ?></label>
                    <input type="text" name="email_template_cc" id="email_template_cc" class="form-control taggable"
                           value="<?php echo $this->mdl_email_templates->form_value('email_template_cc', true); ?>">
                </div>

                <div class="form-group">
                    <label for="email_template_bcc" class="control-label"><?php _trans('bcc'); ?>: </label>
                    <input type="text" name="email_template_bcc" id="email_template_bcc" class="form-control taggable"
                           value="<?php echo $this->mdl_email_templates->form_value('email_template_bcc', true); ?>">
                </div>

                <div class="form-group">
                    <label for="email_template_subject" class="control-label">
                        <?php _trans('subject'); ?>
                    </label>
                    <input type="text" name="email_template_subject" id="email_template_subject"
                           class="form-control taggable"
                           value="<?php echo $this->mdl_email_templates->form_value('email_template_subject', true); ?>">
                </div>

                <div class="form-group">
                    <label for="email_template_pdf_template" class="control-label">
                        <?php _trans('pdf_template'); ?>:
                    </label>
                    <select name="email_template_pdf_template" id="email_template_pdf_template"
                            class="form-control simple-select">
                        <option value=""><?php _trans('none'); ?></option>

                        <optgroup label="<?php _trans('invoices'); ?>">
                            <?php foreach ($invoice_templates as $template): ?>
                                <option class="hidden-invoice" value="<?php echo $template; ?>"
                                    <?php check_select($selected_pdf_template, $template); ?>>
                                    <?php echo $template; ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>

                        <optgroup label="<?php _trans('quotes'); ?>">
                            <?php foreach ($quote_templates as $template): ?>
                                <option class="hidden-quote" value="<?php echo $template; ?>"
                                    <?php check_select($selected_pdf_template, $template); ?>>
                                    <?php echo $template; ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>

            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-xs-12 col-md-6">

                <label for="email_template_body">
                    <?php _trans('body'); ?>
                </label>
                <div class="form-group">

                    <div class="html-tags btn-group btn-group-sm">
                        <span class="html-tag btn btn-default" data-tag-type="text-paragraph">
                            <i class="fa fa-fw fa-paragraph"></i>
                        </span>
                        <span class="html-tag btn btn-default" data-tag-type="text-bold">
                            <i class="fa fa-fw fa-bold"></i>
                        </span>
                        <span class="html-tag btn btn-default" data-tag-type="text-italic">
                            <i class="fa fa-fw fa-italic"></i>
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
                            <i class="fa fa-fw fa-code"></i>
                        </span>
                        <span class="html-tag btn btn-default" data-tag-type="text-hr">
                            &lt;hr/&gt;
                        </span>
                        <span class="html-tag btn btn-default" data-tag-type="text-css">
                            CSS
                        </span>
                    </div>

                    <textarea name="email_template_body" id="email_template_body" rows="5"
                              class="email-template-body form-control taggable"><?php echo $this->mdl_email_templates->form_value('email_template_body', true); ?></textarea>
                    <br>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <?php _trans('preview'); ?>
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
            <div class="col-xs-12 col-md-6">

                <?php $this->layout->load_view('email_templates/template-tags'); ?>

            </div>
        </div>

    </div>

</form>

<script>
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
