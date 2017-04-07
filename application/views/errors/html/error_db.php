<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
if (!isset($CI)) $CI = new CI_Controller();;
$CI->load->helper('url');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Database Error</title>
    <link rel="stylesheet"
          href="<?php echo base_url('assets/invoiceplane/css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/core/css/custom.css'); ?>">
</head>
<body class="has-error">

<div class="container text-center">
    <img src="<?php echo base_url('assets/core/img/logo_400x200.png') ?>"/>

    <div class="row">
        <div class="col-xs-12 col-md-8 col-md-offset-2">

            <br>
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <?php echo $heading; ?>
                </div>
                <div class="panel-body text-danger">
                    <?php echo $message; ?>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
