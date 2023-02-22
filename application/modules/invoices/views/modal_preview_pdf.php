<script type="text/javascript">              
    $(function () {
        $('#modal-preview-pdf').modal('show');  
    });   
     
</script>

<div id="modal-preview-pdf" class="modal fade col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
     role="dialog" aria-labelledby="modal-preview-pdf" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">         
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>
            <h3><i class="fa fa-file-pdf-o"></i><?php echo '  ' . trans('invoice') . ': #' . $invoice->invoice_number ; ?></h3>            
        </div>
        <div class="modal-body" style="padding: 0; margin: 0; background-color: #ededf0;">
            <iframe id="iframe_pdf" src="<?php echo site_url('invoices/generate_pdf/' . $invoice_id); ?>#zoom=page-width" width="100%" height="70%" scrolling="no" 
                style="scrollable:false; overflow:hidden; border: none; height: 70vh; content: ''; clear: both; display: table;"></iframe> 
        </div>
        <div class="modal-footer" style="padding: 5px 15px 5px 5px;">
            <div class="btn-group">
                <button class="btn btn-danger btn-xs" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php echo trans('cancel'); ?>
                </button>
            </div>
        </div>
    </form>
</div>
