<?php
$disabled           = $custom_field_usage ? ' disabled' : '';
$custom_field_table = $this->mdl_custom_fields->form_value('custom_field_table');
$custom_field_type  = $this->mdl_custom_fields->form_value('custom_field_type');
?>
<form method="post">

    <?php _csrf_field(); ?>

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('custom_field_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
        <div class="headerbar-item pull-right">
            <a href="<?php echo site_url('custom_values/field/' . $custom_field_id) ?>" class="btn btn-sm btn-default">
                                <i class="fa fa-list fa-margin"></i> <?php _trans('values'); ?></a>
        </div>
    </div>
<?php
if ($disabled) {
?>
    <input type="hidden" name="custom_field_table" value="<?php echo $custom_field_table; ?>">
    <input type="hidden" name="custom_field_type" value="<?php echo $custom_field_type; ?>">
<?php
}
?>
    <div id="content" class="row">

        <div class="col-xs-12 col-md-6 col-md-offset-3">

            <?php $this->layout->load_view('layout/alerts'); ?>

            <div class="form-group">
                <label for="custom_field_label"><?php _trans('label'); ?></label>
                <input type="text" name="custom_field_label" id="custom_field_label" class="form-control"
                       value="<?php echo $this->mdl_custom_fields->form_value('custom_field_label', true); ?>" required>
            </div>

            <div class="form-group">
                <label for="custom_field_table"><?php _trans('table'); ?></label>
                <select name="custom_field_table" id="custom_field_table" class="form-control simple-select"<?php echo $disabled ?: ' required'; ?>>
<?php
// New field? Auto select (work if come from custom_fields/table/*)
$custom_field_table = $custom_field_table ?: (isset($_SERVER['HTTP_REFERER']) ? 'ip_' . basename($_SERVER['HTTP_REFERER']) . '_custom' : '');
foreach ($custom_field_tables as $table => $label) {
?>
                    <option value="<?php echo $table; ?>" <?php check_select($custom_field_table, $table); ?>><?php _trans($label); ?></option>
<?php
}
?>
                </select>
            </div>

            <div class="form-group">
                <label for="custom_field_location"><?php _trans('position'); ?></label>
                <select name="custom_field_location" id="custom_field_location" class="form-control simple-select"></select>
            </div>

            <div class="form-group">
                <label for="custom_field_type"><?php _trans('type'); ?></label>
                <select name="custom_field_type" id="custom_field_type" class="form-control simple-select"<?php echo $disabled ?: ' required'; ?>>
<?php

foreach ($custom_field_types as $type) {
    $alpha = str_replace('-', '_', mb_strtolower($type));
?>
                        <option value="<?php echo $type; ?>" <?php check_select($custom_field_type, $type); ?>><?php _trans($alpha); ?></option>
<?php
}
?>
                </select>
            </div>
            <div class="form-group">
                <label for="custom_field_order"><?php _trans('order'); ?></label>
                <input type="number" name="custom_field_order" id="custom_field_order" class="form-control"
                       value="<?php echo $this->mdl_custom_fields->form_value('custom_field_order', true); ?>">
            </div>

        </div>

<?php $this->layout->load_view('layout/partial/custom_field_usage_list', ['custom_field_table' => $custom_field_table]); ?>

    </div>
</form>
<script>
    $(function () {
        function updatePositions(index, selKey) {
            $("#custom_field_location option").remove();
            var key = Object.keys(jsonPositions)[index];
            for (var pos in jsonPositions[key]) {
                var opt = $("<option>");
                opt.attr("value", pos);
                opt.text(jsonPositions[key][pos]);
                if (selKey == pos) {
                    opt.attr("selected", "selected");
                }
                $("#custom_field_location").append(opt);
            }
        }

        $("#custom_field_table").on("change", function () {
            var optionIndex = $("#custom_field_table option:selected").index();
            updatePositions(optionIndex);
        });

        var jsonPositions = json_parse('<?php echo json_encode($positions); ?>', <?php echo (int) IP_DEBUG; ?>);
        var optionIndex = $("#custom_field_table option:selected").index();
        // Init Selector with Selected value
        updatePositions(optionIndex, <?php echo $custom_field_location; ?>);
    });
</script>
