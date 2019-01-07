<script>
    $(function () {
        $('#user_all_clients').click(function () {
            all_client_check();
        });
        
        function all_client_check() {
            if ($('#user_all_clients').is(':checked')) {
                $('#list_client').hide();
            } else {
                $('#list_client').show();
            }
        }
        
        all_client_check();
    });
</script>

<form method="post">

    <input type="hidden" name="<?php echo $this->config->item('csrf_token_name'); ?>"
           value="<?php echo $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('assign_client'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">

                <?php $this->layout->load_view('layout/alerts'); ?>

                <input type="hidden" name="user_id" id="user_id"
                       value="<?php echo $user->user_id ?>">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php _htmlsc($user->user_name) ?>
                    </div>
                    <div class="panel-body">
                    
                        <div class="alert alert-info">
                            <label>
                                <input type="checkbox" name="user_all_clients" id="user_all_clients" value="1" <?php echo ($user->user_all_clients)?'checked="checked"':''; ?>> <?php _trans('user_all_clients') ?>
                            </label>
                                
                            <div>
                                <?php _trans('user_all_clients_text') ?>
                            </div>
                        </div>
                        
                        <div id="list_client">
                            <label for="client_id"><?php _trans('client'); ?></label>
                            <select name="client_id" id="client_id" class="form-control simple-select"
                                    autofocus="autofocus">
                                <?php
                                foreach ($clients as $client) {
                                    echo '<option value="' . $client->client_id . '">';
                                    echo htmlsc(format_client($client)) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        

                    </div>
                </div>

            </div>
        </div>

    </div>

</form>