
<div id="headerbar">
<h1 class="headerbar-title">
        Delete Document
</h1>

</div>

<div id="content">
    <?php echo $this->layout->load_view('layout/alerts'); ?>
	<div class="row">
	<div class="col-xs-12 col-sm-6 col-md-5">
	<h3><?php _trans('delete_document'); ?></h3>
	<br />

        <form method="post" enctype="multipart/form-data" action="<?php echo site_url('clients/document_del/'.$client_id."/".$document_id); ?>">

	<input type="hidden" name="<?php echo $this->config->item('csrf_token_name'); ?>"
           value="<?php echo $this->security->get_csrf_hash() ?>">
	<input type="hidden" name="del" value="1" />

	<div class="form-group">
		<?php _trans('delete_are_you_sure'); ?>
	</div>

        <div class="btn-group btn-group-sm index-options">
            <button type="submit" class="btn btn-delete"> 
		 <?php _trans('btn_delete'); ?>
		</button>
	</div>

        </form>
	</div>
    </div>
</div>

<?php
/*
 * vim: tabstop=4 shiftwidth=4 expandtab
 */
?>
