<?php
// Determine for who is (Replace $) (type) & (who) _id system
$who     = empty($user_id) ? 'client' : 'user'; // Basic test
$type    = empty($quote_id) ? 'invoice' : 'quote'; // Basic test
$who_id  = $who . '_id'; // Add *_id user/client
$type_id = $type . '_id'; // Add *_id quote/invoice
$type_id = $this->input->post($type_id) ?: false; // Type exist? get id
if ( ! $type_id) {
    return; // No quote/invoice id do nothing
}
$permissive = get_setting('enable_permissive_search_' . $who . 's');
?>
<script>
    $(function () {
        // Display user change for quote or invoice modal
        $('#change-<?php echo $who; ?>').modal('show');

        <?php $this->layout->load_view($who . 's/script_select2_' . $who . '_id.js'); ?>

        // Change the user or client
        $('#<?php echo $who; ?>_change_confirm').click(function () {
            // Show loader
            show_loader();

            // Posts the data to validate
            $.post("<?php echo site_url($type . 's/ajax/change_' . $who); ?>", {
                    <?php echo $who; ?>_id: $('#change_<?php echo $who; ?>_id').val(),
                    <?php echo $type; ?>_id: $('#<?php echo $type; ?>_id').val()
                },
                function (data) {
                    var response = json_parse(data, <?php echo (int) IP_DEBUG; ?>);
                    if (response.success === 1) {
                        // The validation was successful and quote/invoice was Updated
                        window.location = "<?php echo site_url($type . 's/view'); ?>/" + response.<?php echo $type; ?>_id;
                    }
                    else {
                        // The validation was not successful
                        $('.control-group').removeClass('has-error');
                        for (var key in response.validation_errors) {
                            $('#' + key).parent().parent().addClass('has-error');
                        }
                    }
                }
            );
        });
    });
</script>

<div id="change-<?php echo $who; ?>" class="modal modal-lg" role="dialog" aria-labelledby="modal_change_<?php echo $who; ?>" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title"><?php _trans('change_' . $who); ?></h4>
        </div>
        <div class="modal-body">
            <div class="form-group has-feedback">
                <label for="change_<?php echo $who; ?>_id"><?php _trans($who); ?></label>
                <div class="input-group">
                    <span id="toggle_permissive_search_<?php echo $who; ?>s" class="input-group-addon"
                          title="<?php _trans('enable_permissive_search_' . $who . 's'); ?>" style="cursor:pointer;">
                        <i class="fa fa-toggle-<?php echo $permissive ? 'on' : 'off' ?> fa-fw"></i>
                    </span>
                    <select name="<?php echo $who; ?>_id" id="change_<?php echo $who; ?>_id" class="<?php echo $who; ?>-id-select form-control"
                            autofocus="autofocus" required>
<?php
$who_id = ${$who}->{$who_id} ?? $this->input->post($who_id);

if ($who_id) {
    $format = 'format_' . $who; // func name
    $name   = $who . '_name'; // user or client property
    $name   = empty(${$who}->{$name}) ? $format($who_id) : $user->{$name}
?>
                        <option value="<?php echo $who_id; ?>"><?php _htmlsc($name); ?></option>
<?php
}
?>
                    </select>
                </div>
            </div>

            <input class="hidden" id="<?php echo $type; ?>_id" value="<?php echo $type_id; ?>">
            <input class="hidden" id="input_permissive_search_<?php echo $who; ?>s" value="<?php echo $permissive; ?>">
        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-success ajax_loader" id="<?php echo $who; ?>_change_confirm" type="button">
                    <i class="fa fa-check"></i> <?php _trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
                </button>
            </div>
        </div>

    </form>
</div>
