<script src="<?php echo base_url(); ?>assets/core/js/zxcvbn.js"></script>

<form method="post">

    <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('change_password'); ?></h1>
        <?php echo $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">

                <?php $this->layout->load_view('layout/alerts'); ?>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php _trans('change_password'); ?>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="user_password">
                                <?php _trans('password'); ?>
                            </label>
                            <input type="password" name="user_password" id="user_password"
                                   class="form-control passwordmeter-input">
                            <div class="progress" style="height:3px;">
                                <div class="progress-bar progress-bar-danger passmeter passmeter-1"
                                     style="width: 33%"></div>
                                <div class="progress-bar progress-bar-warning passmeter passmeter-2"
                                     style="display: none; width: 33%"></div>
                                <div class="progress-bar progress-bar-success passmeter passmeter-3"
                                     style="display: none; width: 34%"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="user_passwordv">
                                <?php _trans('verify_password'); ?>
                            </label>
                            <input type="password" name="user_passwordv" id="user_passwordv"
                                   class="form-control">
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>

</form>
