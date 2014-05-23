<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">

	<div class="install-step">

		<h1>FusionInvoice</h1>
		
		<legend><?php echo lang('setup_prerequisites'); ?></legend>

		<p><?php echo lang('setup_prerequisites_message'); ?></p> 
        
		<?php foreach ($basics as $basic) { ?>
			<?php if ($basic['success']) { ?>
			<p><span class="label label-success"><?php echo lang('success'); ?></span> <?php echo $basic['message']; ?></p>
			<?php } else { ?>
			<p><span class="label label-important"><?php echo lang('failure'); ?></span> <?php echo $basic['message']; ?></p>
			<?php } ?>
		<?php } ?>

		<?php foreach ($writables as $writable) { ?>
			<?php if ($writable['success']) { ?>
			<p><span class="label label-success"><?php echo lang('success'); ?></span> <?php echo $writable['message']; ?></p>
			<?php } else { ?>
			<p><span class="label label-important"><?php echo lang('failure'); ?></span> <?php echo $writable['message']; ?></p>
			<?php } ?>
		<?php } ?>

		<?php if ($errors) { ?>
		<input class="btn btn-primary" type="submit" name="btn_try_again" value="<?php echo lang('try_again'); ?>">
		<?php } else { ?>
		<input class="btn btn-primary" type="submit" name="btn_continue" value="<?php echo lang('continue'); ?>">
		<?php } ?>

	</div>

</form>