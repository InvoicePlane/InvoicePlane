<script>
    function getIcon(fullname) {
        var fileFormat = fullname.match(/\.([A-z0-9]{1,5})$/);
        if (fileFormat) {
            fileFormat = fileFormat[1];
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
        return fileIcon;
    }
    
    // Get the template HTML and remove it from the document
    var previewNode = document.querySelector('#template');
    previewNode.id = '';
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);
    var csrf_hash = '<?php echo $this->security->get_csrf_hash(); ?>';

    var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
        url: '<?php echo site_url('upload/upload_file/' . $invoice->client_id . '/client/' . $invoice->invoice_url_key) ?>',
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        parallelUploads: 20,
        uploadMultiple: true,
        previewTemplate: previewTemplate,
        autoQueue: true, // Make sure the files aren't queued until manually added
        previewsContainer: '#previews', // Define the container to display the previews
        clickable: '.fileinput-button', // Define the element that should be used as click trigger to select files.
        success: function(file, response){
            if (response) {
                response = JSON.parse(response);
                if (response.success) {
                    csrf_hash = response.csrf_hash;
                }
            }
        },
        init: function () {
            thisDropzone = this;
            $.getJSON('<?php echo site_url('upload/upload_file/' . $invoice->client_id . '/client/' . $invoice->invoice_url_key) ?>',
                function (data) {
                    $.each(data, function (index, val) {
                        var mockFile = {fullname: val.fullname, size: val.size, name: val.name};

                        thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                        createDownloadButton(mockFile, '<?php echo site_url('upload/get_file/'); ?>' + val.fullname);

                        if (val.fullname.toLowerCase().match(/\.(jpg|jpeg|png|gif)$/)) {
                            thisDropzone.options.thumbnail.call(thisDropzone, mockFile,
                                '<?php echo site_url('upload/get_file/'); ?>' + val.fullname);
                        }
                        else {
                            fileIcon = getIcon(val.fullname);

                            thisDropzone.options.thumbnail.call(thisDropzone, mockFile,
                                '<?php echo base_url('assets/core/img/file-icons/'); ?>' + fileIcon + '.svg');
                        }

                        thisDropzone.emit('complete', mockFile);
                        thisDropzone.emit('success', mockFile);
                    });
                });
        },
    });

    myDropzone.on('addedfile', function (file) {
        var fileIcon = getIcon(file.name);
        myDropzone.emit('thumbnail', file,
            '<?php echo base_url('assets/core/img/file-icons/'); ?>' + fileIcon + '.svg');
        createDownloadButton(file, '<?php echo site_url('upload/get_file/' . $invoice->invoice_url_key . '_') ?>' +
            file.name.replace(/\s+/g, '_'));
    });

    // Update the total progress bar
    myDropzone.on('totaluploadprogress', function (progress) {
        document.querySelector('#total-progress .progress-bar').style.width = progress + '%';
    });

    myDropzone.on('sendingmultiple', function (file, xhr, formData) {
        // Show the total progress bar when upload starts
        formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', csrf_hash);
        document.querySelector('#total-progress').style.opacity = '1';
    });

    // Hide the total progress bar when nothing's uploading anymore
    myDropzone.on('queuecomplete', function (progress) {
        document.querySelector('#total-progress').style.opacity = '0';
    });

    myDropzone.on('removedfile', function (file) {
        $.post({
            url: '<?php echo site_url('upload/delete_file/' . $invoice->invoice_url_key) ?>',
            data: {
                name: file.name,
                '<?php echo $this->security->get_csrf_token_name(); ?>': csrf_hash
            },
            success: function(response){
                if (response) {
                    response = JSON.parse(response);
                    if (response.success) {
                        csrf_hash = response.csrf_hash;
                    }
                }
            }
        });
    });

    function createDownloadButton(file, fileUrl) {
        var downloadButtonList = file.previewElement.querySelectorAll('[data-dz-download]');
        for (var $i = 0; $i < downloadButtonList.length; $i++) {
            downloadButtonList[$i].addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                location.href = fileUrl;
                return false;
            });
        }
    }
</script>
