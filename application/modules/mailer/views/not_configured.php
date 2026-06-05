<form method="post" class="form-horizontal">
    <?php _csrf_field(); ?>

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('email_invoice'); ?></h1>
    </div>

    <div id="content">
        <div class="alert alert-warning"><?php _trans('email_not_configured'); ?></div>
    </div>

</form>
