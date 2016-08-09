<!doctype html>

<!--[if lt IE 7]>
<html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>
<html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>
<html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->

<head>
    <title>
        <?php
        if ($this->mdl_settings->setting('custom_title') != '') {
            echo $this->mdl_settings->setting('custom_title');
        } else {
            echo 'InvoicePlane';
        } ?>
    </title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width">
    <meta name="robots" content="NOINDEX,NOFOLLOW">

    <link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/default/img/favicon.png">

    <link href="<?php echo base_url(); ?>assets/default/css/style.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/default/css/custom.css" rel="stylesheet">

</head>

<body>

<noscript>
    <div class="alert alert-danger no-margin"><?php echo trans('please_enable_js'); ?></div>
</noscript>

<br>

<div class="container">

    <div id="login"
         class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">

        <div class="row"><?php $this->layout->load_view('layout/alerts'); ?></div>

        <?php if ($login_logo) { ?>
            <img src="<?php echo base_url(); ?>uploads/<?php echo $login_logo; ?>" class="login-logo img-responsive">
        <?php } else { ?>
            <h1><?php echo trans('login'); ?></h1>
        <?php } ?>

        <form class="form-horizontal" method="post"
              action="<?php echo site_url($this->uri->uri_string()); ?>">

            <div class="form-group">
                <div class="col-xs-12 col-sm-3">
                    <label for="email" class="control-label"><?php echo trans('email'); ?></label>
                </div>
                <div class="col-xs-12 col-sm-9">
                    <input type="email" name="email" id="email" class="form-control"
                           placeholder="<?php echo trans('email'); ?>"<?php if (!empty($_POST['email'])) : ?> value="<?php echo $_POST['email']; ?>"<?php endif; ?>>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12 col-sm-3">
                    <label for="password" class="control-label"><?php echo trans('password'); ?></label>
                </div>
                <div class="col-xs-12 col-sm-9">
                    <input type="password" name="password" id="password" class="form-control"
                           placeholder="<?php echo trans('password'); ?>"<?php if (!empty($_POST['password'])) : ?> value="<?php echo $_POST['email']; ?>"<?php endif; ?>>
                </div>
            </div>

            <input type="submit" name="btn_login" class="btn btn-block btn-primary"
                   value="<?php echo trans('login'); ?>">

        </form>

        <div class="text-right">
            <small>
                <a href="<?php echo site_url('sessions/passwordreset'); ?>" class="text-muted">
                    <?php echo trans('forgot_your_password'); ?>
                </a>
            </small>
        </div>

    </div>
</div>

<script type="text/javascript">
    document.getElementById("email").focus();
</script>

</body>
</html>
