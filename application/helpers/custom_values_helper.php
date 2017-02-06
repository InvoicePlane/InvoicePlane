<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Kovah (www.kovah.de)
 * @copyright	Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 *
 */

function format_date($txt)
{
    return date_from_mysql($txt);
}

function format_text($txt)
{
    return $txt;
}

function format_singlechoice($txt)
{
    $CI = get_instance();
    $CI->load->model('custom_values/mdl_custom_values', 'cv');
    $el = $CI->cv->get_by_id($txt)->row();
    return $el->custom_values_value;
}

function format_multiplechoice($txt)
{
    $CI = get_instance();
    $CI->load->model('custom_values/mdl_custom_values', 'cv');

    $values = explode(",",$txt);
    $values = $CI->cv->where_in('custom_values_id', $values)->get()->result();
    $values_text = [];
    foreach($values as $value){
        $values_text[] = $value->custom_values_value;
    }
    return implode("\n",$values_text);
}

function format_boolean($txt){
  if($txt == "1")
  {
    return trans("true");
  }
  else if($txt == "0"){
    return trans("false");
  }
  return "";
}

function format_fallback($txt)
{
    return format_text($txt);
}

function print_field($ref, $custom_field, $cv){
?>
<div class="form-group">
  <label><?php echo $custom_field->custom_field_label; ?>: </label>
  <?php $fieldValue = $ref->mdl_clients->form_value('custom[' . $custom_field->custom_field_column . ']'); ?>
  <div class="controls">
    <?php switch($custom_field->custom_field_type){
      case "DATE":
      $dateValue = ($fieldValue == "" ? "" : date_from_mysql($fieldValue));
      ?>
      <input type="text" class="form-control input-sm datepicker"
             name="custom[<?php echo $custom_field->custom_field_column; ?>]"
             id="<?php echo $custom_field->custom_field_column; ?>"
             value="<?php echo form_prep($dateValue); ?>">
      <?php break;
      case "SINGLE-CHOICE":
      $choices = $cv[$custom_field->custom_field_column];
      $formvalue = $ref->mdl_clients->form_value('custom[' . $custom_field->custom_field_column . ']');
      ?>
      <select class="form-control" name="custom[<?php echo $custom_field->custom_field_column; ?>]"
        id="<?php echo $custom_field->custom_field_column; ?>">
        <?php foreach($choices as $val): ?>
          <?php if($val->custom_values_id == $formvalue){ $selected = " selected "; } else { $selected = ""; } ?>
          <option value="<?php echo $val->custom_values_id ?>"<?php echo $selected;?>>
            <?php echo $val->custom_values_value; ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php

      break;

      case "MULTIPLE-CHOICE":
      $choices = $cv[$custom_field->custom_field_column];
      $selChoices = $ref->mdl_clients->form_value('custom[' . $custom_field->custom_field_column . ']');
      $selChoices = explode(",", $selChoices);
      ?>
      <select
          id="<?php echo $custom_field->custom_field_column; ?>"
          name="custom[<?php echo $custom_field->custom_field_column; ?>][]"
          multiple="multiple"
          class="form-control"
        >
        <?php foreach($choices as $choice): ?>
        <?php $sel = (in_array($choice->custom_values_id, $selChoices)?'selected="selected"':""); ?>
        <option value="<?php echo htmlentities($choice->custom_values_id); ?>" <?php echo $sel; ?>><?php echo htmlentities($choice->custom_values_value); ?></option>
        <?php endforeach; ?>
      </select>
      <script>
        $('#<?php echo $custom_field->custom_field_column; ?>').select2();
      </script>
      <?php
      break;

      case "BOOLEAN":
      ?>
      <select
        id="<?php echo $custom_field->custom_field_column; ?>"
        name="custom[<?php echo $custom_field->custom_field_column; ?>]"
        class="form-control"
      >
        <option value="0" <?php echo ($fieldValue == "0"?"selected":"");?>><?php echo trans("false");?></option>
        <option value="1" <?php echo ($fieldValue == "1"?"selected":"");?>><?php echo trans("true");?></option>
      </select>
      <?php
      break;
      default:
      ?>
      <input type="text" class="form-control"
             name="custom[<?php echo $custom_field->custom_field_column; ?>]"
             id="<?php echo $custom_field->custom_field_column; ?>"
             value="<?php echo form_prep($ref->mdl_clients->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>">
    <?php } ?>
  </div>
</div>
<?php
}
