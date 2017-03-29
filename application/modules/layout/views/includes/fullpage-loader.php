<div id="fullpage-loader" style="display: non">
    <div class="loader-content">
        <i id="loader-icon" class="fa fa-cog fa-spin"></i>
        <div id="loader-error" style="display: none">
            <?php echo trans('loading_error'); ?><br/>
            <a href="https://wiki.invoiceplane.com/<?php echo trans('cldr'); ?>/1.0/general/faq"
               class="btn btn-primary btn-sm" target="_blank">
                <i class="fa fa-support"></i> <?php echo trans('loading_error_help'); ?>
            </a>
        </div>
    </div>
    <div class="text-right">
        <button type="button" class="fullpage-loader-close btn btn-link tip" aria-label="<?php echo trans('close'); ?>"
                title="<?php echo trans('close'); ?>" data-placement="left">
            <span aria-hidden="true"><i class="fa fa-close"></i></span>
        </button>
    </div>
</div>
