<script>
    $(function () {
        var user_client_modal = $('#add-user-client');
        user_client_modal.modal('show');

        // Select2 for all select inputs
        $(".simple-select").select2();

        $('#btn_user_client').click(function () {
            $.post("<?php echo site_url('users/ajax/save_user_client'); ?>", {
                user_id: '<?php echo $user_id; ?>',
                client_id: $('#client_id').val()
            }, function (data) {
                <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                $('#div_user_client_table').load('<?php echo site_url('users/ajax/load_user_client_table'); ?>', {
                    user_id: '<?php echo $user_id; ?>'
                });

                user_client_modal.modal('hide');
                $('#modal-placeholder').text('');
            });
        });
    });
</script>

<div id="add-user-client" class="modal modal-lg" role="dialog" aria-labelledby="modal_add_user_client"
     aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title"><?php _trans('add_client'); ?></h4>
        </div>
        <div class="modal-body">

            <div class="form-group">
                <label for="client_id"><?php _trans('client'); ?></label>
                <select name="client_id" id="client_id" class="form-control simple-select" autofocus="autofocus">
                    <?php
                    foreach ($clients as $client) {
                        echo "<option value=\"" . $client->client_id . "\" ";
                        echo ">" . htmlsc(format_client($client)) . "</option>";
                    }
                    ?>
                </select>
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-success" id="btn_user_client" type="button">
                    <i class="fa fa-check"></i> <?php _trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
