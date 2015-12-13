<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>InvoicePlane</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="<?php echo base_url(); ?>assets/default/css/style.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/default/css/custom.css" rel="stylesheet">

    <style>
        body {
            padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
        }
    </style>

    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <script type="text/javascript" src="<?php echo base_url(); ?>assets/default/js/libs/jquery-1.11.2.min.js"></script>

    <script type="text/javascript">
        $(function () {
            $('#email').focus();
        });
    </script>

</head>

<body>
<div class="container">

    <div id="password_reset"
         class="panel panel-default panel-body col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">

        <div class="row"><?php $this->layout->load_view('layout/alerts'); ?></div>

        <h3><?php echo lang('password_reset'); ?></h3>
        <br/>

        <p><?php echo lang('password_reset_info'); ?></p>

        <form class="form-horizontal" method="post"
              action="<?php echo site_url($this->uri->uri_string()); ?>">

            <div class="form-group">
                <div class="col-xs-12 col-sm-3">
                    <label for="email" class="control-label"><?php echo lang('email'); ?></label>
                </div>
                <div class="col-xs-12 col-sm-9">
                    <input type="text" name="email" id="email" class="form-control"
                           placeholder="<?php echo lang('email'); ?>">
                </div>
            </div>

            <input type="submit" name="btn_reset" class="btn btn-block btn-warning"
                   value="<?php echo lang('reset_password'); ?>">

        </form>

    </div>
</div>

</body>
</html>
