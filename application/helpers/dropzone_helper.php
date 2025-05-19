<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2025 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

/**
 * Show html for dropzone (upload module).
 *
 * @param $read_only : Like $invoice->is_read_only
 *
 * @return void
 * */
function _dropzone_html($read_only = true)
{
?>
<div class="panel panel-default no-margin">

    <div class="panel-heading"><?php _trans('attachments'); ?></div>

    <div class="panel-body clearfix">
        <!-- The fileinput-button span is used to style the file input field as button -->
        <button type="button" class="btn btn-sm btn-default fileinput-button<?php echo $read_only ? ' hide" readonly="readonly' : ''; ?>">
            <i class="fa fa-plus"></i> <?php _trans('add_files'); ?>
        </button>
<?php
    if ( ! $read_only) {
?>
        <button type="button" class="btn btn-sm btn-danger removeAllFiles-button pull-right hidden">
            <i class="fa fa-trash-o"></i> <?php _trans('delete_attachments'); ?>
        </button>
<?php
    }
?>
        <!-- dropzone -->
        <div class="row">
            <div id="actions" class="col-xs-12">
                <div class="col-xs-12 col-md-6 col-lg-7"></div>
                <div class="col-xs-12 col-md-6 col-lg-5">
                    <!-- The global file processing state -->
                    <div class="fileupload-process">
                        <div id="total-progress" class="progress progress-striped active"
                             role="progressbar"
                             aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                            <div class="progress-bar progress-bar-success" style="width:0%;"
                                 data-dz-uploadprogress>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="previews" class="table table-condensed files no-margin">
                    <div id="template" class="row file-row">
                        <!-- This is used as the file preview template -->
                        <div class="col-xs-3 col-md-4">
                            <span class="preview pull-left"><img data-dz-thumbnail/></span>
                        </div>
                        <div class="col-xs-5 col-md-4">
                            <p class="size pull-left" data-dz-size></p>
                            <div class="progress progress-striped active pull-right" role="progressbar"
                                 aria-valuemin="0"
                                 aria-valuemax="100" aria-valuenow="0">
                                <div class="progress-bar progress-bar-success" style="width:0%"
                                     data-dz-uploadprogress>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-4 col-md-4">
                            <div class="pull-right btn-group">
                                <button data-dz-download class="btn btn-sm btn-primary">
                                    <i class="fa fa-download"></i>
                                    <span><?php _trans('download'); ?></span>
                                </button>
<?php
    if ( ! $read_only) {
?>
                                <button data-dz-remove class="btn btn-sm btn-danger delete">
                                    <i class="fa fa-trash-o"></i>
                                    <span><?php _trans('delete'); ?></span>
                                </button>
<?php
    }
?>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-8">
                            <p class="name pull-left" data-dz-name></p>
                            <strong class="error text-danger pull-right" data-dz-errormessage></strong>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- stop dropzone -->

    </div>
</div>


<?php
}

/*
 * Show script for dropzone (upload module)
 * @param str $url_key   : Like $quote->quote_url_key, $invoice->invoice_url_key
 * @param str $client_id : Like $quote->client_id, $invoice->client_id
 * @param str $site_url  : upload/ (module by Default)
 * @param arr $acceptedExts
 * @return void
 * */
