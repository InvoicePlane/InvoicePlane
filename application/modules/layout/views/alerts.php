<?php

$alert_class = 'alert';
$alert_class .= isset($without_margin) ? ' no-margin' : '';
$content = null;

// Get validation errors
if (function_exists('validation_errors') && validation_errors()) {
    echo validation_errors('<div class="' . $alert_class . ' alert-danger">', '</div>');
}

// Get flash alert_*type* messages and show them
$types = explode(' ', 'success info warning error');
$class = explode(' ', 'success info warning danger');
$icons = explode(' ', 'info-circle info-circle exclamation-circle warning');
foreach ($types as $x => $type) {
    if ($this->session->flashdata('alert_' . $type)) {
        echo '<div class="' . $alert_class . ' alert-' . $class[$x] . ' alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <i class="fa fa-fw fa-lg fa-' . $icons[$x] . '"></i><span>'
        . $this->session->flashdata('alert_' . $type) . '</span></div>' . PHP_EOL;
    }
}
