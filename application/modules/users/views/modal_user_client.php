<script type="text/javascript">
    $(function () {
        $('#btn_user_client').click(function () {

            $.post("<?php echo site_url('users/ajax/save_user_client'); ?>", {
                user_id: '<?php echo $id; ?>',
                client_name: $('#client_name').val()
            }, function (data) {
                <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                $('#div_user_client_table').load('<?php echo site_url('users/ajax/load_user_client_table'); ?>', {user_id: '<?php echo $id; ?>'});
            });

        });
    });

</script>

<div id="add-user-client" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
     role="dialog" aria-labelledby="modal_add_user_client" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3><?php echo trans('add_client'); ?></h3>
        </div>
        <div class="modal-body">

            <div class="form-group">
                <label for="client_name"><?php echo trans('client'); ?></label>
                <select name="client_name" id="client_name" class="form-control" autofocus="autofocus">
                    <?php
                    foreach ($clients as $client) {
                        echo "<option value=\"" . htmlspecialchars($client->client_name) . "\" ";
                        echo ">" . htmlspecialchars($client->client_name) . "</option>";
                    }
                    ?>
                </select>
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php echo trans('cancel'); ?>
                </button>
                <button class="btn btn-success" id="btn_user_client" type="button">
                    <i class="fa fa-check"></i> <?php echo trans('submit'); ?>
                </button>
            </div>
        </div>

    </form>

</div>