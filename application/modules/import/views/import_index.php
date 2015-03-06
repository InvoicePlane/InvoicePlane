<div class="headerbar">
    <h1><?php echo lang('import_data'); ?></h1>
</div>

<div class="content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div class="panel panel-default">

        <div class="panel-heading">
            <h5><?php echo lang('import_from_csv'); ?></h5>
        </div>

        <div class="panel-body">
            <form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">
                <?php foreach ($files as $file) { ?>
                    <div class="form-group">
                        <div class="controls">
                            <label class="checkbox">
                                <input type="checkbox" name="files[]" class="form-control"
                                       value="<?php echo $file; ?>"> <?php echo $file; ?>
                            </label>
                        </div>
                    </div>
                <?php } ?>
                <div class="form-group">
                    <div class="controls">
                        <input type="submit" class="btn btn-default" name="btn_submit"
                               value="<?php echo lang('import'); ?>">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>