<script>
    // Update check
    $(function () {
        // function to check if update excists for a version if atm on currend version
        function update(currend, checked) {
            // Get the current version
            var curr = currend;
            // set the version to check to the passed variable
            var check = checked;
            if (curr === check) {
               return 0;
            }
            var curr_components = curr.split(".");
            var check_components = check.split(".");
            
            var len = Math.min(curr_components.length, check_components.length);
            for (var i = 0; i < len; i++) {
                // curr bigger than check
                if (parseInt(curr_components[i]) > parseInt(check_components[i])) {
                    return 0;
                }
                // check bigger than curr
                if (parseInt(curr_components[i]) < parseInt(check_components[i])) {
                    return 1;
                }
            }
            if (curr_components.length > check_components.length) {
                return 0;
            }
            if (curr_components.length < check_components.length) {
                return 1;
            }
            return 0;
        }
        var checktime = 2000;
        // Get the current version
        var ip_version = "<?php echo get_setting('current_version'); ?>";
        // Get the latest version from the InvoicePlane IDS
        $.ajax({
            'url': 'https://ids.invoiceplane.com/updatecheck?cv=' + ip_version,
            'dataType': 'json',
            success: function(data) {
                <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                var updatecheck = data.current_version;
                // Compare each versions and replace the placeholder with a download button
                // or info label after 2 seconds
                setTimeout(function() {
                    if (update(ip_version, updatecheck)) {
                        $('#updatecheck-updates-available').attr("href", "https://www.invoiceplane.com/downloads")
                        $('#updatecheck-loading').addClass('hidden');
                        $('#updatecheck-updates-available').removeClass('hidden');
                    }
                    else {
                        $('#updatecheck-loading').addClass('hidden');
                        $('#updatecheck-no-updates').removeClass('hidden');
                    }
                }, checktime);
            },
            error: function(data) {
                $.ajax({
                    'url': 'https://ids.invoiceplane.org/updatecheck?cv=' + ip_version,
                    'dataType': 'json',
                    success: function(data) {
                        <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                        var updatecheck = data.current_version;
                        // Compare each versions and replace the placeholder with a download button
                        // or info label after 2 seconds
                        setTimeout(function() {
                            if (update(ip_version, updatecheck)) {
                                $('#updatecheck-updates-available').attr("href", "https://www.invoiceplane.org/downloads")
                                $('#updatecheck-loading').addClass('hidden');
                                $('#updatecheck-updates-available').removeClass('hidden');
                            }
                            else {
                                $('#updatecheck-loading').addClass('hidden');
                                $('#updatecheck-no-updates').removeClass('hidden');
                            }
                        }, checktime);
                    },
                    error: function(data) {
                        <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                        $('#updatecheck-loading').addClass('hidden');
                        $('#updatecheck-failed').removeClass('hidden');
                    },
                });
            },
        });
        // Get the latest news
        $.ajax({
            'url': 'https://ids.invoiceplane.com/get_news',
            'dataType': 'json',
            'success': function(data) {
                <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                setTimeout(function() {
                    $('#ipnews-loading').addClass('hidden');
                    data.forEach(function(news) {
                        var ipnews = '<div class="alert alert-' + news.type + '">';
                        ipnews += '<b>' + news.title + '</b><br/>';
                        ipnews += news.text + '<br/>';
                        if(news.newsdate.date) ipnews += '<small><?php echo trans('date')?>: ' + news.newsdate.date.substr(0, 11) + '</b><br/>';
                        ipnews += '</div>';
                        ipnews = ipnews.replace(/\n/g, "<br />");
                        $('#ipnews-container').append(ipnews);
                    });
                }, checktime);
            },
            'error': function(data) {
                $.ajax({
                    'url': 'https://ids.invoiceplane.org/get_news',
                    'dataType': 'json',
                    'success': function(data) {
                        <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                        setTimeout(function() {
                            $('#ipnews-loading').addClass('hidden');
                            data.forEach(function(news) {
                                var ipnews = '<div class="alert alert-' + news.type + '">';
                                ipnews += '<b>' + news.title + '</b><br/>';
                                ipnews += news.text + '<br/>';
                                if(news.newsdate.date) ipnews += '<small><?php echo trans('date')?>: ' + news.newsdate.date.substr(0, 11) + '</b><br/>';
                                ipnews += '</div>';
                                ipnews = ipnews.replace(/\n/g, "<br />");
                                $('#ipnews-container').append(ipnews);
                            });
                        }, checktime);
                    },
                    'error': function(data) {
                        <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                        $('#ipnews-loading').addClass('hidden');
                        $('#ipnews-failed').removeClass('hidden');
                    },
                });
            },
        });
    });
</script>

<div class="col-xs-12 col-md-8 col-md-offset-2">

    <div class="panel panel-default">
        <div class="panel-heading">
            <?php _trans('updatecheck'); ?>
        </div>
        <div class="panel-body">

            <div class="form-group">
                <input type="text" class="form-control" value="<?php echo get_setting('current_version'); ?>" readonly="readonly">
            </div>
            <div id="updatecheck-results">
                <div id="updatecheck-loading" class="btn btn-default btn-sm disabled">
                    <i class="fa fa-circle-o-notch fa-spin"></i> <?php _trans('checking_for_updates'); ?>
                </div>

                <div id="updatecheck-no-updates" class="btn btn-default btn-sm disabled hidden">
                    <?php _trans('no_updates_available'); ?>
                </div>

                <div id="updatecheck-failed" class="btn btn-danger btn-sm disabled hidden">
                    <?php _trans('updatecheck_failed'); ?>
                </div>

                <a href="" id="updatecheck-updates-available" class="btn btn-success btn-sm hidden" target="_blank">
                    <?php _trans('updates_available'); ?>
                </a>
            </div>

        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <?php _trans('invoiceplane_news'); ?>
        </div>
        <div class="panel-body">

            <div id="ipnews-results">
                <div id="ipnews-loading" class="btn btn-default btn-sm disabled">
                    <i class="fa fa-circle-o-notch fa-spin"></i> <?php _trans('checking_for_news'); ?>
                </div>

                <div id="ipnews-container"></div>
            </div>

        </div>
    </div>

</div>
