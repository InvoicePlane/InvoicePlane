<script>
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

    const removeAllFilesButton = document.querySelector('.removeAllFiles-button');
    // Get the template HTML and remove it from the document
    var previewNode = document.querySelector('#template');
    previewNode.id = '';
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);

    var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
        url: '<?php echo site_url('upload/upload_file/' . $invoice->client_id . '/' . $invoice->invoice_url_key) ?>',
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        parallelUploads: 20,
        uploadMultiple: false,
        dictFileTooBig: `<?php _trans('upload_dz_invalid_file_size'); ?>`,
        dictFileSizeUnits: {<?php _trans('upload_dz_size_units'); ?>},
        dictRemoveFileConfirmation: `<?php _trans('delete_attachment_warning'); ?>`,
        dictInvalidFileType: `<?php _trans('upload_dz_invalid_file_type'); ?>`,
        acceptedFiles: '.<?php echo implode(',.', array_keys($this->mdl_uploads->content_types)); ?>', // allowed .ext1,.ext2,.ext3, ...
        previewTemplate: previewTemplate,
        autoQueue: true, // Make sure the files aren't queued until manually added
        previewsContainer: '#previews', // Define the container to display the previews
        clickable: '.fileinput-button', // Define the element that should be used as click trigger to select files.
        init: function () {
            thisDropzone = this;
            $.getJSON('<?php echo site_url('upload/show_files/' . $invoice->client_id . '/' . $invoice->invoice_url_key) ?>',
                function (data) {
                    $.each(data, function (index, val) {
                        displayExistingFile(val);
                    });
                    thisDropzone.files.length && removeAllFilesButtonShow(true);
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

    // File accepted, start upload
    myDropzone.on('sending', function (file, xhr, formData) {
        // Get new crsf_token for multiple upload on same page
        formData.append('<?php echo $this->config->item('csrf_token_name'); ?>', Cookies.get('<?php echo $this->config->item('csrf_cookie_name'); ?>'));
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
            url: '<?php echo site_url('upload/delete_file/' . $invoice->invoice_url_key) ?>',
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

    function displayExistingFile(val) {
        var name = sanitizeName(val.name);
        var imageUrl = ! name.match(/\.(avif|gif|jpe?g|png|svg|webp)$/i)
            ? getIcon(name)
            : '<?php echo site_url('upload/get_file/' . $invoice->invoice_url_key . '_'); ?>' + encodeURIComponent(name);

        var mockFile = {
            size: val.size,
            name: name,
            imageUrl: '<?php echo site_url('upload/get_file/' . $invoice->invoice_url_key . '_'); ?>' + encodeURIComponent(name)
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

    removeAllFilesButton.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        confirm(`<?php _trans('delete_attachments_warning'); ?>`) && myDropzone.removeAllFiles();
    });
    function removeAllFilesButtonShow(show) {
        if(show) removeAllFilesButton.classList.remove('hidden');
        else removeAllFilesButton.classList.add('hidden');
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
                    location.href = '<?php echo site_url('upload/get_file/' . $invoice->invoice_url_key . '_') ?>' +
                        encodeURIComponent(sanitizeName(file.name));
                    return false;
                }
            );
        }
    }
</script>
