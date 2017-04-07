<!doctype html lang="<?php _trans('cldr'); ?>">

<!--[if lt IE 7]>
<html class="no-js ie6 oldie" lang="<?php _trans('cldr'); ?>"> <![endif]-->
<!--[if IE 7]>
<html class="no-js ie7 oldie" lang="<?php _trans('cldr'); ?>"> <![endif]-->
<!--[if IE 8]>
<html class="no-js ie8 oldie" lang="<?php _trans('cldr'); ?>"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="<?php _trans('cldr'); ?>"> <!--<![endif]-->

<head>

    <?php
    // Get the page head content
    $this->layout->load_view('layout/includes/head');
    ?>

</head>
<body class="<?php echo get_setting('disable_sidebar') ? 'hidden-sidebar' : ''; ?>">

<noscript>
    <div class="alert alert-danger no-margin"><?php _trans('please_enable_js'); ?></div>
</noscript>

<?php
// Get the navigation bar
$this->layout->load_view('layout/includes/navbar');

?>

<div id="main-area">
    <?php
    // Display the sidebar if enabled
    if (get_setting('disable_sidebar') != 1) {
        $this->layout->load_view('layout/includes/sidebar');
    }
    ?>
    <div id="main-content">
        <?php echo $content; ?>
    </div>

</div>

<div id="modal-placeholder"></div>

<?php echo $this->layout->load_view('layout/includes/fullpage-loader'); ?>

<script defer src="<?php echo base_url(); ?>assets/core/js/scripts.min.js"></script>
<?php if (trans('cldr') != 'en') { ?>
    <script src="<?php echo base_url(); ?>assets/core/js/locales/bootstrap-datepicker.<?php _trans('cldr'); ?>.js"></script>
<?php } ?>

</body>
</html>
