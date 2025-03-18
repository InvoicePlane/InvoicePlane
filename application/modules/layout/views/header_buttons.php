<div class="headerbar-item pull-right">
    <div class="btn-group btn-group-sm">
<?php
if ( ! isset($hide_submit_button))
{
?>
        <button id="btn-submit" name="btn_submit" class="btn btn-success ajax-loader" value="1">
            <i class="fa fa-check"></i> <?php _trans('save'); ?>
        </button>
<?php
}
if ( ! isset($hide_cancel_button))
{
    $attribute_cancel = empty($attribute_cancel) ? 'onclick="window.history.back()"' : $attribute_cancel;
?>
        <button type="button" <?php echo $attribute_cancel; ?> id="btn-cancel" name="btn_cancel" class="btn btn-danger ajax-loader" value="1">
            <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
        </button>
<?php
}
?>
    </div>
</div>
