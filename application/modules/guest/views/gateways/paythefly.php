<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <i class="fa fa-bitcoin"></i>
            <?php echo lang('paythefly_pay_with_crypto'); ?>
        </h3>
    </div>
    <div class="panel-body">

        <div class="text-center" id="paythefly-loading">
            <i class="fa fa-spinner fa-spin fa-2x"></i>
            <p class="text-muted"><?php echo lang('paythefly_generating_payment'); ?></p>
        </div>

        <div id="paythefly-content" style="display: none;">

            <!-- Chain Selection -->
            <div class="form-group">
                <label for="paythefly-chain"><?php echo lang('paythefly_select_chain'); ?></label>
                <select id="paythefly-chain" class="form-control">
                    <option value="BSC" <?php echo ($default_chain === 'BSC') ? 'selected' : ''; ?>>
                        BNB Smart Chain (BSC) — BNB
                    </option>
                    <option value="TRON" <?php echo ($default_chain === 'TRON') ? 'selected' : ''; ?>>
                        TRON — TRX
                    </option>
                </select>
            </div>

            <!-- Payment Summary -->
            <div class="well well-sm">
                <div class="row">
                    <div class="col-xs-6">
                        <strong><?php echo lang('invoice'); ?>:</strong>
                    </div>
                    <div class="col-xs-6 text-right">
                        #<?php echo htmlspecialchars($invoice->invoice_number); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <strong><?php echo lang('amount_due'); ?>:</strong>
                    </div>
                    <div class="col-xs-6 text-right">
                        <?php echo format_currency($invoice->invoice_balance); ?>
                    </div>
                </div>
                <div class="row" id="paythefly-chain-info">
                    <div class="col-xs-6">
                        <strong><?php echo lang('paythefly_chain'); ?>:</strong>
                    </div>
                    <div class="col-xs-6 text-right" id="paythefly-chain-display">
                        BNB Smart Chain
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <strong><?php echo lang('paythefly_payment_deadline'); ?>:</strong>
                    </div>
                    <div class="col-xs-6 text-right" id="paythefly-deadline-display">
                        —
                    </div>
                </div>
            </div>

            <!-- Pay Button -->
            <div class="text-center">
                <a href="#" id="paythefly-pay-btn" class="btn btn-success btn-lg" target="_blank">
                    <i class="fa fa-external-link"></i>
                    <?php echo lang('paythefly_proceed_to_payment'); ?>
                </a>
                <br><br>
                <p class="text-muted small">
                    <i class="fa fa-shield"></i>
                    <?php echo lang('paythefly_secure_notice'); ?>
                </p>
            </div>

            <!-- Info Notices -->
            <div class="alert alert-info small" style="margin-top: 15px;">
                <i class="fa fa-info-circle"></i>
                <?php echo lang('paythefly_payment_info'); ?>
            </div>

        </div>

        <!-- Error State -->
        <div id="paythefly-error" style="display: none;">
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-triangle"></i>
                <?php echo lang('paythefly_payment_error'); ?>
            </div>
            <div class="text-center">
                <button class="btn btn-default" onclick="loadPaymentInfo()">
                    <i class="fa fa-refresh"></i> <?php echo lang('paythefly_retry'); ?>
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    var invoiceUrlKey = '<?php echo $invoice_url_key; ?>';
    var paymentInfoUrl = '<?php echo site_url("guest/gateways/paythefly/get_payment_info/" . $invoice_url_key); ?>';
    var chainNames = {
        'BSC': 'BNB Smart Chain',
        'TRON': 'TRON Network'
    };

    function loadPaymentInfo() {
        var chain = $('#paythefly-chain').val();

        // Show loading, hide content and error
        $('#paythefly-loading').show();
        $('#paythefly-content').hide();
        $('#paythefly-error').hide();

        $.ajax({
            url: paymentInfoUrl + '?chain=' + chain,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                // Update the pay button URL
                $('#paythefly-pay-btn').attr('href', response.payment_url);

                // Update chain display
                $('#paythefly-chain-display').text(chainNames[response.chain] || response.chain);

                // Update deadline display
                var deadline = new Date(response.deadline * 1000);
                $('#paythefly-deadline-display').text(deadline.toLocaleTimeString());

                // Show content
                $('#paythefly-loading').hide();
                $('#paythefly-content').show();
            },
            error: function() {
                $('#paythefly-loading').hide();
                $('#paythefly-error').show();
            }
        });
    }

    $(function() {
        // Load payment info on page load
        loadPaymentInfo();

        // Reload on chain change
        $('#paythefly-chain').on('change', function() {
            loadPaymentInfo();
        });
    });
</script>
