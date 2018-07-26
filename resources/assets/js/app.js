/*
 * InvoicePlane
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (C) 2014 - 2018 InvoicePlane
 * @license     https://invoiceplane.com/license
 * @link        https://invoiceplane.com
 *
 * Based on FusionInvoice by Jesse Terry (FusionInvoice, LLC)
 */

$(document).ready(function(){

  // Sidebar toggling
  $(document).on('click', '.sidebar-toggle', function(e){
    e.preventDefault();
    $('body').toggleClass('sidebar-visible');
  });
});
