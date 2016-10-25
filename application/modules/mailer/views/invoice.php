<script type="text/javascript">
    $(function () {
        var template_fields = ["body", "subject", "from_name", "from_email", "cc", "bcc", "pdf_template"];

        $('#email_template').change(function () {
            var email_template_id = $(this).val();

            if (email_template_id == '') return;

            $.post("<?php echo site_url('email_templates/ajax/get_content'); ?>", {
                email_template_id: email_template_id
            }, function (data) {
                <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                inject_email_template(template_fields, JSON.parse(data));
            });
        });

        var selected_email_template = <?php echo $email_template ?>;
        inject_email_template(template_fields, selected_email_template);
    });
</script>

<form method="post" class="form-horizontal"
      action="<?php echo site_url('mailer/send_invoice/' . $invoice->invoice_id) ?>">

    <div id="headerbar">
        <h1><?php echo trans('email_invoice'); ?></h1>

        <div class="pull-right btn-group">
            <button class="btn btn-sm btn-primary ajax-loader" name="btn_send" value="1">
                <i class="fa fa-send"></i>
                <?php echo trans('send'); ?>
            </button>
            <button class="btn btn-sm btn-danger" name="btn_cancel" value="1">
                <i class="fa fa-times"></i>
                <?php echo trans('cancel'); ?>
            </button>
        </div>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="to_email" class="control-label"><?php echo trans('to_email'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="to_email" id="to_email" class="form-control"
                       value="<?php echo $invoice->client_email; ?>">
            </div>
        </div>

        <hr>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="email_template" class="control-label"><?php echo trans('email_template'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <select name="email_template" id="email_template" class="form-control">
                    <option value=""></option>
                    <?php foreach ($email_templates as $email_template): ?>
                        <option value="<?php echo $email_template->email_template_id; ?>"
                                <?php if ($selected_email_template == $email_template->email_template_id) { ?>selected="selected"<?php } ?>><?php echo $email_template->email_template_title; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="from_name" class="control-label"><?php echo trans('from_name'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="from_name" id="from_name" class="form-control"
                       value="<?php echo $invoice->user_name; ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="from_email" class="control-label"><?php echo trans('from_email'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="from_email" id="from_email" class="form-control"
                       value="<?php echo $invoice->user_email; ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="cc" class="control-label"><?php echo trans('cc'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="cc" id="cc" value="" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="bcc" class="control-label"><?php echo trans('bcc'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="bcc" id="bcc" value="" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="subject" class="control-label"><?php echo trans('subject'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="subject" id="subject" class="form-control"
                       value="<?php echo trans('invoice') . ' #' . $invoice->invoice_number; ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="pdf_template" class="control-label">
                    <?php echo trans('pdf_template'); ?>:
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <select name="pdf_template" id="pdf_template" class="form-control">
                    <option value=""></option>
                    <?php foreach ($pdf_templates as $pdf_template): ?>
                        <option value="<?php echo $pdf_template; ?>"
                                <?php if ($selected_pdf_template == $pdf_template):
                                ?>selected="selected"<?php endif; ?>>
                            <?php echo $pdf_template; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="email_template_body">
                    <?php echo trans('body'); ?>:
                </label>
            </div>

            <div class="col-xs-12 col-sm-6">
                <div class="html-tags btn-group btn-group-sm">
                    <span class="html-tag btn btn-default" data-tag-type="text-paragraph">
                        <i class="fa fa-paragraph"></i>
                    </span>
                    <span class="html-tag btn btn-default" data-tag-type="text-bold">
                        <i class="fa fa-bold"></i>
                    </span>
                    <span class="html-tag btn btn-default" data-tag-type="text-italic">
                        <i class="fa fa-italic"></i>
                    </span>
                </div>
                <div class="html-tags btn-group btn-group-sm">
                    <span class="html-tag btn btn-default" data-tag-type="text-h1">H1</span>
                    <span class="html-tag btn btn-default" data-tag-type="text-h2">H2</span>
                    <span class="html-tag btn btn-default" data-tag-type="text-h3">H3</span>
                    <span class="html-tag btn btn-default" data-tag-type="text-h4">H4</span>
                </div>
                <div class="html-tags btn-group btn-group-sm">
                    <span class="html-tag btn btn-default" data-tag-type="text-code">
                        <i class="fa fa-code"></i>
                    </span>
                    <span class="html-tag btn btn-default" data-tag-type="text-hr">
                        &lt;hr/&gt;
                    </span>
                    <span class="html-tag btn btn-default" data-tag-type="text-css">
                        CSS
                    </span>
                </div>

                <textarea name="body" id="body" style="height: 200px;"
                          class="email-template-body form-control taggable"></textarea>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php echo trans('preview'); ?>
                        <span id="email-template-preview-reload" class="pull-right cursor-pointer">
                            <i class="fa fa-refresh"></i>
                        </span>
                    </div>
                    <div class="panel-body">
                        <iframe id="email-template-preview"></iframe>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label class="control-label"><?php echo trans('attachments'); ?>: </label>
            </div>
            <!-- dropzone -->
            <div id="actions" class="col-xs-12 col-sm-6 row">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="fa fa-plus"></i>
                     <span><?php echo trans('add_files'); ?></span>
                </span>

                <div class="col-lg-7"></div>
                <div class="col-lg-5">
                    <!-- The global file processing state -->
                    <span class="fileupload-process">
                      <div id="total-progress" class="progress progress-striped active" role="progressbar"
                           aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                          <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
                      </div>
                    </span>
                </div>

                <div class="table table-striped" class="files" id="previews">

                    <div id="template" class="file-row">
                        <!-- This is used as the file preview template -->
                        <div>
                            <span class="preview"><img data-dz-thumbnail/></span>
                        </div>
                        <div>
                            <p class="name" data-dz-name></p>
                            <strong class="error text-danger" data-dz-errormessage></strong>
                        </div>
                        <div>
                            <p class="size" data-dz-size></p>

                            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0"
                                 aria-valuemax="100" aria-valuenow="0">
                                <div class="progress-bar progress-bar-success" style="width:0%;"
                                     data-dz-uploadprogress></div>
                            </div>
                        </div>
                        <div>
                            <button data-dz-remove class="btn btn-danger delete">
                                <i class="glyphicon glyphicon-trash"></i>
                                <span><?php echo trans('delete'); ?></span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
            <!-- stop dropzone -->
        </div>

</form>
<script>
    // Get the template HTML and remove it from the document
    var previewNode = document.querySelector("#template");
    previewNode.id = "";
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);
    var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
        url: "<?php echo site_url('upload/upload_file/' . $invoice->client_id . '/' . $invoice->invoice_url_key) ?>", // Set the url
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        parallelUploads: 20,
        uploadMultiple: false,
        previewTemplate: previewTemplate,
        autoQueue: true, // Make sure the files aren't queued until manually added
        previewsContainer: "#previews", // Define the container to display the previews
        clickable: ".fileinput-button", // Define the element that should be used as click trigger to select files.
        init: function () {
            thisDropzone = this;
            $.getJSON("<?php echo site_url('upload/upload_file/' . $invoice->client_id . '/' . $invoice->invoice_url_key) ?>", function (data) {
                $.each(data, function (index, val) {
                    var mockFile = {fullname: val.fullname, size: val.size, name: val.name};
                    thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                    if (val.fullname.match(/\.(jpg|jpeg|png|gif)$/)) {
                        thisDropzone.options.thumbnail.call(thisDropzone, mockFile,
                            '<?php echo base_url(); ?>uploads/customer_files/' + val.fullname);
                    } else {
                        thisDropzone.options.thumbnail.call(thisDropzone, mockFile,
                            '<?php echo base_url(); ?>assets/default/img/favicon.png');
                    }
                    thisDropzone.emit("complete", mockFile);
                    thisDropzone.emit("success", mockFile);
                });
            });
        }
    });

    myDropzone.on("addedfile", function (file) {
        myDropzone.emit("thumbnail", file, '<?php echo base_url(); ?>assets/default/img/favicon.png');
    });

    // Update the total progress bar
    myDropzone.on("totaluploadprogress", function (progress) {
        document.querySelector("#total-progress .progress-bar").style.width = progress + "%";
    });

    myDropzone.on("sending", function (file) {
        // Show the total progress bar when upload starts
        document.querySelector("#total-progress").style.opacity = "1";
    });

    // Hide the total progress bar when nothing's uploading anymore
    myDropzone.on("queuecomplete", function (progress) {
        document.querySelector("#total-progress").style.opacity = "0";

    });

    myDropzone.on("removedfile", function (file) {
        $.ajax({
            url: "<?php echo site_url('upload/delete_file/' . $invoice->invoice_url_key) ?>",
            type: "POST",
            data: {'name': file.name}
        });
    });
</script>
