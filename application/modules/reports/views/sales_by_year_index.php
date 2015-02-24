<div class="headerbar">
	<h1><?php echo lang('sales_by_date'); ?></h1>
</div>

<div class="content">

	<?php $this->layout->load_view('layout/alerts'); ?>
	
	<div id="report_options" class="panel panel-default">
	
		<div class="panel-heading">
			<h3 class="panel-title"> 
				<i class="fa fa-print"></i> 
				<?php echo lang('report_options'); ?>
			</h3>
		</div> <!-- #panel-heading -->
		
		<div class="panel-body">
		
			<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>"> 
		
			 <div class="form-group has-feedback">
                    <label for="from_date">
                        <?php echo lang('from_date'); ?>
                    </label>

									<div class="input-group">
											<input name="from_date" id="from_date"
														class="form-control datepicker">
											<span class="input-group-addon">
													<i class="fa fa-calendar fa-fw"></i>
											</span>
									</div>
                </div>
                
                <div class="form-group has-feedback">
                    <label for="to_date">
                        <?php echo lang('to_date'); ?>
                    </label>

									<div class="input-group">
											<input name="to_date" id="to_date"
														class="form-control datepicker">
											<span class="input-group-addon">
													<i class="fa fa-calendar fa-fw"></i>
											</span>
									</div>
                </div>
            
            
                <div>
                    	
				                    
				                    <div class="col-xs-12 col-md-2" style="margin-right:10px; padding-left:0px;">
				                    <label for="minQuantity">
				                        <?php echo lang('min_quantity'); ?>
				                    </label>
				                    
								                    <div>
								                    <input type="number" id="minQuantity" name="minQuantity" min="0">
								                    </div>
				                    </div>
			                    
			            		
			            		
				            		
				            		<div class="col-xs-12 col-md-2" style=padding-left:0px;>
				                    <label for="maxQuantity">
				                        <?php echo lang('max_quantity'); ?>
				                    </label>
				                    
											         <div>
											         <input type="number" id="maxQuantity" name="maxQuantity" min="0">
											         </div>
				                    
				                    </div>
				                    				                    
				                    <div style="clear:both;"></div>
			                    
                    
            	</div>
             

            <div class="form-group has-feedback" >
                    <label for="IVA">
                        
                    </label>
                    
					                 <div>
					                 <input type="checkbox" id="checkboxTax" name="checkboxTax"> <?php echo lang('values_with_taxes');?>
					                 </div>
                    
                    
            </div> <!-- IVA -->
            
            
            <input type="submit" class="btn btn-success" name="btn_submit"
                       value="<?php echo lang('run_report'); ?>">
                       

     
		</form>
		
		</div> <!-- #panel-body -->
		
		
	</div> <!-- #report_options -->
	
</div> <!-- content -->