<?php
$cv = $this->controller->view_data["custom_values"];
?>
<script>
    function getIcon(fullname) {
        var fileFormat = fullname.match(/\.([A-z0-9]{1,5})$/);
        if (fileFormat) {
            fileFormat = fileFormat[1];
        }
        else {
            fileFormat = "";
        }

        var fileIcon = "default";

        switch (fileFormat) {
            case "pdf":
                fileIcon = "file-pdf";
                break;

            case "mp3":
            case "wav":
            case "ogg":
                fileIcon = "file-audio";
                break;

            case "doc":
            case "docx":
            case "odt":
                fileIcon = "file-document";
                break;

            case "xls":
            case "xlsx":
            case "ods":
                fileIcon = "file-spreadsheet";
                break;

            case "ppt":
            case "pptx":
            case "odp":
                fileIcon = "file-presentation";
                break;
        }
        return fileIcon;
    }
    $(function () {
        $('.btn_add_product').click(function () {
            $('#modal-placeholder').load(
                "<?php echo site_url('products/ajax/modal_product_lookups'); ?>/" + Math.floor(Math.random() * 1000)
            );
        });

        $('.btn_add_row').click(function () {
            $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
        });

        <?php if (!$items) { ?>
        $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
        <?php } ?>

        $('#btn_create_recurring').click(function () {
            $('#modal-placeholder').load(
                "<?php echo site_url('invoices/ajax/modal_create_recurring'); ?>",
                {
                    invoice_id: <?php echo $invoice_id; ?>
                }
            );
        });

        $('#invoice_change_client').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_change_client'); ?>", {
                invoice_id: <?php echo $invoice_id; ?>,
                client_id: "<?php echo $this->db->escape_str($invoice->client_id); ?>"
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
                    invoice_sumex_reason: $("#invoice_sumex_reason").val(),
                    invoice_sumex_treatmentstart: $("#invoice_sumex_treatmentstart").val(),
                    invoice_sumex_treatmentend: $("#invoice_sumex_treatmentend").val(),
                    invoice_sumex_casedate: $("#invoice_sumex_casedate").val(),
                    invoice_sumex_casenumber: $("#invoice_sumex_casenumber").val(),
                    invoice_sumex_diagnosis: $("#invoice_sumex_diagnosis").val(),
                    invoice_sumex_observations: $("#invoice_sumex_observations").val(),
                    items: JSON.stringify(items),
                    invoice_discount_amount: $('#invoice_discount_amount').val(),
                    invoice_discount_percent: $('#invoice_discount_percent').val(),
                    invoice_terms: $('#invoice_terms').val(),
                    custom: $('input[name^=custom],select[name^=custom]').serializeArray(),
                    payment_method: $('#payment_method').val()
                },
                function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);
                    if (response.success === 1) {
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
            window.open('<?php echo site_url('invoices/generate_sumex_copy/' . $invoice_id); ?>', '_blank');
        });

        $('#btn_sumex').click(function () {
            window.open('<?php echo site_url('invoices/generate_sumex_pdf/' . $invoice_id); ?>', '_blank');
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
    <h1 class="headerbar-title">
        <?php
        echo($invoice->invoice_number ? '#' . $invoice->invoice_number : $invoice->invoice_id);
        ?>
    </h1>

    <div class="headerbar-item pull-right <?php if ($invoice->is_read_only != 1 || $invoice->invoice_status_id != 4) { ?>btn-group<?php } ?>">

        <div class="options btn-group pull-left">
            <a class="btn btn-sm btn-default dropdown-toggle"
               data-toggle="dropdown" href="#">
                <i class="fa fa-caret-down no-margin"></i> <?php _trans('options'); ?>
            </a>
            <ul class="dropdown-menu">
                <?php if ($invoice->is_read_only != 1) { ?>
                    <li>
                        <a href="#add-invoice-tax" data-toggle="modal">
                            <i class="fa fa-plus fa-margin"></i> <?php _trans('add_invoice_tax'); ?>
                        </a>
                    </li>
                <?php } ?>
                <li>
                    <a href="#" id="btn_create_credit" data-invoice-id="<?php echo $invoice_id; ?>">
                        <i class="fa fa-minus fa-margin"></i> <?php _trans('create_credit_invoice'); ?>
                    </a>
                </li>
                <?php if ($invoice->invoice_balance != 0) : ?>
                    <li>
                        <a href="#" class="invoice-add-payment"
                           data-invoice-id="<?php echo $invoice_id; ?>"
                           data-invoice-balance="<?php echo $invoice->invoice_balance; ?>"
                           data-invoice-payment-method="<?php echo $invoice->payment_method; ?>">
                            <i class="fa fa-credit-card fa-margin"></i>
                            <?php _trans('enter_payment'); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="#" id="btn_generate_pdf"
                       data-invoice-id="<?php echo $invoice_id; ?>">
                        <i class="fa fa-file-text fa-margin"></i>
                        <?php _trans('generate_copy'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_sumex"
                       data-invoice-id="<?php echo $invoice_id; ?>">
                        <i class="fa fa-user-md fa-margin"></i>
                        <?php _trans('generate_sumex'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo site_url('mailer/invoice/' . $invoice->invoice_id); ?>">
                        <i class="fa fa-send fa-margin"></i>
                        <?php _trans('send_email'); ?>
                    </a>
                </li>
                <li class="divider"></li>
                <li>
                    <a href="#" id="btn_create_recurring"
                       data-invoice-id="<?php echo $invoice_id; ?>">
                        <i class="fa fa-repeat fa-margin"></i>
                        <?php _trans('create_recurring'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_copy_invoice"
                       data-invoice-id="<?php echo $invoice_id; ?>">
                        <i class="fa fa-copy fa-margin"></i>
                        <?php _trans('copy_invoice'); ?>
                    </a>
                </li>
                <?php if ($invoice->invoice_status_id == 1 || ($this->config->item('enable_invoice_deletion') === true && $invoice->is_read_only != 1)) { ?>
                    <li>
                        <a href="#delete-invoice" data-toggle="modal">
                            <i class="fa fa-trash-o fa-margin"></i>
                            <?php _trans('delete'); ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <?php if ($invoice->is_read_only != 1 || $invoice->invoice_status_id != 4) { ?>
            <a href="#" class="btn btn-sm btn-success ajax-loader" id="btn_save_invoice">
                <i class="fa fa-check"></i> <?php _trans('save'); ?>
            </a>
        <?php } ?>
    </div>

    <div class="headerbar-item invoice-labels pull-right">
        <?php if ($invoice->invoice_is_recurring) { ?>
            <span class="label label-info"><?php _trans('recurring'); ?></span>
        <?php } ?>
        <?php if ($invoice->is_read_only == 1) { ?>
            <span class="label label-danger">
                <i class="fa fa-read-only"></i> <?php _trans('read_only'); ?>
            </span>
        <?php } ?>
    </div>

</div>

<div id="content">
    <?php echo $this->layout->load_view('layout/alerts'); ?>
    <div id="invoice_form">
        <div class="invoice">
            <div class="cf row">
                <div class="col-xs-12 col-md-8">
                    <div class="col-md-6">
                        <h2>
                            <a href="<?php echo site_url('clients/view/' . $invoice->client_id); ?>"><?php echo format_client($invoice) ?></a>
                            <?php if ($invoice->invoice_status_id == 1) { ?>
                                <span id="invoice_change_client" class="fa fa-edit cursor-pointer small"
                                      data-toggle="tooltip" data-placement="bottom"
                                      title="<?php echo htmlentities(trans('change_client')); ?>"></span>
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
                        <?php if ($invoice->client_phone): ?>
                            <span>
                              <strong><?php _trans('phone'); ?>:</strong>
                                <?php echo $invoice->client_phone; ?>
                            </span>
                            <br>
                        <?php endif; ?>
                        <?php if ($invoice->client_email): ?>
                            <span>
                              <strong><?php _trans('email'); ?>:</strong>
                                <?php echo $invoice->client_email; ?>
                            </span>
                        <?php endif; ?>
                        <br><br>
                        <?php echo '<b>' . trans('birthdate') . ':</b> ' . format_date($invoice->client_birthdate); ?>
                        <br>
                        <?php echo '<b>' . trans('gender') . ':</b> ' . format_gender($invoice->client_gender); ?>
                    </div>
                    <div class="col-md-6">
                        <h3><?php _trans('treatment'); ?></h3>
                        <br>
                        <div class="col-xs-12 col-md-8">
                            <table class="items table">
                                <tr>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-addon"><?php _trans('start'); ?></span>
                                            <input id="invoice_sumex_treatmentstart" name="sumex_treatmentstart"
                                                   class="input-sm form-control datepicker"
                                                   value="<?php echo date_from_mysql($invoice->sumex_treatmentstart); ?>"
                                                   type="text">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-addon"><?php _trans('end'); ?></span>
                                            <input id="invoice_sumex_treatmentend" name="sumex_treatmentend"
                                                   class="input-sm form-control datepicker"
                                                   value="<?php echo date_from_mysql($invoice->sumex_treatmentend); ?>"
                                                   type="text">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-addon"><?php _trans('reason'); ?></span>
                                            <select name="invoice_sumex_reason" id="invoice_sumex_reason"
                                                    class="form-control input-sm simple-select">
                                                <?php $reasons = ['disease', 'accident', 'maternity', 'prevention', 'birthdefect', 'unknown']; ?>
                                                <?php foreach ($reasons as $key => $reason): ?>
                                                    <?php $selected = ($invoice->sumex_reason == $key ? " selected" : ""); ?>
                                                    <option value="<?php echo $key; ?>"<?php echo $selected; ?>><?php _trans('reason_' . $reason); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-addon"><?php _trans('case_date'); ?></span>
                                            <input id="invoice_sumex_casedate" name="sumex_casedate"
                                                   class="input-sm form-control datepicker"
                                                   value="<?php echo date_from_mysql($invoice->sumex_treatmentend); ?>"
                                                   type="text">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-addon"><?php _trans('case_number'); ?></span>
                                            <input id="invoice_sumex_casenumber" name="sumex_casenumber"
                                                   class="input-sm form-control"
                                                   value="<?php echo htmlentities($invoice->sumex_casenumber); ?>"
                                                   type="text">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-addon"><?php _trans('invoice_sumex_diagnosis'); ?></span>
                                            <input id="invoice_sumex_diagnosis" name="invoice_sumex_diagnosis"
                                                   class="input-sm form-control"
                                                   value="<?php echo htmlentities($invoice->sumex_diagnosis); ?>"
                                                   type="text" maxlength="500">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">

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

                            <div class="col-xs-12 col-sm-12">

                                <div class="invoice-properties">
                                    <label><?php _trans('status');
                                        if ($invoice->is_read_only != 1 || $invoice->invoice_status_id != 4) {
                                            echo ' <span class="small">(' . trans('can_be_changed') . ')</span>';
                                        }
                                        ?>
                                    </label>
                                    <select name="invoice_status_id" id="invoice_status_id"
                                            class="form-control simple-select"
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
                                    <label><?php _trans('invoice'); ?> #</label>
                                    <input type="text" id="invoice_number"
                                           class="input-sm form-control"
                                        <?php if ($invoice->invoice_number) : ?>
                                            value="<?php echo $invoice->invoice_number; ?>"
                                        <?php else : ?>
                                            placeholder="<?php _trans('not_set'); ?>"
                                        <?php endif; ?>
                                        <?php if ($invoice->is_read_only == 1) {
                                            echo 'disabled="disabled"';
                                        } ?>>
                                </div>

                                <div class="invoice-properties has-feedback">
                                    <label><?php _trans('date'); ?></label>

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
                                    <label><?php _trans('due_date'); ?></label>

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

                                <!-- Custom fields -->
                                <?php foreach ($custom_fields as $custom_field): ?>
                                    <?php if ($custom_field->custom_field_location != 1) {
                                        continue;
                                    } ?>
                                    <?php print_field($this->mdl_invoices, $custom_field, $cv); ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <?php $this->layout->load_view('invoices/partial_item_table'); ?>

            <hr/>

            <div class="row">
                <div class="col-xs-12 col-sm-4">

                    <label><?php _trans('sumex_observations'); ?></label>
                    <textarea id="invoice_sumex_observations" name="invoice_sumex_observations" class="form-control"
                              rows="3"
                        <?php if ($invoice->is_read_only == 1) {
                            echo 'disabled="disabled"';
                        } ?>
                    ><?php echo $invoice->sumex_observations; ?></textarea>

                </div>

                <div class="col-xs-12 col-sm-8">

                    <label class="control-label"><?php _trans('attachments'); ?></label>
                    <br/>
                    <!-- The fileinput-button span is used to style the file input field as button -->
                    <span class="btn btn-default fileinput-button">
                        <i class="fa fa-plus"></i>
                        <span><?php _trans('add_files'); ?></span>
                    </span>

                    <!-- dropzone -->
                    <div class="row">
                        <div id="actions" class="col-xs-12 col-sm-12">
                            <div class="col-lg-7"></div>
                            <div class="col-lg-5">
                                <!-- The global file processing state -->
                                <div class="fileupload-process">
                                    <div id="total-progress" class="progress progress-striped active" role="progressbar"
                                         aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                        <div class="progress-bar progress-bar-success" style="width:0%;"
                                             data-dz-uploadprogress></div>
                                    </div>
                                </div>
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
                                            <span><?php _trans('download'); ?></span>
                                        </button>
                                        <?php if ($invoice->is_read_only != 1) { ?>
                                            <button data-dz-remove class="btn btn-danger btn-sm delete">
                                                <i class="fa fa-trash-o"></i>
                                                <span><?php _trans('delete'); ?></span>
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
                <?php $cv = $this->controller->view_data["custom_values"]; ?>
                <div class="row">
                    <div class="col-xs-12">
                        <fieldset>
                            <legend><?php _trans('custom_fields'); ?></legend>
                            <div class="col-xs-6">
                                <?php $i = 0; ?>
                                <?php foreach ($custom_fields as $custom_field): ?>
                                    <?php if ($custom_field->custom_field_location != 0) {
                                        continue;
                                    } ?>
                                    <?php $i++; ?>
                                    <?php if ($i % 2 != 0): ?>
                                        <?php print_field($this->mdl_invoices, $custom_field, $cv); ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <div class="col-xs-6">
                                <?php $i = 0; ?>
                                <?php foreach ($custom_fields as $custom_field): ?>
                                    <?php if ($custom_field->custom_field_location != 0) {
                                        continue;
                                    } ?>
                                    <?php $i++; ?>
                                    <?php if ($i % 2 == 0): ?>
                                        <?php print_field($this->mdl_invoices, $custom_field, $cv); ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </fieldset>
                    </div>
                </div>
            <?php endif; ?>


            <?php if ($invoice->invoice_status_id != 1) { ?>
                <p class="padded">
                    <?php _trans('guest_url'); ?>:
                    <?php echo auto_link(site_url('guest/view/invoice/' . $invoice->invoice_url_key)); ?>
                </p>
            <?php } ?>

        </div>

    </div>

</div>
<script>
    // Get the template HTML and remove it from the document
    var previewNode = document.querySelector("#template");
    previewNode.id = "";
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);

    var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
        url: "<?php echo site_url('upload/upload_file/' . $invoice->client_id . '/' . $invoice->invoice_url_key) ?>",
        params: {
            _ip_csrf: Cookies.get('ip_csrf_cookie')
        },
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        parallelUploads: 20,
        uploadMultiple: false,
        dictRemoveFileConfirmation: '<?php _trans('delete_attachment_warning'); ?>',
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
                    createDownloadButton(mockFile, '<?php echo site_url('upload/get_file'); ?>/' + val.fullname);

                    if (val.fullname.match(/\.(jpg|jpeg|png|gif)$/)) {
                        thisDropzone.options.thumbnail.call(thisDropzone, mockFile,
                            '<?php echo site_url('upload/get_file'); ?>/' + val.fullname);
                    } else {
                        fileIcon = getIcon(val.fullname);

                        thisDropzone.options.thumbnail.call(thisDropzone, mockFile,
                            '<?php echo base_url('assets/core/img/file-icons/'); ?>' + fileIcon + '.svg');
                    }

                    thisDropzone.emit("complete", mockFile);
                    thisDropzone.emit("success", mockFile);
                });
            });
        }
    });

    myDropzone.on("success", function (file, response) {
        <?php echo(IP_DEBUG ? 'console.log(response);' : ''); ?>
        if (typeof response !== 'undefined') {
            response = JSON.parse(response);
            if (response.success !== true) {
                alert(response.message);
            }
        }
    });

    myDropzone.on("addedfile", function (file) {
        var fileIcon = getIcon(file.name);
        myDropzone.emit("thumbnail", file,
            '<?php echo base_url('assets/core/img/file-icons/'); ?>' + fileIcon + '.svg');
        createDownloadButton(file, '<?php echo site_url('upload/get_file/' . $invoice->invoice_url_key . '_') ?>' + file.name.replace(/\s+/g, '_'));
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
        $.post({
            url: "<?php echo site_url('upload/delete_file/' . $invoice->invoice_url_key) ?>",
            data: {
                'name': file.name.replace(/\s+/g, '_'),
                _ip_csrf: Cookies.get('ip_csrf_cookie')
            }
        }, function (response) {
            <?php echo(IP_DEBUG ? 'console.log(response);' : ''); ?>
        });
    });

    function createDownloadButton(file, fileUrl) {
        var downloadButtonList = file.previewElement.querySelectorAll("[data-dz-download]");
        for (var $i = 0; $i < downloadButtonList.length; $i++) {
            downloadButtonList[$i].addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                window.open(fileUrl);
                return false;
            });
        }
    }
</script>
