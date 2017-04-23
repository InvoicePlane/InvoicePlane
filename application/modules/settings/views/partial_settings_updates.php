<script>
    // Update check
    window.onload = function () {
        var checktime = 2000;

        // Get the current version
        var current_version = "<?php echo $current_version; ?>";
        current_version = current_version.replace(/\./g, ''); // Remove the dots from the version

        // Get the latest version from the InvoicePlane IDS
        $.getJSON("https://ids.invoiceplane.com/updatecheck", function (data) {
            <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
            var updatecheck = data.current_version.replace(/\./g, '');

            // Compare each versions and replace the placeholder with a download button
            // or info label after 2 seconds
            setTimeout(function () {
                if (current_version < updatecheck) {
                    $('#updatecheck-loading').addClass('hidden');
                    $('#updatecheck-updates-available').removeClass('hidden');
                } else {
                    $('#updatecheck-loading').addClass('hidden');
                    $('#updatecheck-no-updates').removeClass('hidden');
                }
            }, checktime);
        }).error(function (data) {
            <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
            $('#updatecheck-loading').addClass('hidden');
            $('#updatecheck-failed').removeClass('hidden');
        });

        // Get the latest news
        $.getJSON("https://ids.invoiceplane.com/get_news", function (data) {
            <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
            setTimeout(function () {
                $('#ipnews-loading').addClass('hidden');
                data.forEach(function (news) {
                    var ipnews = '<div class="alert alert-' + news.type + '">';
                    ipnews += '<b>' + news.title + '</b><br/>';
                    ipnews += news.text + '<br/>';
                    ipnews += '<small><?php echo trans('date')?>: ' + news.newsdate.date.substr(0, 11) + '</b><br/>';
                    ipnews += '</div>';
                    $('#ipnews-container').append(ipnews);
                })
            }, checktime);
        }).error(function (data) {
            <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
            $('#ipnews-loading').addClass('hidden');
            $('#ipnews-failed').removeClass('hidden');
        });
    };
</script>

<div class="col-xs-12 col-md-8 col-md-offset-2">

    <div class="panel panel-default">
        <div class="panel-heading">
            <?php _trans('updatecheck'); ?>
        </div>
        <div class="panel-body">

            <div class="form-group">
                <input type="text" class="form-control"
                       value="<?php echo $current_version; ?>" readonly="readonly">
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

                <a href="https://invoiceplane.com/downloads" id="updatecheck-updates-available"
                   class="btn btn-success btn-sm hidden" target="_blank">
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
