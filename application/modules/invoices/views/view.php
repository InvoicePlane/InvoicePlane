<script type="text/javascript">

    $(function () {

        $('.btn_add_product').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('products/ajax/modal_product_lookups'); ?>/" + Math.floor(Math.random() * 1000));
        });

        $('.btn_add_row').click(function () {
            $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
        });

        <?php if (!$items) { ?>
        $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
        <?php } ?>

        $('#btn_create_recurring').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_create_recurring'); ?>", {invoice_id: <?php echo $invoice_id; ?>});
        });

        $('#invoice_change_client').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_change_client'); ?>", {
                invoice_id: <?php echo $invoice_id; ?>,
                client_name: "<?php echo $this->db->escape_str($invoice->client_name); ?>"
            });
        });

        $('#btn_save_invoice').click(function () {
            var items = [];
            var item_order = 1;
            $('table tbody.item').each(function () {
                var row = {};
                $(this).find('input,select,textarea').each(function () {
                    if ($(this).is(':checkbox')) {
                        row[$(this).attr('name')] = $(this).is(':checked');
                    } else {
                        row[$(this).attr('name')] = $(this).val();
                    }
                });
                row['item_order'] = item_order;
                item_order++;
                items.push(row);
            });
            $.post("<?php echo site_url('invoices/ajax/save'); ?>", {
                    invoice_id: <?php echo $invoice_id; ?>,
                    invoice_number: $('#invoice_number').val(),
                    invoice_date_created: $('#invoice_date_created').val(),
                    invoice_date_due: $('#invoice_date_due').val(),
                    invoice_status_id: $('#invoice_status_id').val(),
                    invoice_password: $('#invoice_password').val(),
                    items: JSON.stringify(items),
                    invoice_discount_amount: $('#invoice_discount_amount').val(),
                    invoice_discount_percent: $('#invoice_discount_percent').val(),
                    invoice_terms: $('#invoice_terms').val(),
                    custom: $('input[name^=custom]').serializeArray(),
                    payment_method: $('#payment_method').val()
                },
                function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);
                    if (response.success == '1') {
                        window.location = "<?php echo site_url('invoices/view'); ?>/" + <?php echo $invoice_id; ?>;
                    }
                    else {
                        $('#fullpage-loader').hide();
                        $('.control-group').removeClass('has-error');
                        $('div.alert[class*="alert-"]').remove();
                        var resp_errors = response.validation_errors,
                            all_resp_errors = '';
                        for (var key in resp_errors) {
                            $('#' + key).parent().addClass('has-error');
                            all_resp_errors += resp_errors[key];
                        }
                        $('#invoice_form').prepend('<div class="alert alert-danger">' + all_resp_errors + '</div>');
                    }
                });
        });

        $('#btn_generate_pdf').click(function () {
            window.open('<?php echo site_url('invoices/generate_pdf/' . $invoice_id); ?>', '_blank');
        });

        <?php if ($invoice->is_read_only != 1): ?>
        var fixHelper = function (e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function (index) {
                $(this).width($originals.eq(index).width())
            });
            return $helper;
        };

        $("#item_table").sortable({
            items: 'tbody',
            helper: fixHelper
        });

        $(document).ready(function () {
            if ($('#invoice_discount_percent').val().length > 0) {
                $('#invoice_discount_amount').prop('disabled', true);
            }
            if ($('#invoice_discount_amount').val().length > 0) {
                $('#invoice_discount_percent').prop('disabled', true);
            }
        });
        $('#invoice_discount_amount').keyup(function () {
            if (this.value.length > 0) {
                $('#invoice_discount_percent').prop('disabled', true);
            } else {
                $('#invoice_discount_percent').prop('disabled', false);
            }
        });
        $('#invoice_discount_percent').keyup(function () {
            if (this.value.length > 0) {
                $('#invoice_discount_amount').prop('disabled', true);
            } else {
                $('#invoice_discount_amount').prop('disabled', false);
            }
        });
        <?php endif; ?>
    });

</script>

<?php
echo $modal_delete_invoice;
echo $modal_add_invoice_tax;
if ($this->config->item('disable_read_only') == true) {
    $invoice->is_read_only = 0;
}
?>

