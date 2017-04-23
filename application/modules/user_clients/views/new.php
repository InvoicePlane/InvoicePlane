<form method="post">

    <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

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

</form>
