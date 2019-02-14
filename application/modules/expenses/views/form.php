<?php 
$attributes = array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data');
echo form_open('',$attributes);
//<form method="post" class="form-horizontal" >
?>


    <?php if ($expense_id) { ?>
        <input type="hidden" name="expense_id" value="<?php echo $expense_id; ?>">
        <div id="headerbar">
    	
        <h1>Edit Expense <?php echo $expense_id; ?></h1>
         <?php
         $this->layout->load_view('layout/header_buttons'); 
        ?>
       
        
    	</div>
    <?php }
		else
		{
			?>
            <div id="headerbar">
    	
            <h1>Add Expense</h1>
            <?php
			 $this->layout->load_view('layout/header_buttons'); 
			?>
            
            </div>
            <?php
		}
	 ?>

    

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="client_id" class="control-label"><?php echo lang('client'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <select name="client_id" id="client_id" class="form-control">
                        <option value=""></option>
                        <?php foreach ($clients as $client) { ?>
                            <option value="<?php echo $client->client_id; ?>"
                                    <?php if ($this->mdl_expenses->form_value('client_id') == $client->client_id) { ?>selected="selected"<?php } ?>><?php echo $client->client_name; ?></option>
                        <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-group has-feedback">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="expense_date" class="control-label"><?php echo lang('date'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="input-group">
                    <input name="expense_date" id="expense_date"
                           class="form-control datepicker"
                           value="<?php echo date_from_mysql($this->mdl_expenses->form_value('expense_date')); ?>">
                  <span class="input-group-addon">
                      <i class="fa fa-calendar fa-fw"></i>
                  </span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="expense_amount" class="control-label"><?php echo lang('amount'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="expense_amount" id="expense_amount" class="form-control"
                       value="<?php echo format_amount($this->mdl_expenses->form_value('expense_amount')); ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="tax_rate_id" class="control-label"><?php echo lang('tax_rate'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <select name="tax_rate_id" id="tax_rate_id" class="form-control">
                    <option value=""></option>
                    <?php foreach ($tax_rates as $rate) { ?>
                        <option value="<?php echo $rate->tax_rate_id; ?>"
                                <?php if ($this->mdl_expenses->form_value('tax_rate_id') == $rate->tax_rate_id) { ?>selected="selected"<?php } ?>><?php echo $rate->tax_rate_name; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_method_id" class="control-label">
                    <?php echo lang('payment_method'); ?>
                </label>
            </div>
            <div class="col-xs-12 col-sm-6 payment-method-wrapper">

                <?php
                // Add a hidden input field if a payment method was set to pass the disabled attribute
                if ($this->mdl_expenses->form_value('payment_method_id')) { ?>
                    <input type="hidden" name="payment_method_id" class="hidden"
                           value="<?php echo $this->mdl_expenses->form_value('payment_method_id'); ?>">
                <?php } ?>

                <select id="payment_method_id" name="payment_method_id" class="form-control"
                    <?php echo($this->mdl_expenses->form_value('payment_method_id') ? 'disabled="disabled"' : ''); ?>>

                    <?php foreach ($payment_methods as $payment_method) { ?>
                        <option value="<?php echo $payment_method->payment_method_id; ?>"
                                <?php if ($this->mdl_expenses->form_value('payment_method_id') == $payment_method->payment_method_id) { ?>selected="selected"<?php } ?>>
                            <?php echo $payment_method->payment_method_name; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        
		<div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="expense_file" class="control-label">Select File to upload</label>
            </div>
            <div class="col-xs-12 col-sm-6">
                Current File:<a href='<?php echo site_url('expenses/getfile/').$expense_id; ?>'>Download</a>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="file" name="expense_file"
                          class="form-control" />
            </div>

        </div>
        
        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="expense_note" class="control-label"><?php echo lang('note'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <textarea name="expense_note"
                          class="form-control"><?php echo $this->mdl_expenses->form_value('expense_note'); ?></textarea>
            </div>

        </div>
		
        
        <?php foreach ($custom_fields as $custom_field) { ?>
            <div class="form-group">
                <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                    <label><?php echo $custom_field->custom_field_label; ?>: </label>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <input type="text" name="custom[<?php echo $custom_field->custom_field_column; ?>]"
                           id="<?php echo $custom_field->custom_field_column; ?>"
                           class="form-control"
                           value="<?php echo form_prep($this->mdl_expenses->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>">
                </div>
            </div>
        <?php } ?>
		 
    </div>

<?php echo form_close();?>
