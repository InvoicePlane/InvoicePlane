<!DOCTYPE HTML>
<html>
    <body>
      <?php echo invoice_logo_pdf(); ?>
      <p>CUSTOM Variables ($custom_fields)</p>
      <pre><?php print_r($custom_fields); ?></pre>
      <hr>
      <p>ITEMS ($items)</p>
      <?php foreach ($items as $item) { ?>
        <pre><?php print_r($item); ?></pre>
      <?php } ?>
      <hr>
      <p>TAXES ($invoice_tax_rates)</p>
      <?php foreach ($invoice_tax_rates as $invoice_tax_rate) { ?>
        <pre><?php print_r($invoice_tax_rate); ?></pre>
      <?php } ?>
      <hr>
      <p>INVOICE Variables ($invoice)</p>
      <pre><?php print_r($invoice); ?></pre>
      <hr>
      <p>System Languange</p>
      <pre><?php print_r(get_setting('default_language')); ?></pre>
      <hr>
      <p>System Country</p>
      <pre><?php print_r(get_setting('default_country')); ?></pre>
      <hr>
      <p>Date Replacement<p>
      <pre>You must have something like {{{Month-3}}} {{{Month+1}}} in your item description to see test results here:<br />
      <?php
        // this should come from a helper function
        print_r(replaceDateTags($invoice->invoice_date_created, 
                                     $invoice->client_language, 
                                       $item->item_description));
        ?></pre>
    </body>
</html>
