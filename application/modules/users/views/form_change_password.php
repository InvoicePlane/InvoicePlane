<form method="post" class="form-horizontal">

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
                                   class="form-control">
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
