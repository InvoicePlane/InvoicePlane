<script type="text/javascript">              
    $(function () {
        $('#modal-view-pdf').modal('show');  
    }); 
    

function resizeIFrameToFitContent( iFrame ) {

    iFrame.width  = iFrame.contentWindow.document.body.scrollWidth;
    iFrame.height = iFrame.contentWindow.document.body.scrollHeight;
}

window.addEventListener('DOMContentReady', function(e) {

    var iFrame = document.getElementById( 'iFrame1' );
    resizeIFrameToFitContent( iFrame );

    // or, to resize all iframes:
    var iframes = document.querySelectorAll("iframe");
    for( var i = 0; i < iframes.length; i++) {
        resizeIFrameToFitContent( iframes[i] );
    }
} );    
     
</script>

<div id="modal-view-pdf" class="modal fade col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
     role="dialog" aria-labelledby="modal-view-pdf" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">         
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>
            <h3><i class="fa fa-file-pdf-o"></i><?php echo '  ' . trans('invoice') . ': #' . $invoice->invoice_number ; ?></h3>            
        </div>
        <div class="modal-body" style="padding: 0; background-color: #404040;">
            <iframe id="iFrame1" src="<?php echo site_url('invoices/generate_pdf/' . $invoice_id); ?>#zoom=page-width" width="100%" scrolling="no" style="scrollable:false; overflow:hidden; border: 0; height: 70vh;"></iframe> 
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
