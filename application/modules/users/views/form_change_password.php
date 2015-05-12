<form method="post" class="form-horizontal">

    <div id="headerbar">
        <h1><?php echo lang('change_password'); ?></h1>
        <?php echo $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <fieldset>
            <legend><?php echo lang('change_password'); ?></legend>

            <div class="form-group">
                <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                    <label class="control-label"><?php echo lang('password'); ?>: </label>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <input type="password" name="user_password" id="user_password"
                           class="form-control">
                </div>
            </div>

            <div class="form-group">
                <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                    <label class="control-label">
                        <?php echo lang('verify_password'); ?>
                    </label>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <input type="password" name="user_passwordv" id="user_passwordv"
                           class="form-control">
                </div>
            </div>

        </fieldset>

    </div>

</form>