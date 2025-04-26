<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">

            <?php _csrf_field(); ?>

            <legend><?php _trans('setup_choose_language'); ?></legend>

            <p><?php _trans('setup_choose_language_message'); ?></p>

            <select name="ip_lang" class="form-control simple-select">
<?php
foreach ($languages as $language) {
?>
                <option value="<?php echo $language; ?>"<?php echo $language == 'english' ? ' selected="selected"' : '' ;?>><?php
                    echo ucfirst(str_replace('/', '', $language));
                ?></option>
<?php
}
?>
            </select>

            <br/>

            <input class="btn btn-success" type="submit" name="btn_continue" value="<?php _trans('continue'); ?>">

        </form>

    </div>
</div>
