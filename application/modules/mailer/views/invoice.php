<script type="text/javascript">
    $(function () {
        var template_fields = ["body", "subject", "from_name", "from_email", "cc", "bcc", "pdf_template"];

        $('#email_template').change(function () {
            var email_template_id = $(this).val();

            if (email_template_id == '') return;

            $.post("<?php echo site_url('email_templates/ajax/get_content'); ?>", {
                email_template_id: email_template_id
            }, function (data) {
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
        <h1><?php echo lang('email_invoice'); ?></h1>

        <div class="pull-right btn-group">
            <button class="btn btn-sm btn-primary" name="btn_send" value="1">
                <i class="fa fa-send"></i>
                <?php echo lang('send'); ?>
            </button>
            <button class="btn btn-sm btn-danger" name="btn_cancel" value="1">
                <i class="fa fa-times"></i>
                <?php echo lang('cancel'); ?>
            </button>
        </div>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="to_email" class="control-label"><?php echo lang('to_email'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="to_email" id="to_email" class="form-control"
                       value="<?php echo $invoice->client_email; ?>">
            </div>
        </div>

        <hr>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="email_template" class="control-label"><?php echo lang('email_template'); ?>: </label>
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
                <label for="from_name" class="control-label"><?php echo lang('from_name'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="from_name" id="from_name" class="form-control"
                       value="<?php echo $invoice->user_name; ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="from_email" class="control-label"><?php echo lang('from_email'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="from_email" id="from_email" class="form-control"
                       value="<?php echo $invoice->user_email; ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="cc" class="control-label"><?php echo lang('cc'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="cc" id="cc" value="" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="bcc" class="control-label"><?php echo lang('bcc'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="bcc" id="bcc" value="" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="subject" class="control-label"><?php echo lang('subject'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="subject" id="subject" class="form-control"
                       value="<?php echo lang('invoice') . ' #' . $invoice->invoice_number; ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="pdf_template" class="control-label">
                    <?php echo lang('pdf_template'); ?>:
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
                <label for="body" class="control-label"><?php echo lang('body'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <textarea name="body" id="body" class="form-control" rows="8"></textarea>
            </div>

            <script src="<?php echo base_url(); ?>assets/default/js/libs/sceditor.min.js"></script>
            <script>
                $(document).ready(function(){
                    $("#body").sceditor({
                        toolbar: "bold,italic,underline|left,center,right|font|size,color,code|image,email,link|source",
                        fonts: "Sans-serif,Serif",
                        width: "auto",
                        emoticonsEnabled: false
                    });
                });
            </script>
        </div>

    </div>

</form>
