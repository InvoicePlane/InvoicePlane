<form method="post" class="form-horizontal">

    <div class="headerbar">
        <h1><?php echo lang('invoice_group_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div class="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <div class="form-group">
            <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                <label class="control-label"><?php echo lang('name'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-8 col-lg-8">
                <input type="text" name="invoice_group_name" id="invoice_group_name" class="form-control"
                       value="<?php echo $this->mdl_invoice_groups->form_value('invoice_group_name'); ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                <label class="control-label"><?php echo lang('prefix'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-8 col-lg-8">
                <input type="text" name="invoice_group_prefix" id="invoice_group_prefix" class="form-control"
                       value="<?php echo $this->mdl_invoice_groups->form_value('invoice_group_prefix'); ?>">
            </div>
        </div>
        
		<?php //---it---inizio ?>
		<div class="form-group">
			<div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
				<label class="control-label"><?php echo lang('it_suffisso'); ?>: </label>
			</div>
			<div class="col-xs-12 col-sm-8 col-lg-8">
				<input type="text" name="invoice_it_group_suffix" id="invoice_it_group_suffix" class="form-control"
						value="<?php echo $this->mdl_invoice_groups->form_value('invoice_it_group_suffix'); ?>">
			</div>
		</div>
		<?php //---it---fine ?>
		
        <div class="form-group">
            <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                <label class="control-label"><?php echo lang('next_id'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-8 col-lg-8">
                <input type="text" name="invoice_group_next_id" id="invoice_group_next_id" class="form-control"
                       value="<?php echo $this->mdl_invoice_groups->form_value('invoice_group_next_id'); ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                <label class="control-label"><?php echo lang('left_pad'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-8 col-lg-8">
                <input type="text" name="invoice_group_left_pad" id="invoice_group_left_pad" class="form-control"
                       value="<?php echo $this->mdl_invoice_groups->form_value('invoice_group_left_pad'); ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                <label class="control-label"><?php echo lang('year_prefix'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-8 col-lg-8">
                <select name="invoice_group_prefix_year" id="invoice_group_prefix_year" class="form-control">
                    <option value="0" <?php if ($this->mdl_invoice_groups->form_value('invoice_group_prefix_year') == 0) { ?>selected="selected"<?php } ?>><?php echo lang('no'); ?></option>
                    <option value="1" <?php if ($this->mdl_invoice_groups->form_value('invoice_group_prefix_year') == 1) { ?>selected="selected"<?php } ?>><?php echo lang('yes'); ?></option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                <label class="control-label"><?php echo lang('month_prefix'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-8 col-lg-8">
                <select name="invoice_group_prefix_month" id="invoice_group_prefix_month" class="form-control">
                    <option value="0" <?php if ($this->mdl_invoice_groups->form_value('invoice_group_prefix_month') == 0) { ?>selected="selected"<?php } ?>><?php echo lang('no'); ?></option>
                    <option value="1" <?php if ($this->mdl_invoice_groups->form_value('invoice_group_prefix_month') == 1) { ?>selected="selected"<?php } ?>><?php echo lang('yes'); ?></option>
                </select>
            </div>
        </div>
        
		<?php //---it---inizio ?>	
		<div class="control-group">
			<div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
				<label class="control-label"><?php echo lang('it_suffisso_anno'); ?>: </label>
			</div>
			<div class="col-xs-12 col-sm-8 col-lg-8">
				<select name="invoice_it_group_suffix_year" id="invoice_it_group_suffix_year" class="form-control">
					<option value="0" <?php if ($this->mdl_invoice_groups->form_value('invoice_it_group_suffix_year') == 0) { ?>selected="selected"<?php } ?>><?php echo lang('no'); ?></option>
					<option value="1" <?php if ($this->mdl_invoice_groups->form_value('invoice_it_group_suffix_year') == 1) { ?>selected="selected"<?php } ?>><?php echo lang('yes'); ?></option>
				</select>
			</div>
		</div>
		<?php //---it---fine ?>
		
    </div>

</form>