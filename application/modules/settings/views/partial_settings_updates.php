<script type="text/javascript">
    // Update check
    window.onload = function () {
        var checktime = 2000;

        // Get the current version
        var current_version = "<?php echo $current_version; ?>";
        current_version = current_version.replace(/\./g, ''); // Remove the dots from the version

        // Get the latest version from updates.invoiceplane.com
        $.getJSON("https://ids.invoiceplane.com/updatecheck", function (data) {

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
        }).error(function () {
            $('#updatecheck-loading').addClass('hidden');
            $('#updatecheck-failed').removeClass('hidden');
        });

        // Get the latest news
        $.getJSON("https://ids.invoiceplane.com/get_news", function (data) {
            //console.log(data);
            setTimeout(function () {
                $('#ipnews-loading').addClass('hidden');
                data.forEach(function (news) {
                    var ipnews = '<div class="alert alert-' + news.type + '">';
                    ipnews += '<b>' + news.title + '</b><br/>';
                    ipnews += news.text + '<br/>';
                    ipnews += '<small><?php echo lang('date')?>: ' + news.newsdate.date.substr(0, 11) + '</b><br/>';
                    ipnews += '</div>';
                    $('#ipnews-container').append(ipnews);
                })
            }, checktime);
        }).error(function () {
            $('#ipnews-loading').addClass('hidden');
            $('#ipnews-failed').removeClass('hidden');
        });
    };

</script>

<div class="tab-info">

    <h4><?php echo lang('updatecheck'); ?></h4><br/>

    <div class="form-group">
        <input type="text" class="input-sm form-control"
               value="<?php echo $current_version; ?>" readonly="readonly">
    </div>
    <div id="updatecheck-results">
        <span id="updatecheck-loading" class="btn btn-default btn-sm disabled">
            <i class="fa fa-circle-o-notch fa-spin"></i>  <?php echo lang('checking_for_updates'); ?>
		</span>

        <span id="updatecheck-no-updates" class="btn btn-default btn-sm disabled hidden">
            <?php echo lang('no_updates_available'); ?>
        </span>

        <span id="updatecheck-failed" class="btn btn-danger btn-sm disabled hidden">
            <?php echo lang('updatecheck_failed'); ?>
        </span>

        <a href="https://invoiceplane.com/downloads" id="updatecheck-updates-available"
           class="btn btn-success btn-sm hidden" target="_blank">
            <?php echo lang('updates_available'); ?>
        </a>
    </div>

    <hr/>

    <h4><?php echo lang('invoiceplane_news'); ?></h4>

    <div id="ipnews-results">
        <span id="ipnews-loading" class="btn btn-default btn-sm disabled">
            <i class="fa fa-circle-o-notch fa-spin"></i>  <?php echo lang('checking_for_news'); ?>
		</span>

        <div id="ipnews-container"></div>
    </div>
</div>