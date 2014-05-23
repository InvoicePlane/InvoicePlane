<div class="headerbar">
	<h1><?php echo lang('import_data'); ?></h1>
</div>

<div class="content">

	<?php $this->layout->load_view('layout/alerts'); ?>

	<div class="widget">

		<div class="widget-title">
            <h5><?php echo lang('import_from_csv'); ?></h5>
		</div>

		<div class="padded">
			
			<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="form-horizontal">
                
                <?php foreach ($files as $file) { ?>
                <div class="control-group">
                        <div class="controls">
                            <label class="checkbox">
                                <input type="checkbox" name="files[]" value="<?php echo $file; ?>"> <?php echo $file; ?>
                            </label>
                        </div>
                    </div>
                <?php } ?>

				<div class="control-group">
					<div class="controls">
						<input type="submit" class="btn" name="btn_submit" value="<?php echo lang('import'); ?>">	
					</div>
				</div>

			</form>

		</div>

	</div>

</div>