<div id="headerbar">
    <h1>
        <?php
        echo trans('invoice') . ' ';
        echo($invoice->invoice_number ? '#' . $invoice->invoice_number : $invoice->invoice_id);
        ?>
    </h1>

    <div
        class="pull-right <?php if ($invoice->is_read_only != 1 || $invoice->invoice_status_id != 4) { ?>btn-group<?php } ?>">

        <div class="options btn-group pull-left">
            <a class="btn btn-sm btn-default dropdown-toggle"
               data-toggle="dropdown" href="#">
                <i class="fa fa-caret-down no-margin"></i> <?php echo trans('options'); ?>
            </a>
            <ul class="dropdown-menu">
                <?php if ($invoice->is_read_only != 1) { ?>
                    <li>
                        <a href="#add-invoice-tax" data-toggle="modal">
                            <i class="fa fa-plus fa-margin"></i> <?php echo trans('add_invoice_tax'); ?>
                        </a>
                    </li>
                <?php } ?>
                <li>
                    <a href="#" id="btn_create_credit" data-invoice-id="<?php echo $invoice_id; ?>">
                        <i class="fa fa-minus fa-margin"></i> <?php echo trans('create_credit_invoice'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" class="invoice-add-payment"
                       data-invoice-id="<?php echo $invoice_id; ?>"
                       data-invoice-balance="<?php echo $invoice->invoice_balance; ?>"
                       data-invoice-payment-method="<?php echo $invoice->payment_method; ?>">
                        <i class="fa fa-credit-card fa-margin"></i>
                        <?php echo trans('enter_payment'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_generate_pdf"
                       data-invoice-id="<?php echo $invoice_id; ?>">
                        <i class="fa fa-print fa-margin"></i>
                        <?php echo trans('download_pdf'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo site_url('mailer/invoice/' . $invoice->invoice_id); ?>">
                        <i class="fa fa-send fa-margin"></i>
                        <?php echo trans('send_email'); ?>
                    </a>
                </li>
                <li class="divider"></li>
                <li>
                    <a href="#" id="btn_create_recurring"
                       data-invoice-id="<?php echo $invoice_id; ?>">
                        <i class="fa fa-repeat fa-margin"></i>
                        <?php echo trans('create_recurring'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_copy_invoice"
                       data-invoice-id="<?php echo $invoice_id; ?>">
                        <i class="fa fa-copy fa-margin"></i>
                        <?php echo trans('copy_invoice'); ?>
                    </a>
                </li>
                <?php if ($invoice->invoice_status_id == 1 || ($this->config->item('enable_invoice_deletion') === true && $invoice->is_read_only != 1)) { ?>
                    <li>
                        <a href="#delete-invoice" data-toggle="modal">
                            <i class="fa fa-trash-o fa-margin"></i>
                            <?php echo trans('delete'); ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <?php if ($invoice->is_read_only != 1) { ?>
            <a href="#" class="btn_add_row btn btn-sm btn-default">
                <i class="fa fa-plus"></i> <?php echo trans('add_new_row'); ?>
            </a>
            <a href="#" class="btn_add_product btn btn-sm btn-default">
                <i class="fa fa-database"></i>
                <?php echo trans('add_product'); ?>
            </a>
        <?php }
        if ($invoice->is_read_only != 1 || $invoice->invoice_status_id != 4) { ?>
            <a href="#" class="btn btn-sm btn-success ajax-loader" id="btn_save_invoice">
                <i class="fa fa-check"></i> <?php echo trans('save'); ?>
            </a>
        <?php } ?>
    </div>

    <div class="invoice-labels pull-right">
        <?php if ($invoice->invoice_is_recurring) { ?>
            <span class="label label-info"><?php echo trans('recurring'); ?></span>
        <?php } ?>
        <?php if ($invoice->is_read_only == 1) { ?>
            <span class="label label-danger">
                <i class="fa fa-read-only"></i> <?php echo trans('read_only'); ?>
            </span>
        <?php } ?>
    </div>

</div>

<div id="content">

    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <form id="invoice_form" class="form-horizontal">

        <div class="invoice">

            <div class="cf row">

                <div class="col-xs-12 col-md-5">
                    <div class="pull-left">

                        <h2>
                            <a href="<?php echo site_url('clients/view/' . $invoice->client_id); ?>"><?php echo $invoice->client_name; ?></a>
                            <?php if ($invoice->invoice_status_id == 1) { ?>
                                <span id="invoice_change_client" class="fa fa-edit cursor-pointer small"
                                      data-toggle="tooltip" data-placement="bottom"
                                      title="<?php echo trans('change_client'); ?>"></span>
                            <?php } ?>
                        </h2><br>
                        <span>
                            <?php echo ($invoice->client_address_1) ? $invoice->client_address_1 . '<br>' : ''; ?>
                            <?php echo ($invoice->client_address_2) ? $invoice->client_address_2 . '<br>' : ''; ?>
                            <?php echo ($invoice->client_city) ? $invoice->client_city : ''; ?>
                            <?php echo ($invoice->client_state) ? $invoice->client_state : ''; ?>
                            <?php echo ($invoice->client_zip) ? $invoice->client_zip : ''; ?>
                            <?php echo ($invoice->client_country) ? '<br>' . $invoice->client_country : ''; ?>
                        </span>
                        <br><br>
                        <?php if ($invoice->client_phone) { ?>
                            <span><strong><?php echo trans('phone'); ?>
                                    :</strong> <?php echo $invoice->client_phone; ?></span><br>
                        <?php } ?>
                        <?php if ($invoice->client_email) { ?>
                            <span><strong><?php echo trans('email'); ?>
                                    :</strong> <?php echo $invoice->client_email; ?></span>
                        <?php } ?>

                    </div>
                </div>

                <div class="col-xs-12 col-md-7">

                    <div class="details-box">

                        <div class=" row">

                            <?php if ($invoice->invoice_sign == -1) { ?>
                                <div class="col-xs-12">
                                <span class="label label-warning">
                                    <i class="fa fa-credit-invoice"></i>&nbsp;
                                    <?php echo trans('credit_invoice_for_invoice') . ' ';
                                    echo anchor('/invoices/view/' . $invoice->creditinvoice_parent_id,
                                        $invoice->creditinvoice_parent_id) ?>
                                </span>
                                </div>
                            <?php } ?>

                            <div class="col-xs-12 col-sm-6">

                                <div class="invoice-properties">
                                    <label><?php echo trans('invoice'); ?> #</label>
                                    <input type="text" id="invoice_number"
                                           class="input-sm form-control"
                                        <?php if ($invoice->invoice_number) : ?>
                                            value="<?php echo $invoice->invoice_number; ?>"
                                        <?php else : ?>
                                            placeholder="<?php echo trans('not_set'); ?>"
                                        <?php endif; ?>
                                        <?php if ($invoice->is_read_only == 1) {
                                            echo 'disabled="disabled"';
                                        } ?>>
                                </div>

                                <div class="invoice-properties has-feedback">
                                    <label><?php echo trans('date'); ?></label>

                                    <div class="input-group">
                                        <input name="invoice_date_created" id="invoice_date_created"
                                               class="form-control datepicker"
                                               value="<?php echo date_from_mysql($invoice->invoice_date_created); ?>"
                                            <?php if ($invoice->is_read_only == 1) {
                                                echo 'disabled="disabled"';
                                            } ?>>
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar fa-fw"></i>
                                    </span>
                                    </div>
                                </div>

                                <div class="invoice-properties has-feedback">
                                    <label><?php echo trans('due_date'); ?></label>

                                    <div class="input-group">
                                        <input name="invoice_date_due" id="invoice_date_due"
                                               class="form-control datepicker"
                                               value="<?php echo date_from_mysql($invoice->invoice_date_due); ?>"
                                            <?php if ($invoice->is_read_only == 1) {
                                                echo 'disabled="disabled"';
                                            } ?>>
		                                <span class="input-group-addon">
		                                    <i class="fa fa-calendar fa-fw"></i>
		                                </span>
                                    </div>
                                </div>

                            </div>


                            <div class="col-xs-12 col-sm-6">

                                <div class="invoice-properties">
                                    <label><?php echo trans('status');
                                        if ($invoice->is_read_only != 1 || $invoice->invoice_status_id != 4) {
                                            echo ' <span class="small">(' . trans('can_be_changed') . ')</span>';
                                        }
                                        ?>
                                    </label>
                                    <select name="invoice_status_id" id="invoice_status_id"
                                            class="form-control"
                                        <?php if ($invoice->is_read_only == 1 && $invoice->invoice_status_id == 4) {
                                            echo 'disabled="disabled"';
                                        } ?>>
                                        <?php foreach ($invoice_statuses as $key => $status) { ?>
                                            <option value="<?php echo $key; ?>"
                                                    <?php if ($key == $invoice->invoice_status_id) { ?>selected="selected"<?php } ?>>
                                                <?php echo $status['label']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="invoice-properties">
                                    <label><?php echo trans('payment_method'); ?></label>
                                    <select name="payment_method" id="payment_method" class="form-control"
                                        <?php if ($invoice->is_read_only == 1 && $invoice->invoice_status_id == 4) {
                                            echo 'disabled="disabled"';
                                        } ?>>
                                        <option value="0"><?php echo trans('select_payment_method'); ?></option>
                                        <?php foreach ($payment_methods as $payment_method) { ?>
                                            <option <?php if ($invoice->payment_method == $payment_method->payment_method_id) echo "selected" ?>
                                                value="<?php echo $payment_method->payment_method_id; ?>">
                                                <?php echo $payment_method->payment_method_name; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="invoice-properties">
                                    <label><?php echo trans('invoice_password'); ?></label>
                                    <input type="text" id="invoice_password"
                                           class="input-sm form-control"
                                           value="<?php echo $invoice->invoice_password; ?>"
                                        <?php if ($invoice->is_read_only == 1) {
                                            echo 'disabled="disabled"';
                                        } ?>>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <?php $this->layout->load_view('invoices/partial_item_table'); ?>

            <hr/>

            <div class="row">
                <div class="col-xs-12 col-sm-4">

                    <label><?php echo trans('invoice_terms'); ?></label>
                    <textarea id="invoice_terms" name="invoice_terms" class="form-control" rows="3"
                        <?php if ($invoice->is_read_only == 1) {
                            echo 'disabled="disabled"';
                        } ?>
                    ><?php echo $invoice->invoice_terms; ?></textarea>

                </div>
                <div class="col-xs-12 col-sm-8">

                    <label class="control-label"><?php echo trans('attachments'); ?></label>
                    <br/>
                    <!-- The fileinput-button span is used to style the file input field as button -->
                    <span class="btn btn-default fileinput-button">
                        <i class="fa fa-plus"></i>
                        <span><?php echo trans('add_files'); ?></span>
                    </span>

                    <!-- dropzone -->
                    <div class="row">
                        <div id="actions" class="col-xs-12 col-sm-12">
                            <div class="col-lg-7"></div>
                            <div class="col-lg-5">
                                <!-- The global file processing state -->
                                <span class="fileupload-process">
                                    <div id="total-progress" class="progress progress-striped active" role="progressbar"
                                         aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                        <div class="progress-bar progress-bar-success" style="width:0%;"
                                             data-dz-uploadprogress></div>
                                    </div>
                                </span>
                            </div>

                            <div id="previews" class="table table-condensed table-striped files">
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

                                        <div class="progress progress-striped active" role="progressbar"
                                             aria-valuemin="0"
                                             aria-valuemax="100" aria-valuenow="0">
                                            <div class="progress-bar progress-bar-success" style="..."
                                                 data-dz-uploadprogress></div>
                                        </div>
                                    </div>
                                    <div class="pull-left btn-group">
                                        <button data-dz-download class="btn btn-sm btn-primary">
                                            <i class="fa fa-download"></i>
                                            <span><?php echo trans('download'); ?></span>
                                        </button>
                                        <?php if ($invoice->is_read_only != 1) { ?>
                                            <button data-dz-remove class="btn btn-danger btn-sm delete">
                                                <i class="fa fa-trash-o"></i>
                                                <span><?php echo trans('delete'); ?></span>
                                            </button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- stop dropzone -->
                    </div>
                </div>
            </div>

            <?php if ($custom_fields): ?>
                <h4 class="no-margin"><?php echo trans('custom_fields'); ?></h4>
            <?php endif; ?>
            <?php foreach ($custom_fields as $custom_field) { ?>
                <label><?php echo $custom_field->custom_field_label; ?></label>
                <input type="text" class="form-control"
                       name="custom[<?php echo $custom_field->custom_field_column; ?>]"
                       id="<?php echo $custom_field->custom_field_column; ?>"
                       value="<?php echo form_prep($this->mdl_invoices->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>"
                    <?php if ($invoice->is_read_only == 1) {
                        echo 'disabled="disabled"';
                    } ?>>
            <?php } ?>


            <?php if ($invoice->invoice_status_id != 1) { ?>
                <p class="padded">
                    <?php echo trans('guest_url'); ?>:
                    <?php echo auto_link(site_url('guest/view/invoice/' . $invoice->invoice_url_key)); ?>
                </p>
            <?php } ?>

        </div>

    </form>

</div>
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
        dictRemoveFileConfirmation: '<?php echo trans('delete_attachment_warning'); ?>',
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
                    createDownloadButton(mockFile, '<?php echo base_url(); ?>uploads/customer_files/' + val.fullname);
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
        createDownloadButton(file, '<?php echo base_url() . 'uploads/customer_files/' . $invoice->invoice_url_key . '_' ?>' + file.name.replace(/\s+/g, '_'));
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
            data: {'name': file.name.replace(/\s+/g, '_')}
        });
    });

    function createDownloadButton(file, fileUrl) {
        var downloadButtonList = file.previewElement.querySelectorAll("[data-dz-download]");
        for (_i = 0; _i < downloadButtonList.length; _i++) {
            downloadButtonList[_i].addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                window.open(fileUrl);
                return false;
            });
        }
    }
</script>
