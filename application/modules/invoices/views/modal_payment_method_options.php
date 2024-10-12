<script>
    $(function () {
        // Select2 for all select inputs
        $(".simple-select").select2();

        <?php
        $keys_of_filtered_payment_method_types = [];

        foreach ($payment_method_types as $key => $element) {
            if (isset($element['class']) && !is_null($element['class'])) {
                $options = $element['class']->getInvoiceOptions();
                if (is_array($options) && !empty($options)) {
                    $keys_of_filtered_payment_method_types[] = '' . $key;
                }
            }
        }
        ?>

        const allowed_types = <?=json_encode($keys_of_filtered_payment_method_types)?>;
        const function_types = new Map();

        $('#payment_method_options_submit').click(function () {
            let type = $('#payment_method option:selected').val();
            function_types.get('payment_method_type_' + type)();
        });

        $('#payment_method_options').on('show.bs.modal', function () {
            let type = $('#payment_method option:selected').val();
            if (allowed_types.includes(type)) {
                $('#payment_method_' + type).removeClass('hidden');
                $('#payment_method_default').addClass('hidden');
                $('#payment_method_options_submit').removeClass('hidden');
            }
        });

        $('#payment_method_options').on('hide.bs.modal', function () {
            let type = $('#payment_method option:selected').val();
            if (allowed_types.includes(type)) {
                $('#payment_method_' + type).addClass('hidden');
                $('#payment_method_default').removeClass('hidden');
                $('#payment_method_options_submit').addClass('hidden');
            }
        });

        <?php
        foreach ($payment_method_types as $key => $type) {
            if (!isset($type['class']) || $type['class']->getInvoiceOptions() == []) {
                continue;
            }
        ?>
        function_types.set('<?="payment_method_type_". $key?>', () => {
            $.post("<?php echo site_url('invoices/ajax/save_payment_method_options_rate'); ?>", {
                    invoice_id: <?php echo $invoice_id; ?>,
                    'key': "<?php echo $key; ?>",
                    <?php
                    foreach ($type['class']->getInvoiceOptions() as $fieldK => $field) {
                        echo "'".$fieldK."': $('#".$fieldK."').val(),";
                    }
                    ?>
                },
                function (data) {
                    <?php if(IP_DEBUG) { echo 'console.log(data);'; } ?>
                    var response = JSON.parse(data);
                    if (response.success === 1) {
                        window.location = "<?php echo site_url('invoices/view/').$invoice_id; ?>";
                    }
                });
        });
        <?php } ?>
    });
</script>

<div id="payment_method_options" class="modal modal-lg" role="dialog" aria-labelledby="payment_method_options" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title"><?php _trans('payment_method_type_invoice_options'); ?></h4>
        </div>
        <div class="modal-body">
            <?php
            foreach ($payment_method_types as $key => $type) {
                if (!isset($type['class']) || $type['class']->getInvoiceOptions() == []) {
                    continue;
                }
                ?>
                <div id="payment_method_<?=$key?>" class="form-group hidden">
                    <?php foreach ($type['class']->getInvoiceOptions() as $fieldK => $field) { ?>
                        <div class="form-group">
                            <label for="<?=$fieldK?>">
                                <?=$field['label']?>
                            </label>
                            <input
                                type="<?=$field['type']?>"
                                name="<?=$fieldK?>"
                                id="<?=$fieldK?>"
                                class="form-control <?=isset($field['tags']) && $field['tags'] ? 'taggable' : ""?>"
                                value="<?php echo get_setting('payment_method_' . $key . '_' . $invoice_id . '_' . $fieldK, $field['default'] ?? ""); ?>"
                                <?=$field['required'] ? "required" : ""?>
                                <?php if ($invoice->is_read_only == 1) {
                                    echo 'disabled="disabled"';
                                } ?>>
                        </div>
                        <?php if (isset($field['tags']) && $field['tags']) { ?>
                            <!-- Assurez-vous de fermer la div form-group précédente si nécessaire -->
                            <div class="form-group">
                                <label for="settings[<?=$fieldK?>]">
                                    <?php _trans("payment_method_type_". $type['name'] . "_" . $fieldK . '_t'); ?>
                                </label>
                                <div>
                                    <?php $this->layout->load_view('email_templates/template-tags-invoices'); ?>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            <?php } ?>
            <div id="payment_method_default" class="form-group">
                <p style="color: red;"><?php _trans('payment_method_type_invoice_options_error'); ?></p>
            </div>
        </div>
        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-success hidden" id="payment_method_options_submit" type="button">
                    <i class="fa fa-check"></i> <?php _trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
                </button>
            </div>
        </div>
    </form>
</div>
