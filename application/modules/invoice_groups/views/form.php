<form method="post">

    <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('invoice_group_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">

                <?php $this->layout->load_view('layout/alerts'); ?>

                <div class="form-group">
                    <label class="control-label" for="invoice_group_name">
                        <?php _trans('name'); ?>
                    </label>
                    <input type="text" name="invoice_group_name" id="invoice_group_name" class="form-control"
                           value="<?php echo $this->mdl_invoice_groups->form_value('invoice_group_name', true); ?>">
                </div>

                <div class="form-group">
                    <label class="control-label" for="invoice_group_identifier_format">
                        <?php _trans('identifier_format'); ?>
                    </label>
                    <input type="text" class="form-control taggable"
                           name="invoice_group_identifier_format" id="invoice_group_identifier_format"
                           value="<?php echo $this->mdl_invoice_groups->form_value('invoice_group_identifier_format', true); ?>"
                           placeholder="INV-{{{id}}}">
                </div>

                <div class="form-group">
                    <label class="control-label" for="invoice_group_next_id">
                        <?php _trans('next_id'); ?>
                    </label>
                    <input type="number" name="invoice_group_next_id" id="invoice_group_next_id" class="form-control"
                           value="<?php echo $this->mdl_invoice_groups->form_value('invoice_group_next_id'); ?>">
                </div>

                <div class="form-group">
                    <label class="control-label" for="invoice_group_left_pad">
                        <?php _trans('left_pad'); ?>
                    </label>
                    <input type="number" name="invoice_group_left_pad" id="invoice_group_left_pad" class="form-control"
                           value="<?php echo $this->mdl_invoice_groups->form_value('invoice_group_left_pad'); ?>">
                </div>

                <hr>

                <div class="form-group no-margin">

                    <label for="tags_client"><?php _trans('identifier_format_template_tags'); ?></label>

                    <p class="small"><?php _trans('identifier_format_template_tags_instructions'); ?></p>

                    <select id="tags_client" class="tag-select form-control">
                        <option value="{{{id}}}">
                            <?php _trans('id'); ?>
                        </option>
                        <option value="{{{year}}}">
                            <?php _trans('current_year'); ?>
                        </option>
                        <option value="{{{yy}}}">
                            <?php _trans('current_yy'); ?>
                        </option>
                        <option value="{{{month}}}">
                            <?php _trans('current_month'); ?>
                        </option>
                        <option value="{{{day}}}">
                            <?php _trans('current_day'); ?>
                        </option>
                    </select>

                </div>

            </div>
        </div>

    </div>

</form>
