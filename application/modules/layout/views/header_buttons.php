<div class="headerbar-item pull-right">
    <div class="btn-group btn-group-sm">
        <?php if (!isset($hide_submit_button)) : ?>
            <button id="btn-submit" name="btn_submit" class="btn btn-success ajax-loader" value="1">
                <i class="fa fa-check"></i> <?php _trans('save'); ?>
            </button>
        <?php endif; ?>
        <?php if (!isset($hide_cancel_button)) : ?>
            <button id="btn-cancel" name="btn_cancel" class="btn btn-danger" value="1">
                <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
            </button>
        <?php endif; ?>
    </div>
</div>
