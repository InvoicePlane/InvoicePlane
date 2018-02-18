<?php
$alert_class = 'alert';
$alert_class .= isset($without_margin) ? ' no-margin' : '';
$content = null;

// Get validation errors
if (function_exists('validation_errors')) {
    if (validation_errors()) {
        echo validation_errors('<div class="' . $alert_class . ' alert-danger">', '</div>');
    }
}

// Get success messages
if ($this->session->flashdata('alert_success')) {
    echo '<div class="' . $alert_class . ' alert-success">' . $this->session->flashdata('alert_success') . '</div>';
}

if ($this->session->flashdata('alert_info')) {
    echo '<div class="' . $alert_class . ' alert-info">' . $this->session->flashdata('alert_info') . '</div>';
}

if ($this->session->flashdata('alert_error')) {
    echo '<div class="' . $alert_class . ' alert-danger">' . $this->session->flashdata('alert_error') . '</div>';
}
