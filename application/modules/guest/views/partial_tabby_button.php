<?php
if (tabby_available($invoice)) {
    ?>
<form class="btn-group" method="post" action="<?php echo site_url('guest/gateways/tabby/checkout/' . $invoice->invoice_url_key); ?>">
    <?php _csrf_field(); ?>
    <button type="submit" class="btn <?php echo htmlsc($button_class); ?>">
        <i class="fa fa-credit-card"></i> <?php _trans('pay_later_with_tabby'); ?>
    </button>
</form>
<?php
}
