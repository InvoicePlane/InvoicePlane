<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2017 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * @param $txt
 * @return bool|DateTime|string
 */
function format_date($txt)
{
    if ($txt == null) {
        return '';
    }

    return date_from_mysql($txt);
}

/**
 * @param $txt
 * @return string
 */
function format_text($txt)
{
    if ($txt == null) {
        return '';
    }

    return $txt;
}

/**
 * @param $txt
 * @return string
 */
function format_singlechoice($txt)
{
    if ($txt == null) {
        return '';
    }

    $CI = get_instance();
    $CI->load->model('custom_values/mdl_custom_values', 'cv');
    $el = $CI->cv->get_by_id($txt)->row();

    return $el->custom_values_value;
}

/**
 * @param $txt
 * @return string
 */
function format_multiplechoice($txt)
{
    if ($txt == null) {
        return '';
    }

    $CI = get_instance();
    $CI->load->model('custom_values/mdl_custom_values', 'cv');

    $values = explode(',', $txt);
    $values = $CI->cv->where_in('custom_values_id', $values)->get()->result();
    $values_text = [];

    foreach ($values as $value) {
        $values_text[] = $value->custom_values_value;
    }

    return implode("\n", $values_text);
}

function format_boolean($txt)
{
    if ($txt == null) {
        return '';
    }

    if ($txt == '1') {
        return trans('true');
    } else if ($txt == '0') {
        return trans('false');
    }

    return '';
}

function format_avs($txt)
{
    if (!preg_match('/(\d{3})(\d{4})(\d{4})(\d{2})/', $txt, $matches)) {
        return $txt;
    }
    return $matches[1] . "." . $matches[2] . "." . $matches[3] . "." . $matches[4];

}

/**
 * @param $txt
 * @return string
 */
function format_fallback($txt)
{
    return format_text($txt);
}

/**
 * Print a custom form field based on the type
 * @param $module
 * @param $custom_field
 * @param $cv
 * @param string $class_top
 * @param string $class_bottom
 * @param string $label_class
 */
function print_field($module, $custom_field, $cv, $class_top = '', $class_bottom = 'controls', $label_class = '')
{
    ?>
    <div class="form-group">
        <div class="<?php echo $class_top; ?>">
            <label<?php echo($label_class != '' ? " class='" . $label_class . "'" : ''); ?>
                    for="custom[<?php echo $custom_field->custom_field_id; ?>]">
                <?php _htmlsc($custom_field->custom_field_label); ?>
            </label>
        </div>
        <?php $fieldValue = $module->form_value('custom[' . $custom_field->custom_field_id . ']'); ?>
        <div class="<?php echo $class_bottom; ?>">
            <?php
            switch ($custom_field->custom_field_type) {
            case 'DATE':
                $dateValue = ($fieldValue == "" ? "" : date_from_mysql($fieldValue));
                ?>
            <input type="text" class="form-control input-sm datepicker"
                   name="custom[<?php echo $custom_field->custom_field_id; ?>]"
                   id="<?php echo $custom_field->custom_field_id; ?>"
                   value="<?php echo $dateValue; ?>">
            <?php
            break;
            case 'SINGLE-CHOICE':
            $choices = $cv[$custom_field->custom_field_id];
            ?>
                <select class="form-control simple-select" name="custom[<?php echo $custom_field->custom_field_id; ?>]"
                        id="<?php echo $custom_field->custom_field_id; ?>">
                    <option value=""><?php echo trans('none'); ?></option>
                    <?php foreach ($choices as $val): ?>
                        <option value="<?php echo $val->custom_values_id; ?>"
                            <?php check_select($val->custom_values_id, $fieldValue); ?>>
                            <?php _htmlsc($val->custom_values_value); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php
            break;
            case 'MULTIPLE-CHOICE':
            $choices = $cv[$custom_field->custom_field_id];
            $selChoices = explode(',', $fieldValue); ?>
                <select id="<?php echo $custom_field->custom_field_id; ?>"
                        name="custom[<?php echo $custom_field->custom_field_id; ?>][]"
                        multiple="multiple"
                        class="form-control">
                    <option value=""><?php echo trans('none'); ?></option>
                    <?php foreach ($choices as $choice): ?>
                        <option value="<?php echo $choice->custom_values_id; ?>" <?php check_select(in_array($choice->custom_values_id, $selChoices)); ?>>
                            <?php _htmlsc($choice->custom_values_value); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <script>
                    $('#<?php echo $custom_field->custom_field_id; ?>').select2();
                </script>
            <?php
            break;
            case 'BOOLEAN':
            ?>
                <select id="<?php echo $custom_field->custom_field_id; ?>"
                        name="custom[<?php echo $custom_field->custom_field_id; ?>]"
                        class="form-control">
                    <option value="0" <?php check_select($fieldValue, '0'); ?>><?php echo trans('false'); ?></option>
                    <option value="1" <?php check_select($fieldValue, '1'); ?>><?php echo trans('true'); ?></option>
                </select>
            <?php
            break;
            default:
            ?>
            <input type="text" class="form-control"
                   name="custom[<?php echo $custom_field->custom_field_id; ?>]"
                   id="<?php echo $custom_field->custom_field_id; ?>"
                   value="<?php _htmlsc($fieldValue); ?>">
            <?php } ?>
        </div>
    </div>
    <?php
}
