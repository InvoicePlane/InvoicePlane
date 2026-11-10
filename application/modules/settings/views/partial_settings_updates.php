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
        // Upper bound for every release and IDS request: without it a stalled request never reaches its
        // error handler, so the fallback chain (and the failed state) is never shown.
        var ajaxTimeout = 8000;
        // Get the current version
        var ip_version = <?php echo json_encode((string) get_setting('current_version'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        // Legacy IDS update check, used when GitHub is unreachable or returns no tag
        function legacyUpdateCheck() {
            $.ajax({
                'url': 'https://ids.invoiceplane.com/updatecheck?cv=' + ip_version,
                'dataType': 'json',
                'timeout': ajaxTimeout,
                success: function(data) {
                    <?php echo IP_DEBUG ? 'console.log(data);' : ''; ?>
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
                        'timeout': ajaxTimeout,
                        success: function(data) {
                            <?php echo IP_DEBUG ? 'console.log(data);' : ''; ?>
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
                            <?php echo IP_DEBUG ? 'console.log(data);' : ''; ?>
                            $('#updatecheck-loading').addClass('hidden');
                            $('#updatecheck-failed').removeClass('hidden');
                        },
                    });
                },
            });
        }

        // Get the latest version from the GitHub releases
        $.ajax({
            'url': 'https://api.github.com/repos/InvoicePlane/InvoicePlane/releases/latest',
            'headers': {'Accept': 'application/vnd.github+json'},
            'dataType': 'json',
            'timeout': ajaxTimeout,
            success: function(data) {
                <?php echo IP_DEBUG ? 'console.log(data);' : ''; ?>
                var tag = (data.tag_name || '').replace(/^v/i, '');
                if (!tag) {
                    // GitHub answered but gave no release tag: use the legacy check
                    legacyUpdateCheck();
                    return;
                }
                // Compare each versions and replace the placeholder with a download button
                // or info label after 2 seconds
                setTimeout(function() {
                    if (update(ip_version, tag)) {
                        $('#updatecheck-updates-available').attr("href", data.html_url || "https://github.com/InvoicePlane/InvoicePlane/releases")
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
                // GitHub is unreachable, fall back to the legacy IDS update check
                legacyUpdateCheck();
            },
        });
        // Render the legacy IDS news list. Every field is set as text, never concatenated into HTML,
        // so a compromised or spoofed IDS answer cannot inject markup.
        function renderLegacyNews(list) {
            $('#ipnews-loading').addClass('hidden');
            list.forEach(function(news) {
                if (!news || typeof news !== 'object') {
                    return;
                }
                var ipnews = $('<div/>', {'class': 'alert alert-' + String(news.type || 'info').replace(/[^a-z0-9_-]/gi, '')});
                ipnews.append($('<b/>').text(String(news.title || '')));
                ipnews.append($('<br/>'));
                String(news.text || '').split(/\r?\n/).forEach(function(line, index) {
                    if (index > 0) {
                        ipnews.append($('<br/>'));
                    }
                    ipnews.append(document.createTextNode(line));
                });
                ipnews.append($('<br/>'));
                if (news.newsdate && news.newsdate.date) {
                    ipnews.append($('<small/>').text(<?php echo json_encode(trans('date')); ?> + ': ' + String(news.newsdate.date).substr(0, 11)));
                    ipnews.append($('<br/>'));
                }
                $('#ipnews-container').append(ipnews);
            });
        }
        // Second IDS host for the legacy news: used when the first one fails or does not answer with a list
        function legacyNewsOrg() {
            $.ajax({
                'url': 'https://ids.invoiceplane.org/get_news',
                'dataType': 'json',
                'timeout': ajaxTimeout,
                success: function(data) {
                    <?php echo IP_DEBUG ? 'console.log(data);' : ''; ?>
                    if (!Array.isArray(data)) {
                        // Not a news list either: show the failed state
                        $('#ipnews-loading').addClass('hidden');
                        $('#ipnews-failed').removeClass('hidden');
                        return;
                    }
                    setTimeout(function() { renderLegacyNews(data); }, checktime);
                },
                'error': function(data) {
                    <?php echo IP_DEBUG ? 'console.log(data);' : ''; ?>
                    $('#ipnews-loading').addClass('hidden');
                    $('#ipnews-failed').removeClass('hidden');
                },
            });
        }
        // Legacy IDS news, used when GitHub is unreachable or returns no release list
        function legacyNews() {
            $.ajax({
                'url': 'https://ids.invoiceplane.com/get_news',
                'dataType': 'json',
                'timeout': ajaxTimeout,
                success: function(data) {
                    <?php echo IP_DEBUG ? 'console.log(data);' : ''; ?>
                    if (!Array.isArray(data)) {
                        // Not a news list: ask the second IDS host, as for a failed request
                        legacyNewsOrg();
                        return;
                    }
                    setTimeout(function() { renderLegacyNews(data); }, checktime);
                },
                'error': function(data) {
                    legacyNewsOrg();
                },
            });
        }

        // Get the latest news from the GitHub releases
        $.ajax({
            'url': 'https://api.github.com/repos/InvoicePlane/InvoicePlane/releases?per_page=5',
            'headers': {'Accept': 'application/vnd.github+json'},
            'dataType': 'json',
            'timeout': ajaxTimeout,
            'success': function(data) {
                <?php echo IP_DEBUG ? 'console.log(data);' : ''; ?>
                if (!Array.isArray(data)) {
                    // Not a release list: use the legacy news
                    legacyNews();
                    return;
                }
                setTimeout(function() {
                    $('#ipnews-loading').addClass('hidden');
                    data.forEach(function(release) {
                        // Build the alert with DOM APIs only, never with HTML strings
                        var title = release.name || release.tag_name || '';
                        var date = (release.published_at || '').substr(0, 10);
                        var is_prerelease = release.prerelease
                            || /-(rc|beta|alpha|dev|pre|preview|nightly|snapshot|test)\d*/i.test(release.tag_name || '')
                            || /-(rc|beta|alpha|dev|pre|preview|nightly|snapshot|test)\d*/i.test(release.name || '');
                        var alert_class = is_prerelease ? 'alert alert-default' : 'alert alert-success';

                        // Same wording and layout as the old news list
                        var ipnews = $('<div/>', {'class': alert_class});
                        ipnews.append($('<b/>').text('RELEASE NOTICE - ' + title));
                        ipnews.append($('<br/>'));
                        ipnews.append(document.createTextNode(
                            'InvoicePlane ' + title + ' was released, update your version in order to protect your self and benefit from the last features and bugfixes. Visit: '
                        ));
                        var site_link = $('<a/>', {
                            'href': 'https://invoiceplane.com',
                            'target': '_blank',
                            'rel': 'noopener noreferrer'
                        });
                        site_link.text('https://invoiceplane.com');
                        ipnews.append(site_link);
                        ipnews.append(document.createTextNode(' to download'));
                        ipnews.append($('<br/>'));
                        ipnews.append($('<small/>').text(<?php echo json_encode(trans('date')); ?> + ': ' + date));
                        $('#ipnews-container').append(ipnews);
                    });
                }, checktime);
            },
            'error': function(data) {
                // GitHub is unreachable, fall back to the legacy IDS news
                legacyNews();
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
                <input type="text" class="form-control" value="<?php echo html_escape(get_setting('current_version')); ?>" readonly="readonly">
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

                <div id="ipnews-failed" class="btn btn-danger btn-sm disabled hidden">
                    <?php _trans('updatecheck_failed'); ?>
                </div>

                <div id="ipnews-container"></div>
            </div>

        </div>
    </div>

</div>
