<script type="text/javascript">
    // Update check
    window.onload = function () {
        var checktime = 2000;

        // Get the current version
        var current_version = "<?php echo $current_version; ?>";
        current_version = current_version.replace(/\./g, ''); // Remove the dots from the version

        // Get the latest version from updates.invoiceplane.com
        //$.getJSON("https://ids.invoiceplane.com/updatecheck", function (data) {	// ---it--- ORIGINALE
		//$.getJSON("http://127.0.0.1:8080/invoiceplane.it/updatecheck?callback=?", function (data) {		// ---it--- Check versione italiana DEBUG
		$.getJSON("https://api.github.com/repos/InvoicePlane-it/InvoicePlane/releases/latest?callback=?", function (response) {		// ---it--- Check versione italiana da GitHub
			
			var updatecheck = response.data.tag_name.replace(/\./g, '');		// ---it---
            //var updatecheck = data.current_version.replace(/\./g, '');	// ---it--- ORIGINALE
			
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

	<?php if (FALSE): // ---it--- ORIGINALE ?>
    <h4><?php echo lang('updatecheck'); ?></h4><br/>
	<?php else:	// ---it--- titolo modificato ?>
	<h4>Controllo aggiornamenti (edizione italiana)</h4><br/>
	<?php endif; ?>
	
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
		
		<?php if (FALSE):	// ---it--- ORIGINALE ?>
        <a href="https://invoiceplane.com/downloads" id="updatecheck-updates-available"
           class="btn btn-success btn-sm hidden" target="_blank">
            <?php echo lang('updates_available'); ?>
        </a>
        <?php else:			// ---it--- download da invoiceplane.it ?>
        <a href="http://invoiceplane.it/" id="updatecheck-updates-available"
           class="btn btn-success btn-sm hidden" target="_blank">
            <?php echo lang('updates_available'); ?>
        </a>
        <?php endif; ?>
    </div>

    <hr/>
	
	<?php if (FALSE): // ---it--- ORIGINALE ?>
    <h4><?php echo lang('invoiceplane_news'); ?></h4>
	<?php else: // ---it--- modifica titolo ?>
	<h4>InvoicePlane International News</h4>
	<?php endif; ?>
	
    <div id="ipnews-results">
        <span id="ipnews-loading" class="btn btn-default btn-sm disabled">
            <i class="fa fa-circle-o-notch fa-spin"></i>  <?php echo lang('checking_for_news'); ?>
		</span>

        <div id="ipnews-container"></div>
    </div>
</div>