function _dropzone_script($url_key = null, $client_id = 1, $site_url = '', $acceptedExts = null)
{
    $site_url = site_url(empty($site_url) ? 'upload/' : (mb_rtrim($site_url, '/') . '/'));

    // Allow extentions system
    $content_types = [];
    if ($acceptedExts === null) {
        // Default
        $CI = & get_instance();
        $CI->load->model('upload/mdl_uploads');
        $content_types = array_keys($CI->mdl_uploads->content_types);
    } elseif (is_array($acceptedExts)) {
        // User Overide
        $content_types = $acceptedExts;
    }

    $guest = $acceptedExts === false ? 'true' : 'false';
?>
<script>
const
site_url        = '<?php echo $site_url; ?>',
client_id       = '/<?php echo $client_id; ?>',
url_key         = '/<?php echo $url_key; ?>',
url_get_file    = site_url + 'get_file'    + url_key,
url_show_file   = site_url + 'show_files'  + url_key,
url_delete_file = site_url + 'delete_file' + url_key,
url_upload_file = site_url + 'upload_file' + client_id + url_key,
acceptedExts    = '.<?php echo implode(',.', $content_types); ?>'; // allowed .ext1,.ext2,.ext3, ...

    function getIcon(fullname) {
        var fileFormat = fullname.match(/\.([A-z0-9]{1,5})$/);
        if (fileFormat) {
            fileFormat = fileFormat[1].toLowerCase();
        }
        else {
            fileFormat = '';
        }

        var fileIcon = 'default';

        switch (fileFormat) {
            case 'pdf':
                fileIcon = 'file-pdf';
                break;

            case 'mp3':
            case 'wav':
            case 'oga':
            case 'ogg':
                fileIcon = 'file-audio';
                break;

            case 'doc':
            case 'docx':
            case 'odt':
                fileIcon = 'file-document';
                break;

            case 'xls':
            case 'xlsx':
            case 'ods':
                fileIcon = 'file-spreadsheet';
                break;

            case 'ppt':
            case 'pptx':
            case 'odp':
                fileIcon = 'file-presentation';
                break;
        }
        return '<?php echo base_url('assets/core/img/file-icons/'); ?>' + fileIcon + '.svg';
    }

    // Clean filename (same of sanitize_file_name in Upload.php)
    function sanitizeName(filename) {
        return filename.trim().replace(/[^\p{L}\p{N}\s\-_'’.]/gu, '');
    }

    const is_guest = <?php echo $guest; ?>;
    const removeAllFilesButton = document.querySelector('.removeAllFiles-button');
    // Get the template HTML and remove it from the document
    var previewNode = document.querySelector('#template');
    previewNode.id = '';
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);

    var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
        url: url_upload_file,
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        parallelUploads: 20,
        uploadMultiple: false,
        dictFileTooBig: `<?php _trans('upload_dz_invalid_file_size'); ?>`,
        dictFileSizeUnits: {<?php _trans('upload_dz_size_units'); ?>},
        dictRemoveFileConfirmation: `<?php _trans('delete_attachment_warning'); ?>`,
        dictInvalidFileType: `<?php _trans('upload_dz_invalid_file_type'); ?>`,
        acceptedFiles: acceptedExts, // allowed .ext1,.ext2,.ext3, ...
        previewTemplate: previewTemplate,
        autoQueue: true, // Make sure the files aren't queued until manually added
        previewsContainer: '#previews', // Define the container to display the previews
        clickable: '.fileinput-button', // Define the element that should be used as click trigger to select files.
        init: function () {
            thisDropzone = this;
            $.getJSON(url_show_file,
                function (data) {
                    $.each(data, function (index, val) {
                        displayExistingFile(val);
                    });
                    thisDropzone.files.length && ! is_guest && removeAllFilesButtonShow(true);
                }
            );
        },
    });
    // Uploading process complete
    myDropzone.on('complete', function (file) {
        // Check if logged out (303) redirected to sessions/login. .
        if(file.xhr && file.xhr.responseURL.match(/sessions\/login/) !== null) {
            this.emit('error', file, `<?php _trans('upload_dz_disconnected'); ?>`);
        }
    });

    // Uploading process error
    myDropzone.on('error', function (file, message) {
        <?php echo (IP_DEBUG ? 'console.log("dropzone error", file, message, this);' : '') . PHP_EOL; ?>
        alert(file.name + "\n\n" + message + (file.accepted ? '' : "\n\n(📎👌: " + this.options.acceptedFiles.replace(/\./g, ' ').trim() + ')'));
        file.previewElement.remove();
        // Remove last in list. (Important to determine delete attachements button show or not)
        this.files.pop();
    });

    // Update the name to reflect same after sanitized by php, set download button & thumbnail (temporary or not)
    myDropzone.on('addedfile', function (file) {
        changeTextName(file);
        createDownloadButton(file);
        this.emit('thumbnail', file, getIcon(file.name));
    });

    // Update the total progress bar
    myDropzone.on('totaluploadprogress', function (progress) {
        document.querySelector('#total-progress .progress-bar').style.width = progress + '%';
    });

