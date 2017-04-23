<form method="post">

    <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('custom_field_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content" class="row">

        <div class="col-xs-12 col-md-6 col-md-offset-3">

            <?php $this->layout->load_view('layout/alerts'); ?>

            <div class="form-group">
                <label for="custom_field_table"><?php _trans('table'); ?></label>
                <select name="custom_field_table" id="custom_field_table" class="form-control simple-select">
                    <?php foreach ($custom_field_tables as $table => $label) { ?>
                        <option value="<?php echo $table; ?>"
                            <?php check_select($this->mdl_custom_fields->form_value('custom_field_table'), $table); ?>>
                            <?php echo lang($label); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="custom_field_label"><?php _trans('label'); ?></label>
                <input type="text" name="custom_field_label" id="custom_field_label" class="form-control"
                       value="<?php echo $this->mdl_custom_fields->form_value('custom_field_label', true); ?>">
            </div>

            <div class="form-group">
                <label for="custom_field_type"><?php _trans('type'); ?></label>
                <select name="custom_field_type" id="custom_field_type" class="form-control simple-select">
                    <?php foreach ($custom_field_types as $type) { ?>
                        <?php $alpha = str_replace("-", "_", strtolower($type)); ?>
                        <option value="<?php echo $type; ?>"
                            <?php check_select($this->mdl_custom_fields->form_value('custom_field_type'), $type); ?>>
                            <?php _trans($alpha); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="custom_field_order"><?php _trans('order'); ?></label>
                <input type="number" name="custom_field_order" id="custom_field_order" class="form-control"
                       value="<?php echo $this->mdl_custom_fields->form_value('custom_field_order', true); ?>">
            </div>

            <div class="form-group">
                <label for="custom_field_location"><?php _trans('position'); ?></label>

                <?php
                $positions = array(
                    'client' => Mdl_client_custom::$positions,
                    'invoice' => Mdl_invoice_custom::$positions,
                    'payment' => Mdl_payment_custom::$positions,
                    'quote' => Mdl_quote_custom::$positions,
                    'user' => Mdl_user_custom::$positions,
                );

                foreach ($positions as $key => $val) {
                    foreach ($val as $key2 => $val2) {
                        $val[$key2] = trans($val2);
                    }
                    $positions[$key] = $val;
                }
                ?>
                <script>
                    var jsonPositions = '<?php echo json_encode($positions); ?>';
                    jsonPositions = JSON.parse(jsonPositions);
                </script>
                <?php $valueSelected = $this->mdl_custom_fields->form_value('custom_field_location'); ?>
                <select name="custom_field_location" id="custom_field_location"
                        class="form-control simple-select"></select>
            </div>

        </div>

    </div>

</form>

<script>
    $(function () {
        function updatePositions(index, selKey) {
            $("#custom_field_location option").remove();
            var key = Object.keys(jsonPositions)[index];
            for (pos in jsonPositions[key]) {
                var opt = $("<option>");
                opt.attr("value", pos);
                opt.text(jsonPositions[key][pos]);
                if (selKey == pos) {
                    opt.attr("selected", "selected");
                }
                $("#custom_field_location").append(opt);
            }
        }

        var optionIndex = $("#custom_field_table option:selected").index();

        $("#custom_field_table").on("change", function () {
            optionIndex = $("#custom_field_table option:selected").index();
            updatePositions(optionIndex);
        });

        updatePositions(optionIndex, <?php echo $valueSelected; ?>);
    });
</script>
