<script>
    $(function () {
        $('#clicksend_check_api_cred').click(function () {
            let $btn = $(this);
            let btntext = "<?php _trans('check_api_credentials'); ?>";
            $.post("<?php echo site_url('clicksend/ajax/check_api_credentials'); ?>", {api_username: $("#clicksend_api_username").val(), api_key: $("#clicksend_api_key").val()}, function (data) {
                $(".fullpage-loader-close").click();
                if(data.success){
                    $btn.html(btntext+" <i class=\"fa fa-check\"></i>").removeClass("btn-primary").addClass("btn-success");
                }else{
                    $btn.html(btntext+" <i class=\"fa fa-times\"></i>").removeClass("btn-primary").addClass("btn-danger");
                }
                window.setTimeout(function () {
                    $btn.html(btntext).removeClass("btn-success btn-danger").addClass("btn-primary");
                }, 2000);
            });
        });
    });
</script>

<div class="row">
    <div class="col-xs-12 col-md-8 col-md-offset-2">

        <div class="panel panel-default">
            <div class="panel-heading">
                <?php _trans('Clicksend'); ?>
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-xs-12 col-md-6">

                    <div class="form-group">
                            <label for="clicksend_api_username">
                                <?php _trans('clicksend_api_username'); ?>
                            </label>
                            <input type="text" name="settings[clicksend_api_username]" id="clicksend_api_username"
                                class="form-control"
                                value="<?php echo get_setting('clicksend_api_username', '', true); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="clicksend_api_key">
                                <?php _trans('clicksend_api_key'); ?>
                            </label>
                            <input type="text" name="settings[clicksend_api_key]" id="clicksend_api_key"
                                class="form-control"
                                value="<?php echo get_setting('clicksend_api_key', '', true); ?>">
                        </div>
                        
                        <div class="form-group">
                            <btn class="btn btn-primary ajax-loader" id="clicksend_check_api_cred"><?php _trans('check_api_credentials'); ?></btn>
                        </div>
                        
                        <div class="form-group">
                            <label for="clicksend_return_address_id">
                                <?php _trans('clicksend_return_address_id'); ?>
                            </label>
                            <input type="text" name="settings[clicksend_return_address_id]" id="clicksend_return_address_id"
                                class="form-control"
                                value="<?php echo get_setting('clicksend_return_address_id', '', true); ?>">
                            <small><?php _trans('clicksend_return_address_id_info'); ?></small>
                        </div>

                        <div class="form-group">
                            <label for="letter_standard_duplex">
                                <?php _trans('letter_standard_duplex'); ?>
                            </label>
                            <select name="settings[letter_standard_duplex]" id="letter_standard_duplex"
                                class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                <option value="0"><?php _trans('no'); ?></option>
                                <option value="1" <?php check_select(get_setting('letter_standard_duplex', true), '1'); ?>>
                                    <?php _trans('yes'); ?>
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="letter_standard_color">
                                <?php _trans('letter_standard_color'); ?>
                            </label>
                            <select name="settings[letter_standard_color]" id="letter_standard_color"
                                class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                <option value="0"><?php _trans('no'); ?></option>
                                <option value="1" <?php check_select(get_setting('letter_standard_color'), '1'); ?>>
                                    <?php _trans('yes'); ?>
                                </option>
                            </select>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
