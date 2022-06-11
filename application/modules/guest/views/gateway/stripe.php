<div id="payment-form">
  <div id="card-element">
    <!-- Elements will create input elements here -->
  </div>

  <!-- We'll put the error messages in this element -->
  <div id="card-errors" role="alert"></div>

  <br></br>

  <a onclick="process_payment()" class="btn btn-success btn-lg ajax-loader">Pay now</a>
</div>
<script src="/application/modules/guest/views/gateway/stripe.js"></script>