<?php
    if ($acceptedExts !== false) {
?>
    // File accepted, start upload
    myDropzone.on('sending', function (file, xhr, formData) {
        // Get new crsf_token for multiple upload on same page
        formData.append('<?php echo config_item('csrf_token_name'); ?>', Cookies.get('<?php echo config_item('csrf_cookie_name'); ?>'));
        // Show the total progress bar
        document.querySelector('#total-progress').style.opacity = '1';
    });

    // Hide & reset the total progress bar when nothing's uploading anymore
    myDropzone.on('queuecomplete', function () {
        document.querySelector('#total-progress').style.opacity = '0';
        // Reset after hidden
        window.setTimeout(function(){document.querySelector('#total-progress .progress-bar').style.width = '0%';}, 300);
        // Show delete_attachements button if have a file
        this.files.length && removeAllFilesButtonShow(true);
    });

    myDropzone.on('removedfile', function (file) {
        // Disable delete attachments button
        removeAllFilesButton.disabled = true;
        var val = file; // Make global: Need in function if response status 410 (undeletable file)
        $.post({
            url: url_delete_file,
            data: {
                name: encodeURIComponent(sanitizeName(file.name))
            },
            statusCode: {
                // upload_error_file_delete
                410: function (response) {
                    alert(sanitizeName(val.name) + "\n\n" + response.responseText );
                    displayExistingFile(val);
                }
            }
        })
        .done(function (response) {
            // Unreachable redirect (Status 303). If html (have doctype) maybe logged out.
            if (response.match(/DOCTYPE/i) !== null) {
                alert(sanitizeName(val.name) + "\n\n" + `<?php _trans('upload_dz_disconnected'); ?>`);
                displayExistingFile(val);
                return;
            }
            // All it's ok, enable delete attachments button
            removeAllFilesButton.disabled = false;
        })
        .always(function (response) {
            myDropzone.files.length || removeAllFilesButtonShow(false);
            // Reset the total progress bar (the remove process set to 100%)
            document.querySelector('#total-progress .progress-bar').style.width = '0%';
        });
    });

    removeAllFilesButton.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        confirm(`<?php _trans('delete_attachments_warning'); ?>`) && myDropzone.removeAllFiles();
    });
    function removeAllFilesButtonShow(show) {
        if(show) removeAllFilesButton.classList.remove('hidden');
        else removeAllFilesButton.classList.add('hidden');
    }

<?php
    } // End if $acceptedExts === false
?>

    function displayExistingFile(val) {
        var name = sanitizeName(val.name);
        var imageUrl = ! name.match(/\.(avif|gif|jpe?g|png|svg|webp)$/i)
            ? getIcon(name)
            : url_get_file + '_'  + encodeURIComponent(name);

        var mockFile = {
            size: val.size,
            name: name,
            imageUrl: url_get_file + '_' + encodeURIComponent(name)
        };

        // mockFile needs to have these attributes { name: 'name', size: 12345, imageUrl: '' }
        myDropzone.displayExistingFile(
            mockFile,
            imageUrl,
            null, // callback
            null, // crossOrigin
            false // resizeThumbnail
        );
        myDropzone.files.push(mockFile); // Important (Need for delete attachements)
        myDropzone.emit('success', mockFile); // Hide progress
    }

    // Update the name to reflect same after sanitized by php
    function changeTextName(file) {
        for (var node of file.previewElement.querySelectorAll('[data-dz-name]')) {
            node.textContent = sanitizeName(file.name);
        }
    }

    // Set download button
    function createDownloadButton(file) {
        for (var node of file.previewElement.querySelectorAll('[data-dz-download]')) {
            node.addEventListener('click',
                function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    location.href = url_get_file + '_' + encodeURIComponent(sanitizeName(file.name));
                    return false;
                }
            );
        }
    }
</script>

<?php
} // End function _dropzone_script
