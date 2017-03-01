<!doctype html>

<!--[if lt IE 7]>
<html class="no-js ie6 oldie" lang="<?php echo trans('cldr'); ?>"> <![endif]-->
<!--[if IE 7]>
<html class="no-js ie7 oldie" lang="<?php echo trans('cldr'); ?>"> <![endif]-->
<!--[if IE 8]>
<html class="no-js ie8 oldie" lang="<?php echo trans('cldr'); ?>"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="<?php echo trans('cldr'); ?>"> <!--<![endif]-->

<head>

    <?php
    // Get the page head content
    $this->layout->load_view('layout/includes/head');
    ?>

</head>
<body class="<?php echo get_setting('disable_sidebar') ? 'hidden-sidebar' : ''; ?>">

<noscript>
    <div class="alert alert-danger no-margin"><?php echo trans('please_enable_js'); ?></div>
</noscript>

<?php
// Get the navigation bar
$this->layout->load_view('layout/includes/navbar');

// Display the sidebar if enabled
if (get_setting('disable_sidebar') != 1) {
    $this->layout->load_view('layout/includes/sidebar');
}
?>

<div id="main-area">

    <?php echo $content; ?>

</div>

<div id="modal-placeholder"></div>

<div id="fullpage-loader" style="display: none">
    <div class="loader-content">
        <i class="fa fa-cog fa-spin"></i>
        <div id="loader-error" style="display: none">
            <?php echo trans('loading_error'); ?><br/>
            <a href="https://wiki.invoiceplane.com/<?php echo trans('cldr'); ?>/1.0/general/faq"
               class="btn btn-primary btn-sm" target="_blank">
                <i class="fa fa-support"></i> <?php echo trans('loading_error_help'); ?>
            </a>
        </div>
    </div>
</div>

<script defer src="<?php echo base_url(); ?>assets/default/js/scripts.min.js"></script>
<?php if (trans('cldr') != 'en') { ?>
    <script src="<?php echo base_url(); ?>assets/default/js/locales/bootstrap-datepicker.<?php echo trans('cldr'); ?>.min.js"></script>
<?php } ?>

</body>
</html>
