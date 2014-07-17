<div class="container">
    <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
        <div class="install-panel">

            <h1><span>InvoicePlane</span></h1>

            <form method="post"
                  action="<?php echo site_url($this->uri->uri_string()); ?>">

                <legend><?php echo lang('setup_choose_language'); ?></legend>

                <p><?php echo lang('setup_choose_language_message'); ?></p>

                <select name="ip_lang" class="form-control">
                    <?php foreach ($languages as $language) { ?>
                    <option value="<?php echo $language; ?>" <?php if ($language == /*'english'*/'italian'/*---it---*/) { ?>selected="selected"<?php } ?>><?php echo ucfirst($language); ?></option>
                    <?php } ?>
                </select>

                <br/>

                <input class="btn btn-success" type="submit" name="btn_continue" value="<?php echo lang('continue'); ?>">

            </form>

        </div>
    </div>
</div>

