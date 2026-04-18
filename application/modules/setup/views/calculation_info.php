<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">

            <?php _csrf_field(); ?>

            <h2><?php _trans('setup_calculation_info'); ?></h2>

            <p>
                <?php _trans('setup_calculation_info_message'); ?>
            </p>

            <p class="alert alert-warning">
                <?php _trans('setup_calculation_info_note'); ?>
            </p>

            <input type="submit" class="btn btn-success" name="btn_agree"
                   value="<?php _trans('setup_calculation_info_btn_agree'); ?>">

            <input type="submit" class="btn btn-warning" name="btn_continue"
                   value="<?php _trans('setup_calculation_info_btn_disagree'); ?>">

        </form>
    </div>
</div>
