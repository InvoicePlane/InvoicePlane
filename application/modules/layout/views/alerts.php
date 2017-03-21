<?php if (function_exists('validation_errors')) {
    if (validation_errors()) {
        echo validation_errors('<div class="alert alert-danger">', '</div>');
    }
} ?>

<?php if ($this->session->flashdata('alert_success')) { ?>
    <div class="alert alert-success"><?php echo $this->session->flashdata('alert_success'); ?></div>
<?php } ?>

<?php if ($this->session->flashdata('alert_info')) { ?>
    <div class="alert alert-info"><?php echo $this->session->flashdata('alert_info'); ?></div>
<?php } ?>

<?php if ($this->session->flashdata('alert_error')) { ?>
    <div class="alert alert-danger"><?php echo $this->session->flashdata('alert_error'); ?></div>
<?php } ?>

