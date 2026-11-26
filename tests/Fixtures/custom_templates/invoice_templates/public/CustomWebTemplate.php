<?php

/*
 * Fixture for GuestViewCustomTemplateTest (InvoicePlane/InvoicePlane#1780).
 *
 * Deliberately mirrors the one line of the built-in InvoicePlane_Web.php public template
 * that a real custom template copied from it would also carry: a call to
 * $this->layout->load_view(), which only works when render_template_view() includes the
 * file bound to the controller instance.
 */
?>
CUSTOM-TEMPLATE-MARKER-<?php echo htmlsc($invoice->invoice_number); ?>
<?php echo $this->layout->load_view('layout/alerts'); ?>
