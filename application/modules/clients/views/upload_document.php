<form method="post" enctype="multipart/form-data" action="<?php echo site_url('clients/do_upload_document/'.$client->client_id); ?>">

    <div id="headerbar">

        <h1 class="headerbar-title">
            <?php _trans('upload_document_for'); ?>
            <?php echo $client->client_name; ?>
            <?php echo $client->client_surname; ?>
            (<?php echo $client->client_id; ?>)
        </h1>

        <?php $this->layout->load_view('layout/header_buttons'); ?>
        <script>$('#btn-submit').html('<i class="fa fa-check"></i> <?php _trans('upload'); ?>');</script>

    </div>

    <div id="content">

        <?php echo $this->layout->load_view('layout/alerts'); ?>

        <div class="row">

            <div class="col-xs-12 col-sm-6 col-md-5">

                <h3><?php _trans('upload_document'); ?></h3>

                    <input type="hidden" name="<?php echo $this->config->item('csrf_token_name'); ?>"
                           value="<?php echo $this->security->get_csrf_hash() ?>">
<!-- TODO
                    <div class="form-group">
                        <label for"document_description"><?php _trans('description'); ?></label>
                        <textarea id="document_description" name="document_description" type="text" class="form-control" rows=3 cols=40></textarea>
                    </div>
-->
                    <div class="form-group">
                        <label><?php _trans('select_document'); ?></label>
                        <input class="form-control" type="file" name="document" required>
                    </div>

                </div>

            </div>

        </div>

    </div>

</form>
