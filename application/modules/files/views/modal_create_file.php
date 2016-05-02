<script type="text/javascript">
    $(function () {
        // Display the create quote modal
        $('#create-file').modal('show');

        $('#create-file').on('shown', function () {
            $("#client_name").focus();
        });

        $().ready(function () {
            $("[name='client_name']").select2();
            $("#client_name").focus();
        });

        // Creates the quote
        $('#file_create_confirm').on('click', function () {
            console.log('clicked');
            // Posts the data to validate and create the quote;
            // will create the new client if necessary

            var form = $('#create-file-form')[0];
            var formData = new FormData(form);
            var file = $('input[type=file]')[0].files[0];

            if (file === undefined ) {
                bootbox.alert('Please select a file');
                return;
            }

            formData.append('file', file);

            $.ajax({
                url: "<?php echo site_url('files/ajax/create'); ?>",
                data: formData,
                type: "POST",
                processData: false,
                contentType: false,
                cache: false,
                success: function (data) {
                    <?php echo (IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);
                    if (response.success == '1') {
                        // The validation was successful and quote was created
                        window.location = "<?php echo site_url('files/'); ?>";
                    }
                    else {
                        // The validation was not successful
                        $('.control-group').removeClass('has-error');
                        if(response.validation_errors) {
                            bootbox.alert(response.validation_errors);

                            return;
                        }
                        $('#create-file').hidden();
                    }
                }
            });
        });
    });
</script>

<div id="create-file" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
     role="dialog" aria-labelledby="modal_create_file" aria-hidden="true">
    <form class="modal-content" id="create-file-form">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3><?php echo lang('create_file'); ?></h3>
        </div>
        <div class="modal-body">

            <div class="form-group">
                <label for="file"><?php echo lang('select-file'); ?>: </label>

                <div class="controls">
                    <input type="file" id="file" name="file">
                </div>
            </div>

            <div class="form-group">
                <label for="client_name"><?php echo lang('client'); ?></label>
                <select name="client_id" id="client_id" class="form-control" autofocus="autofocus">
                    <?php
                    foreach ($clients as $client){
                        echo "<option value='".htmlentities($client->client_id)."' ";
                        if ($client_name == $client->client_name) echo 'selected';
                        echo ">".htmlentities($client->client_name)."</option>";
                    }
                    ?>
                </select>
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php echo lang('cancel'); ?>
                </button>
                <button class="btn btn-success ajax-loader" id="file_create_confirm" type="button">
                    <i class="fa fa-check"></i> <?php echo lang('submit'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
