<script>
    $(function () {
        <?php $this->layout->load_view('clients/script_select2_client_id.js'); ?>
    });
</script>

<?php 
$attributes = array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data');
echo form_open('',$attributes);
//<form method="post" class="form-horizontal" >
?>


    <?php if ($car_id) { ?>
        <input type="hidden" name="car_id" value="<?php echo $car_id; ?>">
        <div id="headerbar">
    	
        <h1>Fahrzeug bearbeiten <?php echo $this->mdl_cars->form_value('car_vehicle'); ?></h1>
         <?php
         $this->layout->load_view('layout/header_buttons'); 
        ?>
       
        
    	</div>
    <?php }
		else
		{
			?>
            <div id="headerbar">
    	
            <h1><?php echo lang('add_car'); ?></h1>
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
                <label for="car_client_id" class="control-label"><?php echo lang('client'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <select name="car_client_id" id="car_client_id" class="client-id-select form-control" autofocus="autofocus">
                        <?php foreach ($clients as $client) { ?>
                        <option value="<?php echo $client->client_id; ?>"
                        <?php check_select($this->mdl_cars->form_value('car_client_id'), $client->client_id) ?>
                ><?php echo $client->client_fullname; ?></option>
                        <?php } ?>
                </select>
            </div>
        </div>
	
        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="car_vehicle" class="control-label"><?php echo lang('car_vehicle'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="car_vehicle" id="car_vehicle" class="form-control"
                       value="<?php echo $this->mdl_cars->form_value('car_vehicle'); ?>">
            </div>
        </div>
        
        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="car_chassnr" class="control-label"><?php echo lang('car_chassnr'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="car_chassnr" id="car_chassnr" class="form-control"
                       value="<?php echo $this->mdl_cars->form_value('car_chassnr'); ?>">
            </div>
        </div>
        
        <div class="col-xs-12 col-sm-6 payment-method-wrapper">
                    <input type="hidden" name="car_date_created" class="hidden"
                           value="<?php echo date('d.m.Y'); ?>">
        </div>
        
        <div class="col-xs-12 col-sm-6 payment-method-wrapper">
                    <input type="hidden" name="car_date_modified" class="hidden"
                           value="<?php echo date('d.m.Y'); ?>">
        </div>
        
         <div class="form-group has-feedback">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="car_builddate" class="control-label"><?php echo lang('car_builddate'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="input-group">
                    <input name="car_builddate" id="car_builddate"
                           class="form-control datepicker"
                           value="<?php echo date_from_mysql($this->mdl_cars->form_value('car_builddate')); ?>">
                  <span class="input-group-addon">
                      <i class="fa fa-calendar fa-fw"></i>
                  </span>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="car_kmstand" class="control-label"><?php echo lang('car_kmstand'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="car_kmstand" id="car_kmstand" class="form-control"
                       value="<?php echo $this->mdl_cars->form_value('car_kmstand'); ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="car_licenseplate" class="control-label"><?php echo lang('car_licenseplate'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="car_licenseplate" id="car_licenseplate" class="form-control"
                       value="<?php echo $this->mdl_cars->form_value('car_licenseplate'); ?>">
            </div>
        </div>

<div class="form-group has-feedback">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="car_auhu" class="control-label"><?php echo lang('car_auhu'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="input-group">
                    <input name="car_auhu" id="car_auhu"
                           class="form-control datepicker"
                           value="<?php echo date_from_mysql($this->mdl_cars->form_value('car_auhu')); ?>">
                  <span class="input-group-addon">
                      <i class="fa fa-calendar fa-fw"></i>
                  </span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="car_note" class="control-label"><?php echo lang('note'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <textarea name="car_note"
                          class="form-control"><?php echo $this->mdl_cars->form_value('car_note'); ?></textarea>
            </div>
        </div>
        
        <div class="form-group">

		<div class="col-xs-12 col-md-6">

                    <?php if ($this->mdl_cars->has_url_key($this->mdl_cars->form_value('car_id')) == true) {$this->layout->load_view('upload/dropzone-car-html');} ?>

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
                           value="<?php echo form_prep($this->mdl_cars->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>">
                </div>
            </div>
        <?php } ?>
		 
    </div>
<?php $this->layout->load_view('upload/dropzone-car-scripts'); ?>
<?php echo form_close();?>
