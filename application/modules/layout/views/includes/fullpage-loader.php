<div id="fullpage-loader" style="display: none">
    <div class="loader-content">
        <i id="loader-icon" class="fa fa-cog fa-spin"></i>
        <img id="loader-error-icon" src="<?php echo base_url('assets/core/img/loader-error.svg'); ?>" 
             alt="Error">
        <div id="loader-error" class="loader-error-message" style="display: none">
            <div class="alert alert-danger">
                <strong><i class="fa fa-exclamation-triangle"></i> <?php _trans('loading_error'); ?></strong>
                <div class="loader-error-actions">
                    <a href="https://wiki.invoiceplane.com/<?php _trans('cldr'); ?>/1.0/general/faq"
                       class="btn btn-primary btn-sm" target="_blank">
                        <i class="fa fa-support"></i> <?php _trans('loading_error_help'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="text-right">
        <button type="button" class="fullpage-loader-close btn btn-link tip" aria-label="<?php _trans('close'); ?>"
                title="<?php _trans('close'); ?>" data-placement="left">
            <span aria-hidden="true"><i class="fa fa-close"></i></span>
        </button>
    </div>
</div>
