<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>FusionInvoice</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">

		<link href="<?php echo base_url(); ?>assets/default/css/bootstrap.min.css" rel="stylesheet">
		<link href="<?php echo base_url(); ?>assets/default/css/style.css" rel="stylesheet">
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/default/js/libs/jquery-1.7.1.min.js"></script>
		<style>
			body {
				padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
			}
		</style>

		<!--[if lt IE 9]>
		  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
        
        <script type="text/javascript">
            $(function() { $('#email').focus(); });
        </script>

	</head>

	<body>

	<div id="login">
        
        <?php if ($login_logo) { ?>
        <img src="<?php echo base_url(); ?>uploads/<?php echo $login_logo; ?>" class="login-logo">
        <?php } else { ?>
        <h1><?php echo lang('login'); ?></h1>
        <?php } ?>
		
		<form class="form-horizontal" method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">

			<div class="control-group">
				<label class="control-label"><?php echo lang('email'); ?></label>
				<div class="controls">
					<input type="text" name="email" id="email" placeholder="<?php echo lang('email'); ?>">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label"><?php echo lang('password'); ?></label>
				<div class="controls">
					<input type="password" name="password" id="password"  placeholder="<?php echo lang('password'); ?>">
				</div>
			</div>

			<div class="control-group">
				<div class="controls">
					<input type="submit" name="btn_login" value="<?php echo lang('login'); ?>" class="btn btn-primary">
				</div>
			</div>

		</form>

	</div><!--end.container-->

	</body>
</html>