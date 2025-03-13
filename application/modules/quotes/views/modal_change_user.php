<script>
    $(function () {
        // Display change quote modal
        $('#change-user').modal('show');

        <?php $this->layout->load_view('users/script_select2_user_id.js'); ?>

        // Change the user
        $('#user_change_confirm').click(function () {
            // Posts the data to validate
            $.post("<?php echo site_url('quotes/ajax/change_user'); ?>", {
                    user_id: $('#change_user_id').val(),
                    quote_id: $('#quote_id').val()
                },
                function (data) {
                    <?php echo (IP_DEBUG ? 'console.log(data);' : '') . PHP_EOL; ?>
                    var response = JSON.parse(data);
                    if (response.success === 1) {
                        // The validation was successful and quote was Updated
                        window.location = "<?php echo site_url('quotes/view'); ?>/" + response.quote_id;
                    }
                    else {
                        // The validation was not successful
                        $('.control-group').removeClass('has-error');
                        for (var key in response.validation_errors) {
                            $('#' + key).parent().parent().addClass('has-error');
                        }
                    }
                });
        });
    });
</script>

<div id="change-user" class="modal modal-lg" role="dialog" aria-labelledby="modal_change_user" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title"><?php _trans('change_user'); ?></h4>
        </div>
        <div class="modal-body">

            <input class="hidden" id="quote_id" value="<?php echo $quote_id; ?>">
            <input class="hidden" id="input_permissive_search_users"
                   value="<?php echo get_setting('enable_permissive_search_users'); ?>">

            <div class="form-group has-feedback">
                <label for="change_user_id"><?php _trans('user'); ?></label>
                <div class="input-group">
                    <select name="user_id" id="change_user_id" class="user-id-select form-control"
                            autofocus="autofocus" required>
<?php
$user_id = isset($user->user_id) ? $user->user_id : $this->input->post('user_id');
if ($user_id)
{
?>
                            <option value="<?php echo $user_id; ?>"><?php _htmlsc(empty($user->user_name) ? format_user($user_id) : $user->user_name); ?></option>
<?php
}
?>
                    </select>
                    <span id="toggle_permissive_search_users" class="input-group-addon"
                          title="<?php _trans('enable_permissive_search_users'); ?>" style="cursor:pointer;">
                        <i class="fa fa-toggle-<?php echo get_setting('enable_permissive_search_users') ? 'on' : 'off' ?> fa-fw"></i>
                    </span>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-success" id="user_change_confirm" type="button">
                    <i class="fa fa-check"></i> <?php _trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
                </button>
            </div>
        </div>

    </form>
</div>
