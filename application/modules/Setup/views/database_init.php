<h2><?php _trans('setup_db_database_init'); ?></h2>

<p class="mt-5 mb-5"><?php _trans('setup_db_database_init_info'); ?></p>

<form action="/setup/database-init/" method="post">

    <?php _csrf_field(); ?>

    <button class="btn btn-primary" type="submit" name="btn_continue" value="1">
        <?php _trans('continue'); ?>
    </button>

</form>
