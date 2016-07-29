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

    <div id="password_reset"
         class="panel panel-default panel-body col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">

        <div class="row"><?php $this->layout->load_view('layout/alerts'); ?></div>

        <h3><?php echo trans('password_reset'); ?></h3>

        <br/>

        <p><?php echo trans('password_reset_info'); ?></p>

        <form class="form-horizontal" method="post"
              action="<?php echo site_url($this->uri->uri_string()); ?>">

            <div class="form-group">
                <div class="col-xs-12 col-sm-3">
                    <label for="email" class="control-label"><?php echo trans('email'); ?></label>
                </div>
                <div class="col-xs-12 col-sm-9">
                    <input type="text" name="email" id="email" class="form-control"
                           placeholder="<?php echo trans('email'); ?>">
                </div>
            </div>

            <input type="submit" name="btn_reset" class="btn btn-block btn-danger"
                   value="<?php echo trans('reset_password'); ?>">

        </form>

    </div>

</div>

<script type="text/javascript">
    document.getElementById("email").focus();
</script>

</body>
</html>
