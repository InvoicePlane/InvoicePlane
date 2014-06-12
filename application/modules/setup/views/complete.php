<script type="text/javascript">

    $(function(){
        $('#aftersetup_deny').click(function(){
            window.location = '<?php echo site_url('sessions/login'); ?>';
        });
        $('#aftersetup_agree').click(function(){
            $('#aftersetup_agree').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
            window.setTimeout(function(){
                window.location = '<?php echo site_url('setup/ping'); ?>';
            },2000);

        });
    });

</script>

<div class="container">

    <?php
    if ( $update == false ) {
    ?>

    <div id="modal-placeholder">
        <div id="after-setup"
             class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
             role="dialog" aria-labelledby="modal_after_setup" aria-hidden="true"
            style="overflow: auto; margin-top: 50px;">
            <div class="modal-content">
                <div class="modal-header">
                    <a data-dismiss="modal" class="close">×</a>
                    <h3><?php echo lang('aftersetup_heading'); ?></h3>
                </div>
                <div class="modal-body">

                    <p class="alert alert-info">
                        <?php echo lang('aftersetup_question'); ?>
                        <br/>
                        <small class="text-muted">
                            <?php echo lang('aftersetup_hint'); ?>
                        </small>
                    </p>

                </div>
                <div class="modal-footer">
                    <div class="btn-group">
                        <button class="btn btn-danger" id="aftersetup_deny" type="button">
                            <i class="fa fa-times"></i> <?php echo lang('aftersetup_no_thanks'); ?>
                        </button>
                        <button class="btn btn-success" id="aftersetup_agree" type="button">
                            <i class="fa fa-check"></i> <?php echo lang('yes'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

    <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
        <div class="install-panel">

            <h1><span>InvoicePlane</span></h1>

            <h2><?php echo lang('setup_complete'); ?></h2>

            <p>
                <span class="label label-success">
                    <?php echo lang('success'); ?>
                </span>&nbsp;
                <?php echo lang('setup_complete_message'); ?>
            </p>

            <?php if ( $update == false ) { ?>
                <a href="#after-setup" class="btn btn-success" data-toggle="modal">
                    <i class="fa fa-check fa-margin"></i> <?php echo lang('continue'); ?>
                </a>
            <?php } else { ?>
                <a href="<?php echo site_url('sessions/login'); ?>" class="btn btn-success" >
                    <i class="fa fa-check fa-margin"></i> <?php echo lang('continue'); ?>
                </a>
            <?php } ?>
        </div>
    </div>
</div>